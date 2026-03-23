<?php

declare(strict_types=1);

$footerNavigationItems = [
    'inicio' => ['href' => '/', 'label' => 'Inicio', 'icon' => 'house'],
    'ofertas' => ['href' => '/ofertas', 'label' => 'Ofertas', 'icon' => 'badge-percent'],
    'negocios' => ['href' => '/negocios', 'label' => 'Negocios', 'icon' => 'store'],
    'mapa' => ['href' => '/mapa', 'label' => 'Mapa', 'icon' => 'map'],
];
?>
<nav class="md:hidden fixed inset-x-0 bottom-0 z-50 border-t border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <ul class="grid grid-cols-4 gap-1 px-2 py-2">
        <?php foreach ($footerNavigationItems as $routeName => $item) : ?>
            <?php $isCurrent = ($currentRoute ?? '') === $routeName; ?>
            <li>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    class="flex flex-col items-center justify-center gap-1 rounded-2xl px-2 py-2 text-xs font-semibold transition <?= $isCurrent ? 'bg-red-50 text-red-600' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800' ?>"
                    aria-current="<?= $isCurrent ? 'page' : 'false' ?>"
                >
                    <i data-lucide="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-5 h-5"></i>
                    <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
