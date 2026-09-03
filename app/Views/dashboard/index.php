<h2 class="text-2xl font-semibold text-stone-800 mb-6">Dashboard financiero</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs uppercase tracking-wide text-stone-400">Proyectos activos</p>
        <p class="text-2xl font-semibold text-stone-800 mt-1"><?= (int) $activeProjects ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs uppercase tracking-wide text-stone-400">Por cobrar a clientes</p>
        <p class="text-2xl font-semibold text-emerald-600 mt-1"><?= money($totalReceivable) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs uppercase tracking-wide text-stone-400">Por pagar a proveedores</p>
        <p class="text-2xl font-semibold text-red-600 mt-1"><?= money($payableToSuppliers) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
        <p class="text-xs uppercase tracking-wide text-stone-400">Utilidad neta del mes</p>
        <p class="text-2xl font-semibold <?= $monthlyNetUtility >= 0 ? 'text-emerald-600' : 'text-red-600' ?> mt-1">
            <?= money($monthlyNetUtility) ?>
        </p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 mb-8">
    <h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-3">Resumen del mes</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div class="flex justify-between border-b border-stone-100 pb-2">
            <span class="text-stone-500">Ingresos (abonos)</span>
            <span class="font-medium text-stone-800"><?= money($monthlyIncome) ?></span>
        </div>
        <div class="flex justify-between border-b border-stone-100 pb-2">
            <span class="text-stone-500">Gastos totales (materiales + mano de obra + operativos)</span>
            <span class="font-medium text-stone-800"><?= money($monthlyExpenses) ?></span>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5 mb-8">
    <h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-3">Ingresos vs. gastos (últimos 6 meses)</h3>
    <canvas id="trendChart" height="90"></canvas>
</div>

<?php if ($upcomingDeliveries || $overdueBalances): ?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
    <?php if ($upcomingDeliveries): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-amber-800 uppercase tracking-wide mb-3">Entregas próximas (7 días)</h3>
        <ul class="space-y-2 text-sm">
            <?php foreach ($upcomingDeliveries as $d): ?>
                <li class="flex justify-between">
                    <a href="<?= url('projects/' . $d['id']) ?>" class="text-amber-800 hover:underline"><?= e($d['name']) ?></a>
                    <span class="text-amber-600"><?= e(date('d/m/Y', strtotime($d['delivery_date']))) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php if ($overdueBalances): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-red-800 uppercase tracking-wide mb-3">Saldo pendiente con entrega vencida</h3>
        <ul class="space-y-2 text-sm">
            <?php foreach ($overdueBalances as $o): ?>
                <li class="flex justify-between">
                    <a href="<?= url('projects/' . $o['id']) ?>" class="text-red-800 hover:underline"><?= e($o['name']) ?></a>
                    <span class="text-red-600"><?= money($o['balance_due']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide">Proyectos recientes</h3>
        <a href="<?= url('projects') ?>" class="text-sm text-amber-600 hover:underline">Ver todos</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 pr-4">Proyecto</th>
                    <th class="py-2 pr-4">Cliente</th>
                    <th class="py-2 pr-4">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProjects as $p): ?>
                    <tr class="border-b border-stone-50 hover:bg-stone-50">
                        <td class="py-2 pr-4">
                            <a href="<?= url('projects/' . $p['id']) ?>" class="text-amber-700 hover:underline"><?= e($p['name']) ?></a>
                        </td>
                        <td class="py-2 pr-4 text-stone-600"><?= e($p['client_name']) ?></td>
                        <td class="py-2 pr-4">
                            <span class="px-2 py-1 rounded-full text-xs bg-stone-100 text-stone-600"><?= e(str_replace('_', ' ', $p['status'])) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentProjects): ?>
                    <tr><td colspan="3" class="py-4 text-stone-400 text-center">Aún no hay proyectos registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($monthLabels) ?>,
        datasets: [
            { label: 'Ingresos', data: <?= json_encode($monthIncome) ?>, backgroundColor: '#059669' },
            { label: 'Gastos', data: <?= json_encode($monthExpenses) ?>, backgroundColor: '#dc2626' },
        ],
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } },
    },
});
</script>
