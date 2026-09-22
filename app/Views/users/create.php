<h2 class="text-2xl font-semibold text-stone-800 mb-6">Nuevo usuario</h2>

<form method="POST" action="<?= url('users') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre *</label>
        <input type="text" name="name" required class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Correo *</label>
        <input type="email" name="email" required autocomplete="off" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Contraseña *</label>
        <input type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <p class="text-xs text-stone-400 mt-1">Mínimo 8 caracteres.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Rol *</label>
        <select name="role" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <option value="operador">Operador — puede crear y editar, no eliminar</option>
            <option value="admin">Administrador — control total</option>
        </select>
    </div>
    <label class="flex items-center gap-2 text-sm text-stone-600">
        <input type="checkbox" name="is_active" value="1" checked> Cuenta activa
    </label>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Crear usuario</button>
        <a href="<?= url('users') ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
