<?php

// Raíz del proyecto: siempre el padre de esta carpeta (config/), sin
// importar si app/config viven junto a public/ (deploy normal) o todos
// dentro de un htdocs fijo (hostings que no dejan mover el document root).
define('PROJECT_ROOT', dirname(__DIR__));

require_once __DIR__ . '/env.php';

loadEnv(__DIR__ . '/../.env');

// Ajustar a la zona horaria real del taller.
date_default_timezone_set('America/Mexico_City');

$isLocal = env('APP_ENV', 'local') === 'local';
error_reporting($isLocal ? E_ALL : 0);
ini_set('display_errors', $isLocal ? '1' : '0');
