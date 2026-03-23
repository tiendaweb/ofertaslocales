<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
?>
<section class="grid gap-6 lg:grid-cols-[1fr_1.05fr]">
    <article class="rounded-3xl border border-red-100 bg-white p-6 shadow-sm md:p-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-red-500 mb-3">Mi panel</p>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Publicar una oferta</h2>
            <p class="text-gray-600">Completa el formulario y publica en minutos. El flujo está pensado para que sea simple, claro y rápido.</p>
        </div>

        <div class="mt-6 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            Modo actual:
            <strong><?= ($approvalMode ?? 'manual') === 'auto' ? 'Aprobación automática' : 'Revisión manual' ?></strong>
        </div>

        <?php if (($formErrors['coordinates'] ?? null) !== null || ($formErrors['image'] ?? null) !== null) : ?>
            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <?= htmlspecialchars((string) ($formErrors['coordinates'] ?? $formErrors['image']), ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form class="mt-6 grid gap-4 md:grid-cols-2" action="/panel/ofertas" method="post" enctype="multipart/form-data">
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">Categoría</span>
                <input name="category" type="text" value="<?= htmlspecialchars((string) ($old['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Gastronomía" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
                <?php if (($formErrors['category'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['category'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">WhatsApp</span>
                <input name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ($currentUser['whatsapp'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" placeholder="+54 9 11..." class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
                <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="mb-2 block text-sm font-medium text-gray-700">Título</span>
                <input name="title" type="text" value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: 2x1 en desayunos" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
                <?php if (($formErrors['title'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['title'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="mb-2 block text-sm font-medium text-gray-700">Descripción</span>
                <textarea name="description" rows="4" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none" placeholder="Condiciones, stock y horario."><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (($formErrors['description'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['description'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block md:col-span-2">
                <span class="mb-2 block text-sm font-medium text-gray-700">Ubicación visible</span>
                <input name="location" type="text" value="<?= htmlspecialchars((string) ($old['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Av. Siempre Viva 123" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
                <?php if (($formErrors['location'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['location'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">Latitud</span>
                <input name="lat" type="text" value="<?= htmlspecialchars((string) ($old['lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="-34.6037" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">Longitud</span>
                <input name="lon" type="text" value="<?= htmlspecialchars((string) ($old['lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="-58.3816" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">Expira el</span>
                <input name="expires_at" type="datetime-local" value="<?= htmlspecialchars((string) ($old['expires_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 focus:border-red-400 focus:outline-none">
                <?php if (($formErrors['expires_at'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['expires_at'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-medium text-gray-700">Imagen</span>
                <input name="image" type="file" accept="image/png,image/jpeg,image/webp" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-700 file:mr-4 file:rounded-full file:border-0 file:bg-red-100 file:px-4 file:py-2 file:text-red-700">
            </label>
            <button type="submit" class="md:col-span-2 rounded-2xl bg-red-600 px-4 py-3 font-semibold text-white transition hover:bg-red-500">Guardar oferta</button>
        </form>
    </article>

    <article class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.25em] text-red-500">Mis publicaciones</p>
                <h3 class="text-2xl font-bold text-gray-900">Estado de ofertas</h3>
            </div>
            <span class="rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700"><?= count($offers) ?> registradas</span>
        </div>

        <div class="space-y-4">
            <?php foreach ($offers as $offer) : ?>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-red-500"><?= htmlspecialchars($offer['category'], ENT_QUOTES, 'UTF-8') ?></p>
                            <h4 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                            <p class="mt-2 text-sm text-gray-600"><?= htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold <?= match ($offer['status']) {
                            'active' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'rejected' => 'bg-rose-100 text-rose-700',
                            default => 'bg-gray-200 text-gray-700',
                        } ?>">
                            <?= htmlspecialchars((string) $offer['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="mt-4 grid gap-3 text-sm text-gray-600 md:grid-cols-2">
                        <p><strong class="text-gray-900">Ubicación:</strong> <?= htmlspecialchars($offer['location'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-gray-900">WhatsApp:</strong> <?= htmlspecialchars($offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-gray-900">Vence:</strong> <?= htmlspecialchars($offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong class="text-gray-900">Coordenadas:</strong> <?= htmlspecialchars((string) ($offer['lat'] ?? '—'), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars((string) ($offer['lon'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($offers === []) : ?>
                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-gray-600">
                    Aún no publicaste ofertas. Usa el formulario para cargar la primera.
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
