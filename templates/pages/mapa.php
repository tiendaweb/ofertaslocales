<?php

declare(strict_types=1);

$mapOffers = $mapOffers ?? [];
$coverageLabel = $coverageLabel ?? 'Tu zona';

?>
<div class="flex min-h-[100dvh] flex-col bg-gray-50 overflow-hidden border-b border-gray-200 shadow-2xl">
    <header class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 shrink-0 flex justify-between items-center shadow-md z-20">
        <div>
            <h1 class="font-black text-xl tracking-tight">OFERTAS CERCA</h1>
            <p class="text-xs text-red-100 opacity-80"><?= count($mapOffers) ?> marcadores en <?= htmlspecialchars($coverageLabel, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </header>

    <main class="flex-1 relative overflow-hidden p-0 md:p-4">
        <div class="h-full grid gap-0 md:gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.55fr)]">
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

                <div class="relative h-[calc(100dvh-11.5rem)] md:h-[calc(100dvh-12.5rem)] lg:h-[calc(100dvh-9rem)]">
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

            <button type="button" id="mobile-offers-toggle" aria-controls="map-mobile-sheet" aria-expanded="false" class="md:hidden absolute right-4 bottom-24 z-40 inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-3 text-xs font-bold uppercase tracking-widest text-white shadow-xl">
                <i data-lucide="list" class="w-4 h-4"></i>
                Ofertas
            </button>

            <aside id="map-mobile-sheet" aria-expanded="false" class="map-mobile-sheet map-mobile-sheet--collapsed w-full bg-white border border-gray-200 rounded-t-3xl md:rounded-3xl shadow-xl flex flex-col overflow-hidden fixed md:static inset-x-0 bottom-0 z-30 max-h-[70dvh] md:max-h-none transition-transform duration-300">
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
                        <div
                            data-map-offer-trigger="<?= (int) $offer['id'] ?>"
                            data-offer-lat="<?= htmlspecialchars((string) $offer['lat'], ENT_QUOTES, 'UTF-8') ?>"
                            data-offer-lon="<?= htmlspecialchars((string) $offer['lon'], ENT_QUOTES, 'UTF-8') ?>"
                            class="cursor-pointer"
                        >
                            <?php
                            $context = 'map';
                            $whatsappCta = 'Consultar';
                            include __DIR__ . '/../partials/offer-card.php';
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </main>
</div>

<div id="map-offer-modal" class="hidden fixed inset-0 z-[1200] bg-gray-950/70 backdrop-blur-sm px-4 py-6 md:p-8">
    <div class="max-w-xl mx-auto bg-white rounded-3xl overflow-hidden shadow-2xl max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-red-500 font-semibold mb-1">Detalle del marcador</p>
                <h2 class="text-xl font-bold text-gray-900">Oferta seleccionada</h2>
            </div>
            <button type="button" id="map-modal-close" class="rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 p-2 transition" aria-label="Cerrar modal">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="map-modal-card-container" class="p-5"></div>
    </div>
</div>

<template id="map-offer-card-template">
    <?php
    $context = 'map';
    $whatsappCta = 'Consultar por WhatsApp';
    $offer = [
        'id' => 0,
        'business_name' => '__BUSINESS__',
        'title' => '__TITLE__',
        'category' => '__CATEGORY__',
        'description' => '__DESCRIPTION__',
        'image_url' => '__IMAGE__',
        'location' => '__LOCATION__',
        'expires_label' => '__EXPIRES_LABEL__',
        'expires_at' => '__EXPIRES_AT__',
        'badge' => '__BADGE__',
        'whatsapp_url' => '__WHATSAPP_URL__',
    ];
    include __DIR__ . '/../partials/offer-card.php';
    ?>
</template>

<style>
    .map-leaflet-tooltip {
        background: transparent;
        border: 0;
        box-shadow: none;
        padding: 0;
    }

    .map-leaflet-tooltip .leaflet-tooltip-content {
        margin: 0;
    }

    .map-offer-tooltip {
        width: 180px;
        max-width: 180px;
        overflow: hidden;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #fee2e2;
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.22);
        padding: 10px;
    }

    .map-offer-tooltip__image {
        width: 100%;
        height: 104px;
        border-radius: 10px;
        object-fit: cover;
        margin-bottom: 8px;
    }

    .map-offer-tooltip__business {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        color: #111827;
        margin-bottom: 4px;
        letter-spacing: .04em;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .map-offer-tooltip__title {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        color: #dc2626;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }

    .map-offer-tooltip__location {
        font-size: 12px;
        color: #6b7280;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }

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

    .map-mobile-sheet--collapsed {
        transform: translateY(82%);
    }

    .map-mobile-sheet--expanded {
        transform: translateY(0);
    }

@media (min-width: 768px) {
        .map-mobile-sheet,
        .map-mobile-sheet--collapsed,
        .map-mobile-sheet--expanded {
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        #map-offers-list {
            max-height: 50vh;
        }
    }
</style>
