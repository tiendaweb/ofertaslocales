<?php

declare(strict_types=1);

$selectedBusiness = $selectedBusiness ?? null;
$businessFilterLabel = $selectedBusiness['business_name'] ?? null;
$totalOffers = isset($totalOffers) ? (int) $totalOffers : (isset($pageData['offers']) && is_array($pageData['offers']) ? count($pageData['offers']) : 0);
?>
<section class="bg-gradient-to-br from-red-600 to-red-800 text-white pt-16 pb-20 px-4">
    <div class="max-w-6xl mx-auto grid gap-8 lg:grid-cols-[1.15fr_0.85fr] items-center">
        <div>
            <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30">
                🏷️ Ofertas activas desde la base de datos
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">
                Promociones reales con vencimiento dinámico y contacto directo por WhatsApp.
            </h1>
            <p class="text-lg text-red-100 mb-8 max-w-2xl">
                Cada tarjeta refleja la misma información pública disponible en inicio, negocios y mapa: categoría, comercio,
                oferta, ubicación, imagen y tiempo restante calculado según <code class="font-semibold text-white">expires_at</code>.
            </p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Ofertas visibles</p>
                    <p class="text-3xl font-black"><?= (int) $totalOffers ?></p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Filtro de negocio</p>
                    <p class="text-lg md:text-2xl font-black">
                        <?= htmlspecialchars($businessFilterLabel ?? 'Todos', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white text-gray-800 rounded-3xl p-6 md:p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-4">Qué muestra cada tarjeta</h2>
            <ul class="space-y-4">
                <li class="flex gap-3">
                    <i data-lucide="layout-grid" class="text-red-500 w-5 h-5 mt-0.5"></i>
                    <span>Categoría, negocio, oferta y ubicación visibles sin datos hardcodeados.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="timer-reset" class="text-yellow-500 w-5 h-5 mt-0.5"></i>
                    <span>Cuenta regresiva viva basada en la fecha real de expiración de cada publicación.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="message-circle-more" class="text-green-500 w-5 h-5 mt-0.5"></i>
                    <span>CTA de WhatsApp listo para contactar al comercio desde la misma tarjeta.</span>
                </li>
            </ul>
            <?php if ($selectedBusiness !== null) : ?>
                <a href="/ofertas" class="inline-flex mt-6 items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Ver nuevamente todas las ofertas
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="ofertas" class="max-w-6xl mx-auto px-4 py-16">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-red-500 font-semibold mb-3">Descuentos activos</p>
            <h2 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
                <i data-lucide="zap" class="text-yellow-500 w-6 h-6"></i>
                Ofertas de Hoy
            </h2>
        </div>

        <div id="category-filters" class="flex overflow-x-auto pb-2 w-full md:w-auto hide-scrollbar gap-2"></div>
    </div>

    <div id="offers-container"></div>
</section>
