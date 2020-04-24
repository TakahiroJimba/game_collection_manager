<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mail\PasswordResetRequestShipped;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Model\User;
use App\Model\PasswordResetRequest;

use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    private $VIEW_INDEX     = 'user.reset_password.index';
    private $VIEW_INPUT     = 'user.reset_password.input';
    private $VIEW_COMPLETE  = 'user.reset_password.complete';
    private $VIEW_EXPIRED   = 'user.reset_password.expired';

    // パスワードリセット画面
    public function index(Request $request)
    {
        log::debug('User/ResetPassword/index');

        $data['validation'] = $this->get_init_array();
        $data['mail_address'] = "";

        return view($this->VIEW_INDEX, $data);
    }

    // パスワードリセットメール送信
    public function send_mail(Request $request)
    {
        log::debug('User/ResetPassword/send_mail');

        // パラメータの取得
        $data['mail_address'] = $request->input('mail_address');

        $data['validation'] = $this->validate_mail_address($data);
        if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
        {
            return view($this->VIEW_INDEX, $data);
        }

        // メールアドレスからユーザ情報を取得する
        $user = User::getUserForResetPasswordByMailAddress($data['mail_address']);

        // banされている場合
        if ($user->ban != 0)
        {
            $data['validation']['err_msg_array'] = array('このアカウントは凍結されています。');
            return view($this->VIEW_INDEX, $data);
        }

        // 登録されていないメールアドレスの場合
        if (empty($user))
        {
            // メールを送信したふりをする
            $msg = "パスワードリセットメールを送信しました。";
            log::warning($data['mail_address'].'は登録されていないメールアドレスです。');
            $data['validation']['msg'] = $msg;
            return view($this->VIEW_INDEX, $data);
        }

        // DBに登録
        $now = Carbon::now();
        $token = str_random(64);    // 64文字のランダムな文字列を作成.
        $input_data = [
            'user_id'           => $user->id,
            'token'             => $token,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
        try
        {
            DB::table("password_reset_requests")->insert($input_data);
        }
        catch (\Exception $e)
        {
            $err_msg = "データベース処理に失敗しました。管理者へお問い合わせください。";
            $data['validation']['err_msg_array'] = array($err_msg);
            log::error($err_msg);
            log::error($e->getMessage());
            return view($this->VIEW_INDEX, $data);
        }

        // メールを送信する
        $send_data = [
            'user_id' => $user->id,
            'token'   => $token,
        ];
        Mail::to($data['mail_address'])
            ->bcc(ADMIN_MAIL_ADDRESS)->send(new PasswordResetRequestShipped($send_data));

        $msg = "パスワードリセットメールを送信しました。";
        log::debug($msg);
        $data['validation']['msg'] = $msg;

        return view($this->VIEW_INDEX, $data);
    }

    // パスワード入力画面
    public function input(Request $request, $user_id, $token)
    {
        log::debug('User/ResetPassword/input');

        // パスワードリセット申請を取得
        $reset_request = PasswordResetRequest::getPasswordResetRequestByUserIdAndToken($user_id, $token);

        // 不正なパラメータ または有効期限切れの場合は、有効期限切れページを表示
        if (empty($reset_request))
        {
            log::info('パスワードリセット有効期限切れ user_id: ' . $user_id . ', token: ' . $token);
            return view($this->VIEW_EXPIRED);
        }
        $data['validation'] = $this->get_init_array_for_password_input();
        $data['user_id']    = $user_id;
        $data['token']      = $token;

        return view($this->VIEW_INPUT, $data);
    }

    // パスワード更新
    public function reset(Request $request)
    {
        log::debug('User/ResetPassword/reset');

        // パラメータ取得
        $data['user_id'] = $request->input('user_id');
        $data['token']   = $request->input('token');

        // パスワードリセット申請を取得
        $reset_request = PasswordResetRequest::getPasswordResetRequestByUserIdAndToken($data['user_id'], $data['token']);

        // 不正なトークン または有効期限切れの場合は、有効期限切れページを表示
        if (empty($reset_request))
        {
            log::info('パスワードリセット有効期限切れ user_id: ' . $data['user_id'] . ', token: ' . $data['token']);
            return view($this->VIEW_EXPIRED);
        }

        // パラメータ取得
        $data['$params'] = $params = $this->get_password_params($request);

        // バリデーション
        $data['validation'] = $this->validate_password_params($params);
        if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
        {
            return view($this->VIEW_INPUT, $data);
        }

        // パスワードを更新する
        DB::beginTransaction();
        try
        {
            log::info('パスワードを更新します。user_id: '.$reset_request->user_id);
            // ユーザ情報を更新する
            User::updatePassword($reset_request->user_id, $params['password']);

            log::info('パスワードリセット申請を削除します。');
            PasswordResetRequest::deletePasswordResetRequest($data['user_id']);
            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollback();
            $err_msg = "パスワードの更新処理に失敗しました。管理者へお問い合わせください。";
            $data['validation']['err_msg_array'] = array($err_msg);
            log::error($err_msg."rollbackしたため、データメンテは不要です。");
            log::error($e->getMessage());
            return view($this->VIEW_INPUT, $data);
        }
        log::debug('パスワードの更新に成功しました。user_id: '.$reset_request->user_id);
        return view($this->VIEW_COMPLETE, $data);
    }

    private function get_password_params(Request $request)
    {
        // パラメータを取得
        $params["password"]              = $request->input('password');
        $params["password_confirmation"] = $request->input('password_confirmation');
        return $params;
    }

    private function validate_mail_address($params)
    {
        $validation = $this->get_init_array();

        // メールアドレス
        if (empty($params["mail_address"]))
        {
            $validation['err_msg_array'][] = 'メールアドレスを入力してください。';
            $validation['mail_address'] = VALIDATION_ERR_CLASS;
        }
        elseif (!preg_match(USER_MAIL_ADDRESS_REGEXP, $params["mail_address"]))
        {
            $validation['err_msg_array'][] = 'メールアドレスに登録できない文字が含まれている、または不正なメールアドレスです。';
            $validation['mail_address'] = VALIDATION_ERR_CLASS;
        }
        return $validation;
    }

    private function get_init_array()
    {
        return array(
            'mail_address' => "",
        );
    }

    private function get_init_array_for_password_input()
    {
        return array(
            'password'              => "",
            'password_confirmation' => "",
        );
    }

    private function validate_password_params($params)
    {
        $validation = $this->get_init_array_for_password_input();

        // パスワード
        if (empty($params["password"]))
        {
            $validation['err_msg_array'][] = 'パスワードを入力してください。';
            $validation['password'] = VALIDATION_ERR_CLASS;
        }
        elseif (!preg_match(USER_PASSWORD_REGEXP, $params["password"]))
        {
            $validation['err_msg_array'][] = 'パスワードは半角英数字' . USER_PASSWORD_MIN_LENGTH . '文字〜' . USER_PASSWORD_MAX_LENGTH . '文字で入力してください。';
            $validation['password'] = VALIDATION_ERR_CLASS;
        }
        // 確認用パスワード
        elseif (empty($params["password_confirmation"]) ||
                strcmp($params["password"], $params["password_confirmation"]))
        {
            $validation['err_msg_array'][] = '確認用パスワードは同じものを入力してください。';
            $validation['password_confirmation'] = VALIDATION_ERR_CLASS;
        }
        return $validation;
    }
}
