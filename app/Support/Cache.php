<?php

namespace App\Support;

class Cache
{
    protected static string $path;

    public static function configure(string $path): void
    {
        static::$path = rtrim($path, '/');

        if (!is_dir(static::$path)) {
            mkdir(static::$path, 0775, true);
        }
    }

    public static function remember(string $key, int $ttlSeconds, callable $resolve): mixed
    {
        $file = static::$path . '/' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $key) . '.json';

        if (is_file($file)) {
            $cached = json_decode(file_get_contents($file), true);

            if (is_array($cached) && ($cached['expires_at'] ?? 0) > time()) {
                return $cached['value'];
            }
        }

        $value = $resolve();

        if ($value !== null) {
            file_put_contents($file, json_encode([
                'expires_at' => time() + $ttlSeconds,
                'value'      => $value,
            ]));
        } elseif (is_file($file)) {
            $stale = json_decode(file_get_contents($file), true);
            return $stale['value'] ?? null;
        }

        return $value;
    }
}
