<div class="flex items-center justify-between mb-6 pb-4 border-b border-stone-200">
    <div class="flex items-center gap-3">
        <?php if (!empty($settings['logo_path'])): ?>
            <img src="<?= url($settings['logo_path']) ?>" alt="<?= e($settings['business_name']) ?>" class="h-10 w-10 rounded-full object-cover">
        <?php endif; ?>
        <div>
            <p class="text-xs text-stone-400 uppercase tracking-wide"><?= e($settings['business_name']) ?></p>
            <h1 class="text-xl font-semibold text-stone-800">Reporte de proyecto</h1>
        </div>
    </div>
    <p class="text-xs text-stone-400">Generado: <?= e(date('d/m/Y H:i')) ?></p>
</div>

<h2 class="text-lg font-semibold text-stone-800 mb-1"><?= e($project['name']) ?></h2>
<p class="text-sm text-stone-500 mb-6">
    Cliente: <?= e($project['client_name']) ?> ·
    Estado: <span class="capitalize"><?= e(str_replace('_', ' ', $project['status'])) ?></span>
</p>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    <div class="border border-stone-200 rounded-lg p-3">
        <p class="text-xs text-stone-400">Costo total</p>
        <p class="font-semibold"><?= money($summary['total_cost']) ?></p>
    </div>
    <div class="border border-stone-200 rounded-lg p-3">
        <p class="text-xs text-stone-400">Abonado</p>
        <p class="font-semibold"><?= money($summary['total_payments']) ?></p>
    </div>
    <div class="border border-stone-200 rounded-lg p-3">
        <p class="text-xs text-stone-400">Saldo pendiente</p>
        <p class="font-semibold"><?= money($summary['balance_due']) ?></p>
    </div>
    <div class="border border-stone-200 rounded-lg p-3">
        <p class="text-xs text-stone-400">Utilidad</p>
        <p class="font-semibold"><?= money($summary['utility']) ?></p>
    </div>
</div>

<h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-2">Abonos</h3>
<table class="w-full text-sm mb-6">
    <thead><tr class="text-left text-stone-400 border-b border-stone-200">
        <th class="py-1">Fecha</th><th class="py-1">Método</th><th class="py-1 text-right">Monto</th>
    </tr></thead>
    <tbody>
        <?php foreach ($payments as $p): ?>
        <tr class="border-b border-stone-100">
            <td class="py-1"><?= e($p['payment_date']) ?></td>
            <td class="py-1 capitalize"><?= e($p['payment_method']) ?></td>
            <td class="py-1 text-right"><?= money($p['amount']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$payments): ?><tr><td colspan="3" class="py-2 text-stone-400">Sin abonos.</td></tr><?php endif; ?>
    </tbody>
</table>

<h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-2">Gastos directos</h3>
<table class="w-full text-sm mb-6">
    <thead><tr class="text-left text-stone-400 border-b border-stone-200">
        <th class="py-1">Fecha</th><th class="py-1">Descripción</th><th class="py-1 text-right">Monto</th>
    </tr></thead>
    <tbody>
        <?php foreach ($expenses as $e): ?>
        <tr class="border-b border-stone-100">
            <td class="py-1"><?= e($e['expense_date']) ?></td>
            <td class="py-1"><?= e($e['description']) ?></td>
            <td class="py-1 text-right"><?= money($e['amount']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$expenses): ?><tr><td colspan="3" class="py-2 text-stone-400">Sin gastos.</td></tr><?php endif; ?>
    </tbody>
</table>

<h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-2">Mano de obra</h3>
<table class="w-full text-sm mb-6">
    <thead><tr class="text-left text-stone-400 border-b border-stone-200">
        <th class="py-1">Fecha</th><th class="py-1">Trabajador</th><th class="py-1 text-right">Monto</th>
    </tr></thead>
    <tbody>
        <?php foreach ($labor as $l): ?>
        <tr class="border-b border-stone-100">
            <td class="py-1"><?= e($l['labor_date']) ?></td>
            <td class="py-1"><?= e($l['worker_name']) ?></td>
            <td class="py-1 text-right"><?= money($l['amount']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$labor): ?><tr><td colspan="3" class="py-2 text-stone-400">Sin registros.</td></tr><?php endif; ?>
    </tbody>
</table>

<?php if ($extras): ?>
<h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-2">Modificaciones al costo</h3>
<table class="w-full text-sm">
    <thead><tr class="text-left text-stone-400 border-b border-stone-200">
        <th class="py-1">Fecha</th><th class="py-1">Descripción</th><th class="py-1 text-right">Monto</th>
    </tr></thead>
    <tbody>
        <?php foreach ($extras as $ex): ?>
        <tr class="border-b border-stone-100">
            <td class="py-1"><?= e($ex['extra_date']) ?></td>
            <td class="py-1"><?= e($ex['description']) ?></td>
            <td class="py-1 text-right"><?= money($ex['amount']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
