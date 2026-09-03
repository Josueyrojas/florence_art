<?php

/**
 * Crea un usuario administrador desde la línea de comandos.
 * Uso: php bin/create_admin.php "Nombre Apellido" correo@ejemplo.com contraseñaSegura
 */

require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

[, $name, $email, $password] = array_pad($argv, 4, null);

if (!$name || !$email || !$password) {
    fwrite(STDERR, "Uso: php bin/create_admin.php \"Nombre Apellido\" correo@ejemplo.com contraseñaSegura\n");
    exit(1);
}

$db = Database::connection();

$exists = $db->prepare('SELECT id FROM users WHERE email = ?');
$exists->execute([$email]);
if ($exists->fetch()) {
    fwrite(STDERR, "Ya existe un usuario con ese correo.\n");
    exit(1);
}

$stmt = $db->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin']);

echo "Usuario administrador creado correctamente: {$email}\n";
