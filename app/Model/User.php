<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    // ユーザ情報更新時に使用
    static public function getUserById($user_id)
    {
        return DB::table('users')
            ->where('id', $user_id)
            ->whereNull('deleted_at')
            ->first();
    }

    // ユーザ情報更新時に使用
    static public function getUserByName($name)
    {
        return DB::table('users')
            ->where('name', $name)
            ->whereNull('deleted_at')
            ->first();
    }

    // ログイン時、メールアドレス変更時に使用
    static public function getUserByMailAddress($mail_address)
    {
        return DB::table('users')
            ->where('mail_address', $mail_address)
            ->whereNull('deleted_at')
            //->where('lock_at', '<', Carbon::now()->subMinutes(USER_LOCK_MINUTES))
            ->first();
    }

    // パスワード変更申請時に使用
    static public function getUserForResetPasswordByMailAddress($mail_address)
    {
        return DB::table('users')
            ->where('mail_address', $mail_address)
            //->where('ban', 0)
            ->whereNull('deleted_at')
            ->first();
    }

    // ニックネームを更新する
    static public function updateName($user_id, $name)
    {
        DB::table('users')->where('id', $user_id)
            ->update([
                        'name'       => $name,
                        'updated_at' => Carbon::now(),
                    ]);
    }

    // パスワードを更新する
    static public function updatePassword($user_id, $password)
    {
        DB::table('users')->where('id', $user_id)
            ->update([
                        'password'   => Hash::make($password),
                        'updated_at' => Carbon::now(),
                    ]);
    }

    // メールアドレスを更新する
    static public function updateMailAddress($user_id, $mail_address)
    {
        DB::table('users')
            ->where('id', $user_id)
            ->update([
                        'mail_address' => $mail_address,
                        'updated_at'   => Carbon::now(),
                    ]);
    }

    // ユーザ情報を論理削除する
    static public function deleteUser($user_id)
    {
        $now = Carbon::now();
        DB::table('users')
            ->where('id', $user_id)
            ->update([
                        'deleted_at' => $now,
                        'updated_at' => $now,
                    ]);
    }

    // 4桁の認証コードを生成
    static public function createPassPhrase()
    {
        $pass_phrase = "";
        for ($i = 0; $i < USER_REGISTRATION_PASS_PHRASE_LENGTH; $i++) {
            $pass_phrase .= rand(0, 9);
        }
        return $pass_phrase;
    }
}
