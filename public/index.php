<?php

// Carpeta pública real (donde vive este index.php). Los controladores la usan
// para ubicar /uploads y /assets sin depender de cuántos niveles arriba estén
// app/ y config/ — así el mismo código sirve tanto si public/ es hermana de
// app/ (deploy normal) como si todo vive junto dentro de un htdocs fijo
// (hostings que no permiten mover el document root, como InfinityFree).
define('PUBLIC_PATH', __DIR__);

require_once __DIR__ . '/../app/Core/helpers.php';
require_once __DIR__ . '/../config/config.php';

// Algunos hostings compartidos/gratuitos restringen o no comparten la ruta
// de sesiones por defecto de PHP entre peticiones, lo que hace que la
// sesión (y por lo tanto el token CSRF) se pierda silenciosamente entre el
// GET que pinta el formulario y el POST que lo envía. Usamos una carpeta
// propia, dentro del proyecto, para no depender de esa configuración.
$sessionPath = PROJECT_ROOT . '/storage/sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0755, true);
}
session_save_path($sessionPath);

// Que ninguna capa intermedia (proxy/CDN/anti-bot) cachee páginas con
// estado de sesión — si cachea el login con un token CSRF viejo, todos los
// intentos de inicio de sesión fallarían con "Token CSRF inválido" aunque
// la sesión real funcione bien.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Cookie de sesión segura: httpOnly siempre, secure automático si la
// petición llega por HTTPS. El header X-Forwarded-Proto solo se confía si
// el hosting lo declara explícitamente en .env (TRUST_PROXY_HTTPS=true) —
// algunos hostings gratuitos ponen una capa intermedia delante que manda
// ese header como "https" aunque el sitio real sea http, lo que marcaría
// la cookie como "solo HTTPS" y el navegador nunca la reenviaría, rompiendo
// el login por completo.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['SERVER_PORT'] ?? null) == 443;

if (env('TRUST_PROXY_HTTPS', 'false') === 'true') {
    $isHttps = $isHttps || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ClientController;
use App\Controllers\ProjectController;
use App\Controllers\PaymentController;
use App\Controllers\ExpenseController;
use App\Controllers\LaborController;
use App\Controllers\ProjectExtraController;
use App\Controllers\ProjectMediaController;
use App\Controllers\SupplierController;
use App\Controllers\SupplierPaymentController;
use App\Controllers\OperatingExpenseController;
use App\Controllers\SettingsController;
use App\Controllers\UserController;

// --- Control de acceso ---------------------------------------------------
// Toda la app requiere sesión iniciada, salvo el login y el aviso de privacidad.
$publicPaths = ['login', 'privacidad'];
$currentPath = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if (!empty($_SESSION['user_id'])) {
    // Cierre por inactividad: protege equipos compartidos donde alguien deja
    // la sesión abierta y se aleja.
    $idleTimeoutSeconds = 30 * 60;
    $idleExpired = !empty($_SESSION['last_activity'])
        && (time() - $_SESSION['last_activity']) > $idleTimeoutSeconds;

    // Una sola sesión activa por cuenta: si alguien inicia sesión con el
    // mismo usuario en otro navegador/equipo, esta sesión deja de ser válida
    // en cuanto el token guardado en la base de datos ya no coincide.
    $tokenValid = !empty($_SESSION['session_token'])
        && (new \App\Models\User())->hasValidSessionToken((int) $_SESSION['user_id'], $_SESSION['session_token']);

    if ($idleExpired || !$tokenValid) {
        $reason = $idleExpired
            ? 'Tu sesión se cerró por inactividad.'
            : 'Tu sesión se cerró porque se inició sesión con esta cuenta en otro navegador o dispositivo.';
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('error', $reason);
        header('Location: ' . url('login'));
        exit;
    }

    $_SESSION['last_activity'] = time();
}

if (empty($_SESSION['user_id']) && !in_array($currentPath, $publicPaths, true)) {
    header('Location: ' . url('login'));
    exit;
}
if (!empty($_SESSION['user_id']) && $currentPath === 'login') {
    header('Location: ' . url(''));
    exit;
}

$router = new Router();

// Autenticación
$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->post('logout', [AuthController::class, 'logout']);
$router->get('privacidad', [AuthController::class, 'privacy']);

// Dashboard
$router->get('/', [DashboardController::class, 'index']);

// Clientes
$router->get('clients', [ClientController::class, 'index']);
$router->get('clients/create', [ClientController::class, 'create']);
$router->post('clients', [ClientController::class, 'store']);
$router->get('clients/{id}', [ClientController::class, 'show']);
$router->get('clients/{id}/edit', [ClientController::class, 'edit']);
$router->post('clients/{id}/update', [ClientController::class, 'update']);
$router->post('clients/{id}/delete', [ClientController::class, 'destroy']);

// Proyectos
$router->get('projects', [ProjectController::class, 'index']);
$router->get('projects/create', [ProjectController::class, 'create']);
$router->get('projects/export.csv', [ProjectController::class, 'exportAllCsv']);
$router->post('projects', [ProjectController::class, 'store']);
$router->get('projects/{id}', [ProjectController::class, 'show']);
$router->get('projects/{id}/edit', [ProjectController::class, 'edit']);
$router->get('projects/{id}/report', [ProjectController::class, 'report']);
$router->get('projects/{id}/export.csv', [ProjectController::class, 'exportCsv']);
$router->post('projects/{id}/update', [ProjectController::class, 'update']);
$router->post('projects/{id}/delete', [ProjectController::class, 'destroy']);

// Abonos de clientes (ingresos)
$router->post('payments', [PaymentController::class, 'store']);
$router->get('payments/{id}/edit', [PaymentController::class, 'edit']);
$router->post('payments/{id}/update', [PaymentController::class, 'update']);
$router->post('payments/{id}/delete', [PaymentController::class, 'destroy']);

// Gastos directos del proyecto
$router->post('expenses', [ExpenseController::class, 'store']);
$router->get('expenses/{id}/edit', [ExpenseController::class, 'edit']);
$router->post('expenses/{id}/update', [ExpenseController::class, 'update']);
$router->post('expenses/{id}/delete', [ExpenseController::class, 'destroy']);

// Mano de obra
$router->post('labor', [LaborController::class, 'store']);
$router->get('labor/{id}/edit', [LaborController::class, 'edit']);
$router->post('labor/{id}/update', [LaborController::class, 'update']);
$router->post('labor/{id}/delete', [LaborController::class, 'destroy']);

// Modificaciones / extras del proyecto
$router->post('project-extras', [ProjectExtraController::class, 'store']);
$router->get('project-extras/{id}/edit', [ProjectExtraController::class, 'edit']);
$router->post('project-extras/{id}/update', [ProjectExtraController::class, 'update']);
$router->post('project-extras/{id}/delete', [ProjectExtraController::class, 'destroy']);

// Galería / evidencias
$router->post('project-media', [ProjectMediaController::class, 'store']);
$router->post('project-media/{id}/delete', [ProjectMediaController::class, 'destroy']);

// Proveedores
$router->get('suppliers', [SupplierController::class, 'index']);
$router->get('suppliers/create', [SupplierController::class, 'create']);
$router->post('suppliers', [SupplierController::class, 'store']);
$router->get('suppliers/{id}', [SupplierController::class, 'show']);
$router->get('suppliers/{id}/edit', [SupplierController::class, 'edit']);
$router->post('suppliers/{id}/update', [SupplierController::class, 'update']);
$router->post('suppliers/{id}/delete', [SupplierController::class, 'destroy']);

// Abonos a proveedores (fiado)
$router->post('supplier-payments', [SupplierPaymentController::class, 'store']);

// Gastos operativos / generales
$router->get('operating-expenses', [OperatingExpenseController::class, 'index']);
$router->post('operating-expenses', [OperatingExpenseController::class, 'store']);
$router->post('operating-expenses/{id}/delete', [OperatingExpenseController::class, 'destroy']);

// Configuración del negocio
$router->get('settings', [SettingsController::class, 'index']);
$router->post('settings', [SettingsController::class, 'update']);
$router->post('settings/logo', [SettingsController::class, 'uploadLogo']);
$router->post('settings/logo/delete', [SettingsController::class, 'removeLogo']);

// Usuarios (solo admin)
$router->get('users', [UserController::class, 'index']);
$router->get('users/create', [UserController::class, 'create']);
$router->post('users', [UserController::class, 'store']);
$router->get('users/{id}/edit', [UserController::class, 'edit']);
$router->post('users/{id}/update', [UserController::class, 'update']);
$router->post('users/{id}/delete', [UserController::class, 'destroy']);

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (\Throwable $e) {
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());

    if (env('APP_ENV', 'local') === 'local') {
        throw $e;
    }

    http_response_code(500);
    renderView('errors/500');
}
