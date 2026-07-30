<?php

$base = dirname(__DIR__);

if (is_file($base . '/vendor/autoload.php')) {
    require $base . '/vendor/autoload.php';
} else {
    require $base . '/vendor/eftec/bladeone/BladeOne.php';
    require $base . '/app/Support/Env.php';
    require $base . '/app/Support/helpers.php';

    spl_autoload_register(function (string $class) use ($base): void {
        $prefix = 'App\\';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = $base . '/app/' . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require $file;
        }
    });
}

use App\Support\Env;

Env::load($base . '/.env');

$debug = env('APP_DEBUG', false);
ini_set('display_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : 0);

use App\Support\Cache;
use App\Support\View;

View::configure(base_path('resources/views'), base_path('storage/framework/views'));
Cache::configure(base_path('storage/cache'));

$router = router();
require base_path('routes/web.php');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
