<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MailaddressChangeAuth extends Model
{
    // メールアドレス変更時に使用
    static public function getMailaddressChangeAuthByUserIdAndToken($user_id, $token)
    {
        return DB::table('mail_address_change_auth')
            ->where('user_id', $user_id)
            ->where('token', $token)
            ->where('created_at', '>=', Carbon::now()->subMinutes(USER_MAIL_ADDRESS_AUTH_EXPIRATION))  // 期限は30分間以内
            ->first();
    }

    static public function insertMailaddressChangeAuth($user_id, $mail_address, $token)
    {
        $now = Carbon::now();
        $input_data = [
            'user_id'           => $user_id,
            'new_mail_address'  => $mail_address,
            'token'             => $token,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
        DB::table("mail_address_change_auth")->insert($input_data);
    }

    static public function deleteMailaddressChangeAuth($user_id)
    {
        DB::table('mail_address_change_auth')
            ->where('user_id', $user_id)
            ->delete();
    }
}
