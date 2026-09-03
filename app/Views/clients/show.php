<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-stone-800"><?= e($client['name']) ?></h2>
        <p class="text-sm text-stone-500"><?= e($client['phone']) ?><?= $client['phone'] && $client['email'] ? ' · ' : '' ?><?= e($client['email']) ?></p>
    </div>
    <div class="flex gap-2">
        <a href="<?= url('clients/' . $client['id'] . '/edit') ?>" class="text-sm text-stone-600 border border-stone-300 rounded-lg px-4 py-2 hover:bg-stone-50">Editar</a>
        <a href="<?= url('clients') ?>" class="text-sm text-stone-500 px-4 py-2">&larr; Volver</a>
    </div>
</div>

<?php if ($client['address'] || $client['notes']): ?>
<div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4 mb-6 text-sm text-stone-600 space-y-1">
    <?php if ($client['address']): ?><p><strong class="text-stone-800">Dirección:</strong> <?= e($client['address']) ?></p><?php endif; ?>
    <?php if ($client['notes']): ?><p><strong class="text-stone-800">Notas:</strong> <?= e($client['notes']) ?></p><?php endif; ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Total facturado (costo + extras)</p>
        <p class="text-lg font-semibold text-stone-800"><?= money($totals['total_cost']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Total abonado</p>
        <p class="text-lg font-semibold text-emerald-600"><?= money($totals['total_paid']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Saldo pendiente acumulado</p>
        <p class="text-lg font-semibold text-red-600"><?= money($totals['balance_due']) ?></p>
    </div>
</div>

<h3 class="text-sm font-semibold text-stone-500 uppercase tracking-wide mb-3">Proyectos de este cliente</h3>
<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Proyecto</th>
                <th class="py-3 px-4">Costo total</th>
                <th class="py-3 px-4">Saldo pendiente</th>
                <th class="py-3 px-4">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $p): ?>
                <tr class="border-b border-stone-50 hover:bg-stone-50">
                    <td class="py-3 px-4">
                        <a href="<?= url('projects/' . $p['id']) ?>" class="font-medium text-amber-700 hover:underline"><?= e($p['name']) ?></a>
                    </td>
                    <td class="py-3 px-4 text-stone-700"><?= money($p['total_project_cost']) ?></td>
                    <td class="py-3 px-4 <?= $p['balance_due'] > 0 ? 'text-red-600' : 'text-emerald-600' ?>"><?= money($p['balance_due']) ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs bg-stone-100 text-stone-600"><?= e(str_replace('_', ' ', $p['status'])) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$projects): ?>
                <tr><td colspan="4" class="py-6 text-center text-stone-400">Este cliente aún no tiene proyectos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
