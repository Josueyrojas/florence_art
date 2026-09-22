<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar proyecto</h2>

<form method="POST" action="<?= url('projects/' . $project['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-2xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Cliente *</label>
        <select name="client_id" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $project['client_id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre del proyecto *</label>
        <input type="text" name="name" required value="<?= e($project['name']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Descripción</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"><?= e($project['description']) ?></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Costo acordado *</label>
            <input type="number" step="0.01" inputmode="decimal" min="0" name="agreed_cost" required value="<?= e((string) $project['agreed_cost']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Estado</label>
            <select name="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <?php foreach (['cotizado', 'en_proceso', 'entregado', 'finalizado', 'cancelado'] as $s): ?>
                    <option value="<?= $s ?>" <?= $project['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Fecha de inicio</label>
            <input type="date" name="start_date" value="<?= e($project['start_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Fecha de entrega</label>
            <input type="date" name="delivery_date" value="<?= e($project['delivery_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
    </div>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('projects/' . $project['id']) ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
