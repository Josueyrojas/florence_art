<h2 class="text-2xl font-semibold text-stone-800 mb-6">Gastos operativos</h2>

<form method="POST" action="<?= url('operating-expenses') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-6 grid grid-cols-1 sm:grid-cols-6 gap-3">
    <?= csrf_field() ?>
    <select name="category" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <option value="renta">Renta</option>
        <option value="servicios">Servicios (luz, agua, internet)</option>
        <option value="insumos_generales">Insumos generales</option>
        <option value="mantenimiento">Mantenimiento</option>
        <option value="nomina_admin">Nómina administrativa</option>
        <option value="otro">Otro</option>
    </select>
    <input type="text" name="description" placeholder="Descripción" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
    <input type="number" step="0.01" name="amount" placeholder="Monto" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <input type="date" name="expense_date" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <label class="flex items-center gap-2 text-sm text-stone-600">
        <input type="checkbox" name="is_recurring" value="1"> Recurrente
    </label>
    <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2 sm:col-span-6 sm:w-max">Registrar gasto</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Fecha</th><th class="py-3 px-4">Categoría</th><th class="py-3 px-4">Descripción</th><th class="py-3 px-4">Monto</th><th class="py-3 px-4"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $ex): ?>
                <tr class="border-b border-stone-50">
                    <td class="py-3 px-4"><?= e($ex['expense_date']) ?></td>
                    <td class="py-3 px-4 capitalize"><?= e(str_replace('_', ' ', $ex['category'])) ?></td>
                    <td class="py-3 px-4"><?= e($ex['description']) ?></td>
                    <td class="py-3 px-4 text-red-600"><?= money($ex['amount']) ?></td>
                    <td class="py-3 px-4 text-right">
                        <?php if (isAdmin()): ?>
                        <form method="POST" action="<?= url('operating-expenses/' . $ex['id'] . '/delete') ?>" onsubmit="return confirm('¿Eliminar este gasto?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$expenses): ?>
                <tr><td colspan="5" class="py-6 text-center text-stone-400">Sin gastos operativos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
