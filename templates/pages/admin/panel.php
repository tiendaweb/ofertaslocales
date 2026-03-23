<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
?>
<section class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
    <article class="glass rounded-3xl p-8 space-y-6">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Panel del negocio</p>
            <h2 class="text-3xl font-semibold text-white mb-2">Publicar una nueva oferta</h2>
            <p class="text-slate-300">Carga una imagen, define la ubicación o selecciona coordenadas y revisa el estado final según el modo de aprobación actual.</p>
        </div>

        <div class="rounded-2xl border border-blue-400/20 bg-blue-500/10 px-4 py-3 text-sm text-blue-100">
            Modo de publicación actual:
            <strong class="text-white"><?= ($approvalMode ?? 'manual') === 'auto' ? 'Aprobación automática' : 'Revisión manual' ?></strong>
        </div>

        <?php if (($formErrors['coordinates'] ?? null) !== null || ($formErrors['image'] ?? null) !== null) : ?>
            <div class="rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <?= htmlspecialchars((string) ($formErrors['coordinates'] ?? $formErrors['image']), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form class="grid gap-4 md:grid-cols-2" action="/panel/ofertas" method="post" enctype="multipart/form-data">
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">Categoría</span>
                <input name="category" type="text" value="<?= htmlspecialchars((string) ($old['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Gastronomía" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                <?php if (($formErrors['category'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['category'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">WhatsApp</span>
                <input name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ($currentUser['whatsapp'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" placeholder="+54 9 11..." class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="block text-sm text-slate-300 mb-2">Título de la oferta</span>
                <input name="title" type="text" value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: 2x1 en desayunos" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                <?php if (($formErrors['title'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['title'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="block text-sm text-slate-300 mb-2">Descripción</span>
                <textarea name="description" rows="4" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white" placeholder="Describe condiciones, stock y horario."><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (($formErrors['description'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['description'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="block text-sm text-slate-300 mb-2">Ubicación visible</span>
                <input name="location" type="text" value="<?= htmlspecialchars((string) ($old['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Av. Siempre Viva 123, Caseros" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                <?php if (($formErrors['location'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['location'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">Latitud</span>
                <input name="lat" type="text" value="<?= htmlspecialchars((string) ($old['lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="-34.6037" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
            </label>
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">Longitud</span>
                <input name="lon" type="text" value="<?= htmlspecialchars((string) ($old['lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="-58.3816" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
            </label>
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">Expira el</span>
                <input name="expires_at" type="datetime-local" value="<?= htmlspecialchars((string) ($old['expires_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white">
                <?php if (($formErrors['expires_at'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['expires_at'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="block text-sm text-slate-300 mb-2">Imagen</span>
                <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white file:mr-4 file:rounded-full file:border-0 file:bg-blue-500 file:px-4 file:py-2 file:text-slate-950">
            </label>
            <button type="submit" class="md:col-span-2 rounded-2xl bg-emerald-400 px-4 py-3 font-semibold text-slate-950">Guardar oferta</button>
        </form>
    </article>

    <article class="glass rounded-3xl p-8">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-2">Mis publicaciones</p>
                <h3 class="text-2xl font-semibold text-white">Estado de tus ofertas</h3>
            </div>
            <span class="chip rounded-full px-4 py-2 text-sm text-slate-100"><?= count($offers) ?> registradas</span>
        </div>

        <div class="space-y-4">
            <?php foreach ($offers as $offer) : ?>
                <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-blue-200 mb-2"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></p>
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
                    <div class="mt-4 grid gap-3 text-sm text-slate-300 md:grid-cols-2">
                        <p><strong class="text-white">Ubicación:</strong> <?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-white">WhatsApp:</strong> <?= htmlspecialchars($offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-white">Vence:</strong> <?= htmlspecialchars($offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-white">Coordenadas:</strong> <?= htmlspecialchars((string) ($offer['lat'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($offer['lon'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($offers === []) : ?>
                <div class="rounded-3xl border border-dashed border-white/15 bg-slate-900/40 p-6 text-slate-300">
                    Aún no publicaste ofertas. Usa el formulario para cargar la primera.
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
