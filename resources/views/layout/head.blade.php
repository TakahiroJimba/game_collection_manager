@section('head')
    <meta charset="UTF-8">
    <title>@yield('title') | {{APP_NAME}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" itemprop="description" content="@yield('description')">
    <meta name="keywords" itemprop="keywords" content="@yield('keywords')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    @if(App::environment('local'))
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @else
        <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">
    @endif
@endsection
