<?php

declare(strict_types=1);

$navigationItems = [
    'inicio' => ['href' => '/', 'label' => 'Inicio'],
    'ofertas' => ['href' => '/ofertas', 'label' => 'Ofertas'],
    'negocios' => ['href' => '/negocios', 'label' => 'Negocios'],
    'mapa' => ['href' => '/mapa', 'label' => 'Mapa'],
    'login' => ['href' => '/login', 'label' => 'Ingresar'],
    'registro' => ['href' => '/register', 'label' => 'Registro'],
    'panel' => ['href' => '/panel', 'label' => 'Panel'],
    'admin' => ['href' => '/admin', 'label' => 'Admin'],
];
?>
<header class="border-b border-white/10 bg-slate-950/70 backdrop-blur sticky top-0 z-20">
    <div class="max-w-6xl mx-auto px-4 py-4 md:px-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-blue-500/15 border border-blue-400/20 flex items-center justify-center neon-ring">
                <i data-lucide="sparkles" class="w-5 h-5 text-blue-300"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.32em] text-blue-300">OfertasCerca</p>
                <h1 class="text-lg md:text-xl font-semibold text-white">Directorio público y panel privado en Slim 4</h1>
            </div>
        </div>
        <nav class="flex flex-wrap gap-2 text-sm">
            <?php foreach ($navigationItems as $routeName => $item): ?>
                <?php $isCurrent = ($currentRoute ?? '') === $routeName; ?>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    class="rounded-full px-4 py-2 transition <?= $isCurrent ? 'bg-blue-500 text-slate-950 font-semibold' : 'chip text-slate-200 hover:bg-slate-800' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>
