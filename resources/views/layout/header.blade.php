@section('header')
    <header id="header" class="header">
        <div id="header_logo">
            <!-- システム名 -->
            <a id="logo" href='/menu'>{{APP_NAME}}</a>

            <!-- ユーザ名表示 -->
            <span id="header_user_name">
                @if (session('user_id'))
                    {{session('name')}}さん
                @endif
            </span>
        </div>

        <div id="header_menu">
            @if (session('session_id'))
                <!-- ログイン時メニュー -->
                <span class="menu_padding">
                    <a href='/user/update'>ユーザ情報変更</a>
                </span>
                <span class="menu_padding">
                    <a href='/logout'>ログアウト</a>
                </span>
            @else
                <!-- ログイン前メニュー -->
                <span class="menu_padding">
                    <a href='/login'>ログイン</a>
                </span>
                <span class="menu_padding">
                    <a href='/user/registration'>アカウント登録</a>
                </span>
            @endif

        </div>
    </header>
@endsection
