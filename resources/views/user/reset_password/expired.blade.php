@extends('layout.common')

@section('title', 'パスワードリセット申請有効期限切れ')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="reset_password_title">
        <div id="reset_password_title_inner">有効期限切れ</div>
    </div>

    <div id="reset_password_outer">
        <div id="reset_password_inner">
            <div class="_margin-bottom-40px">
                パスワードリセット申請の有効期限が切れております。<br>
                恐れ入りますが、再度、パスワードリセット申請をお願いいたします。
            </div>

            <div>
                <a href="/user/pw/reset">パスワードを忘れた方</a>
            </div>
        </div>
    </div>

@endsection

@include('layout.footer')
