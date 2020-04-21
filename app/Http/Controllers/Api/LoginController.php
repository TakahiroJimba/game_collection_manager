<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;
use App\Model\LoginUser;
use App\Model\User;

class LoginController extends Controller
{
    // ログイン認証処理
    public function auth()
    {
        log::debug('Api/Login/auth');

        // パラメータを取得
        $mail_address = $_POST["mail_address"];
        $password     = $_POST["password"];

        // ログイン失敗時は0
        $data['is_login'] = '0';

        // ログインパラメータのバリデーション
        $data['err_msg'] = $this->validate_login_params($mail_address, $password);
        if (!empty($data['err_msg']))
        {
            return json_encode($data);
        }

        // // app_idのバリデーション
        // $app_info = AppInfo::getAppInfoById($app_id);
        // if (!isset($app_info))
        // {
        //     $data['validation']['err_msg_array'] = array('エラーが発生しました。管理者へお問い合わせください。');
        //     return view('login.index', $data);
        // }
        // $data['app_url'] = $app_info->custom_url;

        // ユーザ情報をDBから取得
        $user = User::getUserByMailAddress($mail_address);
        // DBに未登録なメールアドレスの場合
        if (empty($user))
        {
            $data['err_msg'] = 'メールアドレス または パスワードが正しくありません。';
            return json_encode($data);
        }

        // パスワードが異なる場合
        if (!Hash::check($password, $user->password))
        {
            // TODO: ログイン失敗連続回数を更新する
            // TODO: アカウントロックを実装する
            $data['err_msg'] = 'メールアドレス または パスワードが正しくありません。';
            return json_encode($data);
        }

        // banされている場合
        if ($user->ban != 0)
        {
            $data['err_msg'] = 'このアカウントは凍結されています。';
            return json_encode($data);
        }

        // ロックされている場合
        if ($user->lock_at >= Carbon::now()->subMinutes(USER_LOCK_MINUTES))
        {
            $data['err_msg'] = 'このアカウントはロックされています。<br>解除されるまでしばらくお待ちください。';
            return json_encode($data);
        }

        // --- 認証成功 ---
        // 乱数を取得
        $access_token = str_random(32);
        $now = Carbon::now();

        DB::beginTransaction();
        try
        {
            $login_user = [
                'user_id'           => $user->id,
                'session_id'        => $access_token,
                'expiration_date'   => Carbon::now()->addDay(USER_LOGIN_EXPIRATION_DATE),  // 10日後
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            // DBにセッションIDを登録する
            DB::table('login_users')->insert($login_user);

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
            $data['err_msg'] = 'ログインに失敗しました。管理者へお問い合わせください。';
            log::error($e);
            log::error("ログイン処理に失敗しました。rollbackしたため、データメンテは不要です。");
            return json_encode($data);
        }
        log::debug('ログイン成功 user_id: ' . $user->id);
        $data['is_login']   = '1';
        $data['session_id'] = $access_token;
        return json_encode($data);
    }

    // ログインチェック
    public function is_login()
    {
        log::debug('Api/Login/is_login');
        $user_id    = $_POST["user_id"];
        $session_id = $_POST["session_id"];

        // ログイン認証
        $login_user = LoginUser::getLoginUser($user_id, $session_id);

        if (!isset($login_user))
        {
            log::warning('このユーザはログインしていません。user_id: ' . $user_id);
            $is_login =  "0";
        }
        else
        {
            // ログインしている
            $is_login =  "1";
        }
        $data["is_login"] = $is_login;
        return json_encode($data);
    }

    private function validate_login_params($mail_address, $password)
    {
        // 空白チェック
        if (empty($mail_address) || empty($password))
        {
            return "メールアドレス または パスワードが正しくありません。";
        }
        return "";
    }
}
