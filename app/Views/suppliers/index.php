<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-stone-800">Proveedores</h2>
    <a href="<?= url('suppliers/create') ?>" class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg">+ Nuevo proveedor</a>
</div>

<form method="GET" action="<?= url('suppliers') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-4 mb-6 flex gap-3">
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Buscar por nombre o contacto..."
           class="flex-1 rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <button class="bg-stone-800 hover:bg-stone-900 text-white text-sm px-4 py-2 rounded-lg">Buscar</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Proveedor</th>
                <th class="py-3 px-4">Contacto</th>
                <th class="py-3 px-4">Deuda (fiado)</th>
                <th class="py-3 px-4 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $s): ?>
                <tr class="border-b border-stone-50 hover:bg-stone-50">
                    <td class="py-3 px-4">
                        <a href="<?= url('suppliers/' . $s['id']) ?>" class="font-medium text-amber-700 hover:underline"><?= e($s['name']) ?></a>
                    </td>
                    <td class="py-3 px-4 text-stone-600"><?= e($s['contact_name']) ?></td>
                    <td class="py-3 px-4 <?= $s['balance'] > 0 ? 'text-red-600' : 'text-emerald-600' ?>"><?= money($s['balance']) ?></td>
                    <td class="py-3 px-4">
                        <div class="flex justify-end gap-3 text-sm">
                            <a href="<?= url('suppliers/' . $s['id'] . '/edit') ?>" class="text-amber-700 hover:underline">Editar</a>
                            <?php if (isAdmin()): ?>
                            <form method="POST" action="<?= url('suppliers/' . $s['id'] . '/delete') ?>"
                                  onsubmit="return confirm('¿Eliminar a <?= e($s['name']) ?>? Esta acción no se puede deshacer.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$suppliers): ?>
                <tr><td colspan="4" class="py-6 text-center text-stone-400">Sin proveedores registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$basePath = 'suppliers';
$queryParams = array_filter(['search' => $search]);
require __DIR__ . '/../partials/pagination.php';
?>
