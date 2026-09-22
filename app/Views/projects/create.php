<h2 class="text-2xl font-semibold text-stone-800 mb-6">Nuevo proyecto</h2>

<form method="POST" action="<?= url('projects') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-2xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Cliente *</label>
        <select name="client_id" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <option value="">Selecciona un cliente</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="text-xs text-stone-400 mt-1">
            ¿No está en la lista? <a href="<?= url('clients/create') ?>" class="text-amber-600 hover:underline">Registra un nuevo cliente</a>.
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre del proyecto *</label>
        <input type="text" name="name" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Descripción</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Costo acordado *</label>
            <input type="number" step="0.01" inputmode="decimal" min="0" name="agreed_cost" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Estado</label>
            <select name="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="cotizado">Cotizado</option>
                <option value="en_proceso">En proceso</option>
                <option value="entregado">Entregado</option>
                <option value="finalizado">Finalizado</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Fecha de inicio</label>
            <input type="date" name="start_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-600 mb-1">Fecha de entrega</label>
            <input type="date" name="delivery_date" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
    </div>

    <div class="pt-2">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar proyecto</button>
    </div>
</form>
