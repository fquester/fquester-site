<?php

namespace App\Http\Controllers;

use App\Support\FileInfo;
use App\Support\NextcloudClient;
use App\Support\View;

class PageController
{
    public function home(): void
    {
        $game = require base_path('config/game.php');

        echo View::make('pages.home', ['game' => $game, 'active' => 'home'])->render();
    }

    public function reference(): void
    {
        $game = require base_path('config/game.php');

        echo View::make('pages.reference', ['game' => $game, 'active' => 'reference'])->render();
    }

    public function download(): void
    {
        $sections = require base_path('config/downloads.php');

        $resolved = [];
        foreach ($sections as $title => $items) {
            $resolved[$title] = array_map(
                fn ($item) => FileInfo::resolve($item),
                $items
            );
        }

        echo View::make('pages.download', ['sections' => $resolved, 'active' => 'download'])->render();
    }

    public function fileProxy(string $id): void
    {
        $item = $this->findItem($id);

        if ($item === null) {
            http_response_code(404);
            echo View::make('pages.404')->render();
            return;
        }

        $client = NextcloudClient::fromEnv();

        if ($client === null) {
            http_response_code(503);
            echo 'Хранилище временно недоступно.';
            return;
        }

        $filename = basename($item['path']);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        if (!$client->stream($item['path'])) {
            http_response_code(502);
        }
    }

    protected function findItem(string $id): ?array
    {
        $sections = require base_path('config/downloads.php');

        foreach ($sections as $items) {
            foreach ($items as $item) {
                if (!isset($item['url']) && FileInfo::id($item['path']) === $id) {
                    return $item;
                }
            }
        }

        return null;
    }
}
