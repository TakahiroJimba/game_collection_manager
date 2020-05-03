{{APP_NAME}}をご利用いただきありがとうございます。
パスワードをリセットするには、下記のリンクをクリックしてください。
リンク有効期限は{{PASSWORD_RESET_EXPIRATION}}分間です。

@if(App::environment('local'))
{{ url('user/pw/reset/input', ['user_id' => $sendData['user_id'], 'token' => $sendData['token']]) }}
@else
{{ url('user/pw/reset/input', ['user_id' => $sendData['user_id'], 'token' => $sendData['token']], true) }}
@endif

上記の内容に心当たりがない場合はサイト管理者までご連絡ください。
