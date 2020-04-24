<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use App\Model\AppInfo;
use App\Model\LoginUser;
use App\Model\User;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private $VIEW_INDEX = 'login.index';

    // ログイン画面
    public function index(Request $request)
    {
        log::debug('Login/index');
        return view($this->VIEW_INDEX);
    }

    // ログイン認証
    public function login_auth(Request $request)
    {
        log::debug('Login/login_auth');

        // パラメータを取得
        $mail_address = $request->input('mail_address');
        $password     = $request->input('password');
        $data['mail_address'] = $mail_address;

        // ログインパラメータのバリデーション
        $data['validation'] = $this->validate_login_params($mail_address, $password);
        if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
        {
            return view($this->VIEW_INDEX, $data);
        }

        // ユーザ情報をDBから取得
        $user = User::getUserByMailAddress($mail_address);
        // DBに未登録なメールアドレスの場合
        if (empty($user))
        {
            $data['validation']['err_msg_array'] = array('メールアドレス または パスワードが正しくありません。');
            return view($this->VIEW_INDEX, $data);
        }

        // パスワードが異なる場合
        if (!Hash::check($password, $user->password))
        {
            // TODO: ログイン失敗連続回数を更新する
            // TODO: アカウントロックを実装する
            $data['validation']['err_msg_array'] = array('メールアドレス または パスワードが正しくありません。');
            return view($this->VIEW_INDEX, $data);
        }

        // banされている場合
        if ($user->ban != 0)
        {
            $data['validation']['err_msg_array'] = array('このアカウントは凍結されています。');
            return view($this->VIEW_INDEX, $data);
        }

        // ロックされている場合
        if ($user->lock_at >= Carbon::now()->subMinutes(USER_LOCK_MINUTES))
        {
            $data['validation']['err_msg_array'] = array('このアカウントはロックされています。解除されるまでしばらくお待ちください。');
            return view($this->VIEW_INDEX, $data);
        }

        // --- 認証成功 ---
        $access_token = LoginUser::createAccessToken();
        DB::beginTransaction();
        try
        {
            // DBにセッションIDを登録する
            LoginUser::insertLoginUser($user->id, $access_token, GAME_COLLECTION_MGR_APP_ID);

            // // 最終ログイン日時を更新する
            // DB::table('users')->where('id', $user->id)
            //     ->update([
            //                 'last_login' => $now,
            //             ]);
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            $data['validation']['err_msg_array'] = array('ログインに失敗しました。管理者へお問い合わせください。');
            log::error($e);
            log::error("ログイン処理に失敗しました。rollbackしたため、データメンテは不要です。");
            return view($this->VIEW_INDEX, $data);
        }
        log::debug('ログイン成功 user_id: ' . $user->id . ', app_info_id:' . GAME_COLLECTION_MGR_APP_ID);

        // セッションへデータを保存する
        session()->put('session_id', $access_token);
        session()->put('user_id',    $user->id);

        return view('menu');
    }

    private function validate_login_params($mail_address, $password)
    {
        $validation = array();

        // 空白チェック
        if (empty($mail_address))
        {
            $validation['err_msg_array'][] = 'メールアドレスを入力してください。';
        }
        if (empty($password))
        {
            $validation['err_msg_array'][] = 'パスワードを入力してください。';
        }
        return $validation;
    }
}
