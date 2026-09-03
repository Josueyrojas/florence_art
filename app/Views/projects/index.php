<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <h2 class="text-2xl font-semibold text-stone-800">Proyectos</h2>
    <div class="flex gap-2">
        <a href="<?= url('projects/export.csv') ?>" class="text-sm text-stone-600 border border-stone-300 rounded-lg px-4 py-2 hover:bg-stone-50 text-center">Exportar CSV</a>
        <a href="<?= url('projects/create') ?>" class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg text-center">+ Nuevo proyecto</a>
    </div>
</div>

<form method="GET" action="<?= url('projects') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-4 mb-6 grid grid-cols-1 sm:grid-cols-4 gap-3">
    <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Buscar proyecto o cliente..."
           class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
    <select name="status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <option value="">Todos los estados</option>
        <?php foreach (['cotizado', 'en_proceso', 'entregado', 'finalizado', 'cancelado'] as $s): ?>
            <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="bg-stone-800 hover:bg-stone-900 text-white text-sm px-4 py-2 rounded-lg">Filtrar</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Proyecto</th>
                <th class="py-3 px-4">Cliente</th>
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
                    <td class="py-3 px-4 text-stone-600"><?= e($p['client_name']) ?></td>
                    <td class="py-3 px-4 text-stone-700"><?= money($p['total_project_cost']) ?></td>
                    <td class="py-3 px-4 <?= $p['balance_due'] > 0 ? 'text-red-600' : 'text-emerald-600' ?>"><?= money($p['balance_due']) ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs bg-stone-100 text-stone-600"><?= e(str_replace('_', ' ', $p['status'])) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$projects): ?>
                <tr><td colspan="5" class="py-6 text-center text-stone-400">No se encontraron proyectos.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$basePath = 'projects';
$queryParams = array_filter($filters);
require __DIR__ . '/../partials/pagination.php';
?>
