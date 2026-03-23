<?php

declare(strict_types=1);
?>
<section class="glass rounded-3xl p-8 mb-6">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Ofertas</p>
    <h2 class="text-3xl font-semibold text-white mb-4">Listado base de ofertas activas</h2>
    <div class="flex flex-wrap gap-2">
        <?php foreach ($categories as $category): ?>
            <span class="chip rounded-full px-4 py-2 text-sm"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endforeach; ?>
    </div>
</section>

<div class="grid gap-4 lg:grid-cols-2">
    <?php foreach ($offers as $offer): ?>
        <article class="glass rounded-3xl p-6 flex gap-4">
            <img src="<?= htmlspecialchars($offer['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?>" class="h-28 w-28 rounded-2xl object-cover shrink-0">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip rounded-full px-3 py-1 text-xs uppercase tracking-[0.22em]"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-xs text-slate-400">Estado: <?= htmlspecialchars($offer['status'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <h3 class="text-xl font-semibold text-white"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="text-slate-300 text-sm leading-6"><?= htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8') ?></p>
                <div class="text-sm text-slate-400">
                    <p><?= htmlspecialchars($offer['business_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?> · WhatsApp <?= htmlspecialchars($offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
