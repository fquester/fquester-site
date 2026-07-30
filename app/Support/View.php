<?php

namespace App\Support;

use eftec\bladeone\BladeOne;

class View
{
    protected static ?BladeOne $engine = null;

    public static function configure(string $viewsPath, string $cachePath): void
    {
        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0775, true);
        }

        static::$engine = new BladeOne($viewsPath, $cachePath, BladeOne::MODE_AUTO);
    }

    public static function make(string $view, array $data = []): static
    {
        return new static($view, $data);
    }

    protected string $view;
    protected array $data;

    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function render(): string
    {
        return static::$engine->run($this->view, $this->data);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
