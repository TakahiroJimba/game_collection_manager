@extends('layout.common')

@section('title', 'ユーザアカウント削除完了')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="user_delete_title">
        <div id="user_delete_title_inner">ユーザアカウント削除完了</div>
    </div>

    <div id="user_delete_outer">
        <div id="user_delete_inner">
            アカウントを削除しました。<br>
            ご利用ありがとうございました。
        </div>
    </div>

@endsection

@include('layout.footer')
