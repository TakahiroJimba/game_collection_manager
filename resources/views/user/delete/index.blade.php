@extends('layout.common')

@section('title', 'ユーザアカウント削除')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_delete_title">
        <div id="user_delete_title_inner">ユーザアカウント削除</div>
    </div>

    <div id="user_delete_outer">
        <div id="user_delete_inner">
            @include('parts.show_err_msg_array')
            <form action="/user/delete" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div class="">
                    アカウントを削除します。<br>
                    削除の取り消しはできませんのでご注意ください。
                    <input type="submit" value="アカウント削除" class="btn btn-danger mybtn-red" />
                </div>
            </form>
        </div>
    </div>

@endsection

@include('layout.footer')
