<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reporte — <?= e($settings['business_name']) ?></title>
<link rel="icon" href="<?= !empty($settings['logo_path']) ? url($settings['logo_path']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' rx='20' fill='%23d97706'/%3E%3Ctext x='50' y='71' font-size='58' font-family='Georgia,serif' fill='white' text-anchor='middle'%3EF%3C/text%3E%3C/svg%3E" ?>">
<link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
<style>
    @media print {
        .no-print { display: none !important; }
        body { padding: 0 !important; }
    }
</style>
</head>
<body class="bg-stone-100 text-stone-800 p-6 md:p-10">

<div class="max-w-3xl mx-auto">
    <div class="no-print flex justify-end gap-2 mb-4">
        <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg">Imprimir / Guardar como PDF</button>
        <button onclick="window.close()" class="text-sm text-stone-500 border border-stone-300 rounded-lg px-4 py-2">Cerrar</button>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 p-8">
        <?= $content ?>
    </div>
</div>

</body>
</html>
