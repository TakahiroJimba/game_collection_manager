<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;
use App\Model\LoginUser;

class LogoutController extends Controller
{
    // ログアウト処理
    public function logout()
    {
        log::debug('Api/Logout/logout');

        // パラメータを取得
        $user_id     = $_POST["user_id"];
        $session_id  = $_POST["session_id"];
        $app_info_id = $_POST["app_info_id"];

        // DBのログイン情報を削除する
        LoginUser::deleteLoginUser($user_id, $session_id, $app_info_id);
        log::debug('app_info_id ' . $app_info_id . ' から正常にログアウトしました。user_id: ' . $user_id);

        $data['is_logout']  = '1';
        return json_encode($data);
    }
}
