<?php

declare(strict_types=1);

$isPublicRoute = in_array($currentRoute ?? '', ['inicio', 'ofertas', 'negocios', 'mapa'], true);
$isAdminRoute = ($currentRoute ?? '') === 'admin';
$pageDataJson = isset($pageData)
    ? json_encode($pageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
    : null;
$flash = $flash ?? [];
$manifestPath = dirname(__DIR__) . '/public/manifest.webmanifest';
$manifest = [];
if (is_file($manifestPath)) {
    $manifestContent = file_get_contents($manifestPath);
    $decodedManifest = is_string($manifestContent) ? json_decode($manifestContent, true) : null;
    if (is_array($decodedManifest)) {
        $manifest = $decodedManifest;
    }
}
$pwaName = trim((string) ($manifest['name'] ?? 'OfertasLocales'));
$pwaShortName = trim((string) ($manifest['short_name'] ?? 'Ofertas'));
$pwaThemeColor = trim((string) ($manifest['theme_color'] ?? '#dc2626'));
$pwaIcon192 = '';
if (isset($manifest['icons']) && is_array($manifest['icons'])) {
    foreach ($manifest['icons'] as $icon) {
        if (is_array($icon) && (($icon['sizes'] ?? '') === '192x192')) {
            $pwaIcon192 = (string) ($icon['src'] ?? $pwaIcon192);
            break;
        }
    }
}
$runtimeSettings = [];
$runtimeDbPath = dirname(__DIR__) . '/database/app.sqlite';
if (is_file($runtimeDbPath)) {
    try {
        $runtimePdo = new PDO('sqlite:' . $runtimeDbPath);
        $runtimeStmt = $runtimePdo->query('SELECT key, value FROM settings WHERE key IN (\'custom_css_frontend\', \'custom_js_frontend\', \'custom_css_panel\', \'custom_js_panel\')');
        if ($runtimeStmt !== false) {
            foreach ($runtimeStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $runtimeSettings[(string) $row['key']] = (string) $row['value'];
            }
        }
    } catch (Throwable) {
        // ignore DB errors silently
    }
}
$customCssFrontend = trim((string) ($runtimeSettings['custom_css_frontend'] ?? ''));
$customJsFrontend  = trim((string) ($runtimeSettings['custom_js_frontend'] ?? ''));
$customCssPanel    = trim((string) ($runtimeSettings['custom_css_panel'] ?? ''));
$customJsPanel     = trim((string) ($runtimeSettings['custom_js_panel'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (($metaDescription ?? null) !== null) : ?>
        <meta name="description" content="<?= htmlspecialchars((string) $metaDescription, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (($ogImage ?? null) !== null) : ?>
        <meta property="og:image" content="<?= htmlspecialchars((string) $ogImage, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta name="application-name" content="<?= htmlspecialchars($pwaName, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($pwaShortName, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="<?= htmlspecialchars($pwaThemeColor, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="manifest" href="/manifest.webmanifest">
    <?php if ($pwaIcon192 !== '') : ?>
        <link rel="apple-touch-icon" sizes="192x192" href="<?= htmlspecialchars($pwaIcon192, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($pageTitle ?? 'Ofertas Locales | Ahorra hoy', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <?php if (in_array(($currentRoute ?? ''), ['inicio', 'mapa', 'registro', 'panel'], true)) : ?>
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        >
    <?php endif; ?>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .leaflet-popup-content-wrapper { border-radius: 1rem; }
        .leaflet-container { font-family: inherit; }
        <?php if (!$isPublicRoute) : ?>
        body { background: <?= $isAdminRoute ? "'radial-gradient(circle at top, #fff5f5 0%, #ffffff 65%, #ffe4e6 100%)'" : "'radial-gradient(circle at top, #0f172a 0%, #020617 55%, #01030b 100%)'" ?>; }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(18px); border: 1px solid rgba(148, 163, 184, 0.18); }
        .neon-ring { box-shadow: 0 0 24px rgba(59, 130, 246, 0.18); }
        .chip { border: 1px solid rgba(96, 165, 250, 0.25); background: rgba(30, 41, 59, 0.7); }
        <?php endif; ?>
        <?php if ($isPublicRoute && $customCssFrontend !== '') : ?>
        <?= $customCssFrontend ?>
        <?php elseif (!$isPublicRoute && $customCssPanel !== '') : ?>
        <?= $customCssPanel ?>
        <?php endif; ?>
    </style>
</head>
<body class="<?= $isPublicRoute ? 'min-h-screen bg-gray-50 font-sans text-gray-800' : ($isAdminRoute ? 'min-h-screen text-gray-800' : 'text-slate-100 min-h-screen') ?>">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <?php if ($isPublicRoute && ($currentUser['role'] ?? '') === 'admin') : ?>
        <section class="max-w-6xl mx-auto px-4 pt-4" id="inline-edit-toolbar" data-inline-edit-toolbar>
            <div class="hidden rounded-2xl border border-indigo-200 bg-indigo-50/80 px-4 py-3 md:px-5 md:py-4" data-inline-edit-panel>
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-indigo-700 font-medium">
                        Modo edición activo: podés cambiar los textos y URLs marcados sin salir de esta página.
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" data-inline-edit-cancel class="rounded-xl border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                            Cancelar
                        </button>
                        <button type="button" data-inline-edit-save class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                            Guardar cambios
                        </button>
                    </div>
                </div>
                <p class="mt-2 text-xs text-indigo-600" data-inline-edit-feedback></p>
            </div>
        </section>
    <?php endif; ?>

    <main class="<?= $isPublicRoute ? 'pb-36 md:pb-36' : ($isAdminRoute ? 'max-w-6xl mx-auto px-4 py-6 md:px-6 md:py-8 pb-28' : 'max-w-6xl mx-auto px-4 py-8 md:px-6 md:py-10') ?>">
        <?php if (($flash['success'] ?? null) !== null || ($flash['error'] ?? null) !== null) : ?>
            <section class="<?= $isPublicRoute ? 'max-w-6xl mx-auto px-4 pt-6' : 'mb-6' ?>">
                <?php if (($flash['success'] ?? null) !== null) : ?>
                    <div class="mb-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                        <?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
                <?php if (($flash['error'] ?? null) !== null) : ?>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        <?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php include $contentTemplate; ?>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <?php if ($isPublicRoute) : ?>
        <?php include __DIR__ . '/partials/navigation.php'; ?>
    <?php endif; ?>

    <?php if ($pageDataJson !== false && $pageDataJson !== null) : ?>
        <script id="page-data" type="application/json"><?= $pageDataJson ?></script>
    <?php endif; ?>

    <?php if (in_array(($currentRoute ?? ''), ['inicio', 'mapa', 'registro', 'panel'], true)) : ?>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?php endif; ?>

    <?php if ($isPublicRoute) : ?>
        <script>
            window.inlineEditConfig = {
                isAdmin: <?= (($currentUser['role'] ?? '') === 'admin') ? 'true' : 'false' ?>,
                endpoint: '/admin/inline-content',
            };
        </script>
        <script src="/assets/js/public-pages.js"></script>
    <?php endif; ?>

    <?php if (($currentRoute ?? '') === 'mapa') : ?>
        <script src="/assets/js/map-page.js"></script>
    <?php endif; ?>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {
                    console.warn('No se pudo registrar el Service Worker.');
                });
            });
        }
    </script>
    <?php if ($isPublicRoute && $customJsFrontend !== '') : ?>
        <script><?= $customJsFrontend ?></script>
    <?php elseif (!$isPublicRoute && $customJsPanel !== '') : ?>
        <script><?= $customJsPanel ?></script>
    <?php endif; ?>
</body>
</html>
