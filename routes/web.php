<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ログイン画面
Route::get ('login/{app_id}',                        'Login\LoginController@index');
Route::post('login',                                 'Login\LoginController@login_auth');

// パスワードリセット
Route::get ('pass/reset',                            'User\ResetPasswordController@index');
Route::post('pass/reset',                            'User\ResetPasswordController@send_mail');
Route::get ('pass/reset/input/{token}',              'User\ResetPasswordController@input');
Route::post('pass/reset/input',                      'User\ResetPasswordController@reset');

// ログアウト
Route::get ('logout',                                'Login\LogoutController@index');

// ユーザ登録
Route::get ('user/registration',                     'User\RegistrationController@index');
Route::post('user/registration',                     'User\RegistrationController@regist');
Route::post('user/registration/auth',                'User\RegistrationController@auth');

// 入力チェックAjax
Route::post('user/ajax/check_mail_address',          'User\Ajax\CheckMailAdressController@check');          // メールアドレスの重複チェック
Route::post('user/ajax/check_name',                  'User\Ajax\CheckNameController@check');                // ニックネームの重複チェック
