<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-stone-800"><?= e($project['name']) ?></h2>
        <p class="text-sm text-stone-500"><?= e($project['client_name']) ?> · <span class="capitalize"><?= e(str_replace('_', ' ', $project['status'])) ?></span></p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= url('projects/' . $project['id'] . '/export.csv') ?>" class="text-sm text-stone-600 border border-stone-300 rounded-lg px-4 py-2 hover:bg-stone-50">Exportar CSV</a>
        <a href="<?= url('projects/' . $project['id'] . '/report') ?>" class="text-sm text-stone-600 border border-stone-300 rounded-lg px-4 py-2 hover:bg-stone-50">Reporte / PDF</a>
        <a href="<?= url('projects/' . $project['id'] . '/edit') ?>" class="text-sm text-stone-600 border border-stone-300 rounded-lg px-4 py-2 hover:bg-stone-50">Editar proyecto</a>
        <?php if (isAdmin()): ?>
        <form method="POST" action="<?= url('projects/' . $project['id'] . '/delete') ?>"
              onsubmit="return confirm('¿Eliminar este proyecto por completo? Se borrarán también sus abonos, gastos, mano de obra, modificaciones y galería. Esta acción no se puede deshacer.');">
            <?= csrf_field() ?>
            <button type="submit" class="text-sm text-red-600 border border-red-200 rounded-lg px-4 py-2 hover:bg-red-50">Eliminar proyecto</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Costo total</p>
        <p class="text-lg font-semibold text-stone-800"><?= money($summary['total_cost']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Abonado</p>
        <p class="text-lg font-semibold text-emerald-600"><?= money($summary['total_payments']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Saldo pendiente</p>
        <p class="text-lg font-semibold text-red-600"><?= money($summary['balance_due']) ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-4">
        <p class="text-xs text-stone-400">Utilidad actual</p>
        <p class="text-lg font-semibold <?= $summary['utility'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= money($summary['utility']) ?></p>
    </div>
</div>

<div x-data="{ tab: 'abonos' }">
    <div class="flex flex-wrap gap-2 border-b border-stone-200 mb-6">
        <?php $tabs = ['abonos' => 'Abonos', 'gastos' => 'Gastos', 'mano_obra' => 'Mano de obra', 'extras' => 'Modificaciones', 'galeria' => 'Galería', 'historial' => 'Historial de estados']; ?>
        <?php foreach ($tabs as $key => $label): ?>
            <button @click="tab = '<?= $key ?>'"
                    :class="tab === '<?= $key ?>' ? 'border-amber-600 text-amber-700' : 'border-transparent text-stone-500 hover:text-stone-700'"
                    class="px-4 py-2 text-sm border-b-2 -mb-px">
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Abonos -->
    <div x-show="tab === 'abonos'" x-transition.opacity.duration.150ms>
        <form method="POST" action="<?= url('payments') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-4 grid grid-cols-1 sm:grid-cols-5 gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <input type="number" step="0.01" inputmode="decimal" name="amount" placeholder="Monto" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
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

        <div class="bg-white rounded-xl border border-stone-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 px-4">Fecha</th><th class="py-2 px-4">Monto</th><th class="py-2 px-4">Método</th><th class="py-2 px-4">Notas</th><th class="py-2 px-4"></th>
                </tr></thead>
                <tbody>
                <?php foreach ($payments as $pay): ?>
                    <tr class="border-b border-stone-50">
                        <td class="py-2 px-4"><?= e($pay['payment_date']) ?></td>
                        <td class="py-2 px-4 text-emerald-600"><?= money($pay['amount']) ?></td>
                        <td class="py-2 px-4 capitalize"><?= e($pay['payment_method']) ?></td>
                        <td class="py-2 px-4 text-stone-500"><?= e($pay['notes']) ?></td>
                        <td class="py-2 px-4 text-right whitespace-nowrap">
                            <a href="<?= url('payments/' . $pay['id'] . '/edit') ?>" class="text-xs text-amber-700 hover:underline mr-3">Editar</a>
                            <?php if (isAdmin()): ?>
                            <form method="POST" action="<?= url('payments/' . $pay['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar este abono?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$payments): ?><tr><td colspan="5" class="py-4 text-center text-stone-400">Sin abonos registrados.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gastos -->
    <div x-show="tab === 'gastos'" x-cloak x-transition.opacity.duration.150ms>
        <form method="POST" action="<?= url('expenses') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-4 grid grid-cols-1 sm:grid-cols-6 gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <input type="text" name="description" placeholder="Descripción" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
            <input type="number" step="0.01" inputmode="decimal" name="amount" placeholder="Monto" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <input type="date" name="expense_date" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <select name="supplier_id" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">Sin proveedor</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="is_credit" value="1"> Fiado
            </label>
            <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2 sm:col-span-6 sm:w-max">Registrar gasto</button>
        </form>

        <div class="bg-white rounded-xl border border-stone-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 px-4">Fecha</th><th class="py-2 px-4">Descripción</th><th class="py-2 px-4">Proveedor</th><th class="py-2 px-4">Monto</th><th class="py-2 px-4">Estado</th><th class="py-2 px-4"></th>
                </tr></thead>
                <tbody>
                <?php foreach ($expenses as $ex): ?>
                    <tr class="border-b border-stone-50">
                        <td class="py-2 px-4"><?= e($ex['expense_date']) ?></td>
                        <td class="py-2 px-4"><?= e($ex['description']) ?></td>
                        <td class="py-2 px-4 text-stone-500"><?= e($ex['supplier_name'] ?? '—') ?></td>
                        <td class="py-2 px-4 text-red-600"><?= money($ex['amount']) ?></td>
                        <td class="py-2 px-4">
                            <span class="px-2 py-1 rounded-full text-xs <?= $ex['payment_status'] === 'pendiente' ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' ?>">
                                <?= e($ex['payment_status']) ?>
                            </span>
                        </td>
                        <td class="py-2 px-4 text-right whitespace-nowrap">
                            <a href="<?= url('expenses/' . $ex['id'] . '/edit') ?>" class="text-xs text-amber-700 hover:underline mr-3">Editar</a>
                            <?php if (isAdmin()): ?>
                            <form method="POST" action="<?= url('expenses/' . $ex['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar este gasto?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$expenses): ?><tr><td colspan="6" class="py-4 text-center text-stone-400">Sin gastos registrados.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mano de obra -->
    <div x-show="tab === 'mano_obra'" x-cloak x-transition.opacity.duration.150ms>
        <form method="POST" action="<?= url('labor') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-4 grid grid-cols-1 sm:grid-cols-6 gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <input type="text" name="worker_name" placeholder="Trabajador" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <select name="labor_type" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="interno">Taller propio</option>
                <option value="externo">Externo</option>
            </select>
            <input type="text" name="description" placeholder="Descripción" class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
            <input type="number" step="0.01" inputmode="decimal" name="amount" placeholder="Monto" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <input type="date" name="labor_date" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2 sm:col-span-6 sm:w-max">Registrar mano de obra</button>
        </form>

        <div class="bg-white rounded-xl border border-stone-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 px-4">Fecha</th><th class="py-2 px-4">Trabajador</th><th class="py-2 px-4">Tipo</th><th class="py-2 px-4">Monto</th><th class="py-2 px-4"></th>
                </tr></thead>
                <tbody>
                <?php foreach ($labor as $l): ?>
                    <tr class="border-b border-stone-50">
                        <td class="py-2 px-4"><?= e($l['labor_date']) ?></td>
                        <td class="py-2 px-4"><?= e($l['worker_name']) ?></td>
                        <td class="py-2 px-4 capitalize"><?= e($l['labor_type']) ?></td>
                        <td class="py-2 px-4 text-red-600"><?= money($l['amount']) ?></td>
                        <td class="py-2 px-4 text-right whitespace-nowrap">
                            <a href="<?= url('labor/' . $l['id'] . '/edit') ?>" class="text-xs text-amber-700 hover:underline mr-3">Editar</a>
                            <?php if (isAdmin()): ?>
                            <form method="POST" action="<?= url('labor/' . $l['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar este registro?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$labor): ?><tr><td colspan="5" class="py-4 text-center text-stone-400">Sin registros de mano de obra.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modificaciones / extras -->
    <div x-show="tab === 'extras'" x-cloak x-transition.opacity.duration.150ms>
        <form method="POST" action="<?= url('project-extras') ?>" class="bg-white rounded-xl border border-stone-200 p-4 mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <input type="text" name="description" placeholder="Descripción del cambio" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
            <input type="number" step="0.01" inputmode="decimal" name="amount" placeholder="Monto (+/-)" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <input type="date" name="extra_date" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2 sm:col-span-4 sm:w-max">Registrar modificación</button>
        </form>
        <p class="text-xs text-stone-400 mb-4">Usa montos positivos para cargos adicionales y negativos para descuentos.</p>

        <div class="bg-white rounded-xl border border-stone-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 px-4">Fecha</th><th class="py-2 px-4">Descripción</th><th class="py-2 px-4">Monto</th><th class="py-2 px-4"></th>
                </tr></thead>
                <tbody>
                <?php foreach ($extras as $ex): ?>
                    <tr class="border-b border-stone-50">
                        <td class="py-2 px-4"><?= e($ex['extra_date']) ?></td>
                        <td class="py-2 px-4"><?= e($ex['description']) ?></td>
                        <td class="py-2 px-4 <?= $ex['amount'] >= 0 ? 'text-emerald-600' : 'text-red-600' ?>"><?= money($ex['amount']) ?></td>
                        <td class="py-2 px-4 text-right whitespace-nowrap">
                            <a href="<?= url('project-extras/' . $ex['id'] . '/edit') ?>" class="text-xs text-amber-700 hover:underline mr-3">Editar</a>
                            <?php if (isAdmin()): ?>
                            <form method="POST" action="<?= url('project-extras/' . $ex['id'] . '/delete') ?>" class="inline" onsubmit="return confirm('¿Eliminar esta modificación?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$extras): ?><tr><td colspan="4" class="py-4 text-center text-stone-400">Sin modificaciones registradas.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Galería -->
    <div x-show="tab === 'galeria'" x-cloak x-transition.opacity.duration.150ms>
        <form method="POST" action="<?= url('project-media') ?>" enctype="multipart/form-data" class="bg-white rounded-xl border border-stone-200 p-4 mb-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <?= csrf_field() ?>
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            <input type="file" name="file" accept="image/*" required class="rounded-lg border border-stone-300 px-3 py-2 text-sm sm:col-span-2">
            <select name="media_type" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="foto">Foto</option>
                <option value="boceto">Boceto</option>
                <option value="documento">Documento</option>
            </select>
            <input type="text" name="caption" placeholder="Nota" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
            <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-lg px-4 py-2 sm:col-span-4 sm:w-max">Subir archivo</button>
        </form>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($media as $m): ?>
                <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
                    <img src="<?= url($m['file_path']) ?>" alt="<?= e($m['caption']) ?>" class="w-full h-32 object-cover">
                    <div class="flex items-center justify-between p-2">
                        <p class="text-xs text-stone-500 truncate"><?= e($m['caption'] ?: $m['media_type']) ?></p>
                        <?php if (isAdmin()): ?>
                        <form method="POST" action="<?= url('project-media/' . $m['id'] . '/delete') ?>" onsubmit="return confirm('¿Eliminar este archivo?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                            <button type="submit" class="text-xs text-red-600 hover:underline">Eliminar</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!$media): ?>
                <p class="text-sm text-stone-400 col-span-full text-center py-4">Sin evidencias cargadas.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historial de estados -->
    <div x-show="tab === 'historial'" x-cloak x-transition.opacity.duration.150ms>
        <div class="bg-white rounded-xl border border-stone-200 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-stone-400 border-b border-stone-100">
                    <th class="py-2 px-4">Fecha</th><th class="py-2 px-4">De</th><th class="py-2 px-4">A</th>
                </tr></thead>
                <tbody>
                <?php foreach ($statusHistory as $h): ?>
                    <tr class="border-b border-stone-50">
                        <td class="py-2 px-4"><?= e(date('d/m/Y H:i', strtotime($h['changed_at']))) ?></td>
                        <td class="py-2 px-4 capitalize text-stone-500"><?= e($h['old_status'] ? str_replace('_', ' ', $h['old_status']) : '—') ?></td>
                        <td class="py-2 px-4 capitalize font-medium text-stone-800"><?= e(str_replace('_', ' ', $h['new_status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$statusHistory): ?><tr><td colspan="3" class="py-4 text-center text-stone-400">Sin cambios de estado registrados todavía.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
