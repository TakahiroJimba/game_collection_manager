@extends('layout.common')

@section('title', 'ログイン完了')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
<div id="login_outer">
    <div id="login_inner">
            <form action="/login" accept-charset="UTF-8" method="post">
                {{ csrf_field() }}
                <div>
                    ログインしました！
                </div>
                <div id="login_info" value="1">
                    <input type="hidden" name="user_id"    value="{{$login_user['user_id']}}" />
                    <input type="hidden" name="session_id" value="{{$login_user['session_id']}}" />
                    <input type="hidden" name="app_url"    value="{{$app_url}}" />
                </div>
            </form>
        </div>
    </div>
@endsection

@include('layout.footer')
