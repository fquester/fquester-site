<?php

namespace App\Support;

class FileInfo
{
    public static function resolve(array $item): array
    {
        if (isset($item['url'])) {
            return [
                'label'    => $item['label'],
                'href'     => $item['url'],
                'date'     => $item['date'] ?? '—',
                'size'     => $item['size'] ?? '—',
                'external' => true,
            ];
        }

        $id = static::id($item['path']);
        $client = NextcloudClient::fromEnv();
        $size = '—';
        $date = $item['date'] ?? null;

        if ($client !== null) {
            $stat = Cache::remember(
                'nextcloud-stat-' . $id,
                (int) env('NEXTCLOUD_CACHE_TTL', 3600),
                fn () => $client->stat($item['path'])
            );

            if ($stat !== null) {
                if ($stat['size'] !== null) {
                    $size = static::humanSize($stat['size']);
                }
                if ($date === null && $stat['last_modified'] !== null) {
                    $date = date('d.m.y', $stat['last_modified']);
                }
            }
        }

        return [
            'label'    => $item['label'],
            'href'     => route('file', ['id' => $id]),
            'date'     => $date ?? '—',
            'size'     => $size,
            'external' => false,
        ];
    }

    public static function id(string $path): string
    {
        return substr(hash('sha256', $path), 0, 20);
    }

    public static function humanSize(int $bytes): string
    {
        if ($bytes >= 1024 ** 3) {
            return round($bytes / 1024 ** 3, 2) . ' gb';
        }

        if ($bytes >= 1024 ** 2) {
            return round($bytes / 1024 ** 2, 1) . ' mb';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' kb';
        }

        return $bytes . ' b';
    }
}
