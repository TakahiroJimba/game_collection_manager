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
    </div>
</div>
@endsection

@include('layout.footer')
