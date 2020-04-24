@extends('layout.common')

@section('title', 'パスワードリセット')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="reset_password_title">
        <div id="reset_password_title_inner">パスワードリセット</div>
    </div>
    <div id="reset_password_outer">
        <div id="reset_password_inner">
            @include('parts.show_err_msg_array')

            <form action="/user/pw/reset/input" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div class="_margin-bottom-10px"><span class="_bold">新しいパスワード</span><span class="_require">必須</span></div>
                <span>{{USER_PASSWORD_MIN_LENGTH}}〜{{USER_PASSWORD_MAX_LENGTH}}文字の英数字</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード" type="password" class="<?php echo $validation['password']; ?>" name="password" />
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">パスワード確認用</span><span class="_require">必須</span></div>
                <span>確認のため、もう一度ご入力ください。</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード確認用" type="password" class="<?php echo $validation['password_confirmation']; ?>" name="password_confirmation" />
                </div>

                <input type='hidden' name='user_id' value="{{ $user_id }}" >
                <input type='hidden' name='token' value="{{ $token }}" >

                <div class="actions">
                  <input type="submit" name="commit" value="更新" class="btn btn-primary _margin-top-10px mybtn-blue" data-disable-with="更新" />
                </div>
            </form>
        </div>
    </div>
@endsection

@include('layout.footer')
