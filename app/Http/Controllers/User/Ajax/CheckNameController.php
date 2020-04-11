<?php

namespace App\Http\Controllers\User\Ajax;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class CheckNameController extends Controller
{
    public function check(Request $request)
    {
        log::debug('User/Ajax/CheckName/check');

        // パラメータを取得
        $name = $request->input('name');

        // 空白だった場合
        if (empty($name))
        {
            return $this->getInvalidResponse();
        }
        elseif (mb_strlen($name) >= USER_NAME_MAX_LENGTH)
        {
            return $this->getInvalidResponse();
        }

        // --- 重複チェック ---
        $old_name = $request->input('old_name');
        if(isset($old_name) && $name == $old_name)
        {
            // 登録可能
            return $this->getOkResponse();
        }

        // ユーザ情報をDBから取得
        $user      = DB::table('users')->where('name', $name)->first();
        $temp_user = DB::table('temp_users')->where('name', $name)->first();
        if (isset($user) || isset($temp_user))
        {
            // 登録済みの場合
            return $this->getNgResponse();
        }

        // 登録可能
        return $this->getOkResponse();
    }

    private function getNgResponse()
    {
        return response()->json([
            'result' => true,
            'regist' => 'ng',
        ]);
    }

    private function getInvalidResponse()
    {
        return response()->json([
            'result' => true,
            'regist' => 'invalid',
        ]);
    }

    private function getOkResponse()
    {
        return response()->json([
            'result' => true,
            'regist' => 'ok',
        ]);
    }
}
