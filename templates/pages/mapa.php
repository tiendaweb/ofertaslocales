<?php

declare(strict_types=1);

$mapOffers = $mapOffers ?? [];
$coverageLabel = $coverageLabel ?? 'Tu zona';

?>
<div class="flex flex-col h-screen max-h-[980px] bg-gray-50 overflow-hidden border-b border-gray-200 shadow-2xl">
    <header class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 shrink-0 flex justify-between items-center shadow-md z-20">
        <div>
            <h1 class="font-black text-xl tracking-tight">OFERTAS CERCA</h1>
            <p class="text-xs text-red-100 opacity-80"><?= count($mapOffers) ?> marcadores en <?= htmlspecialchars($coverageLabel, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </header>

    <main class="flex-1 relative overflow-hidden p-3 md:p-4">
        <div class="h-full grid gap-3 md:gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.55fr)]">
            <article class="relative bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-100 bg-white/90 backdrop-blur-md relative z-30">
                    <div class="grid gap-2 md:grid-cols-[1fr_auto]">
                        <label for="map-location-search" class="sr-only">Buscar ubicación</label>
                        <input
                            id="map-location-search"
                            type="search"
                            placeholder="Buscar ubicación (ej: Palermo, CABA)"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-300"
                        >
                        <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
                            <button
                                type="button"
                                id="map-location-search-button"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 sm:text-sm"
                            >
                                <i data-lucide="search" class="h-4 w-4"></i>
                                <span data-button-label data-label-default="Buscar" data-label-busy="Buscando...">Buscar</span>
                            </button>
                            <button
                                type="button"
                                id="map-use-my-location"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-700 sm:text-sm"
                            >
                                <i data-lucide="navigation" class="h-4 w-4"></i>
                                <span data-button-label data-label-default="Mi ubicación" data-label-busy="Ubicando...">Mi ubicación</span>
                            </button>
                            <button
                                type="button"
                                id="map-center-user"
                                class="hidden inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 sm:text-sm"
                            >
                                <i data-lucide="locate-fixed" class="h-4 w-4"></i>
                                <span>Centrar</span>
                            </button>
                            <button
                                type="button"
                                id="map-clear-search"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 sm:text-sm"
                            >
                                <i data-lucide="eraser" class="h-4 w-4"></i>
                                <span>Limpiar</span>
                            </button>
                            <a href="/ofertas" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 sm:text-sm">
                                <i data-lucide="layout-grid" class="h-4 w-4"></i>
                                <span>Ver ofertas</span>
                            </a>
                        </div>
                    </div>
                    <p id="map-location-feedback" class="min-h-5 mt-2 text-xs text-gray-500"></p>
                </div>

                <div class="relative h-[55vh] md:h-[70vh]">
                    <div id="offers-map" class="absolute inset-0 bg-gray-200"></div>

                    <div class="hidden md:block absolute bottom-5 left-5 max-w-sm bg-white/90 backdrop-blur-md p-4 rounded-3xl shadow-2xl border border-white/20 z-20">
                        <div class="flex gap-3 items-center">
                            <div class="bg-red-100 p-2 rounded-xl text-red-600">
                                <i data-lucide="mouse-pointer-click" class="w-5 h-5"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700">Tocá los marcadores para ver detalles y contactar por WhatsApp.</p>
                        </div>
                    </div>
                </div>
            </article>

            <aside class="w-full bg-white border border-gray-200 rounded-3xl shadow-xl flex flex-col overflow-hidden">
                <div class="md:hidden w-12 h-1.5 bg-gray-300 rounded-full mx-auto my-3"></div>

                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">Negocios Activos</h2>
                    <span class="text-xs font-bold text-red-600 uppercase tracking-widest"><?= count($mapOffers) ?> visibles</span>
                </div>

                <div class="flex-1 overflow-y-auto px-4 pb-6 pt-4 space-y-3 custom-scrollbar" id="map-offers-list">
                    <?php if (empty($mapOffers)): ?>
                        <div class="py-10 text-center">
                            <i data-lucide="map-pin-off" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-gray-500">No hay ofertas en esta zona</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($mapOffers as $offer) : ?>
                        <button
                            type="button"
                            data-map-offer-trigger="<?= (int) $offer['id'] ?>"
                            data-offer-lat="<?= htmlspecialchars((string) $offer['lat'], ENT_QUOTES, 'UTF-8') ?>"
                            data-offer-lon="<?= htmlspecialchars((string) $offer['lon'], ENT_QUOTES, 'UTF-8') ?>"
                            class="group w-full text-left bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:border-red-500 hover:shadow-md transition-all duration-300"
                        >
                            <div class="flex justify-between items-start mb-2 gap-2">
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-tighter"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                <div class="flex items-center gap-1.5">
                                    <span data-offer-distance-badge class="hidden text-[10px] font-semibold rounded-full bg-red-50 text-red-700 px-2 py-0.5 whitespace-nowrap"></span>
                                    <span class="bg-red-50 text-red-600 text-[10px] px-2 py-0.5 rounded-full font-bold italic">¡OFERTA!</span>
                                </div>
                            </div>
                            <h3 class="font-extrabold text-gray-900 group-hover:text-red-700 transition-colors uppercase leading-tight mb-1"><?= htmlspecialchars($offer['business_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="text-red-600 font-bold text-lg leading-snug mb-3"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></p>

                            <div class="space-y-2 text-xs text-gray-500 pt-2 border-t border-gray-50">
                                <p class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-red-500"></i>
                                    <span class="truncate"><?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></span>
                                </p>
                                <p class="flex items-center gap-1.5">
                                    <i data-lucide="clock-3" class="w-3.5 h-3.5 text-yellow-500"></i>
                                    <span><?= htmlspecialchars($offer['expires_label'], ENT_QUOTES, 'UTF-8') ?></span>
                                </p>
                                <p class="flex items-center gap-1.5 text-orange-600 font-semibold" data-map-countdown data-expiration="<?= htmlspecialchars($offer['expires_at'], ENT_QUOTES, 'UTF-8') ?>">
                                    <i data-lucide="timer-reset" class="w-3.5 h-3.5"></i>
                                    <span>Restan --:--:--</span>
                                </p>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </main>
</div>

<div id="map-offer-modal" class="hidden fixed inset-0 z-[1200] bg-gray-950/70 backdrop-blur-sm px-4 py-6 md:p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl max-h-[calc(100vh-2rem)] overflow-y-auto">
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

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        #map-offers-list {
            max-height: 45vh;
        }
    }
</style>
