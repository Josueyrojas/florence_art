<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($settings['business_name']) ?> — Control Financiero</title>
<link rel="icon" href="<?= !empty($settings['logo_path']) ? url($settings['logo_path']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%23d97706'/%3E%3Ctext x='50' y='71' font-size='58' font-family='Georgia,serif' fill='white' text-anchor='middle'%3EF%3C/text%3E%3C/svg%3E" ?>">
<link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-stone-100 text-stone-800" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-30 w-64 bg-stone-900 text-stone-100 transform transition-transform duration-200 md:translate-x-0 md:static md:inset-auto flex flex-col"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="px-6 py-5 border-b border-stone-700 flex items-center gap-3">
            <?php if (!empty($settings['logo_path'])): ?>
                <img src="<?= url($settings['logo_path']) ?>" alt="<?= e($settings['business_name']) ?>" class="h-10 w-10 rounded-full object-cover border border-stone-700 flex-shrink-0">
            <?php else: ?>
                <div class="h-10 w-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: radial-gradient(circle at 35% 30%, #2a211a, #17120e 75%); border: 1px solid rgba(202,166,87,0.4);">
                    <svg width="24" height="20" viewBox="0 0 48 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 16 Q5 8 11 8 Q11 4 17 4 Q17 8 24 8 Q24 4 31 4 Q31 8 37 8 Q43 8 43 16" stroke="#caa657" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 16 L44 16" stroke="#caa657" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M8 21 L7 30 M40 21 L41 30" stroke="#caa657" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
            <?php endif; ?>
            <div class="min-w-0">
                <h1 class="text-lg font-semibold tracking-wide truncate"><?= e($settings['business_name']) ?></h1>
                <p class="text-xs text-stone-400 truncate"><?= e($settings['tagline']) ?></p>
            </div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <?php
            $nav = [
                ['Dashboard', '', ''],
                ['Proyectos', 'projects', 'projects'],
                ['Clientes', 'clients', 'clients'],
                ['Proveedores', 'suppliers', 'suppliers'],
                ['Gastos operativos', 'operating-expenses', 'operating-expenses'],
            ];
            if (isAdmin()) {
                $nav[] = ['Configuración', 'settings', 'settings'];
            }
            $current = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            foreach ($nav as [$label, $path, $match]):
                $active = $match === '' ? ($current === '') : str_starts_with($current, $match);
            ?>
                <a href="<?= url($path) ?>"
                   class="block rounded-lg px-3 py-2 hover:bg-stone-800 <?= $active ? 'bg-stone-800 text-amber-400' : 'text-stone-200' ?>">
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="px-3 py-4 border-t border-stone-700">
            <p class="text-xs text-stone-500 px-3 mb-2 truncate"><?= e($_SESSION['user_name'] ?? '') ?></p>
            <form method="POST" action="<?= url('logout') ?>">
                <?= csrf_field() ?>
                <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm text-stone-200 hover:bg-stone-800">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 md:ml-0">
        <!-- Topbar -->
        <header class="sticky top-0 z-20 flex items-center justify-between bg-white border-b border-stone-200 px-4 py-3 md:px-8">
            <button class="md:hidden text-stone-600" @click="sidebarOpen = !sidebarOpen">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="text-sm text-stone-500">Panel de administración</span>
        </header>

        <main class="p-4 md:p-8 max-w-6xl mx-auto">
            <?php if ($msg = flash('success')): ?>
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 text-sm">
                    <?= e($msg) ?>
                </div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    <?= e($msg) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<div class="fixed inset-0 bg-black/40 z-20 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>

</body>
</html>
