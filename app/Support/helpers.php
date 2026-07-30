<?php

use App\Support\Router;

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return rtrim(dirname(__DIR__, 2), '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('router')) {
    function router(): Router
    {
        static $router;
        return $router ??= new Router();
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): string
    {
        return router()->url($name, $params);
    }
}

if (!function_exists('config_get')) {
    function config_get(string $file): array
    {
        static $cache = [];
        return $cache[$file] ??= require base_path("config/{$file}.php");
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }
}
