<?php

require_once __DIR__ . '/env.php';

loadEnv(__DIR__ . '/../.env');

// Ajustar a la zona horaria real del taller.
date_default_timezone_set('America/Mexico_City');

$isLocal = env('APP_ENV', 'local') === 'local';
error_reporting($isLocal ? E_ALL : 0);
ini_set('display_errors', $isLocal ? '1' : '0');
