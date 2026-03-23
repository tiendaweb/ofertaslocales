<?php

declare(strict_types=1);

?>
<section class="bg-gradient-to-br from-red-600 to-red-800 text-white pt-16 pb-20 px-4">
    <div class="max-w-6xl mx-auto grid gap-8 lg:grid-cols-[1.1fr_0.9fr] items-center">
        <div>
            <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30">
                🗺️ OpenStreetMap en vivo
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">Encontrá ofertas activas cerca tuyo y abrí cada detalle desde el mapa.</h1>
            <p class="text-lg text-red-100 mb-8 max-w-2xl">Los marcadores usan las mismas ofertas activas que ves en inicio, ofertas y negocios. Al pasar el mouse aparece la miniatura y al hacer clic se abre un panel completo con CTA directo a WhatsApp.</p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Marcadores activos</p>
                    <p class="text-3xl font-black"><?= count($mapOffers) ?></p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Cobertura visible</p>
                    <p class="text-3xl font-black"><?= htmlspecialchars($coverageLabel, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </div>
        <div class="bg-white text-gray-800 rounded-3xl p-6 md:p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-4">Interacciones disponibles</h2>
            <ul class="space-y-4">
                <li class="flex gap-3">
                    <i data-lucide="mouse-pointer-click" class="text-red-500 w-5 h-5 mt-0.5"></i>
                    <span>Click en marcador para abrir el detalle completo de la oferta.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="image" class="text-blue-500 w-5 h-5 mt-0.5"></i>
                    <span>Tooltip con miniatura, negocio y promoción para comparar rápido.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="message-circle-more" class="text-green-500 w-5 h-5 mt-0.5"></i>
                    <span>Acceso a WhatsApp desde el detalle del marcador.</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <article class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100">
            <div class="p-6 md:p-8 border-b border-gray-100">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">📍 Ofertas activas en el mapa</h2>
                <p class="text-gray-600">Desplazate, acercate y tocá cada punto para ver información completa del comercio y su promoción.</p>
            </div>
            <div id="offers-map" class="h-[28rem] w-full bg-gray-200"></div>
        </article>

        <aside class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6 md:p-8">
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.22em] text-red-500 font-semibold mb-2">Resumen rápido</p>
                    <h2 class="text-2xl font-bold text-gray-900">Negocios visibles</h2>
                </div>
                <a href="/ofertas" class="text-sm font-semibold text-red-600 hover:text-red-700 transition">Ver grilla</a>
            </div>
            <div class="space-y-4 max-h-[28rem] overflow-y-auto pr-1">
                <?php foreach ($mapOffers as $offer) : ?>
                    <button
                        type="button"
                        data-map-offer-trigger="<?= (int) $offer['id'] ?>"
                        class="w-full text-left border border-gray-100 rounded-2xl p-4 hover:border-red-200 hover:bg-red-50/40 transition"
                    >
                        <p class="text-xs uppercase tracking-[0.22em] text-gray-400 font-semibold mb-2"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></p>
                        <h3 class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($offer['business_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-red-600 font-semibold mb-3"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="space-y-2 text-sm text-gray-500">
                            <p class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i><?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="flex items-center gap-2"><i data-lucide="clock-3" class="w-4 h-4 text-yellow-500"></i><?= htmlspecialchars($offer['expires_label'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</section>

<div id="map-offer-modal" class="hidden fixed inset-0 z-[60] bg-gray-950/70 backdrop-blur-sm px-4 py-6 md:p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-red-500 font-semibold mb-1">Detalle del marcador</p>
                <h2 id="map-modal-title" class="text-xl font-bold text-gray-900">Oferta seleccionada</h2>
            </div>
            <button type="button" id="map-modal-close" class="rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 p-2 transition" aria-label="Cerrar modal">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="grid md:grid-cols-[0.95fr_1.05fr]">
            <div class="bg-gray-100 min-h-64">
                <img id="map-modal-image" src="" alt="Oferta seleccionada" class="w-full h-full object-cover">
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p id="map-modal-business" class="text-sm uppercase tracking-[0.22em] text-gray-400 font-semibold mb-2"></p>
                    <p id="map-modal-offer" class="text-2xl font-bold text-red-600 leading-tight"></p>
                    <p id="map-modal-category" class="text-sm font-semibold text-gray-500 mt-2"></p>
                </div>
                <p id="map-modal-description" class="text-gray-600 leading-7"></p>
                <div class="space-y-3 text-sm text-gray-500">
                    <p id="map-modal-location" class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i><span></span></p>
                    <p id="map-modal-expiration" class="flex items-center gap-2"><i data-lucide="clock-3" class="w-4 h-4 text-yellow-500"></i><span></span></p>
                    <p id="map-modal-countdown" class="flex items-center gap-2"><i data-lucide="timer-reset" class="w-4 h-4 text-orange-500"></i><span></span></p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <a id="map-modal-whatsapp" href="#" target="_blank" rel="noreferrer" class="flex-1 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        Consultar por WhatsApp
                    </a>
                    <a href="/ofertas" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors shadow-sm">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        Ver más ofertas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
