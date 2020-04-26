<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailChangeShipped;
use Mail;
use App\Model\LoginUser;
use App\Model\TempUser;
use App\Model\User;
use App\Model\MailaddressChangeAuth;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    private $VIEW_INDEX = 'user.update.index';

    // ユーザ情報変更画面
    public function index(Request $request)
    {
        log::debug('User/Update/index');

        // 空の配列を用意
        $data['validation'] = $this->get_init_basic_array();
        $login_user         = LoginUser::getLoginUser(session()->get('user_id'),
                                                      session()->get('session_id'),
                                                      GAME_COLLECTION_MGR_APP_ID);
        $data['user']       = User::getUserById($login_user->user_id);

        return view($this->VIEW_INDEX, $data);
    }

    // ユーザ情報更新処理
    public function update(Request $request)
    {
        log::debug('User/Update/update');

        // 空の配列を用意
        $data['validation'] = $this->get_init_basic_array();
        $login_user         = LoginUser::getLoginUser(session()->get('user_id'), session()->get('session_id'), GAME_COLLECTION_MGR_APP_ID);
        $data['user']       = User::getUserById($login_user->user_id);

        if ($request->input('basic_commit') != null)
        {
            log::debug('基本情報更新ボタンが押されました');
            // --- 基本情報の更新 ---
            $params = $this->getUserUpdateForBasicParams($request);

            // バリデーション
            $old_name = $data['user']->name;
            $data['user']->name = $params['name'];

            $data['validation'] = $this->validate_user_update_for_basic_params($params, $old_name);
            if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
            {
                return view($this->VIEW_INDEX, $data);
            }

            try
            {
                // ユーザ情報を更新する
                User::updateName($data['user']->id, $params['name']);
            }
            catch (\Exception $e)
            {
                $err_msg = "アカウントの更新処理に失敗しました。管理者へお問い合わせください。";
                $data['validation']['err_msg_array'] = array($err_msg);
                log::error($err_msg);
                log::error($e->getMessage());
                return view($this->VIEW_INDEX, $data);
            }

            log::debug('アカウント情報の更新に成功しました。');
            $data['validation']['msg'] = "アカウント情報を更新しました。";
        }
        elseif ($request->input('password_commit') != null)
        {
            log::debug('パスワード更新ボタンが押されました');
            // --- パスワード更新の場合 ---
            $params['password']              = $request->input('password');
            $params['new_password']          = $request->input('new_password');
            $params['password_confirmation'] = $request->input('password_confirmation');

            $data['validation'] = $this->validate_user_update_for_password($params, $data['user']->password);
            if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
            {
                return view($this->VIEW_INDEX, $data);
            }

            try
            {
                // パスワードを更新する
                User::updatePassword($data['user']->id, $params['new_password']);
            }
            catch (\Exception $e)
            {
                $err_msg = "パスワードの更新処理に失敗しました。管理者へお問い合わせください。";
                $data['validation']['err_msg_array'] = array($err_msg);
                log::error($err_msg);
                log::error($e->getMessage());
                return view($this->VIEW_INDEX, $data);
            }

            log::debug('パスワード更新に成功しました。');
            $data['validation']['msg'] = "パスワードを更新しました。";
        }
        elseif ($request->input('mail_auth_send_commit') != null)
        {
            log::debug('認証メール送信ボタンが押されました');
            // --- 認証メール送信が押された場合 ---
            // 入力値を取得
            $params['mail_address'] = $request->input('mail_address');
            $data['params'] = $params;

            // バリデーション
            $data['validation'] = $this->validate_user_update_for_mail($params);
            if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
            {
                return view($this->VIEW_INDEX, $data);
            }

            // 4桁の認証コードを生成
            $pass_phrase = User::createPassPhrase();

            try
            {
                MailaddressChangeAuth::insertMailaddressChangeAuth($data['user']->id, $params['mail_address'], $pass_phrase);
            }
            catch (\Exception $e)
            {
                $err_msg = "データベース処理に失敗しました。管理者へお問い合わせください。";
                $data['validation']['err_msg_array'] = array($err_msg);
                log::error($err_msg);
                log::error($e->getMessage());
                return view($this->VIEW_INDEX, $data);
            }

            // 認証コードをメールで送信する
            $sendData = [
                'token' => $pass_phrase,
            ];
            Mail::to($params['mail_address'])
                ->bcc(ADMIN_MAIL_ADDRESS)->send(new MailChangeShipped($sendData));

            $msg = $params['mail_address'] . 'に認証コードを送信しました。';
            log::debug($msg);
            $data['validation']['msg'] = $msg;
        }
        elseif ($request->input('mail_auth_commit') != null)
        {
            log::debug('メールアドレス更新ボタンが押されました');
            // --- メールアドレス更新が押された場合 ---
            // 入力値を取得
            $params['mail_auth_code'] = $request->input('mail_auth_code');

            // バリデーション
            $data['validation'] = $this->validate_user_update_for_mail_code($params, $data['user']->id);
            if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
            {
                return view($this->VIEW_INDEX, $data);
            }

            $auth = MailaddressChangeAuth::getMailaddressChangeAuthByUserIdAndToken($data['user']->id, $params["mail_auth_code"]);

            if (empty($auth))
            {
                $data['validation']['err_msg_array'][] = '認証コードが不正、または有効期限切れです。';
                $data['validation']['mail_auth_code'] = VALIDATION_ERR_CLASS;
                return view($this->VIEW_INDEX, $data);
            }

            DB::beginTransaction();
            try
            {
                // メールアドレス更新
                User::updateMailAddress($data['user']->id, $auth->new_mail_address);

                // 認証申請データを削除
                MailaddressChangeAuth::deleteMailaddressChangeAuth($data['user']->id);
                DB::commit();
            }
            catch (\Exception $e)
            {
                DB::rollback();
                $data['validation']['err_msg_array'] = array('メールアドレスの更新に失敗しました。管理者へお問い合わせください。');
                log::error("メールアドレスの更新に失敗しました。rollbackしたため、データメンテは不要です。");
                log::error($e);
                return view($this->VIEW_INDEX, $data);
            }

            $data['params']['mail_address'] = $auth->new_mail_address;

            $msg = "メールアドレスを".$auth->new_mail_address."に更新しました。";
            log::debug($msg);
            $data['validation']['msg'] = $msg;
        }
        else
        {
            Log::warning("想定外の操作がされました。");
            return view('menu');
        }
        return view($this->VIEW_INDEX, $data);
    }

    // フォームに入力されたパラメータを取得する
    private function getUserUpdateForBasicParams(Request $request)
    {
        // パラメータを取得
        $params["name"] = $request->input('name');
        return $params;
    }

    private function get_init_basic_array()
    {
        // 初期化
        $array['name']           = "";
        $array['mail_address']   = "";
        $array['mail_auth_code'] = "";
        $array['password']              = "";
        $array['new_password']          = "";
        $array['password_confirmation'] = "";

        return $array;
    }

    private function validate_user_update_for_basic_params($params, $old_name)
    {
        $validation = $this->get_init_basic_array();

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
            // 変更があった場合のみ重複チェック
            if ($old_name != $params["name"])
            {
                // ユーザ情報をDBから取得
                $user      = User::getUserByName($params["name"]);
                $temp_user = TempUser::getTempUserByName($params["name"]);
                if (isset($user) || isset($temp_user))
                {
                    $validation['err_msg_array'][] = 'すでに登録されているニックネームです。';
                    $validation['name'] = VALIDATION_ERR_CLASS;
                }
            }
        }
        return $validation;
    }

    private function validate_user_update_for_password($params, $now_password)
    {
        $validation = $this->get_init_basic_array();

        // 現在のパスワード
        if (empty($params["password"]))
        {
            $validation['err_msg_array'][] = '現在のパスワードを入力してください。';
            $validation['password'] = VALIDATION_ERR_CLASS;
            return $validation;
        }
        elseif(!Hash::check($params["password"], $now_password))
        {
            $validation['err_msg_array'][] = '現在のパスワードが違います。';
            $validation['password'] = VALIDATION_ERR_CLASS;
            return $validation;
        }

        // 新しいパスワード
        if (empty($params["new_password"]))
        {
            $validation['err_msg_array'][] = '新しいパスワードを入力してください。';
            $validation['new_password'] = VALIDATION_ERR_CLASS;
        }
        elseif (!preg_match("/^[a-zA-Z0-9]{".USER_PASSWORD_MIN_LENGTH.",".USER_PASSWORD_MAX_LENGTH."}+$/", $params["new_password"]))
        {
            $validation['err_msg_array'][] = 'パスワードは半角英数字' . USER_PASSWORD_MIN_LENGTH . '文字〜' . USER_PASSWORD_MAX_LENGTH . '文字で入力してください。';
            $validation['new_password'] = VALIDATION_ERR_CLASS;
        }
        // 確認用パスワード
        elseif (empty($params["password_confirmation"]) ||
                strcmp($params["new_password"], $params["password_confirmation"]))
        {
            $validation['err_msg_array'][] = '確認用パスワードは同じものを入力してください。';
            $validation['password_confirmation'] = VALIDATION_ERR_CLASS;
        }
        return $validation;
    }

    private function validate_user_update_for_mail($params)
    {
        $validation = $this->get_init_basic_array();

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
        else
        {
            // 重複チェック
            // ユーザ情報をDBから取得
            $user      = User::getUserByMailAddress($params["mail_address"]);
            $temp_user = TempUser::getTempUserByMailAddress($params["mail_address"]);
            if (isset($user) || isset($temp_user))
            {
                $validation['err_msg_array'][] = 'すでに登録されているメールアドレスです。';
                $validation['mail_address'] = VALIDATION_ERR_CLASS;
            }
        }
        return $validation;
    }

    private function validate_user_update_for_mail_code($params, $user_id)
    {
        $validation = $this->get_init_basic_array();

        // 認証コード
        if (empty($params["mail_auth_code"]))
        {
            $validation['err_msg_array'][] = '認証コードを入力してください。';
            $validation['mail_auth_code'] = VALIDATION_ERR_CLASS;
        }

        return $validation;
    }
}
