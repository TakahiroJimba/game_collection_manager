<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppInfo extends Model
{
    static public function getAppInfoById($app_id)
    {
        return DB::table('app_info')->where('id', $app_id)->first();
    }
}
