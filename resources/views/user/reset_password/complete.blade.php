@extends('layout.common')

@section('title', 'パスワードリセット完了')
@section('keywords', '')
@section('description', '')

@include('layout.head')

@include('layout.header')

@section('content')
    <div id="reset_password_title">
        <div id="reset_password_title_inner">パスワードリセット完了</div>
    </div>

    <div id="reset_password_outer">
        <div id="reset_password_inner">
            <div class="_margin-bottom-40px">
                パスワードリセットが完了しました。
            </div>
        </div>
    </div>

@endsection

@include('layout.footer')
