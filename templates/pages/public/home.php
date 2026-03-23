<?php

declare(strict_types=1);
?>
<section class="grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
    <article class="glass neon-ring rounded-3xl p-8">
        <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Inicio</p>
        <h2 class="text-4xl font-semibold tracking-tight text-white mb-4">Ofertas locales activas, negocios visibles y una única entrada en Slim.</h2>
        <p class="text-slate-300 leading-7 max-w-3xl">
            Esta base reemplaza la antigua demo de tareas y organiza la aplicación en capas públicas, de autenticación y administrativas.
            El catálogo ya se alimenta desde SQLite usando el contenedor de dependencias de Slim.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
            <span class="chip rounded-full px-4 py-2">Negocios activos: <?= (int) $businessCount ?></span>
            <span class="chip rounded-full px-4 py-2">Rutas base listas</span>
            <span class="chip rounded-full px-4 py-2">Textos visibles en español</span>
        </div>
    </article>
    <aside class="glass rounded-3xl p-8">
        <p class="text-sm uppercase tracking-[0.28em] text-slate-400 mb-2">Accesos</p>
        <ul class="space-y-3 text-slate-200">
            <li class="flex items-center justify-between"><span>Explorar ofertas</span><a href="/ofertas" class="text-blue-300">/ofertas</a></li>
            <li class="flex items-center justify-between"><span>Ver negocios</span><a href="/negocios" class="text-blue-300">/negocios</a></li>
            <li class="flex items-center justify-between"><span>Abrir mapa</span><a href="/mapa" class="text-blue-300">/mapa</a></li>
            <li class="flex items-center justify-between"><span>Ingresar</span><a href="/login" class="text-blue-300">/login</a></li>
        </ul>
    </aside>
</section>

<section class="mt-8 grid gap-4 md:grid-cols-3">
    <?php foreach ($featuredOffers as $offer): ?>
        <article class="glass rounded-3xl overflow-hidden">
            <img src="<?= htmlspecialchars($offer['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?>" class="h-48 w-full object-cover">
            <div class="p-5 space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <span class="chip rounded-full px-3 py-1 text-xs uppercase tracking-[0.22em]"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-xs text-slate-400">Hasta <?= htmlspecialchars($offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <h3 class="text-xl font-semibold text-white"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="text-slate-300 text-sm leading-6"><?= htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="text-sm text-slate-400">
                    <p><?= htmlspecialchars($offer['business_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>
