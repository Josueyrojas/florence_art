<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-stone-800"><?= e($supplier['name']) ?></h2>
        <p class="text-sm text-stone-500"><?= e($supplier['contact_name']) ?></p>
    </div>
    <a href="<?= url('suppliers') ?>" class="text-sm text-stone-500 hover:underline">&larr; Volver</a>
</div>

<form method="POST" action="<?= url('supplier-payments') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-6 grid grid-cols-1 sm:grid-cols-5 gap-3">
    <?= csrf_field() ?>
    <input type="hidden" name="supplier_id" value="<?= $supplier['id'] ?>">
    <input type="number" step="0.01" name="amount" placeholder="Monto" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <input type="date" name="payment_date" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <select name="payment_method" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
        <option value="efectivo">Efectivo</option>
        <option value="transferencia">Transferencia</option>
        <option value="tarjeta">Tarjeta</option>
        <option value="deposito">Depósito</option>
        <option value="otro">Otro</option>
    </select>
    <input type="text" name="notes" placeholder="Notas" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
    <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2">Registrar abono</button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-stone-400 border-b border-stone-100">
                <th class="py-3 px-4">Fecha</th><th class="py-3 px-4">Monto</th><th class="py-3 px-4">Método</th><th class="py-3 px-4">Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
                <tr class="border-b border-stone-50">
                    <td class="py-3 px-4"><?= e($p['payment_date']) ?></td>
                    <td class="py-3 px-4 text-emerald-600"><?= money($p['amount']) ?></td>
                    <td class="py-3 px-4 capitalize"><?= e($p['payment_method']) ?></td>
                    <td class="py-3 px-4 text-stone-500"><?= e($p['notes']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$payments): ?>
                <tr><td colspan="4" class="py-6 text-center text-stone-400">Sin abonos registrados a este proveedor.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
