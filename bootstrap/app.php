<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

/*
|--------------------------------------------------------------------------
| 環境によって読み込む.envファイル切り替え
|--------------------------------------------------------------------------
*/

switch ($_SERVER['SERVER_NAME']) {
    case '127.0.0.1':
        $app->loadEnvironmentFrom('.env.dev');
        break;
    case 'wordpress-shopsite.herokuapp.com':        // test環境に書き換える
        $app->loadEnvironmentFrom('.env.test');
        break;
    case 'production.co.jp':                        // TODO: 本番環境に書き換える
        $app->loadEnvironmentFrom('.env.prod');
        break;
    default:
        $app->loadEnvironmentFrom('.env.dev');
        break;
}

// 定数ファイルを読み込む
require_once app_path() . '/config/const.php';

return $app;
