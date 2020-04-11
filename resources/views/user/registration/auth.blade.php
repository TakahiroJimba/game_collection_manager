@extends('layout.common')

@section('title', 'ユーザ認証')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_registration_title">
        <div id="user_registration_title_inner">ユーザ認証</div>
    </div>

    <div id="registration_outer">
        <div id="registration_inner">
            <div class="_margin-bottom-40px">
                {{ $mail_address }}に認証コードを送信しました。<br>
                受信した認証コードを入力してください。
            </div>

            @include('parts.show_err_msg_array')

            <form action="/user/auth" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div class="_margin-bottom-10px"><span class="_bold">認証コード</span></div>
                <div class="_margin-bottom-40px">
                    <input placeholder="XXXX" class="_width100px" type="text" name="auth_code" value="" />
                </div>

                <div class="">
                    <input type="submit" name="commit" value="送信" class="btn btn-primary mybtn-blue" data-disable-with="送信" />
                </div>
                <input type="hidden" name="mail_address" value="{{$mail_address}}" />
            </form>
        </div>
    </div>

@endsection

@include('layout.footer')
