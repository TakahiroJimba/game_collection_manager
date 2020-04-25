<?php
    // 【定数宣言】
    // システム全般
    define('APP_NAME',              'ゲームコレクション');
    define('ADMIN_MAIL_ADDRESS',    'wpstore.pro.adm@gmail.com');

    define('COPYRIGHT',             'Copyright © 2020 suke. All Rights Reserved.');

    // アプリ情報
    define('GAME_COLLECTION_MGR_APP_ID',            0);
    define('GAME_COLLECTION_APP_ID',                1);
    define('REVERSI_APP_ID',                        2);

    // ユーザ
    define('USER_NAME_MAX_LENGTH',                  10);
    define('USER_PASSWORD_MIN_LENGTH',              8);
    define('USER_PASSWORD_MAX_LENGTH',              20);
    define('USER_REGISTRATION_PASS_PHRASE_LENGTH',  4);
    define('USER_REGISTRATION_AUTH_EXPIRATION',     30);    // 分
    define('USER_MAIL_ADDRESS_REGEXP',              "/^[a-zA-Z0-9_.+-]+[@][a-zA-Z0-9.-]+$/");
    define('USER_PASSWORD_REGEXP',                  "/^[a-zA-Z0-9]{".USER_PASSWORD_MIN_LENGTH.",".USER_PASSWORD_MAX_LENGTH."}+$/");
    define('USER_MAIL_ADDRESS_AUTH_EXPIRATION',     30);    // 分

    // ログイン
    define('USER_LOCK_MINUTES',                     10);    // 分
    define('USER_LOGIN_EXPIRATION_DATE',            10);    // day

    // パスワードリセット
    define('PASSWORD_RESET_EXPIRATION',             60);    // 分

    // 入力フォームのエラーclass名
    define('VALIDATION_ERR_CLASS', "validation_err");
?>
