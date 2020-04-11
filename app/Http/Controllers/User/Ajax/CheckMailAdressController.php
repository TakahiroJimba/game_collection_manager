<?php

namespace App\Http\Controllers\User\Ajax;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class CheckMailAdressController extends Controller
{
    public function check(Request $request)
    {
        log::debug('User/Ajax/CheckMailAdress/check');

        // パラメータを取得
        $mail_address = $request->input('mail_address');

        // 空白だった場合
        if (empty($mail_address))
        {
            return $this->getInvalidResponse();
        }
        elseif (!preg_match("/^[a-zA-Z0-9_.+-]+[@][a-zA-Z0-9.-]+$/", $mail_address))
        {
            return $this->getInvalidResponse();
        }

        // --- 重複チェック ---
        // ユーザ情報をDBから取得
        $user      = DB::table('users')->where('mail_address', $mail_address)->first();
        $temp_user = DB::table('temp_users')->where('mail_address', $mail_address)->first();
        if (isset($user) || isset($temp_user))
        {
            // 登録済みの場合
            return $this->getNgResponse();
        }

        // 登録可能
        return response()->json([
            'result' => true,
            'regist' => 'ok',
        ]);
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
}
