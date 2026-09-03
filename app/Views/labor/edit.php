<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar mano de obra</h2>

<form method="POST" action="<?= url('labor/' . $labor['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="project_id" value="<?= $labor['project_id'] ?>">

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Trabajador</label>
        <input type="text" name="worker_name" value="<?= e($labor['worker_name']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Tipo</label>
        <select name="labor_type" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <option value="interno" <?= $labor['labor_type'] === 'interno' ? 'selected' : '' ?>>Taller propio</option>
            <option value="externo" <?= $labor['labor_type'] === 'externo' ? 'selected' : '' ?>>Externo</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Descripción</label>
        <input type="text" name="description" value="<?= e($labor['description']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Horas</label>
        <input type="number" step="0.01" name="hours" value="<?= e((string) $labor['hours']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Monto *</label>
        <input type="number" step="0.01" name="amount" required value="<?= e((string) $labor['amount']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Fecha *</label>
        <input type="date" name="labor_date" required value="<?= e($labor['labor_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('projects/' . $labor['project_id']) ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
