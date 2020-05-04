<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;
use App\Model\AppInfo;

class ApiInfoController extends Controller
{
    // プライバシーポリシー表示
    public function showPrivacyPolicy(Request $request, $app_info_id)
    {
        log::debug('ApiInfo/showPrivacyPolicy');

        // アプリ情報を取得
        $app_info = AppInfo::getAppInfoById($app_info_id);

        if (empty($app_info))
        {
            // 登録されていないアプリの場合
            // menuにリダイレクト
            log::warning('登録されていないapp_info_id: ' . $app_info_id);
            $is_production = \App::environment('production');
            return redirect('menu', 302, [], $is_production);
        }

        $data['app_name'] = $app_info->name;
        return view('app_info.privacy_policy', $data);
    }
}
