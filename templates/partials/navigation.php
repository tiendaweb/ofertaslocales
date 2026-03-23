<?php

declare(strict_types=1);

$footerNavigationItems = [
    'inicio' => ['href' => '/', 'label' => 'Inicio', 'icon' => 'house'],
    'ofertas' => ['href' => '/ofertas', 'label' => 'Ofertas', 'icon' => 'badge-percent'],
    'negocios' => ['href' => '/negocios', 'label' => 'Negocios', 'icon' => 'store'],
    'mapa' => ['href' => '/mapa', 'label' => 'Mapa', 'icon' => 'map'],
];
?>
<nav class="pointer-events-none fixed inset-x-0 bottom-0 z-50 px-3 pb-[calc(env(safe-area-inset-bottom)+0.75rem)] md:pb-6" aria-label="Navegación principal">
    <ul class="pointer-events-auto mx-auto grid max-w-md grid-cols-4 gap-1 rounded-3xl border border-gray-200 bg-white/95 p-2 shadow-xl backdrop-blur supports-[backdrop-filter]:bg-white/80 md:flex md:max-w-2xl md:justify-center md:gap-2 md:px-3 md:py-2">
        <?php foreach ($footerNavigationItems as $routeName => $item) : ?>
            <?php $isCurrent = ($currentRoute ?? '') === $routeName; ?>
            <li class="md:flex-1 md:max-w-36">
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 text-xs font-semibold transition md:flex-row md:gap-2 md:text-sm <?= $isCurrent ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' ?>"
                    aria-current="<?= $isCurrent ? 'page' : 'false' ?>"
                >
                    <i data-lucide="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" class="h-5 w-5"></i>
                    <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
