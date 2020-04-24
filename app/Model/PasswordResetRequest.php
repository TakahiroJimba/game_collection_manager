<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetRequest extends Model
{
    static public function getPasswordResetRequestByUserIdAndToken($user_id, $token)
    {
        return DB::table('password_reset_requests')
            ->where('user_id', $user_id)
            ->where('token', $token)
            ->where('created_at', '>=', Carbon::now()->subMinutes(PASSWORD_RESET_EXPIRATION))  // リセット申請の期限は60分
            ->first();
    }

    static public function deletePasswordResetRequest($user_id)
    {
        DB::table('password_reset_requests')
            ->where('user_id', $user_id)
            ->delete();
    }
}
