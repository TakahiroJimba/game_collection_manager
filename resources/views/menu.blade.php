@extends('layout.common')

@section('title', 'メニュー')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
<div id="menu_outer">
    <div id="menu_inner">
        <div class="menu_title">メニュー</div>
        <a href='/user/update'>ユーザ情報変更</a><br>
        <a href='/user/delete'>ユーザアカウント削除</a><br>
        <a href='/logout'>ログアウト</a><br>
    </div>
</div>
@endsection

@include('layout.footer')
