<?php
declare(strict_types=1);

// Validación de datos (Manteniendo tu lógica original)
$business = is_array($business ?? null) ? $business : [];
$activeOffers = is_array($activeOffers ?? null) ? $activeOffers : [];

// Configuración de Redes Sociales (Unificadas en Rojo/Blanco)
$socialButtons = array_values(array_filter([
    ['name' => 'Instagram', 'icon' => 'instagram', 'url' => $business['instagram_url'] ?? null],
    ['name' => 'Facebook', 'icon' => 'facebook', 'url' => $business['facebook_url'] ?? null],
    ['name' => 'TikTok', 'icon' => 'music-2', 'url' => $business['tiktok_url'] ?? null],
    ['name' => 'Web', 'icon' => 'globe', 'url' => $business['website_url'] ?? null],
], static fn (array $social): bool => is_string($social['url']) && $social['url'] !== ''));

$whatsappNumber = preg_replace('/\D+/', '', (string) ($business['whatsapp'] ?? ''));
$businessName = htmlspecialchars((string) ($business['business_name'] ?? 'Negocio Local'), ENT_QUOTES, 'UTF-8');
$businessType = (string) ($business['business_type'] ?? 'comercio');
$typeConfig = match($businessType) {
    'emprendedor' => ['label' => 'Emprendedor', 'icon' => 'lightbulb', 'color' => 'bg-amber-100 text-amber-700', 'sectionLabel' => 'Productos & Ofertas'],
    'servicio'    => ['label' => 'Servicio Profesional', 'icon' => 'wrench', 'color' => 'bg-blue-100 text-blue-700', 'sectionLabel' => 'Servicios Disponibles'],
    default       => ['label' => 'Comercio Local', 'icon' => 'store', 'color' => 'bg-red-100 text-red-700', 'sectionLabel' => 'Promociones Disponibles'],
};
?>

<div class="bg-slate-50 min-h-screen font-sans antialiased text-slate-900">
    <main class="max-w-2xl mx-auto bg-white min-h-screen shadow-2xl relative pb-20">
        
        <header class="relative h-56 sm:h-64 bg-slate-200">
            <img 
                src="<?= htmlspecialchars((string) (($business['cover_url'] ?? '') ?: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1200'), ENT_QUOTES, 'UTF-8') ?>" 
                class="w-full h-full object-cover"
                alt="Banner de <?= $businessName ?>"
            >
            <a href="/negocios" class="absolute top-4 left-4 p-2.5 bg-white/90 backdrop-blur-md rounded-full shadow-lg text-red-600 hover:bg-red-600 hover:text-white transition-all z-10">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
        </header>

        <section class="px-6 py-8">
            <div class="flex items-start gap-5">
                <div class="relative shrink-0">
                    <div class="p-1 bg-white rounded-[2rem] shadow-xl border border-slate-100">
                        <img 
                            src="<?= htmlspecialchars((string) ($business['logo_url'] ?: 'https://placehold.co/200x200/fee2e2/dc2626?text=' . urlencode($businessName)), ENT_QUOTES, 'UTF-8') ?>" 
                            class="w-24 h-24 rounded-[1.8rem] object-cover bg-white"
                            alt="Logo <?= $businessName ?>"
                        >
                    </div>
                </div>

                <div class="flex-1 pt-2">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-none tracking-tight mb-2">
                        <?= $businessName ?>
                    </h1>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?= htmlspecialchars($typeConfig['color'], ENT_QUOTES, 'UTF-8') ?>">
                            <i data-lucide="<?= htmlspecialchars($typeConfig['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-3 h-3"></i>
                            <?= htmlspecialchars($typeConfig['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if ($activeOffers !== []): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                <?= count($activeOffers) ?> Ofertas Activas
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest">
                                Sin ofertas hoy
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed italic border-l-4 border-red-100 pl-4">
                    "<?= htmlspecialchars((string) ($business['bio'] ?? 'Este negocio no ha cargado su descripción aún.'), ENT_QUOTES, 'UTF-8') ?>"
                </p>
            </div>
        </section>

        <section class="px-6 mb-8">
            <div class="grid grid-cols-4 gap-4">
                <?php foreach ($socialButtons as $social): ?>
                    <a href="<?= htmlspecialchars((string) $social['url'], ENT_QUOTES, 'UTF-8') ?>" 
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Seguir en <?= $social['name'] ?>"
                       class="flex flex-col items-center gap-2 group">
                        <div class="w-full aspect-square rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:border-red-600 group-hover:text-white transition-all duration-300 shadow-sm">
                            <i data-lucide="<?= $social['icon'] ?>" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter"><?= $social['name'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="px-6 mb-10">
            <div class="bg-slate-900 rounded-[2.5rem] p-2 flex items-center justify-between shadow-xl">
                <div class="flex items-center gap-4 ml-4 py-3 min-w-0">
                    <div class="text-red-500 shrink-0">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.2em]">Ubicación</p>
                        <p class="text-sm font-bold text-white truncate max-w-[160px] sm:max-w-[220px]">
                            <?= htmlspecialchars((string) ($business['location'] ?? 'Consultar dirección'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <?php if (!empty($business['between_streets'])): ?>
                            <p class="text-xs text-slate-400 mt-0.5 truncate max-w-[160px] sm:max-w-[220px]">
                                Entre <?= htmlspecialchars((string) $business['between_streets'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($business['postal_code'])): ?>
                            <p class="text-[10px] text-slate-500 mt-0.5">CP: <?= htmlspecialchars((string) $business['postal_code'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode((string)($business['location'] ?? '')) ?>"
                   target="_blank"
                   class="bg-white text-slate-900 px-6 py-4 rounded-[2rem] font-black text-xs hover:bg-red-600 hover:text-white transition-colors uppercase tracking-widest shrink-0">
                    Ir al mapa
                </a>
            </div>
        </section>

        <section class="px-6 pb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-tighter"><?= htmlspecialchars($typeConfig['sectionLabel'], ENT_QUOTES, 'UTF-8') ?></h2>
                <div class="h-px flex-1 bg-slate-100 ml-4"></div>
            </div>

            <div class="grid gap-5">
                <?php foreach ($activeOffers as $offer): ?>
                    <?php
                    $context = 'business-detail';
                    $whatsappCta = 'Pedir por WhatsApp';
                    include __DIR__ . '/../partials/offer-card.php';
                    ?>
                <?php endforeach; ?>

                <?php if ($activeOffers === []): ?>
                    <div class="rounded-[2.5rem] border-2 border-dashed border-slate-200 bg-slate-50 py-12 text-center">
                        <i data-lucide="tag" class="mx-auto mb-3 h-10 w-10 text-slate-300"></i>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">No hay ofertas publicadas</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <a href="https://wa.me/<?= $whatsappNumber ?>" 
       class="fixed bottom-6 right-6 w-16 h-16 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-2xl hover:scale-110 active:scale-90 transition-all z-50">
        <i data-lucide="message-circle" class="w-8 h-8 fill-white/20"></i>
    </a>
</div>

