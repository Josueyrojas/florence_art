<h2 class="text-2xl font-semibold text-stone-800 mb-6">Nuevo proveedor</h2>

<form method="POST" action="<?= url('suppliers') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre *</label>
        <input type="text" name="name" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Persona de contacto</label>
        <input type="text" name="contact_name" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Teléfono</label>
        <input type="text" name="phone" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Correo</label>
        <input type="email" name="email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Dirección</label>
        <input type="text" name="address" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Notas</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></textarea>
    </div>

    <div class="pt-2">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar proveedor</button>
    </div>
</form>
