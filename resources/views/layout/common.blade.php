<DOCTYPE HTML>
    <html lang="ja">
    <head>
        @yield('head')
    </head>
    <body>
        @yield('header')
        <div class="contents">
            <div id="body_contents">
                @yield('content')
            </div>
        </div>
        @yield('footer')
    </body>
</html>
