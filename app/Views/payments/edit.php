<h2 class="text-2xl font-semibold text-stone-800 mb-6">Editar abono</h2>

<form method="POST" action="<?= url('payments/' . $payment['id'] . '/update') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>
    <input type="hidden" name="project_id" value="<?= $payment['project_id'] ?>">

    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Monto *</label>
        <input type="number" step="0.01" name="amount" required value="<?= e((string) $payment['amount']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Fecha *</label>
        <input type="date" name="payment_date" required value="<?= e($payment['payment_date']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Método de pago</label>
        <select name="payment_method" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <?php foreach (['efectivo', 'transferencia', 'tarjeta', 'deposito', 'otro'] as $m): ?>
                <option value="<?= $m ?>" <?= $payment['payment_method'] === $m ? 'selected' : '' ?>><?= ucfirst($m) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-600 mb-1">Notas</label>
        <input type="text" name="notes" value="<?= e($payment['notes']) ?>" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
    </div>

    <div class="pt-2 flex gap-3">
        <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
        <a href="<?= url('projects/' . $payment['project_id']) ?>" class="text-sm text-stone-500 px-5 py-2">Cancelar</a>
    </div>
</form>
