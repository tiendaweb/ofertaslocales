<?php
declare(strict_types=1);
?>

<section class="max-w-6xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Comercios destacados</h2>
        <p class="text-gray-500">Explora los negocios locales con promociones vigentes.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($businesses as $business) : ?>
            <?php
            $businessImage = (string) ($business['logo_url'] ?? '');
            if ($businessImage === '' && isset($business['cover_image_url'])) {
                $businessImage = (string) $business['cover_image_url'];
            }
            if ($businessImage === '' && isset($business['active_publications'][0]['image_url'])) {
                $businessImage = (string) $business['active_publications'][0]['image_url'];
            }
            $mapLink = isset($business['active_publications'][0]['id'])
                ? '/mapa?oferta=' . (int) $business['active_publications'][0]['id']
                : '/mapa';
            ?>
            <article class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 flex flex-col gap-4">
                <a href="/negocios/<?= (int) $business['id'] ?>" class="block rounded-2xl overflow-hidden border border-gray-100 bg-gray-100 h-40">
                    <img
                        src="<?= htmlspecialchars($businessImage !== '' ? $businessImage : 'https://placehold.co/1200x600/f3f4f6/1f2937?text=Negocio', ENT_QUOTES, 'UTF-8') ?>"
                        alt="Imagen de <?= htmlspecialchars($business['business_name'], ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full h-full object-cover transition duration-500 hover:scale-105"
                    >
                </a>
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-gray-400 font-semibold mb-2">Negocio activo</p>
                        <h3 class="text-xl font-bold text-gray-900">
                            <a href="/negocios/<?= (int) $business['id'] ?>" class="hover:text-red-600 transition">
                                <?= htmlspecialchars($business['business_name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h3>
                    </div>
                    <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap">
                        <?= (int) $business['active_offers'] ?> activas
                    </span>
                </div>

                <div class="space-y-2 text-sm text-gray-500">
                    <p class="flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i>
                        <?= htmlspecialchars($business['location'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="tag" class="w-4 h-4 text-yellow-500"></i>
                        <?= htmlspecialchars($business['category'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4 text-green-500"></i>
                        <?= htmlspecialchars($business['whatsapp'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-sm text-gray-600 gap-3">
                        <span class="flex items-center gap-2"><i data-lucide="store" class="w-4 h-4 text-red-500"></i> Estado</span>
                        <span class="font-semibold text-gray-900">Con promociones</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600 gap-3">
                        <span class="flex items-center gap-2"><i data-lucide="timer" class="w-4 h-4 text-yellow-500"></i> Próximo cierre</span>
                        <span class="font-semibold text-gray-900"><?= htmlspecialchars($business['next_expiration_label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-[0.18em]">Publicaciones</h4>
                        <a href="/ofertas?negocio=<?= (int) $business['id'] ?>" class="text-sm font-semibold text-red-600 hover:text-red-700 transition">
                            Ver todas
                        </a>
                    </div>
                    <div class="space-y-3">
                        <?php foreach (array_slice($business['active_publications'], 0, 2) as $publication) : ?>
                            <a href="/ofertas?negocio=<?= (int) $business['id'] ?>" class="block border border-gray-100 rounded-2xl p-4 hover:border-red-200 hover:bg-red-50/40 transition">
                                <p class="text-xs uppercase tracking-[0.18em] text-gray-400 font-semibold mb-2">
                                    <?= htmlspecialchars($publication['category'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="font-semibold text-gray-900 mb-1 truncate"><?= htmlspecialchars($publication['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="space-y-1 text-sm text-gray-500">
                                    <p class="flex items-center gap-2"><i data-lucide="clock-3" class="w-4 h-4 text-yellow-500"></i><?= htmlspecialchars($publication['expires_label'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-auto flex gap-3 pt-4">
                    <a href="/negocios/<?= (int) $business['id'] ?>" class="flex-1 bg-gray-900 text-white rounded-xl px-4 py-3 text-center font-semibold hover:bg-gray-800 transition">Ver perfil</a>
                    <a href="<?= htmlspecialchars($mapLink, ENT_QUOTES, 'UTF-8') ?>" class="flex-1 bg-red-50 text-red-600 rounded-xl px-4 py-3 text-center font-semibold hover:bg-red-100 transition">Ir al mapa</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="bg-gradient-to-br from-red-600 to-red-800 text-white py-20 px-4">
    <div class="max-w-6xl mx-auto grid gap-8 lg:grid-cols-[1.1fr_0.9fr] items-center">
        <div>
            <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30">
                🏪 Comercios locales
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6 leading-tight">Negocios registrados con ofertas activas.</h1>
            <p class="text-lg text-red-100 mb-8 max-w-2xl">Cada ficha se construye desde las ofertas vigentes para que la información se mantenga sincronizada con el resto del sitio y el mapa interactivo.</p>
            
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Negocios visibles</p>
                    <p class="text-3xl font-black"><?= (int) $summary['totalBusinesses'] ?></p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-2xl px-5 py-4 min-w-40">
                    <p class="text-sm text-red-100">Ofertas activas</p>
                    <p class="text-3xl font-black"><?= (int) $summary['activeOffers'] ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white text-gray-800 rounded-3xl p-6 md:p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-4 text-gray-900">¿Qué encontrás en esta vista?</h2>
            <ul class="space-y-4">
                <li class="flex gap-3">
                    <i data-lucide="badge-check" class="text-green-500 w-5 h-5 mt-0.5"></i>
                    <span>Información comercial, ubicación y categoría del negocio.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="clock-3" class="text-red-500 w-5 h-5 mt-0.5"></i>
                    <span>Estado de las ofertas y fecha de próximo vencimiento.</span>
                </li>
                <li class="flex gap-3">
                    <i data-lucide="scroll-text" class="text-blue-500 w-5 h-5 mt-0.5"></i>
                    <span>Acceso directo a las publicaciones y navegación al mapa.</span>
                </li>
            </ul>
        </div>
    </div>
</section>
