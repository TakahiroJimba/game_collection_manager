@extends('layout.common')

@section('title', 'ユーザ登録完了')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_registration_title">
        <div id="user_registration_title_inner">ユーザ登録完了</div>
    </div>

    <div id="registration_outer">
        <div id="registration_inner">
            <div class="_margin-bottom-40px">
                ユーザ登録が完了しました。<br>
                ご登録ありがとうございます。<br>
                アプリのログイン画面からログインしてみましょう。
            </div>
        </div>
    </div>

@endsection

@include('layout.footer')
