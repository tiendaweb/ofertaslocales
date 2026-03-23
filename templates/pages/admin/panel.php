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

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$defaultWhatsapp = (string) ($old['whatsapp'] ?? ($currentUser['whatsapp'] ?? ''));
$defaultExpiresAt = (string) ($old['expires_at'] ?? gmdate('Y-m-d\TH:i', strtotime('+24 hours')));
?>

<section class="space-y-6">
    <article class="rounded-3xl border border-red-100 bg-white p-6 shadow-sm md:p-8">
        <div class="mb-5">
            <p class="mb-2 text-sm font-semibold uppercase tracking-[0.25em] text-red-500">Publicar desde el panel</p>
            <h2 class="text-3xl font-bold text-gray-900">Crear nueva oferta</h2>
            <p class="mt-2 text-sm text-gray-600">Carga una oferta sin salir del panel. Puedes marcar ubicación en el mapa o escribir coordenadas manuales.</p>
        </div>

        <?php if ($formErrors !== []) : ?>
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($formErrors as $error) : ?>
                        <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/panel/ofertas" method="post" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-category">Categoría</label>
                <input id="panel-offer-category" name="category" required value="<?= htmlspecialchars((string) ($old['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="Ej: Gastronomía">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-title">Título de la oferta</label>
                <input id="panel-offer-title" name="title" required value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="Ej: Combo desayuno con 25% de descuento">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-description">Descripción</label>
                <textarea id="panel-offer-description" name="description" rows="3" required class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="Describe la promoción, stock y condiciones."><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-whatsapp">WhatsApp</label>
                <input id="panel-offer-whatsapp" name="whatsapp" required value="<?= htmlspecialchars($defaultWhatsapp, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="54911...">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-location">Ubicación</label>
                <input id="panel-offer-location" name="location" required value="<?= htmlspecialchars((string) ($old['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="Ej: Av. Siempre Viva 123">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-expires-at">Vence el</label>
                <input id="panel-offer-expires-at" type="datetime-local" name="expires_at" required value="<?= htmlspecialchars($defaultExpiresAt, ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-image">Imagen (JPG/PNG/WEBP)</label>
                <input id="panel-offer-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-red-700 hover:file:bg-red-100">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-lat">Latitud (opcional)</label>
                <input id="panel-offer-lat" name="lat" value="<?= htmlspecialchars((string) ($old['lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="-34.6037">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700" for="panel-offer-lon">Longitud (opcional)</label>
                <input id="panel-offer-lon" name="lon" value="<?= htmlspecialchars((string) ($old['lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" placeholder="-58.3816">
            </div>
            <div class="md:col-span-2">
                <p class="mb-2 text-sm font-semibold text-gray-700">Ubicación exacta en mapa (opcional)</p>
                <div id="panel-offer-map" class="h-72 w-full rounded-2xl border border-red-100 bg-gray-100"></div>
                <p class="mt-2 text-xs text-gray-500">Haz clic en el mapa para actualizar latitud y longitud automáticamente.</p>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Crear oferta desde el panel
                </button>
            </div>
        </form>
    </article>

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

<script>
    (() => {
        if (!window.L) {
            return;
        }

        const mapContainer = document.getElementById('panel-offer-map');
        const latInput = document.getElementById('panel-offer-lat');
        const lonInput = document.getElementById('panel-offer-lon');

        if (!mapContainer || !latInput || !lonInput) {
            return;
        }

        const parsedLat = Number.parseFloat(latInput.value);
        const parsedLon = Number.parseFloat(lonInput.value);
        const hasInitialCoordinates = Number.isFinite(parsedLat) && Number.isFinite(parsedLon);
        const defaultCenter = hasInitialCoordinates ? [parsedLat, parsedLon] : [-34.6037, -58.3816];

        const map = window.L.map(mapContainer).setView(defaultCenter, hasInitialCoordinates ? 14 : 12);
        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const marker = window.L.marker(defaultCenter, {
            draggable: true,
        }).addTo(map);

        const syncFields = (latLng) => {
            latInput.value = Number(latLng.lat).toFixed(6);
            lonInput.value = Number(latLng.lng).toFixed(6);
        };

        if (hasInitialCoordinates) {
            syncFields({ lat: parsedLat, lng: parsedLon });
        }

        marker.on('dragend', () => {
            syncFields(marker.getLatLng());
        });

        map.on('click', (event) => {
            marker.setLatLng(event.latlng);
            syncFields(event.latlng);
        });

        window.setTimeout(() => map.invalidateSize(), 180);
        window.addEventListener('resize', () => map.invalidateSize());
    })();
</script>
