<?php

declare(strict_types=1);

$selectedBusiness = $selectedBusiness ?? null;
$businessFilterLabel = $selectedBusiness['business_name'] ?? null;
$totalOffers = isset($totalOffers) ? (int) $totalOffers : (isset($pageData['offers']) && is_array($pageData['offers']) ? count($pageData['offers']) : 0);
?>
<section id="ofertas" class="max-w-6xl mx-auto px-4 py-16">
    <div class="flex flex-col gap-4 mb-8 xl:flex-row xl:items-end xl:justify-between">
        <div class="flex flex-wrap items-end gap-x-5 gap-y-3">
            <p class="text-sm uppercase tracking-[0.28em] text-red-500 font-semibold">Descuentos activos</p>
            <h2 class="text-2xl md:text-3xl font-bold flex items-center gap-2">
                <i data-lucide="zap" class="text-yellow-500 w-6 h-6"></i>
                Ofertas de Hoy
            </h2>
            <p id="offers-count-summary" class="text-sm font-medium text-gray-500 xl:pb-1">
                Mostrando <?= $totalOffers ?> oferta<?= $totalOffers === 1 ? '' : 's' ?> activa<?= $totalOffers === 1 ? '' : 's' ?>.
            </p>
        </div>

        <div id="category-filters" class="flex overflow-x-auto pb-2 w-full xl:w-auto hide-scrollbar gap-2 xl:max-w-[52%]"></div>
    </div>

    <div id="offers-container"></div>
</section>
