<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar usuario</h2>

<form method="POST" action="<?= url('users/' . $user['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nombre *</label>
        <input type="text" name="name" required value="<?= e($user['name']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Correo *</label>
        <input type="email" name="email" required value="<?= e($user['email']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Nueva contraseña</label>
        <input type="password" name="password" minlength="8" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <p class="text-xs text-stone-400 mt-1">Déjalo en blanco para mantener la contraseña actual. Si la cambias, mínimo 8 caracteres.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Rol *</label>
        <select name="role" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <option value="operador" <?= $user['role'] === 'operador' ? 'selected' : '' ?>>Operador — puede crear y editar, no eliminar</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrador — control total</option>
        </select>
    </div>
    <label class="flex items-center gap-2 text-sm text-stone-600">
        <input type="checkbox" name="is_active" value="1" <?= $user['is_active'] ? 'checked' : '' ?>> Cuenta activa
    </label>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('users') ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
