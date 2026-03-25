<?php

declare(strict_types=1);

$business = is_array($business ?? null) ? $business : [];
$activeOffers = is_array($activeOffers ?? null) ? $activeOffers : [];
$socialButtons = array_values(array_filter([
    [
        'name' => 'Instagram',
        'icon' => 'instagram',
        'url' => $business['instagram_url'] ?? null,
        'container' => 'bg-rose-50 border-rose-200 text-rose-700 group-hover:bg-gradient-to-br group-hover:from-fuchsia-500 group-hover:via-rose-500 group-hover:to-violet-500 group-hover:border-transparent group-hover:text-white',
        'focus' => 'focus-visible:ring-rose-500/60',
        'aria_label' => 'Abrir Instagram del negocio',
    ],
    [
        'name' => 'Facebook',
        'icon' => 'facebook',
        'url' => $business['facebook_url'] ?? null,
        'container' => 'bg-blue-50 border-blue-200 text-blue-700 group-hover:bg-[#1877F2] group-hover:border-[#1877F2] group-hover:text-white',
        'focus' => 'focus-visible:ring-blue-500/60',
        'aria_label' => 'Abrir Facebook del negocio',
    ],
    [
        'name' => 'TikTok',
        'icon' => 'music-4',
        'url' => $business['tiktok_url'] ?? null,
        'container' => 'bg-zinc-100 border-zinc-300 text-zinc-900 group-hover:bg-zinc-900 group-hover:border-cyan-300 group-hover:text-cyan-200',
        'focus' => 'focus-visible:ring-cyan-400/70',
        'aria_label' => 'Abrir TikTok del negocio',
    ],
    [
        'name' => 'Web',
        'icon' => 'globe',
        'url' => $business['website_url'] ?? null,
        'container' => 'bg-slate-100 border-slate-300 text-slate-700 group-hover:bg-slate-700 group-hover:border-slate-800 group-hover:text-white',
        'focus' => 'focus-visible:ring-slate-500/70',
        'aria_label' => 'Abrir sitio web del negocio',
    ],
], static fn (array $social): bool => is_string($social['url']) && $social['url'] !== ''));
?>
<div class="flex justify-center bg-gray-100 min-h-screen">
    <main class="w-full max-w-3xl bg-white min-h-screen shadow-2xl flex flex-col relative">
        <a href="/negocios" class="absolute top-4 left-4 z-20 bg-black/30 backdrop-blur-md text-white p-2 rounded-full hover:bg-black/50 transition">
            <i data-lucide="chevron-left" class="w-6 h-6"></i>
        </a>

        <header class="relative h-56 shrink-0">
            <img
                src="<?= htmlspecialchars((string) (($business['cover_url'] ?? '') ?: 'https://placehold.co/1200x500/fca5a5/7f1d1d?text=Portada+Negocio'), ENT_QUOTES, 'UTF-8') ?>"
                class="w-full h-full object-cover"
                alt="Banner del negocio"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

            <div class="absolute -bottom-6 left-6 p-1 bg-white rounded-2xl shadow-lg">
                <img
                    src="<?= htmlspecialchars((string) ($business['logo_url'] ?: 'https://placehold.co/200x200/f3f4f6/1f2937?text=Logo'), ENT_QUOTES, 'UTF-8') ?>"
                    class="w-20 h-20 rounded-xl bg-gray-100 object-contain"
                    alt="Logo del negocio"
                >
            </div>
        </header>

        <section class="px-6 pt-10 pb-6">
            <div class="flex justify-between items-start mb-2 gap-3">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight"><?= htmlspecialchars((string) ($business['business_name'] ?? 'Negocio local'), ENT_QUOTES, 'UTF-8') ?></h1>
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                    <?= ($activeOffers === []) ? 'Sin ofertas' : 'Con ofertas' ?>
                </span>
            </div>

            <p class="text-gray-600 leading-relaxed mb-6 italic">
                "<?= htmlspecialchars((string) ($business['bio'] ?? 'Este negocio todavía no cargó su descripción comercial.'), ENT_QUOTES, 'UTF-8') ?>"
            </p>

            <?php if ($socialButtons !== []) : ?>
                <div class="flex gap-4 mb-8 overflow-x-auto pb-2 hide-scrollbar">
                    <?php foreach ($socialButtons as $social) : ?>
                        <a
                            href="<?= htmlspecialchars((string) $social['url'], ENT_QUOTES, 'UTF-8') ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="<?= htmlspecialchars((string) ($social['aria_label'] ?? ('Abrir ' . $social['name'] . ' del negocio')), ENT_QUOTES, 'UTF-8') ?>"
                            class="flex flex-col items-center gap-2 group rounded-2xl focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-offset-2 <?= htmlspecialchars((string) $social['focus'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <div class="w-14 h-14 rounded-2xl border flex items-center justify-center transition-all duration-200 <?= htmlspecialchars((string) $social['container'], ENT_QUOTES, 'UTF-8') ?>">
                                <i data-lucide="<?= htmlspecialchars((string) $social['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-6 h-6"></i>
                            </div>
                            <span class="text-[10px] font-bold text-gray-500 uppercase"><?= htmlspecialchars((string) $social['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="bg-gray-50 rounded-3xl p-4 flex items-center justify-between border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-2 rounded-xl shadow-sm text-red-500">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-tighter">Dirección</p>
                        <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars((string) ($business['location'] ?? 'Dirección no informada'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <a href="/mapa" class="text-red-600 p-2"><i data-lucide="navigation" class="w-5 h-5"></i></a>
            </div>
        </section>

        <section class="flex-1 bg-gray-50 rounded-t-[3rem] px-6 py-8 shadow-[0_-15px_40px_rgba(0,0,0,0.03)]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Ofertas vigentes</h2>
                <span class="text-red-600 text-xs font-bold"><?= count($activeOffers) ?> PROMOS</span>
            </div>

            <div class="space-y-4">
                <?php foreach ($activeOffers as $offer) : ?>
                    <article class="bg-white rounded-3xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-extrabold text-gray-900 leading-tight mb-1"><?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                                    <i data-lucide="timer" class="w-3.5 h-3.5 text-orange-500"></i>
                                    <span><?= htmlspecialchars((string) $offer['expires_label'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </div>
                        <a href="https://wa.me/<?= rawurlencode(preg_replace('/\D+/', '', (string) ($business['whatsapp'] ?? ''))) ?>" target="_blank" rel="noopener noreferrer" class="w-full mt-4 bg-[#25D366] text-white font-bold py-3 rounded-2xl flex items-center justify-center gap-2 hover:bg-[#20bd5a] transition-colors">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                            Pedir esta oferta
                        </a>
                    </article>
                <?php endforeach; ?>

                <?php if ($activeOffers === []) : ?>
                    <article class="bg-white rounded-3xl p-5 border border-gray-100 text-center text-gray-500">
                        Este negocio no tiene ofertas activas en este momento.
                    </article>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>
