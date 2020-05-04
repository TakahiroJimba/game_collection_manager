@section('footer')
    <footer>
        <div id="copyright">{{COPYRIGHT}}</div>
    </footer>

    <!-- jquery読み込み -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    @if (App::environment('local'))
        <script src="{{ asset('js/app.js') }}"></script>
    @else
        <script src="{{ secure_asset('js/app.js') }}"></script>
    @endif
@endsection
