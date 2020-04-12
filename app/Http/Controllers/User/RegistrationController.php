<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Mail\RegisterShipped;
use Illuminate\Support\Facades\App;
use Mail;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    // 登録画面表示
    public function index()
    {
        log::debug('User/Registration/index');

        // 空の配列を用意
        $data['validation'] = $this->get_init_array();
        $data['params']     = $this->get_init_array();

        return view('user.registration.index', $data);
    }

    // 仮登録
    public function regist(Request $request)
    {
        log::debug('User/Registration/regist');

        $params = $this->getUserRegistrationParams($request);
        $data['params'] = $params;

        // 登録ボタンが押された時の処理
        // ログインパラメータのバリデーション
        $data['validation'] = $this->validate_user_registration_params($params, true);
        if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
        {
            return view('user.registration.index', $data);
        }

        // 4桁の認証コードを生成
        $pass_phrase = "";
        for ($i = 0; $i < USER_REGISTRATION_PASS_PHRASE_LENGTH; $i++) {
            $pass_phrase .= rand(0, 9);
        }

        // 仮登録処理
        $now = Carbon::now();
        $temp_user = [
            'mail_address'  => $params["mail_address"],
            'name'          => $params["name"],
            'password'      => Hash::make($params['password']),
            'pass_phrase'   => $pass_phrase,
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        try
        {
            $temp_user_id = DB::table("temp_users")->insertGetId($temp_user);
            log::info('temp_usersレコードを登録しました。id: ' . $temp_user_id);
        }
        catch (\Exception $e)
        {
            $err_msg = "登録処理に失敗しました。管理者へお問い合わせください。";
            $data['validation']['err_msg_array'] = array($err_msg);
            log::error($err_msg);
            log::error($e->getMessage());
            return view('user.registration.index', $data);
        }

        // 仮登録完了メールを送信する
        $sendData = [
            'pass_phrase' => $pass_phrase,
        ];
        Mail::to($params["mail_address"])->send(new RegisterShipped($sendData));

        return view('user.registration.auth', $temp_user);
    }

    // 認証処理
    public function auth(Request $request)
    {
        log::debug('User/Registration/auth');

        // パラメータ取得
        $data['mail_address'] = $request->input('mail_address');
        $data['auth_code']    = $request->input('auth_code');

        if (empty($data['auth_code']))
        {
            $data['validation']['err_msg_array'][] = '認証コードを入力してください。';
            return view('user.registration.auth', $data);
        }

        // メールアドレスで仮登録ユーザを検索
        $temp_user = $this->getTempUserByMailAddress($data['mail_address']);

        // 仮登録が未済 または有効期限切れの場合は、有効期限切れページを表示
        if (empty($temp_user))
        {
            log::info('仮登録有効期限切れ mail_address: ' . $data['mail_address']);
            return view('user.registration.expired');
        }

        // コード認証
        if ($temp_user->pass_phrase != $data['auth_code'])
        {
            $data['validation']['err_msg_array'][] = '認証コードが違います。';
            return view('user.registration.auth', $data);
        }

        // --- 認証成功 ---
        try {
            $this->deleteTempUserById($temp_user->id);

            // usersテーブルにデータを登録
            $now = Carbon::now();
            $user['name']         = $temp_user->name;
            $user['mail_address'] = $temp_user->mail_address;
            $user['password']     = $temp_user->password;
            $user['created_at']   = $now;
            $user['updated_at']   = $now;

            DB::table('users')->insert($user);
            log::info('usersレコードを登録しました。' . implode(",", $user));
        } catch (\Exception $e) {
            $data['validation']['err_msg_array'] = array('ユーザ登録処理に失敗しました。管理者へお問い合わせください。');
            log::error("本登録処理に失敗しました。");
            log::error($e);
            return view('user.registration.auth', $data);
        }
        return view('user.registration.complete');
    }

    private function get_init_array()
    {
        // 初期化
        $array['name']   = "";
        $array['mail_address']              = "";
        $array['password']                  = "";
        $array['password_confirmation']     = "";
        return $array;
    }

    // 登録画面に入力された値を配列で取得する
    private function getUserRegistrationParams(Request $request)
    {
        // パラメータを取得
        $params["name"]                  = $request->input('name');
        $params["mail_address"]          = $request->input('mail_address');
        $params["password"]              = $request->input('password');
        $params["password_confirmation"] = $request->input('password_confirmation');
        return $params;
    }

    // バリデーションメソッド
    private function validate_user_registration_params($params, $confirmation = false)
    {
        $validation = $this->get_init_array();

        // ニックネーム
        if (empty($params["name"]))
        {
            $validation['err_msg_array'][] = 'ニックネームを入力してください。';
            $validation['name'] = VALIDATION_ERR_CLASS;

        }
        elseif (mb_strlen($params["name"]) >= USER_NAME_MAX_LENGTH)
        {
            $validation['err_msg_array'][] = 'ニックネームは'.USER_NAME_MAX_LENGTH.'文字以内で入力してください。';
            $validation['name'] = VALIDATION_ERR_CLASS;
        }
        else
        {
            // 重複チェック
            // ユーザ情報をDBから取得
            $user      = DB::table('users')->where('name', $params["name"])->first();
            $temp_user = DB::table('temp_users')->where('name', $params["name"])->first();
            if (isset($user) || isset($temp_user))
            {
                $validation['err_msg_array'][] = 'すでに登録されているニックネームです。';
                $validation['name'] = VALIDATION_ERR_CLASS;
            }
        }

        // メールアドレス
        if (empty($params["mail_address"]))
        {
            $validation['err_msg_array'][] = 'メールアドレスを入力してください。';
            $validation['mail_address'] = VALIDATION_ERR_CLASS;
        }
        elseif (!preg_match("/^[a-zA-Z0-9_.+-]+[@][a-zA-Z0-9.-]+$/", $params["mail_address"]))
        {
            $validation['err_msg_array'][] = 'メールアドレスに登録できない文字が含まれている、または不正なメールアドレスです。';
            $validation['mail_address'] = VALIDATION_ERR_CLASS;
        }
        else
        {
            // 重複チェック
            // ユーザ情報をDBから取得
            $user      = DB::table('users')->where('mail_address', $params["mail_address"])->first();
            $temp_user = DB::table('temp_users')->where('mail_address', $params["mail_address"])->first();
            if (isset($user) || isset($temp_user))
            {
                $validation['err_msg_array'][] = 'すでに登録されているメールアドレスです。';
                $validation['mail_address'] = VALIDATION_ERR_CLASS;
            }
        }

        // パスワード
        if (empty($params["password"]))
        {
            $validation['err_msg_array'][] = 'パスワードを入力してください。';
            $validation['password'] = VALIDATION_ERR_CLASS;
        }
        elseif (!preg_match("/^[a-zA-Z0-9]{".USER_PASSWORD_MIN_LENGTH.",".USER_PASSWORD_MAX_LENGTH."}+$/", $params["password"]))
        {
            $validation['err_msg_array'][] = 'パスワードは半角英数字' . USER_PASSWORD_MIN_LENGTH . '文字〜' . USER_PASSWORD_MAX_LENGTH . '文字で入力してください。';
            $validation['password'] = VALIDATION_ERR_CLASS;
        }
        // 確認用パスワード
        elseif (!$confirmation &&
                (empty($params["password_confirmation"]) ||
                strcmp($params["password"], $params["password_confirmation"])))
        {
            $validation['err_msg_array'][] = '確認用パスワードは同じものを入力してください。';
            $validation['password_confirmation'] = VALIDATION_ERR_CLASS;
        }
        return $validation;
    }

    // メールアドレスで仮登録ユーザを検索
    private function getTempUserByMailAddress($mail_address)
    {
        return DB::table('temp_users')
            ->where('mail_address', $mail_address)
            //->where('pass_phrase', $pass_phrase)
            ->where('created_at', '>=', Carbon::now()->subMinutes(USER_REGISTRATION_AUTH_EXPIRATION))  // 仮登録の期限は30分間
            ->whereNull('deleted_at')
            ->first();
    }

    // 仮登録ユーザ削除
    private function deleteTempUserById($id)
    {
        // temp_usersテーブルからレコードを削除
        DB::table('temp_users')->where('id', $id)->delete();
        log::info('temp_usersレコードを削除しました。id: ' . $id);
    }
}
