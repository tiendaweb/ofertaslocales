<?php

declare(strict_types=1);

$offer = is_array($offer ?? null) ? $offer : [];
$context = (string) ($context ?? 'list');
$businessName = (string) ($offer['business_name'] ?? 'Negocio local');
$title = (string) ($offer['title'] ?? 'Oferta disponible');
$category = (string) ($offer['category'] ?? 'General');
$description = (string) ($offer['description'] ?? '');
$imageUrl = trim((string) ($offer['image_url'] ?? ''));
$location = (string) ($offer['location'] ?? 'Ubicación no informada');
$expiresAt = (string) ($offer['expires_at'] ?? '');
$expiresLabel = (string) ($offer['expires_label'] ?? 'Vigente por tiempo limitado');
$badge = (string) ($offer['badge'] ?? 'Oferta');
$whatsappUrl = trim((string) ($offer['whatsapp_url'] ?? ''));
$whatsappCta = (string) ($whatsappCta ?? 'Consultar por WhatsApp');

$resolvedWhatsapp = $whatsappUrl;
if ($resolvedWhatsapp === '') {
    $sanitizedWhatsapp = preg_replace('/\D+/', '', (string) ($offer['whatsapp'] ?? ''));
    if (is_string($sanitizedWhatsapp) && $sanitizedWhatsapp !== '') {
        $resolvedWhatsapp = 'https://wa.me/' . $sanitizedWhatsapp;
    }
}

$whatsappMessage = urlencode("Hola! Me interesa la oferta: {$title}");
$whatsappHref = $resolvedWhatsapp !== '' ? sprintf('%s?text=%s', $resolvedWhatsapp, $whatsappMessage) : '#';
$placeholderImage = 'https://placehold.co/800x500/fee2e2/dc2626?text=Oferta';
?>
<article class="group overflow-hidden rounded-2xl border border-red-100 bg-white shadow-sm transition hover:shadow-lg" data-offer-card data-offer-id="<?= (int) ($offer['id'] ?? 0) ?>" data-category="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>">
    <div class="relative h-44 overflow-hidden bg-red-50">
        <img
            src="<?= htmlspecialchars($imageUrl !== '' ? $imageUrl : $placeholderImage, ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
        >
        <span class="absolute left-3 top-3 rounded-full bg-red-600 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white shadow" data-offer-badge>
            <?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>

    <div class="space-y-3 p-4">
        <div class="flex items-center justify-between gap-2">
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400" data-offer-category><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="rounded-full bg-red-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-wider text-red-600" data-offer-business><?= htmlspecialchars($businessName, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <h3 class="line-clamp-2 text-lg font-extrabold leading-tight text-gray-900" data-offer-title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if ($description !== '') : ?>
            <p class="line-clamp-2 text-sm text-gray-600" data-offer-description><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-xs text-gray-600">
            <p class="mb-1 flex items-center gap-2"><i data-lucide="map-pin" class="h-3.5 w-3.5 text-red-500"></i><span class="line-clamp-1" data-offer-location><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></span></p>
            <p class="mb-1 flex items-center gap-2"><i data-lucide="clock-3" class="h-3.5 w-3.5 text-amber-500"></i><span data-offer-expiration-label><?= htmlspecialchars($expiresLabel, ENT_QUOTES, 'UTF-8') ?></span></p>
            <p class="flex items-center gap-2 font-semibold text-orange-600" data-countdown data-expiration="<?= htmlspecialchars($expiresAt, ENT_QUOTES, 'UTF-8') ?>">
                <i data-lucide="timer-reset" class="h-3.5 w-3.5"></i>
                <span>Restan --:--:--</span>
            </p>
        </div>

        <div class="flex gap-2">
            <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noreferrer" class="flex-1 rounded-xl bg-[#25D366] px-3 py-2.5 text-center text-sm font-bold text-white transition hover:bg-[#20bd5a] <?= $resolvedWhatsapp === '' ? 'pointer-events-none opacity-60' : '' ?>" data-offer-whatsapp>
                <?= htmlspecialchars($whatsappCta, ENT_QUOTES, 'UTF-8') ?>
            </a>
            <?php if ($context !== 'map') : ?>
                <a href="/mapa?oferta=<?= (int) ($offer['id'] ?? 0) ?>" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Mapa
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
