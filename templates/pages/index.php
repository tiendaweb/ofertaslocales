<?php

declare(strict_types=1);

$homePrimaryCtaHref = ($currentUser ?? null) !== null ? '/panel' : '/register';
$homePrimaryCtaLabel = ($currentUser ?? null) !== null ? 'Ir a mi panel' : 'Registrar mi negocio';

?>
<header class="bg-gradient-to-br from-red-600 to-red-800 text-white pt-16 pb-20 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30" data-editable-key="hero_badge" data-editable-type="text">
            <?= htmlspecialchars((string) ($labels['hero_badge'] ?? '📍 Descubrí tu zona'), ENT_QUOTES, 'UTF-8') ?>
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight" data-editable-key="hero_title" data-editable-type="textarea">
            <?= htmlspecialchars((string) ($labels['hero_title'] ?? 'Ofertas cerca tuyo que te hacen ahorrar HOY'), ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="text-lg md:text-xl text-red-100 mb-8 max-w-2xl mx-auto font-light" data-editable-key="hero_description" data-editable-type="textarea">
            <?= htmlspecialchars((string) ($labels['hero_description'] ?? 'Encontrá descuentos reales, contactá directo al vendedor por WhatsApp y asegurá tu precio antes de que el reloj llegue a cero.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?= htmlspecialchars($homePrimaryCtaHref, ENT_QUOTES, 'UTF-8') ?>" class="bg-yellow-400 text-yellow-900 px-8 py-4 rounded-xl font-bold text-lg hover:bg-yellow-300 transition shadow-lg flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-5 h-5"></i>
                <span><?= htmlspecialchars($homePrimaryCtaLabel, ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        </div>
    </div>
</header>

<div class="max-w-6xl mx-auto px-4 -mt-10 relative z-10">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 flex flex-col md:flex-row justify-around gap-6 text-center border border-gray-100">
        <?php foreach ($stats as $index => $stat) : ?>
            <div class="flex flex-col items-center">
                <div class="<?= htmlspecialchars($stat['containerClass'], ENT_QUOTES, 'UTF-8') ?> p-3 rounded-full mb-3">
                    <i data-lucide="<?= htmlspecialchars($stat['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-7 h-7"></i>
                </div>
                <div class="text-3xl font-black text-gray-800"><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-sm text-gray-500 font-medium"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php if ($index < count($stats) - 1) : ?>
                <div class="hidden md:block w-px bg-gray-100"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-16">
    <section class="mb-16 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-8">¿Cómo funciona?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($howItWorks as $step) : ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="<?= htmlspecialchars($step['badgeClass'], ENT_QUOTES, 'UTF-8') ?> w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4"><?= (int) $step['step'] ?></div>
                    <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-gray-600 text-sm"><?= htmlspecialchars($step['description'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include __DIR__ . '/ofertas.php'; ?>

    <section class="mb-16">
        <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-gray-100">
            <div class="p-6 md:p-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">📍 Ofertas activas en el mapa</h2>
                        <p class="text-gray-600">Explorá y tocá puntos desde Home para ver detalles sin salir de esta pantalla.</p>
                    </div>
                    <a href="/mapa" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition md:self-start">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Ver mapa completo
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <button type="button" id="home-map-center-me" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                        <i data-lucide="locate-fixed" class="w-4 h-4"></i>
                        Centrar en mí
                    </button>
                    <button type="button" id="home-map-filter-nearby" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                        <i data-lucide="radar" class="w-4 h-4"></i>
                        Filtrar cercanas
                    </button>
                    <p id="home-map-feedback" class="text-xs md:text-sm text-gray-500">Tip: hacé clic en un marcador para abrir la oferta.</p>
                </div>
            </div>
            <div class="h-[26rem] w-full bg-gray-200 border-t border-gray-100">
                <div class="h-full w-full" id="home-map-preview"></div>
            </div>
        </div>
    </section>

    <div id="home-map-offer-modal" class="hidden fixed inset-0 z-[1100] bg-gray-950/70 backdrop-blur-sm px-4 py-6 md:p-8">
        <div class="max-w-2xl mx-auto h-full flex items-center justify-center">
            <div class="w-full bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-5 md:p-6 flex items-start justify-between gap-4 border-b border-gray-100">
                    <div>
                        <p id="home-map-modal-business" class="text-xs uppercase tracking-[0.2em] text-gray-400 font-semibold mb-1">Negocio</p>
                        <h3 id="home-map-modal-title" class="text-xl md:text-2xl font-bold text-gray-900">Oferta seleccionada</h3>
                    </div>
                    <button type="button" id="home-map-modal-close" class="rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 p-2 transition" aria-label="Cerrar modal">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-5 md:p-6 grid gap-6 md:grid-cols-[220px,1fr]">
                    <div class="rounded-2xl overflow-hidden bg-gray-100 h-44 md:h-full">
                        <img id="home-map-modal-image" src="" alt="Oferta seleccionada" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p id="home-map-modal-category" class="text-sm font-semibold text-gray-500 mb-1"></p>
                        <p id="home-map-modal-description" class="text-gray-600 leading-7 mb-4"></p>
                        <p id="home-map-modal-location" class="flex items-center gap-2 text-gray-700 text-sm mb-4">
                            <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i>
                            <span></span>
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a id="home-map-modal-whatsapp" href="#" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                Pedir por WhatsApp
                            </a>
                            <a href="/mapa" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-sm">
                                <i data-lucide="map" class="w-4 h-4"></i>
                                Ir al mapa completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
