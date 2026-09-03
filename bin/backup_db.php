<?php

/**
 * Crea un respaldo .sql con fecha en /backups usando mysqldump.
 * Uso: php bin/backup_db.php
 *
 * Para automatizarlo:
 *   - Windows: programar una tarea con el Programador de tareas (schtasks)
 *     que ejecute `php bin/backup_db.php` diariamente.
 *   - Linux/hosting: agregar una línea de cron, p. ej.
 *     0 3 * * * php /ruta/al/proyecto/bin/backup_db.php
 */

require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../config/config.php';

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$name = env('DB_NAME', 'florence_art');
$user = env('DB_USER', 'root');
$pass = env('DB_PASS', '');

$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$file = $backupDir . '/' . $name . '_' . date('Y-m-d_His') . '.sql';

$binary = 'mysqldump';
foreach ([
    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
    'C:\\Program Files\\MySQL\\MySQL Server 9.4\\bin\\mysqldump.exe',
] as $candidate) {
    if (file_exists($candidate)) {
        $binary = $candidate;
        break;
    }
}

$args = [$binary, '--host=' . $host, '--port=' . $port, '--user=' . $user];
if ($pass !== '') {
    $args[] = '--password=' . $pass;
}
$args[] = $name;

$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['file', $file, 'w'],
    2 => ['pipe', 'w'],
];

$process = proc_open($args, $descriptors, $pipes);
if (!is_resource($process)) {
    fwrite(STDERR, "No se pudo iniciar mysqldump. ¿Está instalado y accesible?\n");
    exit(1);
}

fclose($pipes[0]);
$stderr = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$exitCode = proc_close($process);

if ($exitCode !== 0) {
    fwrite(STDERR, "Error al respaldar la base de datos:\n{$stderr}\n");
    if (file_exists($file)) {
        unlink($file);
    }
    exit(1);
}

echo "Respaldo creado: {$file} (" . round(filesize($file) / 1024, 1) . " KB)\n";
