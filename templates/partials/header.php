<?php

declare(strict_types=1);

$publicNavigationItems = [
    'inicio' => ['href' => '/', 'label' => 'Inicio', 'icon' => 'house'],
    'ofertas' => ['href' => '/ofertas', 'label' => 'Ofertas', 'icon' => 'badge-percent'],
    'negocios' => ['href' => '/negocios', 'label' => 'Negocios', 'icon' => 'store'],
    'mapa' => ['href' => '/mapa', 'label' => 'Mapa', 'icon' => 'map'],
];

$privateNavigationItems = $publicNavigationItems + [
    'login' => ['href' => '/login', 'label' => 'Ingresar', 'icon' => 'log-in'],
    'registro' => ['href' => '/register', 'label' => 'Registro', 'icon' => 'user-plus'],
    'panel' => ['href' => '/panel', 'label' => 'Mi panel', 'icon' => 'layout-dashboard'],
    'admin' => ['href' => '/admin', 'label' => 'Admin', 'icon' => 'shield-check'],
];

$isPublicRoute = in_array($currentRoute ?? '', array_keys($publicNavigationItems), true);
$isAdminRoute = ($currentRoute ?? '') === 'admin';
$publishHref = ($currentRoute ?? '') === 'inicio' ? '#publicar' : '/#publicar';
$currentUser = $currentUser ?? null;
$isImpersonating = isset($_SESSION['auth']['impersonator_id']);
?>
<?php if ($isPublicRoute) : ?>
    <nav class="sticky top-0 z-50 bg-white shadow-sm px-4 py-3 flex justify-between items-center">
        <a href="/" class="flex items-center gap-2 text-red-600 font-bold text-xl tracking-tight">
            <i data-lucide="map-pin" class="w-6 h-6"></i>
            <span>OfertasCerca</span>
        </a>
        <div class="flex items-center gap-2">
            <a
                href="<?= htmlspecialchars($publishHref, ENT_QUOTES, 'UTF-8') ?>"
                class="hidden sm:inline-flex bg-red-50 text-red-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-red-100 transition"
            >
                Publicar Gratis
            </a>
            <?php if ($currentUser !== null) : ?>
                <a
                    href="<?= htmlspecialchars($currentUser['role'] === 'admin' ? '/admin' : '/panel', ENT_QUOTES, 'UTF-8') ?>"
                    class="hidden md:inline-flex bg-gray-900 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-800 transition"
                >
                    Mi panel
                </a>
            <?php else : ?>
                <a
                    href="/login"
                    class="hidden md:inline-flex bg-gray-900 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-800 transition"
                >
                    Ingresar
                </a>
            <?php endif; ?>
        </div>
    </nav>
<?php else : ?>
    <header class="sticky top-0 z-20 border-b <?= $isAdminRoute ? 'border-red-100 bg-white/95' : 'border-white/10 bg-slate-950/70' ?> backdrop-blur">
        <div class="max-w-6xl mx-auto px-4 py-4 md:px-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-2xl <?= $isAdminRoute ? 'bg-red-50 border border-red-200' : 'bg-blue-500/15 border border-blue-400/20 neon-ring' ?> flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-5 h-5 <?= $isAdminRoute ? 'text-red-500' : 'text-blue-300' ?>"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.32em] <?= $isAdminRoute ? 'text-red-500' : 'text-blue-300' ?>">OfertasCerca</p>
                    <h1 class="text-lg md:text-xl font-semibold <?= $isAdminRoute ? 'text-gray-900' : 'text-white' ?>">
                        <?= $currentUser !== null && ($currentUser['role'] ?? '') === 'admin'
                            ? 'Centro de control administrativo'
                            : 'Directorio público y panel privado en Slim 4' ?>
                    </h1>
                </div>
            </div>
            <div class="flex flex-col gap-3 md:items-end">
                <?php if (!$isAdminRoute) : ?>
                    <nav class="flex flex-wrap gap-2 text-sm">
                        <?php foreach ($privateNavigationItems as $routeName => $item) : ?>
                            <?php
                            if ($routeName === 'admin' && (($currentUser['role'] ?? null) !== 'admin')) {
                                continue;
                            }
                            if ($routeName === 'panel' && !in_array(($currentUser['role'] ?? null), ['business', 'user'], true)) {
                                continue;
                            }
                            if (in_array($routeName, ['login', 'registro'], true) && $currentUser !== null) {
                                continue;
                            }
                            $isCurrent = ($currentRoute ?? '') === $routeName;
                            ?>
                            <a
                                href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                                class="rounded-full px-4 py-2 transition <?= $isCurrent ? 'bg-blue-500 text-slate-950 font-semibold' : 'chip text-slate-200 hover:bg-slate-800' ?>"
                            >
                                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
                <?php if ($currentUser !== null) : ?>
                    <div class="flex items-center gap-3 text-sm <?= $isAdminRoute ? 'text-gray-600' : 'text-slate-300' ?>">
                        <?php if ($isImpersonating) : ?>
                            <form action="/impersonation/stop" method="post">
                                <button type="submit" class="rounded-full border <?= $isAdminRoute ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100' : 'border-amber-400/30 bg-amber-500/20 text-amber-200 hover:bg-amber-500/30' ?> px-4 py-2 transition">
                                    Volver a admin
                                </button>
                            </form>
                        <?php endif; ?>
                        <span>
                            Sesión: <strong class="<?= $isAdminRoute ? 'text-gray-900' : 'text-white' ?>"><?= htmlspecialchars((string) ($currentUser['business_name'] ?? $currentUser['email']), ENT_QUOTES, 'UTF-8') ?></strong>
                        </span>
                        <form action="/logout" method="post">
                            <button type="submit" class="rounded-full border px-4 py-2 transition <?= $isAdminRoute ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-white/10 text-slate-200 hover:bg-slate-800' ?>">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>
