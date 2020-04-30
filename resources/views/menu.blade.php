@extends('layout.common')

@section('title', 'メニュー')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
<div id="menu_outer">
    <div id="menu_inner" class="_margin-top-30px">
        <h1>メニュー</h1>
        <div id="main_menu">
            <a href='/user/update'>ユーザ情報変更</a><br>
            <a href='/user/delete'>アカウント削除</a><br>
            <a href='/logout'>ログアウト</a><br>
        </div>
    </div>
</div>
@endsection

@include('layout.footer')
