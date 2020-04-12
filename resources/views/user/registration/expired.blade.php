@extends('layout.common')

@section('title', 'ユーザ登録有効期限切れ')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_registration_title">
        <div id="user_registration_title_inner">有効期限切れ</div>
    </div>

    <div id="registration_outer">
        <div id="registration_inner">
            <div class="_margin-bottom-40px">
                ユーザ登録の有効期限が切れております。<br>
                恐れ入りますが、再度登録をお願いいたします。
            </div>
            <div>
                <a href="/user/registration">ユーザ登録する</a>
            </div>
        </div>
    </div>

@endsection

@include('layout.footer')
