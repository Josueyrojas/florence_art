<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar modificación</h2>

<form method="POST" action="<?= url('project-extras/' . $extra['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="project_id" value="<?= $extra['project_id'] ?>">

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Descripción *</label>
        <input type="text" name="description" required value="<?= e($extra['description']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Monto (+/-) *</label>
        <input type="number" step="0.01" name="amount" required value="<?= e((string) $extra['amount']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Fecha *</label>
        <input type="date" name="extra_date" required value="<?= e($extra['extra_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('projects/' . $extra['project_id']) ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
