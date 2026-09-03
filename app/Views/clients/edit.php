<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar cliente</h2>

<form method="POST" action="<?= url('clients/' . $client['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre *</label>
        <input type="text" name="name" required value="<?= e($client['name']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Teléfono</label>
        <input type="text" name="phone" value="<?= e($client['phone']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Correo</label>
        <input type="email" name="email" value="<?= e($client['email']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Dirección</label>
        <input type="text" name="address" value="<?= e($client['address']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Notas</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"><?= e($client['notes']) ?></textarea>
    </div>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('clients') ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
