<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TempUser extends Model
{
    // ユーザ情報更新時に使用
    static public function getTempUserByName($name)
    {
        return DB::table('temp_users')
            ->where('name', $name)
            ->whereNull('deleted_at')
            ->first();
    }

    // ログイン時、メールアドレス変更時に使用
    static public function getTempUserByMailAddress($mail_address)
    {
        return DB::table('temp_users')
            ->where('mail_address', $mail_address)
            ->whereNull('deleted_at')
            //->where('lock_at', '<', Carbon::now()->subMinutes(USER_LOCK_MINUTES))
            ->first();
    }
}
