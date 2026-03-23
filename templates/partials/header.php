<?php
declare(strict_types=1);

$navigationItems = [
    'inicio' => ['href' => '/', 'label' => 'Inicio', 'icon' => 'house'],
    'ofertas' => ['href' => '/ofertas', 'label' => 'Ofertas', 'icon' => 'badge-percent'],
    'negocios' => ['href' => '/negocios', 'label' => 'Negocios', 'icon' => 'store'],
    'mapa' => ['href' => '/mapa', 'label' => 'Mapa', 'icon' => 'map'],
];

$isPublicRoute = in_array($currentRoute ?? '', ['inicio', 'ofertas', 'negocios', 'mapa'], true);
$isAdminRoute = ($currentRoute ?? '') === 'admin';
$publishHref = ($currentRoute ?? '') === 'inicio' ? '#publicar' : '/#publicar';
$currentUser = $currentUser ?? null;
$isImpersonating = isset($_SESSION['auth']['impersonator_id']);
?>

<?php if ($isPublicRoute) : ?>
    <nav class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-100 px-4 py-3">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-8">
                <a href="/" class="flex items-center gap-2 text-red-600 font-bold text-xl tracking-tight">
                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                    <span>OfertasCerca</span>
                </a>

                <div class="hidden lg:flex items-center gap-6 text-sm font-medium text-gray-600">
                    <?php foreach ($navigationItems as $key => $item) : ?>
                        <a href="<?= $item['href'] ?>" class="hover:text-red-600 transition-colors <?= ($currentRoute ?? '') === $key ? 'text-red-600' : '' ?>">
                            <?= $item['label'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center gap-3">

                <?php if ($currentUser !== null && ($currentUser['role'] ?? '') === 'admin') : ?>
                    <button
                        type="button"
                        id="inline-edit-toggle"
                        data-inline-edit-toggle
                        class="hidden sm:inline-flex bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-indigo-100 transition"
                    >
                        Activar Modo Edición
                    </button>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($publishHref, ENT_QUOTES, 'UTF-8') ?>"
                   class="hidden sm:inline-flex bg-red-50 text-red-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-red-100 transition">
                    Publicar Gratis
                </a>
                
                <?php if ($currentUser !== null) : ?>
                    <a href="<?= ($currentUser['role'] === 'admin') ? '/admin' : '/panel' ?>"
                       class="bg-gray-900 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-800 transition">
                        Mi panel
                    </a>
                <?php else : ?>
                    <div class="flex items-center gap-2">
                        <a href="/login" class="text-sm font-semibold text-gray-700 hover:text-gray-900 px-3">Ingresar</a>
                        <a href="/register" class="bg-gray-900 text-white px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-800 transition">Registro</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<?php else : ?>
    <header class="sticky top-0 z-20 border-b border-gray-100 bg-white/95 backdrop-blur-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 md:px-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center transition-colors <?= $isAdminRoute ? 'bg-red-50 border border-red-100' : 'bg-gray-50 border border-gray-100' ?>">
                    <i data-lucide="<?= $isAdminRoute ? 'shield-check' : 'layout-dashboard' ?>" 
                       class="w-5 h-5 <?= $isAdminRoute ? 'text-red-500' : 'text-gray-500' ?>"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.25em] font-bold <?= $isAdminRoute ? 'text-red-500' : 'text-gray-400' ?>">
                        OfertasCerca
                    </p>
                    <h1 class="text-base md:text-lg font-bold text-gray-900">
                        <?= $currentUser && ($currentUser['role'] === 'admin') ? 'Administración' : 'Panel de Control' ?>
                    </h1>
                </div>
            </div>

            <div class="flex flex-col gap-3 md:items-end">
                <nav class="flex flex-wrap gap-1.5 text-sm">
                    <?php foreach ($navigationItems as $key => $item) : ?>
                        <?php $isActive = ($currentRoute ?? '') === $key; ?>
                        <a href="<?= $item['href'] ?>" 
                           class="rounded-full px-4 py-1.5 font-medium transition-all <?= $isActive 
                                ? 'bg-red-600 text-white shadow-md shadow-red-600/20' 
                                : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900' ?>">
                            <?= $item['label'] ?>
                        </a>
                    <?php endforeach; ?>

                    <?php if (($currentUser['role'] ?? null) === 'business' && isset($currentUser['id'])) : ?>
                        <a href="/negocios/<?= (int) $currentUser['id'] ?>" class="rounded-full bg-red-50 text-red-700 px-4 py-1.5 font-semibold hover:bg-red-100 transition-all">
                            Mi Negocio
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($currentUser === null) : ?>
                        <a href="/login" class="rounded-full bg-gray-900 text-white px-4 py-1.5 hover:bg-gray-800 transition shadow-sm">
                            Ingresar
                        </a>
                    <?php endif; ?>
                </nav>

                <?php if ($currentUser !== null) : ?>
                    <div class="flex items-center gap-3 text-xs">
                        <?php if ($isImpersonating) : ?>
                            <form action="/impersonation/stop" method="post">
                                <button type="submit" class="group flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-amber-700 hover:bg-amber-100 transition">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    <span class="font-bold">Salir de impersonación</span>
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-100">
                            <span class="text-gray-400">Usuario:</span>
                            <span class="font-bold text-gray-700">
                                <?= htmlspecialchars((string) ($currentUser['business_name'] ?? $currentUser['email'])) ?>
                            </span>
                        </div>

                        <form action="/logout" method="post">
                            <button type="submit" class="font-bold text-red-600 hover:text-red-700 hover:underline px-2 transition">
                                Salir
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>
