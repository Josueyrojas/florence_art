<h2 class="text-2xl font-semibold text-stone-800 mb-6">Configuración del negocio</h2>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 text-center">
            <p class="text-sm font-medium text-stone-600 mb-4">Logo</p>

            <div class="mx-auto mb-4 h-32 w-32 rounded-full overflow-hidden border border-stone-200 flex items-center justify-center bg-stone-50">
                <?php if (!empty($settings['logo_path'])): ?>
                    <img src="<?= url($settings['logo_path']) ?>" alt="Logo actual" class="h-full w-full object-cover">
                <?php else: ?>
                    <svg width="56" height="46" viewBox="0 0 48 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 16 Q5 8 11 8 Q11 4 17 4 Q17 8 24 8 Q24 4 31 4 Q31 8 37 8 Q43 8 43 16"
                              stroke="#b45309" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 16 L44 16" stroke="#b45309" stroke-width="1.3" stroke-linecap="round"/>
                        <path d="M4 16 L4 21 M44 16 L44 21" stroke="#b45309" stroke-width="1.3" stroke-linecap="round"/>
                        <path d="M8 21 L7 30 M18 21 L18 30 M30 21 L30 30 M40 21 L41 30" stroke="#b45309" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?= url('settings/logo') ?>" enctype="multipart/form-data" class="space-y-3">
                <?= csrf_field() ?>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" required
                       class="w-full text-xs rounded-lg border border-stone-300 px-3 py-2">
                <button class="w-full bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg">Subir logo</button>
            </form>

            <?php if (!empty($settings['logo_path'])): ?>
                <form method="POST" action="<?= url('settings/logo/delete') ?>" class="mt-2"
                      onsubmit="return confirm('¿Quitar el logo y volver al emblema por defecto?');">
                    <?= csrf_field() ?>
                    <button class="w-full text-xs text-red-600 hover:underline">Quitar logo</button>
                </form>
            <?php endif; ?>

            <p class="text-xs text-stone-400 mt-4">JPG, PNG o WEBP. Máximo 5MB. Se recomienda una imagen cuadrada.</p>
        </div>
    </div>

    <div class="lg:col-span-2">
        <form method="POST" action="<?= url('settings') ?>" class="bg-white rounded-xl shadow-sm border border-stone-200 p-6 space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-stone-600 mb-1">Nombre del negocio *</label>
                <input type="text" name="business_name" required value="<?= e($settings['business_name']) ?>"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-600 mb-1">Eslogan / descripción</label>
                <input type="text" name="tagline" value="<?= e($settings['tagline']) ?>"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1">Teléfono</label>
                    <input type="text" name="phone" value="<?= e($settings['phone']) ?>"
                           class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1">WhatsApp</label>
                    <input type="text" name="whatsapp" value="<?= e($settings['whatsapp']) ?>"
                           class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1">Correo</label>
                    <input type="email" name="email" value="<?= e($settings['email']) ?>"
                           class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-600 mb-1">Sitio web</label>
                    <input type="text" name="website" value="<?= e($settings['website']) ?>"
                           class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-600 mb-1">Dirección</label>
                <input type="text" name="address" value="<?= e($settings['address']) ?>"
                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
            </div>

            <div class="pt-2">
                <button class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-5 py-2 rounded-lg">Guardar cambios</button>
            </div>
        </form>
    </div>

</div>
