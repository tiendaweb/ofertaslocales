<?php

declare(strict_types=1);

$isPublicRoute = in_array($currentRoute ?? '', ['inicio', 'ofertas', 'negocios', 'mapa'], true);
?>
<?php if ($isPublicRoute) : ?>
    <footer class="bg-gray-950 text-gray-400 py-12 text-center border-t border-gray-800">
        <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2 text-white font-bold text-xl">
                <i data-lucide="map-pin" class="text-red-500 w-6 h-6"></i>
                <span>OfertasCerca</span>
            </div>
            <div class="text-sm">
                <p>© <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8') ?> Ofertas Cerca | Generador de clientes locales</p>
                <p class="mt-1">Hecho con ❤️ para potenciar negocios de barrio.</p>
            </div>
            <div class="flex gap-4 text-sm">
                <a href="/register" class="hover:text-white transition">Publicar</a>
                <a href="/login" class="hover:text-white transition">Ingresar</a>
                <a href="/mapa" class="hover:text-white transition">Mapa</a>
            </div>
        </div>
    </footer>

    <a
        href="https://wa.me/5491112345678?text=Hola,%20tengo%20una%20consulta%20sobre%20OfertasCerca"
        target="_blank"
        rel="noreferrer"
        class="fixed bottom-24 right-4 md:bottom-6 md:right-6 bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-transform z-40 flex items-center justify-center"
        aria-label="Contactar soporte"
    >
        <i data-lucide="message-circle" class="w-7 h-7"></i>
    </a>
<?php else : ?>
    <footer class="border-t border-white/10 mt-10">
        <div class="max-w-6xl mx-auto px-4 py-6 md:px-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <p>Base pública y privada inicial para OfertasCerca. Todo el contenido visible está en español.</p>
            <p>© <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8') ?> · Slim 4 + SQLite + Tailwind CSS</p>
        </div>
    </footer>
<?php endif; ?>
