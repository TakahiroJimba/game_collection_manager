@extends('layout.common')

@section('title', 'ユーザ登録')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')

    <div id="user_registration_title">
        <div id="user_registration_title_inner">ユーザ登録</div>
    </div>
    <div id="registration_outer">
        <div id="registration_inner">
            @include('parts.show_err_msg_array')

            <form action="/user/registration" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div class="_margin-bottom-10px"><span class="_bold">メールアドレス</span><span class="_require">必須</span></div>
                <div class="_margin-bottom-40px">
                    <input placeholder="メールアドレス" class="<?php echo $validation['mail_address']; ?> _width300px" type="text" name="mail_address" value="{{ $params['mail_address']}}" />
                    <div id="mail_address_validation" class="_margin-top-10px"></div>
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">ニックネーム</span><span class="_require">必須</span></div>
                <span>{{USER_NAME_MAX_LENGTH}}文字以内</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="ニックネーム" class="<?php echo $validation['name']; ?>" type="text" name="name" value="{{ $params['name']}}" />
                    <div id="name_validation" class="_margin-top-10px"></div>
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">パスワード</span><span class="_require">必須</span></div>
                <span>{{USER_PASSWORD_MIN_LENGTH}}〜{{USER_PASSWORD_MAX_LENGTH}}文字の英数字</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード" type="password" class="<?php echo $validation['password']; ?>" name="password" />
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">パスワード確認用</span><span class="_require">必須</span></div>
                <span>確認のため、もう一度ご入力ください。</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード確認用" type="password" class="<?php echo $validation['password_confirmation']; ?>" name="password_confirmation" />
                </div>

                <div class="">
                    <input type="submit" name="commit" value="登録" class="btn btn-primary mybtn-blue" data-disable-with="登録" />
                </div>
            </form>
        </div>
    </div>

@endsection

@include('layout.footer')
