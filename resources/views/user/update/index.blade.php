@extends('layout.common')

@section('title', 'ユーザ情報更新')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_update_title">
        <div id="user_update_title_inner">ユーザ情報更新</div>
    </div>

    <div id="user_update_outer">
        <div id="user_update_inner">
            @include('parts.show_err_msg_array')
            @include('parts.show_msg')

            <form action="/user/update" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div class="_margin-bottom-10px"><span class="_bold">ニックネーム</span><span class="_require">必須</span></div>
                <span>{{USER_NAME_MAX_LENGTH}}文字以内</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="ニックネーム" class="<?php echo $validation['name']; ?>" type="text" name="name" value="{{ $user->name }}" />
                    <div id="name_validation" class="_margin-top-10px"></div>
                    <input type='hidden' id='old_name' value="{{ $user->name }}" >
                </div>

                <div class="">
                    <input type="submit" name="basic_commit" value="更新" class="btn btn-primary mybtn-blue" data-disable-with="更新" />
                    <input type='hidden' name="basic_commit" value="basic_commit" >
                </div>
            </form>

            <form action="/user/update" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <!-- *************** メールアドレス *************** -->
                <hr>
                <div class="_margin-bottom-10px"><span class="_bold">メールアドレス</span><span class="_require">必須</span></div>
                <div class="_margin-bottom-40px">
                    <input placeholder="新しいメールアドレス" class="<?php echo $validation['mail_address']; ?> _width300px" type="text" name="mail_address" value="{{ isset($params['mail_address']) ? $params['mail_address'] : $user->mail_address }}" />
                    <div class="">
                        <input type="submit" name="mail_auth_send_commit" value="認証コード送信" class="btn mybtn-green _margin-top-10px" data-disable-with="メールアドレス更新" />
                        <input type='hidden' name="mail_auth_send_commit" value="mail_auth_send_commit" >
                    </div>
                    <div id="mail_address_validation" class="_margin-top-10px"></div>
                </div>
            </form>
            <form action="/user/update" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div>
                    <div class="_margin-top-20px">受信した認証コードを入力してください。</div>
                    <input placeholder="XXXX" class="<?php echo $validation['mail_auth_code']; ?> _width100px" type="text" name="mail_auth_code" />
                </div>

                <div class="">
                    <input type="submit" name="mail_auth_commit" value="メールアドレス更新" class="btn btn-primary mybtn-blue" data-disable-with="メールアドレス更新" />
                    <input type='hidden' name="mail_auth_commit" value="mail_auth_commit" >
                </div>
            </form>

            <form action="/user/update" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <!-- *************** パスワード *************** -->
                <hr>
                <div class="_margin-bottom-10px"><span class="_bold">現在のパスワード</span><span class="_require">必須</span></div>
                <div class="_margin-bottom-40px">
                    <input placeholder="現在のパスワード" type="password" class="<?php echo $validation['password']; ?>" name="password" />
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">新しいパスワード</span><span class="_require">必須</span></div>
                <span>{{USER_PASSWORD_MIN_LENGTH}}〜{{USER_PASSWORD_MAX_LENGTH}}文字の英数字</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード" type="password" class="<?php echo $validation['new_password']; ?>" name="new_password" />
                </div>

                <div class="_margin-bottom-10px"><span class="_bold">パスワード確認用</span><span class="_require">必須</span></div>
                <span>確認のため、もう一度ご入力ください。</span>
                <div class="_margin-bottom-40px">
                    <input placeholder="パスワード確認用" type="password" class="<?php echo $validation['password_confirmation']; ?>" name="password_confirmation" />
                </div>

                <div class="">
                    <input type="submit" name="password_commit" value="パスワード更新" class="btn btn-primary mybtn-blue" data-disable-with="パスワード更新" />
                    <input type='hidden' name="password_commit" value="password_commit" >
                </div>
            </form>
        </div>
    </div>

@endsection

@include('layout.footer')
