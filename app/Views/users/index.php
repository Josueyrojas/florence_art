<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold text-stone-800">Usuarios</h2>
    <a href="<?= url('users/create') ?>" class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg">+ Nuevo usuario</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Nombre</th>
                <th class="py-3 px-4">Correo</th>
                <th class="py-3 px-4">Rol</th>
                <th class="py-3 px-4">Estado</th>
                <th class="py-3 px-4 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr class="border-b border-stone-50 hover:bg-stone-50">
                    <td class="py-3 px-4 font-medium text-stone-800">
                        <?= e($u['name']) ?>
                        <?php if ((int) $u['id'] === $currentUserId): ?>
                            <span class="text-xs text-stone-400">(tú)</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4 text-stone-600"><?= e($u['email']) ?></td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs <?= $u['role'] === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-stone-100 text-stone-600' ?> capitalize">
                            <?= e($u['role']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <?php if ($u['is_active']): ?>
                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-50 text-emerald-600">Activo</span>
                        <?php else: ?>
                            <span class="px-2 py-1 rounded-full text-xs bg-red-50 text-red-600">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-end gap-3 text-sm">
                            <a href="<?= url('users/' . $u['id'] . '/edit') ?>" class="text-amber-700 hover:underline">Editar</a>
                            <?php if ((int) $u['id'] !== $currentUserId): ?>
                            <form method="POST" action="<?= url('users/' . $u['id'] . '/delete') ?>"
                                  onsubmit="return confirm('¿Eliminar a <?= e($u['name']) ?>? Esta acción no se puede deshacer.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$users): ?>
                <tr><td colspan="5" class="py-6 text-center text-stone-400">Sin usuarios registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
