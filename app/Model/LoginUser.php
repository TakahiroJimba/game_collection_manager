<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginUser extends Model
{
    static public function getLoginUser($user_id, $session_id, $app_info_id)
    {
        $now = Carbon::now();
        return DB::table('login_users')
            ->where('user_id', $user_id)
            ->where('session_id', $session_id)
            ->where('app_info_id', $app_info_id)
            ->where('expiration_date', '>', $now)
            ->first();
    }
}
