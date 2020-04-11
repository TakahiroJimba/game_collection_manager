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

    private function get_init_array()
    {
        // 初期化
        $array['name']   = "";
        $array['mail_address']              = "";
        $array['password']                  = "";
        $array['password_confirmation']     = "";
        return $array;
    }

    // // 登録内容確認
    // public function confirm(Request $request)
    // {
    //     log::debug('UserRegist/confirm');
    //
    //     $params = $this->getUserRegistrationParams($request);
    //     $data['params'] = $params;
    //
    //     // ログインパラメータのバリデーション
    //     $data['validation'] = $this->validate_user_registration_params($params);
    //     if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
    //     {
    //         return view('user_registration.index', $data);
    //     }
    //     return view('user_registration.confirm', $data);
    // }
    //
    // // 仮登録
    // public function pro_regist(Request $request)
    // {
    //     log::debug('UserRegist/pro_regist');
    //
    //     $params = $this->getUserRegistrationParams($request, true);
    //     $data['params'] = $params;
    //
    //     if ($request->input('back') != null)
    //     {
    //         // 空の配列を用意
    //         $data['validation'] = $this->get_init_array();
    //
    //         // 修正ボタンが押された時の処理
    //         return view('user_registration.index', $data);
    //     }
    //     elseif ($request->input('commit') != null)
    //     {
    //         // 登録ボタンが押された時の処理
    //         // ログインパラメータのバリデーション
    //         $data['validation'] = $this->validate_user_registration_params($params, true);
    //         if (isset($data['validation']['err_msg_array']) && count($data['validation']['err_msg_array']) != 0)
    //         {
    //             $err_msg = "不正な操作を検出しました。もう一度やり直してください。";
    //             $data['validation']['err_msg_array'] = array($err_msg);
    //             log::warning($err_msg);
    //
    //             return view('user_registration.index', $data);
    //         }
    //     }
    //
    //     $now = Carbon::now();
    //     // 仮登録処理
    //     $temp_user = $this->newTempUser($params);
    //     try
    //     {
    //         DB::table("temp_users")->insert($temp_user);
    //     }
    //     catch (\Exception $e)
    //     {
    //         $err_msg = "登録処理に失敗しました。管理者へお問い合わせください。";
    //         $data['validation']['err_msg_array'] = array($err_msg);
    //         log::error($err_msg);
    //         //log::error(print_r($temp_user, true));
    //         log::error($e->getMessage());
    //         return view('user_registration.index', $data);
    //     }
    //
    //     // 仮登録完了メールを送信する
    //     $sendData = [
    //         'register_token' => $temp_user['token'],
    //     ];
    //
    //     Mail::to($temp_user['mail'])
    //         ->bcc(ADMIN_MAIL_ADDRESS)->send(new RegisterShipped($sendData));
    //
    //     return view('user_registration.pro_regist', $temp_user);
    // }
    //
    // // SMS認証コード入力画面表示
    // public function sms_input(Request $request, $token)
    // {
    //     log::debug('UserRegist/sms_input');
    //
    //     // tokenで仮登録ユーザを検索
    //     $temp_user = $this->getTempUserByToken($token);
    //
    //     // 仮登録が未済 または有効期限切れの場合は、有効期限切れページを表示
    //     if (empty($temp_user))
    //     {
    //         log::info('仮登録有効期限切れ token: ' . $token);
    //         return view('user_registration.expired');
    //     }
    //
    //     $data['token'] = $token;
    //     $data['phone'] = $temp_user->phone;
    //
    //     // 認証トークンを生成する
    //     $sms_token = rand(1000, 9999);
    //
    //     $now = Carbon::now();
    //
    //     $insert_data = [
    //         'phone'       => $data['phone'],
    //         'token'       => $sms_token,
    //         'send_status' => NOT_SEND,
    //         'created_at'  => $now,
    //         'updated_at'  => $now,
    //     ];
    //
    //     // SMS認証データを登録する
    //     DB::table('sms_auth')->insert($insert_data);
    //
    //     return view('user_registration.sms_input', $data);
    // }
    //
    // // SMS認証処理
    // public function sms_auth(Request $request)
    // {
    //     log::debug('UserRegist/sms_auth');
    //
    //     // パラメータ取得
    //     $data['token']    = $request->input('token');
    //     $data['sms_code'] = $request->input('sms_code');
    //
    //     // tokenで仮登録ユーザを検索
    //     $temp_user = $this->getTempUserByToken($data['token']);
    //
    //     // 仮登録が未済 または有効期限切れの場合は、有効期限切れページを表示
    //     if (empty($temp_user))
    //     {
    //         log::info('仮登録有効期限切れ token: ' . $data['token']);
    //         return view('user_registration.expired');
    //     }
    //
    //     $data['phone'] = $temp_user->phone;
    //
    //     if (empty($data['sms_code']))
    //     {
    //         $data['validation']['err_msg_array'][] = '認証コードを入力してください。';
    //         return view('user_registration.sms_input', $data);
    //     }
    //
    //     // SMSコード認証
    //     $sms_auth = DB::table('sms_auth')
    //         ->where('token', $data['sms_code'])
    //         ->where('phone', $temp_user->phone)
    //         ->first();
    //
    //     // エラーハンドリング
    //     if (empty($sms_auth) || count($sms_auth) == 0)
    //     {
    //         $data['validation']['err_msg_array'][] = '認証コードが違います。';
    //         return view('user_registration.sms_input', $data);
    //     }
    //
    //     $now = Carbon::now();
    //
    //     // --- 認証成功 ---
    //     DB::beginTransaction();
    //     try {
    //         // sms_authテーブルからレコードを削除
    //         DB::table('sms_auth')->where('id', $sms_auth->id)->delete();
    //         log::info('sms_authレコードを削除しました。id: ' . $sms_auth->id);
    //
    //         // temp_usersテーブルからレコードを削除
    //         DB::table('temp_users')->where('id', $temp_user->id)->delete();
    //         log::info('temp_usersレコードを削除しました。id: ' . $temp_user->id);
    //
    //         // usersテーブルにデータを登録
    //         $user['last_name']  = $temp_user->last_name;
    //         $user['first_name'] = $temp_user->first_name;
    //         $user['nickname']   = $temp_user->nickname;
    //         $user['mail']       = $temp_user->mail;
    //         $user['phone']      = $temp_user->phone;
    //         $user['password']   = $temp_user->password;
    //         $user['lang']       = $temp_user->lang;
    //         $user['created_at'] = $now;
    //         $user['updated_at'] = $now;
    //
    //         $new_user_id = DB::table('users')->insertGetId($user);
    //
    //         $user['id'] = $new_user_id;
    //         log::info('usersレコードを登録しました。' . implode(",", $user));
    //
    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $data['validation']['err_msg_array'] = array('本登録処理に失敗しました。管理者へお問い合わせください。');
    //         log::error("本登録処理に失敗しました。rollbackしたため、データメンテは不要です。");
    //         log::error($e);
    //         return view('user_registration.sms_input', $data);
    //     }
    //
    //     return view('user_registration.complete');
    // }
    //
    // // 登録画面に入力された値を配列で取得する
    // private function getUserRegistrationParams(Request $request, $confirmation = false)
    // {
    //     // パラメータを取得
    //     $params["last_name"]  = $request->input('last_name');
    //     $params["first_name"] = $request->input('first_name');
    //     $params["nickname"]   = $request->input('nickname');
    //     $params["phone1"]     = $request->input('phone1');
    //     $params["phone2"]     = $request->input('phone2');
    //     $params["phone3"]     = $request->input('phone3');
    //     $params["mail_address"]              = $request->input('mail_address');
    //     $params["mail_address_confirmation"] = $request->input('mail_address_confirmation');
    //     $params["password"]                  = $request->input('password');
    //
    //     // 確認画面では不要なパラメータ
    //     if (!$confirmation)
    //     {
    //         $params["password_confirmation"] = $request->input('password_confirmation');
    //     }
    //     return $params;
    // }

    // private function validate_user_registration_params($params, $confirmation = false)
    // {
    //     $validation = $this->get_init_array();
    //
    //     // 姓
    //     if (empty($params["last_name"]))
    //     {
    //         $validation['err_msg_array'][] = '姓を入力してください。';
    //         $validation['last_name'] = VALIDATION_ERR_CLASS;
    //     }
    //     elseif (mb_strlen($params["last_name"]) >= LAST_NAME_MAX_LENGTH)
    //     {
    //         $validation['err_msg_array'][] = '姓は'.LAST_NAME_MAX_LENGTH.'文字以内で入力してください。';
    //         $validation['last_name'] = VALIDATION_ERR_CLASS;
    //     }
    //
    //     // 名
    //     if (empty($params["first_name"]))
    //     {
    //         $validation['err_msg_array'][] = '名を入力してください。';
    //         $validation['first_name'] = VALIDATION_ERR_CLASS;
    //     }
    //     elseif (mb_strlen($params["first_name"]) >= FIRST_NAME_MAX_LENGTH)
    //     {
    //         $validation['err_msg_array'][] = '名は'.FIRST_NAME_MAX_LENGTH.'文字以内で入力してください。';
    //         $validation['first_name'] = VALIDATION_ERR_CLASS;
    //     }
    //
    //     // ニックネーム
    //     if (empty($params["nickname"]))
    //     {
    //         $validation['err_msg_array'][] = 'ニックネームを入力してください。';
    //         $validation['nickname'] = VALIDATION_ERR_CLASS;
    //
    //     }
    //     elseif (mb_strlen($params["nickname"]) >= NICKNAME_MAX_LENGTH)
    //     {
    //         $validation['err_msg_array'][] = 'ニックネームは'.NICKNAME_MAX_LENGTH.'文字以内で入力してください。';
    //         $validation['nickname'] = VALIDATION_ERR_CLASS;
    //     }
    //     else
    //     {
    //         // 重複チェック
    //         // ユーザ情報をDBから取得
    //         $user      = DB::table('users')->where('nickname', $params["nickname"])->first();
    //         $temp_user = DB::table('temp_users')->where('nickname', $params["nickname"])->first();
    //         if (isset($user) || isset($temp_user))
    //         {
    //             $validation['err_msg_array'][] = 'すでに登録されているニックネームです。';
    //             $validation['nickname'] = VALIDATION_ERR_CLASS;
    //         }
    //     }
    //
    //     // 電話番号
    //     $phone_err = false;
    //     if (empty($params["phone1"]))
    //     {
    //         $validation['phone1'] = VALIDATION_ERR_CLASS;
    //         $phone_err = true;
    //     }
    //     if (empty($params["phone2"]))
    //     {
    //         $validation['phone2'] = VALIDATION_ERR_CLASS;
    //         $phone_err = true;
    //     }
    //     if (empty($params["phone3"]))
    //     {
    //         $validation['phone3'] = VALIDATION_ERR_CLASS;
    //         $phone_err = true;
    //     }
    //
    //     if ($phone_err)
    //     {
    //         $validation['err_msg_array'][] = '電話番号は半角数字で入力してください。';
    //     } else
    //     {
    //         $phone = $params["phone1"] . '-' . $params["phone2"] . '-' . $params["phone3"];
    //         if (!preg_match("/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}$/", $phone))
    //         {
    //             $validation['err_msg_array'][] = '電話番号は半角数字で入力してください。';
    //             $validation['phone1'] = VALIDATION_ERR_CLASS;
    //             $validation['phone2'] = VALIDATION_ERR_CLASS;
    //             $validation['phone3'] = VALIDATION_ERR_CLASS;
    //         }
    //     }
    //
    //     // メールアドレス
    //     if (empty($params["mail_address"]))
    //     {
    //         $validation['err_msg_array'][] = 'メールアドレスを入力してください。';
    //         $validation['mail_address'] = VALIDATION_ERR_CLASS;
    //     }
    //     elseif (!preg_match("/^[a-zA-Z0-9_.+-]+[@][a-zA-Z0-9.-]+$/", $params["mail_address"]))
    //     {
    //         $validation['err_msg_array'][] = 'メールアドレスに登録できない文字が含まれている、または不正なメールアドレスです。';
    //         $validation['mail_address'] = VALIDATION_ERR_CLASS;
    //     }
    //     // 確認用メールアドレス
    //     elseif (!$confirmation && (
    //             empty($params["mail_address_confirmation"]) ||
    //             strcmp($params["mail_address"], $params["mail_address_confirmation"])))
    //     {
    //         $validation['err_msg_array'][] = '確認用メールアドレスは同じものを入力してください。';
    //         $validation['mail_address_confirmation'] = VALIDATION_ERR_CLASS;
    //     }
    //     else
    //     {
    //         // 重複チェック
    //         // ユーザ情報をDBから取得
    //         $user      = DB::table('users')->where('mail', $params["mail_address"])->first();
    //         $temp_user = DB::table('temp_users')->where('mail', $params["mail_address"])->first();
    //         if (isset($user) || isset($temp_user))
    //         {
    //             $validation['err_msg_array'][] = 'すでに登録されているメールアドレスです。';
    //             $validation['mail_address'] = VALIDATION_ERR_CLASS;
    //         }
    //     }
    //
    //     // パスワード
    //     if (empty($params["password"]))
    //     {
    //         $validation['err_msg_array'][] = 'パスワードを入力してください。';
    //         $validation['password'] = VALIDATION_ERR_CLASS;
    //     }
    //     elseif (!preg_match("/^[a-zA-Z0-9]{".PASSWORD_MIN_LENGTH.",".PASSWORD_MAX_LENGTH."}+$/", $params["password"]))
    //     {
    //         $validation['err_msg_array'][] = 'パスワードは半角英数字' . PASSWORD_MIN_LENGTH . '文字〜' . PASSWORD_MAX_LENGTH . '文字で入力してください。';
    //         $validation['password'] = VALIDATION_ERR_CLASS;
    //     }
    //     // 確認用パスワード
    //     elseif (!$confirmation &&
    //             (empty($params["password_confirmation"]) ||
    //             strcmp($params["password"], $params["password_confirmation"])))
    //     {
    //         $validation['err_msg_array'][] = '確認用パスワードは同じものを入力してください。';
    //         $validation['password_confirmation'] = VALIDATION_ERR_CLASS;
    //     }
    //
    //     return $validation;
    // }
    //
    // private function newTempUser($params)
    // {
    //     $now = Carbon::now();
    //     // 仮登録処理
    //     $temp_user = [
    //         'last_name'     => $params["last_name"],
    //         'first_name'    => $params["first_name"],
    //         'nickname'      => $params["nickname"],
    //         'mail'          => $params["mail_address"],
    //         'phone'         => $params["phone1"]."-".$params["phone2"]."-".$params["phone3"],
    //         'password'      => Hash::make($params['password']),
    //         'token'         => str_random(64),        // 64文字のランダムな文字列を作成.
    //         'created_at'    => $now,
    //         'updated_at'    => $now,
    //     ];
    //     return $temp_user;
    // }
    //
    // // tokenで仮登録ユーザを検索
    // private function getTempUserByToken($token)
    // {
    //     return DB::table('temp_users')
    //         ->where('token', $token)
    //         ->where('created_at', '>=', Carbon::now()->addDay(-1))  // 仮登録の期限は24時間
    //         ->whereNull('deleted_at')
    //         ->first();
    // }
}
