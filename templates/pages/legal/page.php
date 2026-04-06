<?php

declare(strict_types=1);

$legalPage = $legalPage ?? [];
$pageKey = $legalPage['page_key'] ?? '';
$title = $legalPage['title'] ?? 'Página Legal';
$content = $legalPage['content_html'] ?? '';

?>
<div class="max-w-4xl mx-auto px-4 py-8 md:py-12">
    <article class="prose prose-sm md:prose-base max-w-none prose-a:text-red-600 prose-a:hover:text-red-700">
        <header class="mb-8 pb-8 border-b border-gray-200">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-gray-500">
                Última actualización: <?= htmlspecialchars($legalPage['last_updated_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </header>

        <div class="bg-white rounded-xl border border-gray-200 p-6 md:p-8">
            <?= $content ?>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
                ¿Preguntas sobre nuestras políticas?
                <a href="/" class="text-red-600 hover:text-red-700 font-semibold">Contactanos</a>
            </p>
        </div>
    </article>
</div>
