@section('header')
    <header class="header">
        <div><a href='/top'>{{APP_NAME}}</a></div>

        <!-- <a href='/product/list'>販売商品一覧</a><br>

        @if (session('session_id'))
            <a href='/product/registration/list'>Myテーマ一覧</a><br>
            <a href='/user/update'>会員情報変更</a><br>
            <a href='/board_menu'>掲示板メニュー</a><br>
            <a href='/logout'>ログアウト</a><br>
        @else
            <a href='/login'>ログイン</a><br>
        @endif

        <a href='/admin_inquiry'>管理者問い合わせ</a><br>

        @if (session('admin_session_id'))
            <div>管理者でログイン中です。</div>
            <a href='/admin/logout'>管理者からログアウト</a>
        @endif -->
    </header>
@endsection
