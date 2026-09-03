<?php

/**
 * Suite de pruebas de integración ligera (sin dependencias externas como PHPUnit).
 * Corre contra una base de datos desechable (florence_art_test) que se crea,
 * se llena con datos de prueba y se destruye en cada ejecución.
 *
 * Uso: php tests/run.php
 */

require __DIR__ . '/../app/Core/helpers.php';
require __DIR__ . '/../config/config.php';

$_ENV['DB_NAME'] = 'florence_art_test';
putenv('DB_NAME=florence_art_test');

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

use App\Models\Client;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Labor;
use App\Models\ProjectExtra;
use App\Models\SupplierPayment;
use App\Models\ProjectStatusHistory;

$passed = 0;
$failed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  OK  {$label}\n";
    } else {
        $failed++;
        echo "FAIL  {$label}\n";
    }
}

function checkEquals(string $label, $expected, $actual): void
{
    check("{$label} (esperado: {$expected}, obtenido: {$actual})", (float) $expected === (float) $actual);
}

$testDbName = env('DB_NAME');
$rootDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', env('DB_HOST', '127.0.0.1'), env('DB_PORT', '3306'));
$root = new PDO($rootDsn, env('DB_USER', 'root'), env('DB_PASS', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$root->exec("DROP DATABASE IF EXISTS `{$testDbName}`");

$schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
$schemaSql = preg_replace('/^--.*$/m', '', $schemaSql);
$schemaSql = str_replace('florence_art', $testDbName, $schemaSql);

foreach (array_filter(array_map('trim', explode(';', $schemaSql))) as $statement) {
    $root->exec($statement);
}
$root = null;

echo "Base de datos de prueba '{$testDbName}' creada.\n\n";

try {
    // --- Fixtures ---------------------------------------------------------
    $clientModel = new Client();
    $clientId = $clientModel->create([
        'name' => 'Cliente Test', 'phone' => null, 'email' => null, 'address' => null, 'notes' => null,
    ]);

    $projectModel = new Project();
    $projectId = $projectModel->create([
        'client_id' => $clientId, 'name' => 'Proyecto Test', 'description' => null,
        'agreed_cost' => 10000, 'start_date' => null, 'delivery_date' => null, 'status' => 'cotizado',
    ]);

    (new ProjectExtra())->create(['project_id' => $projectId, 'description' => 'Extra', 'amount' => 500, 'extra_date' => '2026-01-01']);
    (new Payment())->create(['project_id' => $projectId, 'amount' => 4000, 'payment_date' => '2026-01-02', 'payment_method' => 'efectivo', 'notes' => null]);
    (new Expense())->create(['project_id' => $projectId, 'supplier_id' => null, 'category' => 'material', 'description' => 'Madera', 'amount' => 1500, 'expense_date' => '2026-01-03', 'is_credit' => false]);
    (new Labor())->create(['project_id' => $projectId, 'worker_name' => 'Juan', 'labor_type' => 'interno', 'description' => null, 'hours' => null, 'amount' => 800, 'labor_date' => '2026-01-04']);

    // --- Resumen financiero del proyecto -----------------------------------
    echo "-- Resumen financiero de proyecto --\n";
    $summary = $projectModel->financialSummary($projectId);
    checkEquals('costo total = costo acordado + extras', 10500, $summary['total_cost']);
    checkEquals('saldo pendiente = costo total - abonos', 6500, $summary['balance_due']);
    checkEquals('utilidad = abonos - (gastos + mano de obra)', 4000 - (1500 + 800), $summary['utility']);

    // --- Saldo de proveedor (fiado) ----------------------------------------
    echo "\n-- Saldo de proveedor --\n";
    $supplierModel = new Supplier();
    $supplierId = $supplierModel->create(['name' => 'Proveedor Test', 'contact_name' => null, 'phone' => null, 'email' => null, 'address' => null, 'notes' => null]);
    (new Expense())->create(['project_id' => $projectId, 'supplier_id' => $supplierId, 'category' => 'material', 'description' => 'Fiado', 'amount' => 2000, 'expense_date' => '2026-01-05', 'is_credit' => true]);
    (new SupplierPayment())->create(['supplier_id' => $supplierId, 'amount' => 800, 'payment_date' => '2026-01-06', 'payment_method' => 'efectivo', 'notes' => null]);

    $found = null;
    foreach ($supplierModel->allWithBalance() as $s) {
        if ((int) $s['id'] === $supplierId) {
            $found = $s;
        }
    }
    check('proveedor encontrado en allWithBalance()', $found !== null);
    if ($found) {
        checkEquals('saldo proveedor = crédito - abonado', 1200, $found['balance']);
    }

    // --- Protección de integridad referencial -------------------------------
    echo "\n-- Integridad referencial --\n";
    try {
        $clientModel->delete($clientId);
        check('eliminar cliente con proyectos asociados debe fallar', false);
    } catch (\PDOException $e) {
        check('eliminar cliente con proyectos asociados lanza PDOException (FK RESTRICT)', true);
    }

    // --- Paginación y búsqueda ------------------------------------------------
    echo "\n-- Paginación --\n";
    for ($i = 0; $i < 5; $i++) {
        $clientModel->create(['name' => "Cliente paginación {$i}", 'phone' => null, 'email' => null, 'address' => null, 'notes' => null]);
    }
    checkEquals('count() refleja todos los clientes creados', 6, $clientModel->count(''));
    $page1 = $clientModel->paginated('', 1, 3);
    check('paginated() respeta el límite por página', count($page1) === 3);
    check('búsqueda por nombre encuentra coincidencias', count($clientModel->paginated('paginación', 1, 15)) === 5);

    // --- Bitácora de estados --------------------------------------------------
    echo "\n-- Historial de estados --\n";
    $historyModel = new ProjectStatusHistory();
    $historyModel->log($projectId, 'cotizado', 'en_proceso');
    $history = $historyModel->forProject($projectId);
    check('el cambio de estado queda registrado', count($history) === 1 && $history[0]['new_status'] === 'en_proceso');
} finally {
    $cleanup = new PDO($rootDsn, env('DB_USER', 'root'), env('DB_PASS', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $cleanup->exec("DROP DATABASE IF EXISTS `{$testDbName}`");
    echo "\nBase de datos de prueba eliminada.\n";
}

echo "\n===================================\n";
echo "Resultado: {$passed} exitosas, {$failed} fallidas\n";
exit($failed > 0 ? 1 : 0);
