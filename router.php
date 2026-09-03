<?php

/**
 * Router para el servidor embebido de PHP en desarrollo local.
 * Uso: php -S localhost:8000 router.php
 *
 * Sin este script, `php -S` intenta servir como archivo estático cualquier
 * URL con una extensión reconocida (como projects/export.csv) y devuelve su
 * propio 404 sin pasar por public/index.php. Un servidor real con el
 * public/.htaccess incluido (Apache + mod_rewrite) no tiene este problema;
 * este router solo replica ese comportamiento para desarrollo local.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicFile = __DIR__ . '/public' . $uri;

if ($uri !== '/' && is_file($publicFile)) {
    return false;
}

require __DIR__ . '/public/index.php';
