<?php
/**
 * Espera: $page, $perPage, $totalCount, $basePath, $queryParams (filtros actuales sin 'page').
 */
$totalPages = max(1, (int) ceil($totalCount / $perPage));
if ($totalPages <= 1) {
    return;
}
?>
<div class="flex items-center justify-between mt-4 text-sm text-stone-500">
    <span><?= $totalCount ?> registro<?= $totalCount === 1 ? '' : 's' ?> · página <?= $page ?> de <?= $totalPages ?></span>
    <div class="flex gap-2">
        <?php if ($page > 1): ?>
            <a href="<?= url($basePath . '?' . http_build_query(array_merge($queryParams, ['page' => $page - 1]))) ?>"
               class="px-3 py-1.5 rounded-lg border border-stone-300 hover:bg-stone-50">Anterior</a>
        <?php endif; ?>
        <?php if ($page < $totalPages): ?>
            <a href="<?= url($basePath . '?' . http_build_query(array_merge($queryParams, ['page' => $page + 1]))) ?>"
               class="px-3 py-1.5 rounded-lg border border-stone-300 hover:bg-stone-50">Siguiente</a>
        <?php endif; ?>
    </div>
</div>
