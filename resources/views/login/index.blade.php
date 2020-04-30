@extends('layout.common')

@section('title', 'ログイン')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
<div id="login_outer">
    <div id="login_inner">
        @include('parts.show_err_msg_array')

        <div class="login_title">{{APP_NAME}}にログイン</div>
        <form action="/login" accept-charset="UTF-8" method="post">
            {{ csrf_field() }}
            <div>
              <input id="mail_address" placeholder="メールアドレス" class="login_form" type="text" name="mail_address" value="{{$mail_address ?? ""}}" />
            </div>

            <div>
              <input id="password" placeholder="パスワード" type="password" class="login_form" name="password" />
            </div>

            <div class="actions">
              <input type="submit" name="commit" value="ログイン" class="btn login_form mybtn-green" data-disable-with="ログイン" />
            </div>
        </form>
        <div class="_text-align-center">
            <a href="/user/pw/reset">パスワードを忘れた場合</a>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')
