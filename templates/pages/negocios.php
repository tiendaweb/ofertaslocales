<?php

declare(strict_types=1);

?>
<section class="bg-gradient-to-br from-red-600 to-red-800 text-white pt-16 pb-20 px-4">
    <div class="max-w-6xl mx-auto grid gap-8 lg:grid-cols-[1.1fr_0.9fr] items-center">
        <div>
            <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30">
                🏪 Comercios locales
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">Negocios registrados con ofertas listas para activarse en tu zona.</h1>
            <p class="text-lg text-red-100 mb-8 max-w-2xl">Explorá comercios de barrio, revisá cuántas promociones tienen activas y elegí con quién contactar primero.</p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Negocios visibles</p>
                    <p class="text-3xl font-black"><?= (int) $summary['totalBusinesses'] ?></p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Ofertas activas</p>
                    <p class="text-3xl font-black"><?= (int) $summary['activeOffers'] ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white text-gray-800 rounded-3xl p-6 md:p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-4">¿Qué encontrás en esta vista?</h2>
            <ul class="space-y-4">
                <li class="flex gap-3">
                    <i data-lucide="badge-check" class="text-green-500 w-5 h-5 mt-0.5"></i>
                    <span>Comercios organizados por cantidad de ofertas activas.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="clock-3" class="text-red-500 w-5 h-5 mt-0.5"></i>
                    <span>Próximo vencimiento visible para priorizar contactos rápidos.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="map-pinned" class="text-blue-500 w-5 h-5 mt-0.5"></i>
                    <span>Acceso directo al mapa para pasar de la lista al recorrido.</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($businesses as $business) : ?>
            <article class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 flex flex-col gap-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-gray-400 font-semibold mb-2"><?= htmlspecialchars($business['role'], ENT_QUOTES, 'UTF-8') ?></p>
                        <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($business['business_name'] ?: 'Sin nombre comercial', ENT_QUOTES, 'UTF-8') ?></h3>
                    </div>
                    <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full"><?= (int) $business['active_offers'] ?> activas</span>
                </div>
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-sm text-gray-600 gap-3">
                        <span class="flex items-center gap-2"><i data-lucide="store" class="w-4 h-4 text-red-500"></i> Estado visible</span>
                        <span class="font-semibold text-gray-900"><?= (int) $business['active_offers'] > 0 ? 'Con promociones' : 'Sin promociones' ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600 gap-3">
                        <span class="flex items-center gap-2"><i data-lucide="timer" class="w-4 h-4 text-yellow-500"></i> Próximo cierre</span>
                        <span class="font-semibold text-gray-900"><?= htmlspecialchars($business['next_expiration_label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <div class="mt-auto flex gap-3">
                    <a href="/ofertas" class="flex-1 bg-gray-900 text-white rounded-xl px-4 py-3 text-center font-semibold hover:bg-gray-800 transition">Ver ofertas</a>
                    <a href="/mapa" class="flex-1 bg-red-50 text-red-600 rounded-xl px-4 py-3 text-center font-semibold hover:bg-red-100 transition">Ir al mapa</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
