@if(isset($validation['err_msg_array']) && count($validation['err_msg_array']) != 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($validation['err_msg_array'] as $err_msg)
                <li>{{ $err_msg }}</li>
            @endforeach
        </ul>
    </div>
@endif
