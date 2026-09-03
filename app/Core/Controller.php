<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        renderView($view, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica el token CSRF de un envío POST. Corta la ejecución si no coincide.
     */
    protected function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['_csrf'] ?? '', $token)) {
            http_response_code(419);
            exit('Token CSRF inválido. Actualiza la página e intenta de nuevo.');
        }
    }

    /**
     * Restringe una acción (normalmente eliminar) al rol 'admin'.
     * Un 'operador' puede crear y editar, pero no borrar.
     */
    protected function requireAdmin(): void
    {
        if (($_SESSION['user_role'] ?? null) !== 'admin') {
            flash('error', 'Solo un administrador puede realizar esta acción.');
            http_response_code(403);
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('')));
            exit;
        }
    }
}
