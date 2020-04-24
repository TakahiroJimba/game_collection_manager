<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    // ログイン時に使用
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

    // パスワードを更新する
    static public function updatePassword($user_id, $password)
    {
        DB::table('users')->where('id', $user_id)
            ->update([
                        'password'   => Hash::make($password),
                        'updated_at' => Carbon::now(),
                    ]);
    }
}
