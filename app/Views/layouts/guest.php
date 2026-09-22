<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0d0a09">
<title>Iniciar sesión — <?= e($settings['business_name']) ?></title>
<link rel="icon" href="<?= !empty($settings['logo_path']) ? url($settings['logo_path']) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%230a0808'/%3E%3Ccircle cx='50' cy='50' r='46' fill='none' stroke='%23caa657' stroke-width='1.5'/%3E%3Ctext x='50' y='66' font-size='46' font-family='Georgia,serif' fill='%23caa657' text-anchor='middle'%3EF%3C/text%3E%3C/svg%3E" ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/tailwind.css') ?>">
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<style>
    body { font-family: 'Cormorant Garamond', 'Georgia', serif; }
    .font-display { font-family: 'Playfair Display', 'Georgia', serif; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: radial-gradient(circle at 50% 15%, #1c1512 0%, #0d0a09 55%, #060505 100%);">

<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="mx-auto mb-5 h-24 w-24 rounded-full flex items-center justify-center overflow-hidden"
             style="background: radial-gradient(circle at 35% 30%, #1e1712, #0a0807 75%); border: 1px solid rgba(202,166,87,0.45); box-shadow: 0 0 0 1px rgba(202,166,87,0.12), 0 8px 30px rgba(0,0,0,0.6), inset 0 0 20px rgba(202,166,87,0.05);">
            <?php if (!empty($settings['logo_path'])): ?>
                <img src="<?= url($settings['logo_path']) ?>" alt="<?= e($settings['business_name']) ?>" class="h-full w-full object-cover">
            <?php else: ?>
                <svg width="46" height="40" viewBox="0 0 48 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 16 Q5 8 11 8 Q11 4 17 4 Q17 8 24 8 Q24 4 31 4 Q31 8 37 8 Q43 8 43 16"
                          stroke="#caa657" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 16 L44 16" stroke="#caa657" stroke-width="1.3" stroke-linecap="round"/>
                    <path d="M4 16 L4 21 M44 16 L44 21" stroke="#caa657" stroke-width="1.3" stroke-linecap="round"/>
                    <path d="M8 21 L7 30 M18 21 L18 30 M30 21 L30 30 M40 21 L41 30" stroke="#caa657" stroke-width="1.3" stroke-linecap="round"/>
                </svg>
            <?php endif; ?>
        </div>
        <h1 class="font-display text-3xl tracking-wide" style="color: #ecdfc0;"><?= e($settings['business_name']) ?></h1>
        <p class="text-[11px] uppercase mt-2" style="color: #a3895a; letter-spacing: 0.25em;">Control financiero</p>
    </div>

    <?php if ($msg = flash('success')): ?>
        <div class="flash-message mb-4 rounded-lg px-4 py-3 text-sm" style="background: rgba(34,120,80,0.12); border: 1px solid rgba(34,120,80,0.35); color: #8fd3ae;"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="flash-message mb-4 rounded-lg px-4 py-3 text-sm" style="background: rgba(139,29,45,0.15); border: 1px solid rgba(139,29,45,0.4); color: #e2a3ac;"><?= e($msg) ?></div>
    <?php endif; ?>

    <?= $content ?>

    <?php if ($settings['tagline']): ?>
    <p class="text-center text-[11px] mt-7 tracking-wide font-display italic" style="color: #6b5c44;">
        <?= e($settings['tagline']) ?>
    </p>
    <?php endif; ?>
</div>

</body>
</html>
