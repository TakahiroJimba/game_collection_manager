<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Model
{
    static public function getUserByMailAddress($mail_address)
    {
        return DB::table('users')
            ->where('mail_address', $mail_address)
            //->where('ban', 0)
            ->whereNull('deleted_at')
            //->where('lock_at', '<', Carbon::now()->subMinutes(USER_LOCK_MINUTES))
            ->first();
    }
}
