@extends('layout.common')

@section('title', 'パスワードリセット申請')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="reset_password_title">
        <div id="reset_password_title_inner">パスワードリセット申請</div>
    </div>
    <div id="reset_password_outer">
        <div id="reset_password_inner">
            @include('parts.show_err_msg_array')
            @include('parts.show_msg')

            <span>パスワードをリセットしたいアカウントのメールアドレスを入力してください。</span>

                <form action="/user/pw/reset" accept-charset="UTF-8" method="post">
                    {{ csrf_field() }}
                    <div>
                      <input id="mail_address" placeholder="メールアドレス" class="<?php echo $validation['mail_address']; ?> _width80per"
                        type="text" name="mail_address" value="{{ $mail_address }}" />
                    </div>

                    <div class="actions">
                      <input type="submit" name="commit" value="メール送信" class="btn _margin-top-10px mybtn-green" data-disable-with="メール送信" />
                    </div>
                </form>
            </div>
        </div>
@endsection

@include('layout.footer')
