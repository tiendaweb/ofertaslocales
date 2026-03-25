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
$flashOld = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$offerDraft = is_array($offerDraft ?? null) ? $offerDraft : [];
$old = $flashOld !== [] ? $flashOld : [
    'category' => (string) ($offerDraft['category'] ?? ''),
    'title' => (string) ($offerDraft['title'] ?? ''),
    'description' => (string) ($offerDraft['description'] ?? ''),
    'location' => (string) ($offerDraft['location'] ?? ''),
    'whatsapp' => (string) ($offerDraft['whatsapp'] ?? ''),
];
$defaultWhatsapp = (string) ($old['whatsapp'] ?? ($currentUser['whatsapp'] ?? ''));
$defaultExpiresAt = (string) ($old['expires_at'] ?? gmdate('Y-m-d\TH:i', strtotime('+24 hours')));
$approvedCategories = is_array($approvedCategories ?? null) ? $approvedCategories : [];
$businessProfile = is_array($businessProfile ?? null) ? $businessProfile : [];
$businessAddressParts = array_filter([
    trim(((string) ($businessProfile['street'] ?? '')) . ' ' . ((string) ($businessProfile['street_number'] ?? ''))),
    trim((string) ($businessProfile['city'] ?? '')),
    trim((string) ($businessProfile['province'] ?? '')),
], static fn (string $value): bool => $value !== '');
$businessAddress = implode(', ', $businessAddressParts);
$businessLat = $businessProfile['address_lat'] ?? null;
$businessLon = $businessProfile['address_lon'] ?? null;
?>

<section class="max-w-7xl mx-auto space-y-8 text-black">
    <?php if (($currentUser['role'] ?? null) === 'business' && isset($currentUser['id'])) : ?>
        <article class="rounded-3xl border border-red-100 bg-white p-4 md:p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-red-500">Perfil público</p>
                    <p class="text-sm text-gray-600">Gestiona tus ofertas y revisa cómo ve tu negocio la audiencia.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="/negocios/<?= (int) $currentUser['id'] ?>" class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-bold text-red-700 transition hover:bg-red-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-red-500/30">
                        <i data-lucide="store" class="h-4 w-4"></i>
                        Mi Negocio
                    </a>
                    <a href="/panel/negocio/editar" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-gray-500/20">
                        <i data-lucide="pencil" class="h-4 w-4"></i>
                        Editar Negocio
                    </a>
                </div>
            </div>
        </article>
    <?php endif; ?>
    
    <article class="rounded-3xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 md:p-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-1 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-red-500">
                    <i data-lucide="bar-chart-2" class="h-4 w-4"></i> Mi panel
                </p>
                <h2 class="text-3xl font-extrabold tracking-tight text-black">Resumen de ofertas</h2>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-bold text-red-600">
                <i data-lucide="layers" class="h-4 w-4"></i> <?= count($offers) ?> registradas
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 transition hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <i data-lucide="check-circle" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Activas</p>
                    <p class="text-3xl font-black text-black"><?= $kpis['active'] ?></p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 transition hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                    <i data-lucide="clock" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Pendientes</p>
                    <p class="text-3xl font-black text-black"><?= $kpis['pending'] ?></p>
                </div>
            </div>
            <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 transition hover:shadow-md">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-600">
                    <i data-lucide="archive-x" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Vencidas</p>
                    <p class="text-3xl font-black text-black"><?= $kpis['expired'] ?></p>
                </div>
            </div>
        </div>
    </article>

    <article class="rounded-3xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 md:p-8">
        <div class="mb-8">
            <p class="mb-1 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-red-500">
                <i data-lucide="plus-square" class="h-4 w-4"></i> Publicar
            </p>
            <h2 class="text-3xl font-extrabold tracking-tight text-black">Crear nueva oferta</h2>
            <p class="mt-2 text-sm text-gray-500">Carga una oferta rápidamente usando la dirección registrada en tu negocio.</p>
        </div>

        <?php if ($formErrors !== []) : ?>
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-start gap-3">
                <i data-lucide="alert-circle" class="h-5 w-5 shrink-0 text-red-600 mt-0.5"></i>
                <div>
                    <p class="font-bold mb-2">Revisa los siguientes campos:</p>
                    <ul class="list-disc pl-5 space-y-1 text-red-700">
                        <?php foreach ($formErrors as $error) : ?>
                            <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <form action="/panel/ofertas" method="post" enctype="multipart/form-data" class="grid gap-6 md:grid-cols-2">
            <?php 
                // Estilo base para inputs repetitivos
                $inputClass = "w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-black transition-all focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10 placeholder-gray-400";
            ?>
            
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-category">
                    <i data-lucide="tag" class="h-4 w-4 text-gray-400"></i> Categoría
                </label>
                <select id="panel-offer-category" name="category" required class="<?= $inputClass ?>">
                    <option value="">Selecciona una categoría aprobada</option>
                    <?php foreach ($approvedCategories as $categoryOption) : ?>
                        <option value="<?= htmlspecialchars((string) $categoryOption, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($old['category'] ?? '') === (string) $categoryOption ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $categoryOption, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="requested_category" value="<?= htmlspecialchars((string) ($old['requested_category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?> mt-2" placeholder="¿No existe? Propón una nueva categoría">
            </div>
            
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-title">
                    <i data-lucide="type" class="h-4 w-4 text-gray-400"></i> Título de la oferta
                </label>
                <input id="panel-offer-title" name="title" required value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?>" placeholder="Ej: Combo desayuno con 25% OFF">
            </div>
            
            <div class="md:col-span-2">
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-description">
                    <i data-lucide="align-left" class="h-4 w-4 text-gray-400"></i> Descripción
                </label>
                <textarea id="panel-offer-description" name="description" rows="3" required class="<?= $inputClass ?> resize-none" placeholder="Describe la promoción, stock y condiciones..."><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-whatsapp">
                    <i data-lucide="message-circle" class="h-4 w-4 text-gray-400"></i> WhatsApp
                </label>
                <input id="panel-offer-whatsapp" name="whatsapp" required value="<?= htmlspecialchars($defaultWhatsapp, ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?>" placeholder="54911...">
            </div>
            
            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Ubicación registrada del negocio</p>
                <p class="mt-2 text-sm text-gray-700"><?= htmlspecialchars($businessAddress !== '' ? $businessAddress : 'Sin dirección registrada', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="mt-1 text-xs text-gray-500">Lat: <?= htmlspecialchars((string) $businessLat, ENT_QUOTES, 'UTF-8') ?> | Lon: <?= htmlspecialchars((string) $businessLon, ENT_QUOTES, 'UTF-8') ?></p>
                <input type="hidden" name="location" value="<?= htmlspecialchars($businessAddress, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-expires-at">
                    <i data-lucide="calendar-clock" class="h-4 w-4 text-gray-400"></i> Vence el
                </label>
                <input id="panel-offer-expires-at" type="datetime-local" name="expires_at" required value="<?= htmlspecialchars($defaultExpiresAt, ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?>">
            </div>
            
            <div>
                <label class="mb-2 flex items-center gap-2 text-sm font-bold text-gray-700" for="panel-offer-image">
                    <i data-lucide="image" class="h-4 w-4 text-gray-400"></i> Imagen (JPG/PNG/WEBP)
                </label>
                <input id="panel-offer-image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-800 transition-all file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-black file:px-4 file:py-2 file:text-xs file:font-bold file:text-white file:transition-colors hover:file:bg-red-600 focus:border-red-500 focus:outline-none focus:ring-4 focus:ring-red-500/10">
            </div>
            
            <input type="hidden" name="lat" value="<?= htmlspecialchars((string) $businessLat, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="lon" value="<?= htmlspecialchars((string) $businessLon, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="md:col-span-2 mt-4">
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-black px-8 py-4 text-sm font-bold text-white shadow-lg shadow-black/10 transition-all hover:-translate-y-0.5 hover:bg-red-600 hover:shadow-red-600/20">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Publicar oferta
                </button>
            </div>
        </form>
    </article>

    <article class="rounded-3xl bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 md:p-8">
        <div class="mb-6">
            <p class="mb-1 flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-red-500">
                <i data-lucide="settings-2" class="h-4 w-4"></i> Gestión
            </p>
            <h3 class="text-3xl font-extrabold tracking-tight text-black">Lista de ofertas</h3>
        </div>

        <div class="space-y-4">
            <?php foreach ($offers as $offer) : ?>
                <?php
                $status = (string) ($offer['status'] ?? 'pending');
                $statusLabel = $statusLabels[$status] ?? 'Pendiente';
                $renewLabel = $status === 'expired' ? 'Renovar' : 'Duplicar';
                
                // Colores dinámicos según el estado para el badge
                $badgeColors = match($status) {
                    'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'expired' => 'bg-gray-50 text-gray-700 border-gray-200',
                    default => 'bg-red-50 text-red-700 border-red-200'
                };
                ?>
                <div class="group overflow-hidden rounded-2xl border border-gray-100 bg-white transition-all hover:border-red-200 hover:shadow-md">
                    <div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between bg-gray-50/30 group-hover:bg-red-50/10 transition-colors">
                        <div class="min-w-0">
                            <p class="mb-1 text-xs font-bold uppercase tracking-widest text-red-500 flex items-center gap-1">
                                <i data-lucide="tag" class="h-3 w-3"></i> <?= htmlspecialchars((string) $offer['category'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <h4 class="truncate text-xl font-bold text-black"><?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                            <p class="mt-1 flex items-center gap-3 text-sm font-medium text-gray-500">
                                <span class="flex items-center gap-1"><i data-lucide="calendar-clock" class="h-3 w-3"></i> Vence: <?= htmlspecialchars((string) $offer['expires_at'], ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>
                        <span class="inline-flex w-max items-center justify-center rounded-full border px-3 py-1 text-xs font-bold <?= $badgeColors ?>">
                            <?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <div class="border-t border-gray-100 p-4 bg-white">
                        <div class="mb-4 overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                            <img
                                src="<?= htmlspecialchars((string) ($offer['image_url'] ?: 'https://placehold.co/1200x600/f3f4f6/6b7280?text=Sin+imagen'), ENT_QUOTES, 'UTF-8') ?>"
                                alt="Imagen de <?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?>"
                                class="h-48 w-full object-cover"
                                loading="lazy"
                            >
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <details class="group/edit">
                                <summary class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-gray-50 px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-100 hover:text-black list-none [&::-webkit-details-marker]:hidden">
                                    <i data-lucide="pencil" class="h-4 w-4"></i> Editar
                                </summary>
                                
                                <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    <form class="grid gap-3 md:grid-cols-2" action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                                        <input type="hidden" name="operation" value="editar">
                                        
                                        <div>
                                            <label class="mb-1 block text-xs font-bold text-gray-500">Categoría</label>
                                            <select name="category" class="<?= $inputClass ?> py-2" required>
                                                <?php foreach ($approvedCategories as $categoryOption) : ?>
                                                    <option value="<?= htmlspecialchars((string) $categoryOption, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $offer['category'] === (string) $categoryOption ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars((string) $categoryOption, ENT_QUOTES, 'UTF-8') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-bold text-gray-500">Título</label>
                                            <input name="title" value="<?= htmlspecialchars((string) $offer['title'], ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?> py-2" required>
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-bold text-gray-500">WhatsApp</label>
                                            <input name="whatsapp" value="<?= htmlspecialchars((string) $offer['whatsapp'], ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?> py-2" required>
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-bold text-gray-500">Ubicación</label>
                                            <input name="location" value="<?= htmlspecialchars((string) $offer['location'], ENT_QUOTES, 'UTF-8') ?>" class="<?= $inputClass ?> py-2" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-1 block text-xs font-bold text-gray-500">Descripción</label>
                                            <textarea name="description" rows="2" class="<?= $inputClass ?> py-2 resize-none" required><?= htmlspecialchars((string) $offer['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                        </div>
                                        <div class="md:col-span-2 mt-2">
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-black px-4 py-2 text-sm font-bold text-white transition hover:bg-red-600">
                                                <i data-lucide="save" class="h-4 w-4"></i> Guardar cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </details>

                            <form action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                                <input type="hidden" name="operation" value="estado">
                                <input type="hidden" name="status" value="<?= $status === 'active' ? 'pending' : 'active' ?>">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-100 hover:text-black">
                                    <i data-lucide="power" class="h-4 w-4"></i> Cambiar estado
                                </button>
                            </form>

                            <form action="/panel/ofertas/<?= (int) $offer['id'] ?>" method="post">
                                <input type="hidden" name="operation" value="duplicar_renovar">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-100 hover:text-black">
                                    <i data-lucide="copy" class="h-4 w-4"></i> <?= $renewLabel ?>
                                </button>
                            </form>

                            <form action="/panel/ofertas/<?= (int) $offer['id'] ?>/eliminar" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar esta oferta de forma permanente?');" class="ml-auto">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg text-gray-400 px-4 py-2 text-sm font-bold transition hover:bg-red-50 hover:text-red-600">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($offers === []) : ?>
                <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-12 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-500 mb-4">
                        <i data-lucide="folder-open" class="h-8 w-8"></i>
                    </div>
                    <h4 class="text-lg font-bold text-black">No hay ofertas</h4>
                    <p class="mt-1 text-sm text-gray-500">Aún no tienes ofertas cargadas en el panel.</p>
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>

<script>
    // Inicializar iconos de Lucide (asegúrate de incluir la librería lucide en el layout general).
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Mapa Leaflet
    (() => {
        const mapContainer = document.getElementById('panel-offer-map');
        const latInput = document.getElementById('panel-offer-lat');
        const lonInput = document.getElementById('panel-offer-lon');
        const locationSearchInput = document.getElementById('panel-offer-location-search');
        const locationSearchButton = document.getElementById('panel-offer-location-search-button');
        const useMyLocationButton = document.getElementById('panel-offer-use-my-location');
        const locationFeedback = document.getElementById('panel-offer-location-feedback');

        if (!mapContainer || !latInput || !lonInput) {
            return;
        }

        const setLocationFeedback = (message, type = 'neutral') => {
            if (!locationFeedback) {
                return;
            }

            locationFeedback.textContent = message;
            locationFeedback.classList.remove('text-gray-500', 'text-red-600', 'text-emerald-600');
            if (type === 'error') {
                locationFeedback.classList.add('text-red-600');
                return;
            }

            if (type === 'success') {
                locationFeedback.classList.add('text-emerald-600');
                return;
            }

            locationFeedback.classList.add('text-gray-500');
        };

        const syncFields = (latLng) => {
            latInput.value = Number(latLng.lat).toFixed(6);
            lonInput.value = Number(latLng.lng).toFixed(6);
        };

        const geocodeLocation = async (query) => {
            const endpoint = new URL('https://nominatim.openstreetmap.org/search');
            endpoint.searchParams.set('q', query);
            endpoint.searchParams.set('format', 'jsonv2');
            endpoint.searchParams.set('limit', '1');
            endpoint.searchParams.set('addressdetails', '1');
            endpoint.searchParams.set('accept-language', 'es');

            const response = await fetch(endpoint.toString(), {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('No fue posible buscar la dirección. Inténtalo nuevamente.');
            }

            const data = await response.json();
            if (!Array.isArray(data) || data.length === 0) {
                throw new Error('No encontramos resultados para esa dirección.');
            }

            const result = data[0];
            const lat = Number(result.lat);
            const lon = Number(result.lon);
            if (!Number.isFinite(lat) || !Number.isFinite(lon)) {
                throw new Error('La dirección encontrada no tiene coordenadas válidas.');
            }

            return {
                lat,
                lon,
                label: result.display_name || query,
            };
        };

        const initLeafletMap = () => {
            if (!window.L) {
                setLocationFeedback('El mapa todavía se está cargando. Esperá un instante.', 'error');
                return null;
            }

            const parsedLat = Number.parseFloat(latInput.value);
            const parsedLon = Number.parseFloat(lonInput.value);
            const hasInitialCoordinates = Number.isFinite(parsedLat) && Number.isFinite(parsedLon);
            const defaultCenter = hasInitialCoordinates ? [parsedLat, parsedLon] : [-34.6037, -58.3816];

            const map = window.L.map(mapContainer).setView(defaultCenter, hasInitialCoordinates ? 14 : 12);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);
            const redMarkerIcon = window.L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41],
            });

            const marker = window.L.marker(defaultCenter, {
                draggable: true,
                icon: redMarkerIcon,
            }).addTo(map);

            return { map, marker, hasInitialCoordinates, parsedLat, parsedLon };
        };

        const bootMap = () => {
            const mapContext = initLeafletMap();
            if (!mapContext) {
                return;
            }

            const { map, marker, hasInitialCoordinates, parsedLat, parsedLon } = mapContext;

            const updateMarkerAndCoordinates = (lat, lon, zoom = 15) => {
                marker.setLatLng([lat, lon]);
                syncFields({ lat, lng: lon });
                map.setView([lat, lon], zoom, { animate: true });
            };

            const handleSearchLocation = async () => {
                if (!locationSearchInput) {
                    return;
                }

                const query = locationSearchInput.value.trim();
                if (query === '') {
                    setLocationFeedback('Escribe una dirección antes de buscar.', 'error');
                    return;
                }

                locationSearchButton?.setAttribute('disabled', 'disabled');
                locationSearchButton?.classList.add('opacity-70', 'cursor-not-allowed');
                setLocationFeedback('Buscando dirección en el mapa...', 'neutral');

                try {
                    const result = await geocodeLocation(query);
                    updateMarkerAndCoordinates(result.lat, result.lon);
                    setLocationFeedback(`Dirección encontrada: ${result.label}`, 'success');
                } catch (error) {
                    setLocationFeedback(error instanceof Error ? error.message : 'No pudimos completar la búsqueda.', 'error');
                } finally {
                    locationSearchButton?.removeAttribute('disabled');
                    locationSearchButton?.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            };

            if (hasInitialCoordinates) {
                syncFields({ lat: parsedLat, lng: parsedLon });
                setLocationFeedback('Se cargaron las coordenadas actuales de tu oferta.', 'neutral');
            } else {
                setLocationFeedback('Define la ubicación con el mapa, búsqueda o geolocalización.', 'neutral');
            }

            marker.on('dragend', () => {
                syncFields(marker.getLatLng());
                setLocationFeedback('Ubicación actualizada moviendo el marcador.', 'success');
            });

            map.on('click', (event) => {
                marker.setLatLng(event.latlng);
                syncFields(event.latlng);
                setLocationFeedback('Ubicación actualizada desde el mapa.', 'success');
            });

            locationSearchButton?.addEventListener('click', () => {
                void handleSearchLocation();
            });
            locationSearchInput?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    void handleSearchLocation();
                }
            });

            useMyLocationButton?.addEventListener('click', () => {
                if (!navigator.geolocation) {
                    setLocationFeedback('Tu navegador no permite geolocalización.', 'error');
                    return;
                }

                setLocationFeedback('Detectando tu ubicación actual...', 'neutral');
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const { latitude, longitude } = position.coords;
                        updateMarkerAndCoordinates(latitude, longitude);
                        setLocationFeedback('Ubicación detectada correctamente con GPS del navegador.', 'success');
                    },
                    (error) => {
                        let message = 'No pudimos acceder a tu ubicación.';
                        if (error.code === 1) {
                            message = 'Permiso de ubicación denegado. Habilítalo en tu navegador.';
                        } else if (error.code === 2) {
                            message = 'No se pudo determinar tu ubicación actual.';
                        } else if (error.code === 3) {
                            message = 'La solicitud de ubicación tardó demasiado tiempo.';
                        }

                        setLocationFeedback(message, 'error');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 12000,
                        maximumAge: 0,
                    }
                );
            });

            window.setTimeout(() => map.invalidateSize(), 180);
            window.addEventListener('resize', () => map.invalidateSize());
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    map.invalidateSize();
                }
            });
        };

        const waitForLeaflet = (attempt = 0) => {
            if (window.L) {
                bootMap();
                return;
            }

            if (attempt >= 30) {
                setLocationFeedback('No se pudo cargar el mapa. Recargá la página e intentá nuevamente.', 'error');
                return;
            }

            window.setTimeout(() => waitForLeaflet(attempt + 1), 120);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => waitForLeaflet(), { once: true });
        } else {
            waitForLeaflet();
        }
    })();
</script>
