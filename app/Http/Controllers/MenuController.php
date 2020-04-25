<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 「\」を入れないで使うには下記の一文を入れておくこと
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    // トップメニュー画面
    public function index()
    {
        log::debug('Menu/index');
        return view('menu');
    }
}
