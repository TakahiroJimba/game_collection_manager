<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;    // ディレクトリ階層が別な場合に必要
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Model\LoginUser;
use App\Model\User;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class DeleteController extends Controller
{
    private $VIEW_INDEX    = 'user.delete.index';
    private $VIEW_COMPLETE = 'user.delete.complete';

    // ユーザアカウント削除画面
    public function index(Request $request)
    {
        log::debug('User/Delete/index');
        return view($this->VIEW_INDEX);
    }

    // ユーザアカウント削除処理
    public function delete(Request $request)
    {
        log::debug('User/Delete/delete');

        // ユーザIDを取得
        $login_user = LoginUser::getLoginUser(session()->get('user_id'),
                                              session()->get('session_id'),
                                              GAME_COLLECTION_MGR_APP_ID);

        try
        {
            // ユーザ情報削除
            User::deleteUser($login_user->user_id);
        }
        catch (\Exception $e)
        {
            $err_msg = "アカウントの削除に失敗しました。管理者へお問い合わせください。";
            $data['validation']['err_msg_array'] = array($err_msg);
            log::error($err_msg);
            log::error($e->getMessage());
            return view($this->VIEW_INDEX, $data);
        }
        return view($this->VIEW_COMPLETE);
    }
}
