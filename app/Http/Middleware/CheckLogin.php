<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\LoginUser;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 未ログインだった場合、ログイン画面に遷移する
        log::debug('CheckLogin start');

        // セッションIDを取得
        $user_id    = $request->session()->get('user_id');
        $session_id = $request->session()->get('session_id');

        // セッションIDが取得できなかった場合
        if (empty($user_id) || empty($session_id))
        {
            // response()としてviewを返さないと、"setCookie() on null"でエラーとなる
            return response(view('login.index'));
        }

        // ユーザ情報をDBから取得
        $login_user = LoginUser::getLoginUser($user_id, $session_id, GAME_COLLECTION_MGR_APP_ID);

        // 未ログインの場合
        if (empty($login_user))
        {
            // セッションのデータを破棄する
            session()->put('session_id', null);
            session()->put('user_id',    null);
            return response(view('login.index'));
        }
        return $next($request);
    }
}
