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

    static public function insertLoginUser($user_id, $session_id, $app_info_id)
    {
        $now = Carbon::now();
        $login_user = [
            'user_id'           => $user_id,
            'session_id'        => $session_id,
            'expiration_date'   => Carbon::now()->addDay(USER_LOGIN_EXPIRATION_DATE),  // 10日後
            'app_info_id'       => $app_info_id,
            'created_at'        => $now,
            'updated_at'        => $now,
        ];
        DB::table('login_users')->insert($login_user);
    }

    // session_idに使用するランダムな文字列を生成
    static public function createAccessToken()
    {
        // 乱数を取得
        return str_random(32);
    }
}
