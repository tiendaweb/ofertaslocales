<?php

declare(strict_types=1);

$isPublicRoute = in_array($currentRoute ?? '', ['inicio', 'ofertas', 'negocios', 'mapa'], true);
$isAdminRoute = ($currentRoute ?? '') === 'admin';
$siteLogoUrl = trim((string) (($labels['site_logo_url'] ?? ($settings['site_logo_url'] ?? ''))));
$currentUser = $currentUser ?? null;
?>
<?php if ($isPublicRoute) : ?>
    <footer class="bg-gray-950 text-gray-400 py-12 text-center border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2 text-white font-bold text-xl">
                <?php if ($siteLogoUrl !== '') : ?>
                    <img src="<?= htmlspecialchars($siteLogoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Logo de OfertasLocales" class="h-8 w-auto max-w-[180px] object-contain">
                <?php else : ?>
                    <i data-lucide="map-pin" class="text-red-500 w-6 h-6"></i>
                <?php endif; ?>
                <?php if ($siteLogoUrl === '') : ?>
                    <span>OfertasLocales</span>
                <?php endif; ?>
            </div>
            <div class="text-sm">
                <p>© <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8') ?> Ofertas Locales | Generador de clientes locales</p>
                <p class="mt-1" data-editable-key="footer_tagline" data-editable-type="text"><?= htmlspecialchars((string) ($labels['footer_tagline'] ?? 'Hecho con ❤️ para potenciar negocios de barrio.'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="flex gap-4 text-sm">
                <a href="<?= htmlspecialchars((string) ($labels['footer_link_publish_url'] ?? '/register'), ENT_QUOTES, 'UTF-8') ?>" data-editable-key="footer_link_publish_url" data-editable-attr="href" data-editable-type="url" class="hover:text-white transition">Publicar</a>
                <?php if ($currentUser === null) : ?>
                    <a href="<?= htmlspecialchars((string) ($labels['footer_link_login_url'] ?? '/login'), ENT_QUOTES, 'UTF-8') ?>" data-editable-key="footer_link_login_url" data-editable-attr="href" data-editable-type="url" class="hover:text-white transition">Ingresar</a>
                <?php else : ?>
                    <a href="<?= (($currentUser['role'] ?? '') === 'admin') ? '/admin' : '/panel' ?>" class="hover:text-white transition">Mi panel</a>
                <?php endif; ?>
                <a href="<?= htmlspecialchars((string) ($labels['footer_link_map_url'] ?? '/mapa'), ENT_QUOTES, 'UTF-8') ?>" data-editable-key="footer_link_map_url" data-editable-attr="href" data-editable-type="url" class="hover:text-white transition">Mapa</a>
            </div>
        </div>
    </footer>

    <a
        href="<?= htmlspecialchars((string) ($labels['footer_whatsapp_url'] ?? 'https://wa.me/5491112345678?text=Hola,%20tengo%20una%20consulta%20sobre%20OfertasLocales'), ENT_QUOTES, 'UTF-8') ?>" data-editable-key="footer_whatsapp_url" data-editable-attr="href" data-editable-type="url"
        target="_blank"
        rel="noreferrer"
        class="fixed bottom-24 right-4 md:bottom-6 md:right-6 bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-transform z-40 flex items-center justify-center"
        aria-label="Contactar soporte"
    >
        <i data-lucide="message-circle" class="w-7 h-7"></i>
    </a>
<?php else : ?>
    <footer class="mt-10 border-t <?= $isAdminRoute ? 'border-red-100 bg-white/70' : 'border-white/10' ?>">
        <div class="max-w-6xl mx-auto px-4 py-6 md:px-6 text-sm flex flex-col gap-2 md:flex-row md:items-center md:justify-between <?= $isAdminRoute ? 'text-gray-600' : 'text-slate-400' ?>">
            <p>Base pública y privada inicial para OfertasLocales. Todo el contenido visible está en español.</p>
            <p>© <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8') ?> · Slim 4 + SQLite + Tailwind CSS</p>
        </div>
    </footer>
<?php endif; ?>
