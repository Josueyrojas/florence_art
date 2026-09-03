<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar gasto</h2>

<form method="POST" action="<?= url('expenses/' . $expense['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="project_id" value="<?= $expense['project_id'] ?>">

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Descripción *</label>
        <input type="text" name="description" required value="<?= e($expense['description']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Monto *</label>
        <input type="number" step="0.01" name="amount" required value="<?= e((string) $expense['amount']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Fecha *</label>
        <input type="date" name="expense_date" required value="<?= e($expense['expense_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Categoría</label>
        <select name="category" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <?php foreach (['material', 'herraje', 'acabado', 'transporte', 'otro'] as $cat): ?>
                <option value="<?= $cat ?>" <?= $expense['category'] === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Proveedor</label>
        <select name="supplier_id" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <option value="">Sin proveedor</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $expense['supplier_id'] == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <label class="flex items-center gap-2 text-sm text-stone-600">
        <input type="checkbox" name="is_credit" value="1" <?= $expense['is_credit'] ? 'checked' : '' ?>> Comprado al fiado
    </label>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('projects/' . $expense['project_id']) ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
