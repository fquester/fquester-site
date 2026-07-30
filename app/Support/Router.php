<?php

namespace App\Support;

class Router
{
    protected array $routes = [];

    public function get(string $uri, callable $action, ?string $name = null): static
    {
        $this->routes[] = [
            'method' => 'GET',
            'uri'    => trim($uri, '/'),
            'action' => $action,
            'name'   => $name,
        ];

        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['uri'], $uri);

            if ($params !== null) {
                call_user_func_array($route['action'], $params);
                return;
            }
        }

        http_response_code(404);
        echo View::make('pages.404')->render();
    }

    public function url(string $name, array $params = []): string
    {
        foreach ($this->routes as $route) {
            if ($route['name'] === $name) {
                $uri = $route['uri'];
                foreach ($params as $key => $value) {
                    $uri = str_replace('{' . $key . '}', (string) $value, $uri);
                }
                return '/' . $uri;
            }
        }

        throw new \RuntimeException("Маршрут '$name' не найден");
    }

    protected function match(string $routeUri, string $requestUri): ?array
    {
        $routeParts = $routeUri === '' ? [] : explode('/', $routeUri);
        $requestParts = $requestUri === '' ? [] : explode('/', $requestUri);

        if (count($routeParts) !== count($requestParts)) {
            return null;
        }

        $params = [];

        foreach ($routeParts as $i => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $params[] = $requestParts[$i];
                continue;
            }

            if ($part !== $requestParts[$i]) {
                return null;
            }
        }

        return $params;
    }
}
