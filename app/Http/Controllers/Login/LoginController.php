<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use App\Model\AppInfo;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // ログイン画面
    public function index(Request $request, $app_id)
    {
        log::debug('Login/index');

        $data['app_id'] = $app_id;
        return view('login.index', $data);
    }

    // ログイン認証
    public function login_auth(Request $request)
    {
        log::debug('Login/login_auth');

        // パラメータを取得
        $mail_address = $request->input('mail_address');
        $password     = $request->input('password');
        $app_id       = $request->input('app_id');
        $data['mail_address'] = $mail_address;
        $data['app_id']       = $app_id;

        // ログインパラメータのバリデーション
        $data['validation'] = $this->validate_login_params($mail_address, $password);
        if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
        {
            return view('login.index', $data);
        }

        // app_idのバリデーション
        $app_info = AppInfo::getAppInfoById($app_id);
        if (!isset($app_info))
        {
            $data['validation']['err_msg_array'] = array('エラーが発生しました。管理者へお問い合わせください。');
            return view('login.index', $data);
        }
        $data['app_url'] = $app_info->custom_url;

        // ユーザ情報をDBから取得
        // TODO: 削除されていない、banされていない、ロックされていないことを考慮する
        $user = DB::table('users')->where('mail_address', $mail_address)->first();

        // DBに未登録なメールアドレス、またはパスワードが異なる場合
        if (empty($user) || !Hash::check($password, $user->password))
        {
            // TODO: ログイン失敗連続回数を更新する
            // TODO: アカウントロックを実装する

            $data['validation']['err_msg_array'] = array('メールアドレス または パスワードが正しくありません。');
            return view('login.index', $data);
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
                'expiration_date'   => Carbon::now()->addDay(1),  // 24時間後
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
            $data['validation']['err_msg_array'] = array('ログインに失敗しました。管理者へお問い合わせください。');
            log::error($e);
            log::error("ログイン処理に失敗しました。rollbackしたため、データメンテは不要です。");
            return view('login.index', $data);
        }
        log::debug('ログイン成功 user_id: ' . $user->id);

        $data['login_user'] = $login_user;
        return view('login.complete', $data);
    }

    // localStorageの値を取得する
    public function get_local_storage()
    {
        return view('login.get_local_storage');
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
