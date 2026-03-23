<?php

declare(strict_types=1);
?>
<section class="space-y-6">
    <section class="glass rounded-3xl p-8">
        <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Administración</p>
        <h2 class="text-3xl font-semibold text-white mb-6">Moderación, labels y SEO</h2>
        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
                <p class="text-sm text-slate-400 mb-2">Ofertas pendientes</p>
                <p class="text-4xl font-semibold text-white"><?= (int) $pendingOffers ?></p>
            </article>
            <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
                <p class="text-sm text-slate-400 mb-2">Administradores</p>
                <p class="text-4xl font-semibold text-white"><?= (int) $adminCount ?></p>
            </article>
            <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
                <p class="text-sm text-slate-400 mb-2">Negocios</p>
                <p class="text-4xl font-semibold text-white"><?= (int) $businessCount ?></p>
            </article>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="glass rounded-3xl p-8">
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-2">Revisión</p>
                    <h3 class="text-2xl font-semibold text-white">Aprobar o rechazar ofertas</h3>
                </div>
                <span class="chip rounded-full px-4 py-2 text-sm text-slate-100"><?= count($offers) ?> ofertas</span>
            </div>
            <div class="space-y-4 max-h-[42rem] overflow-y-auto pr-2">
                <?php foreach ($offers as $offer) : ?>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5 space-y-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.18em] text-blue-200 mb-2"><?= htmlspecialchars($offer['business_name'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($offer['email'], ENT_QUOTES, 'UTF-8') ?></p>
                                <h4 class="text-lg font-semibold text-white"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                                <p class="mt-2 text-sm text-slate-300"><?= htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= match ($offer['status']) {
                                'active' => 'bg-emerald-500/20 text-emerald-200',
                                'pending' => 'bg-amber-500/20 text-amber-200',
                                'rejected' => 'bg-rose-500/20 text-rose-200',
                                default => 'bg-slate-700 text-slate-200',
                            } ?>">
                                <?= htmlspecialchars((string) $offer['status'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <div class="grid gap-3 text-sm text-slate-300 md:grid-cols-2">
                            <p><strong class="text-white">Categoría:</strong> <?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong class="text-white">Ubicación:</strong> <?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong class="text-white">WhatsApp:</strong> <?= htmlspecialchars($offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p><strong class="text-white">Expira:</strong> <?= htmlspecialchars($offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <form action="/admin/offers/<?= (int) $offer['id'] ?>/status" method="post" class="flex flex-wrap gap-3">
                            <button name="status" value="active" class="rounded-full bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950">Aprobar</button>
                            <button name="status" value="rejected" class="rounded-full bg-rose-400 px-4 py-2 text-sm font-semibold text-slate-950">Rechazar</button>
                            <button name="status" value="pending" class="rounded-full bg-amber-300 px-4 py-2 text-sm font-semibold text-slate-950">Dejar pendiente</button>
                            <button name="status" value="expired" class="rounded-full bg-slate-300 px-4 py-2 text-sm font-semibold text-slate-950">Marcar expirada</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>

        <div class="space-y-6">
            <article class="glass rounded-3xl p-8">
                <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Publicación</p>
                <h3 class="text-2xl font-semibold text-white mb-4">Aprobación automática o manual</h3>
                <form action="/admin/approval-mode" method="post" class="space-y-4">
                    <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-3">
                        <input type="radio" name="approval_mode" value="manual" <?= ($settings['approval_mode'] ?? 'manual') === 'manual' ? 'checked' : '' ?>>
                        <span>Revisión manual antes de publicar</span>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/60 px-4 py-3">
                        <input type="radio" name="approval_mode" value="auto" <?= ($settings['approval_mode'] ?? 'manual') === 'auto' ? 'checked' : '' ?>>
                        <span>Aprobación automática al crear la oferta</span>
                    </label>
                    <button type="submit" class="rounded-2xl bg-blue-500 px-4 py-3 font-semibold text-slate-950">Guardar modo</button>
                </form>
            </article>

            <article class="glass rounded-3xl p-8">
                <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Labels</p>
                <h3 class="text-2xl font-semibold text-white mb-4">Editar textos del sitio</h3>
                <form action="/admin/settings" method="post" class="space-y-4">
                    <?php foreach ([
                        'site_name' => 'Nombre del sitio',
                        'hero_badge' => 'Badge principal',
                        'hero_title' => 'Título del hero',
                        'hero_description' => 'Descripción del hero',
                        'hero_primary_cta' => 'CTA principal',
                        'merchant_badge' => 'Badge comerciantes',
                        'merchant_title' => 'Título comerciantes',
                        'merchant_description' => 'Descripción comerciantes',
                        'footer_tagline' => 'Texto footer',
                    ] as $key => $label) : ?>
                        <label class="block">
                            <span class="block text-sm text-slate-300 mb-2"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                            <input type="text" name="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) ($settings[$key] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                        </label>
                    <?php endforeach; ?>
                    <button type="submit" class="rounded-2xl bg-emerald-400 px-4 py-3 font-semibold text-slate-950">Guardar labels</button>
                </form>
            </article>
        </div>
    </section>

    <section class="glass rounded-3xl p-8">
        <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">SEO</p>
        <h3 class="text-2xl font-semibold text-white mb-6">Actualizar SEO por página</h3>
        <div class="grid gap-6 xl:grid-cols-2">
            <?php foreach ($seoPages as $seo) : ?>
                <form action="/admin/seo/<?= htmlspecialchars($seo['page_name'], ENT_QUOTES, 'UTF-8') ?>" method="post" class="rounded-3xl border border-white/10 bg-slate-900/60 p-5 space-y-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-blue-200 mb-2">Página <?= htmlspecialchars($seo['page_name'], ENT_QUOTES, 'UTF-8') ?></p>
                        <h4 class="text-lg font-semibold text-white">Metadatos configurables</h4>
                    </div>
                    <label class="block">
                        <span class="block text-sm text-slate-300 mb-2">Título</span>
                        <input type="text" name="title" value="<?= htmlspecialchars($seo['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                    </label>
                    <label class="block">
                        <span class="block text-sm text-slate-300 mb-2">Meta description</span>
                        <textarea name="meta_description" rows="3" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white"><?= htmlspecialchars($seo['meta_description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                    <label class="block">
                        <span class="block text-sm text-slate-300 mb-2">Imagen OG</span>
                        <input type="text" name="og_image" value="<?= htmlspecialchars((string) $seo['og_image'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                    </label>
                    <button type="submit" class="rounded-2xl bg-blue-500 px-4 py-3 font-semibold text-slate-950">Guardar SEO</button>
                </form>
            <?php endforeach; ?>
        </div>
    </section>
</section>
