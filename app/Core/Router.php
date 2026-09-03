<?php

namespace App\Core;

/**
 * Router minimalista basado en expresiones regulares.
 * Soporta segmentos dinámicos con la sintaxis {param}.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $trimmed = trim($path, '/');
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $trimmed);

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => '#^' . $pattern . '$#',
            'handler'    => $handler,
            'paramNames' => $this->extractParamNames($trimmed),
        ];
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('#\{([a-zA-Z_]+)\}#', $path, $matches);
        return $matches[1];
    }

    public function dispatch(string $method, string $requestUri): void
    {
        $uri = trim((string) parse_url($requestUri, PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $params = array_combine($route['paramNames'], $matches);

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        http_response_code(404);
        renderView('errors/404');
    }
}
