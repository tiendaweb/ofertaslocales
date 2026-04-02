<?php

declare(strict_types=1);

$selectedBusiness = $selectedBusiness ?? null;
$businessFilterLabel = $selectedBusiness['business_name'] ?? null;
$offers = is_array($offers ?? null) ? $offers : [];
$totalOffers = isset($totalOffers) ? (int) $totalOffers : count($offers);
?>
<section id="ofertas" class="mx-auto max-w-6xl px-4 py-16">
    <div class="mb-8 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="flex flex-wrap items-end gap-x-5 gap-y-3">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-red-500">Descuentos activos</p>
            <h2 class="flex items-center gap-2 text-2xl font-bold md:text-3xl">
                <i data-lucide="zap" class="h-6 w-6 text-yellow-500"></i>
                Ofertas de Hoy
            </h2>
            <p id="offers-count-summary" class="text-sm font-medium text-gray-500 xl:pb-1">
                Mostrando <?= $totalOffers ?> oferta<?= $totalOffers === 1 ? '' : 's' ?> activa<?= $totalOffers === 1 ? '' : 's' ?>.
            </p>
            <?php if ($businessFilterLabel !== null) : ?>
                <p class="rounded-full border border-red-200 bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                    Filtro por negocio: <?= htmlspecialchars((string) $businessFilterLabel, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>
        </div>

        <div id="category-filters" class="hide-scrollbar flex w-full gap-2 overflow-x-auto pb-2 xl:w-auto xl:max-w-[52%]"></div>
    </div>

    <div id="offers-container" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-7 2xl:gap-8">
        <?php foreach ($offers as $offer) : ?>
            <?php
            $context = 'offers';
            $whatsappCta = 'Quiero esta oferta';
            include __DIR__ . '/../partials/offer-card.php';
            ?>
        <?php endforeach; ?>
    </div>
</section>
