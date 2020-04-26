<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use App\Model\LoginUser;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    // ログアウト処理
    public function index(Request $request)
    {
        log::debug('Logout/index');

        // ユーザIDとセッションIDを取得
        $user_id = $request->session()->get('user_id');
        $session_id = $request->session()->get('session_id');

        // DBのログイン情報を削除する
        LoginUser::deleteLoginUser($user_id, $session_id, GAME_COLLECTION_MGR_APP_ID);

        // セッションのデータを破棄する
        session()->put('session_id', null);
        session()->put('user_id',    null);

        log::debug(APP_NAME . 'から正常にログアウトしました。user_id: ' . $user_id);
        return view('login.index');
    }
}
