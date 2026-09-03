<?php

/**
 * Funciones auxiliares globales usadas por controladores y vistas.
 */

function env(string $key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

function url(string $path = ''): string
{
    $base = rtrim(env('APP_URL', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(?float $value): string
{
    return '$' . number_format($value ?? 0, 2);
}

/**
 * Datos de la empresa (nombre, logo, contacto), cacheados por petición para
 * no repetir la consulta en cada vista/layout que los necesita.
 */
function currentSettings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = (new \App\Models\Settings())->current();
    }
    return $cache;
}

/**
 * Renderiza una vista con su layout fuera del contexto de un Controller
 * (usada por el Router para la página 404 y por el manejador global de errores).
 */
function renderView(string $view, array $data = [], ?string $layout = 'layouts/app'): void
{
    $data['settings'] = $data['settings'] ?? currentSettings();
    extract($data);

    $viewFile = __DIR__ . '/../Views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        throw new \RuntimeException("Vista no encontrada: {$view}");
    }

    ob_start();
    require $viewFile;
    $content = ob_get_clean();

    if ($layout) {
        require __DIR__ . '/../Views/' . $layout . '.php';
    } else {
        echo $content;
    }
}

function isAdmin(): bool
{
    return ($_SESSION['user_role'] ?? null) === 'admin';
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function csrf_field(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="_csrf" value="' . $_SESSION['_csrf'] . '">';
}

/**
 * Escribe un mensaje flash cuando se pasa $message, o lo lee y lo consume
 * (una sola lectura) cuando se llama sin segundo argumento.
 */
function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}
