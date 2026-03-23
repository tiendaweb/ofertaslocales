<?php

declare(strict_types=1);

$statusLabels = [
    'active' => 'Activa',
    'pending' => 'Pendiente',
    'rejected' => 'Rechazada',
    'expired' => 'Vencida',
];

$kpis = [
    'active' => 0,
    'pending' => 0,
    'expired' => 0,
];

foreach ($offers as $offer) {
    $status = (string) ($offer['status'] ?? '');
    if (array_key_exists($status, $kpis)) {
        $kpis[$status]++;
    }
}
?>

<section class="space-y-6">
    <article class="rounded-3xl border border-red-100 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.25em] text-red-500">Mi panel</p>
                <h2 class="text-3xl font-bold text-gray-900">Resumen de ofertas</h2>
            </div>
            <span class="inline-flex rounded-full border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700"><?= count($offers) ?> registradas</span>
        </div>

        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Activas</p>
                <p class="mt-2 text-3xl font-bold text-emerald-700"><?= $kpis['active'] ?></p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Pendientes</p>
                <p class="mt-2 text-3xl font-bold text-amber-700"><?= $kpis['pending'] ?></p>
            </div>
            <div class="rounded-2xl border border-gray-300 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-700">Vencidas</p>
                <p class="mt-2 text-3xl font-bold text-gray-700"><?= $kpis['expired'] ?></p>
            </div>
        </div>
    </article>

    <article class="rounded-3xl border border-red-100 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-4">
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.25em] text-red-500">Gestión rápida</p>
            <h3 class="text-2xl font-bold text-gray-900">Lista compacta de ofertas</h3>
        </div>

        <div class="space-y-3">
            <?php foreach ($offers as $offer) : ?>
                <?php
                $status = (string) ($offer['status'] ?? 'pending');
                $statusLabel = $statusLabels[$status] ?? 'Pendiente';
                $renewLabel = $status === 'expired' ? 'Renovar' : 'Duplicar';
                ?>
                <div class="rounded-2xl border border-red-100 bg-red-50/50 p-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-red-500"><?= htmlspecialchars((string) $offer['category'], ENT_QUOTES, 'UTF-8') ?></p>
                            <h4 class="truncate text-lg font-semibold text-gray-900"><?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                            <p class="text-sm text-gray-600">Estado: <strong><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></strong> · Vence: <?= htmlspecialchars((string) $offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                        <span class="rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-700"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <details class="group">
                            <summary class="cursor-pointer rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Editar</summary>
                            <form class="mt-3 grid gap-2 md:grid-cols-2" action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                                <input type="hidden" name="operation" value="editar">
                                <input name="category" value="<?= htmlspecialchars((string) $offer['category'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required>
                                <input name="title" value="<?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required>
                                <input name="whatsapp" value="<?= htmlspecialchars((string) $offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required>
                                <input name="location" value="<?= htmlspecialchars((string) $offer['location'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required>
                                <textarea name="description" rows="2" class="md:col-span-2 rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required><?= htmlspecialchars((string) $offer['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                <button type="submit" class="md:col-span-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500">Guardar cambios</button>
                            </form>
                        </details>

                        <form action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                            <input type="hidden" name="operation" value="estado">
                            <input type="hidden" name="status" value="<?= $status === 'active' ? 'pending' : 'active' ?>">
                            <button type="submit" class="rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">Cambiar estado</button>
                        </form>

                        <form action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                            <input type="hidden" name="operation" value="duplicar_renovar">
                            <button type="submit" class="rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100"><?= $renewLabel ?></button>
                        </form>

                        <form action="/panel/ofertas/<?= (int) $offer['id'] ?>/eliminar" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar esta oferta?');">
                            <button type="submit" class="rounded-xl border border-rose-300 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-100">Eliminar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($offers === []) : ?>
                <div class="rounded-2xl border border-dashed border-red-200 bg-red-50 p-6 text-sm text-red-700">
                    Aún no tienes ofertas cargadas en el panel.
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
