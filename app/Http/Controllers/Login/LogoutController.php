<?php

namespace App\Http\Controllers\Login;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    // ログアウト画面
    public function index(Request $request)
    {
        log::debug('Logout/index');

        // セッションIDを取得
        $session_id = $request->session()->get('session_id');

        // セッションIDが取得できなかった場合
        if (empty($session_id))
        {
            return view('login.index');
        }

        // ユーザ情報をDBから取得
        $login_user = DB::table('login_users')->where('session_id', $session_id)->first();

        // 未ログインの場合
        if (empty($login_user))
        {
            return view('login.index');
        }

        // DBのセッションIDを削除する
        DB::table('login_users')->where('user_id', $login_user->user_id)
            ->update([
                        'session_id' => null,
                        'updated_at' => Carbon::now(),
                    ]);

        // セッションのデータを破棄する
        session()->put('session_id', null);
        session()->put('user_id',    null);

        log::debug('正常にログアウトしました。user_id: ' . $login_user->user_id);
        return view('login.index');
    }
}
