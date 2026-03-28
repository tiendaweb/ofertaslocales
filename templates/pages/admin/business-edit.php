<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$flashOld = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$account = is_array($account ?? null) ? $account : [];
$old = $flashOld !== [] ? $flashOld : $account;
$defaultLat = is_numeric($old['address_lat'] ?? null) ? (float) $old['address_lat'] : -34.6037;
$defaultLon = is_numeric($old['address_lon'] ?? null) ? (float) $old['address_lon'] : -58.3816;
$locationCatalog = is_array($locationCatalog ?? null) ? $locationCatalog : ['provinces' => ['Buenos Aires'], 'municipalities' => ['Tres de Febrero' => ['Ciudadela']]];
$municipalities = is_array($locationCatalog['municipalities'] ?? null) ? $locationCatalog['municipalities'] : ['Tres de Febrero' => ['Ciudadela']];
?>

<section class="max-w-5xl mx-auto rounded-[2rem] border border-red-100 bg-white p-6 md:p-8 shadow-xl shadow-red-900/10 space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-red-600">Panel negocio</p>
            <h2 class="text-3xl font-semibold text-gray-900">Editar perfil comercial</h2>
        </div>
        <a href="/panel" class="inline-flex items-center gap-2 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Volver al panel
        </a>
    </div>

    <form class="grid gap-4 md:grid-cols-2" action="/panel/negocio/editar" method="post" enctype="multipart/form-data">
        <label class="block md:col-span-2">
            <span class="block text-sm text-gray-700 mb-2">Nombre del negocio</span>
            <input name="business_name" required type="text" value="<?= htmlspecialchars((string) ($old['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            <?php if (($formErrors['business_name'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['business_name'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block md:col-span-2">
            <span class="block text-sm text-gray-700 mb-2">WhatsApp</span>
            <input name="whatsapp" required type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>

        <label class="block md:col-span-2">
            <span class="block text-sm text-gray-700 mb-2">Bio corta</span>
            <textarea name="bio" rows="3" maxlength="280" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?= htmlspecialchars((string) ($old['bio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (($formErrors['bio'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['bio'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>

        <label class="block"><span class="block text-sm text-gray-700 mb-2">Instagram</span><input name="instagram_url" type="text" value="<?= htmlspecialchars((string) ($old['instagram_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php if (($formErrors['instagram_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['instagram_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Facebook</span><input name="facebook_url" type="text" value="<?= htmlspecialchars((string) ($old['facebook_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php if (($formErrors['facebook_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['facebook_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">TikTok</span><input name="tiktok_url" type="text" value="<?= htmlspecialchars((string) ($old['tiktok_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php if (($formErrors['tiktok_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['tiktok_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Sitio web</span><input name="website_url" type="text" value="<?= htmlspecialchars((string) ($old['website_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php if (($formErrors['website_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['website_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block md:col-span-2"><span class="block text-sm text-gray-700 mb-2">Logo (URL)</span><input name="logo_url" type="text" value="<?= htmlspecialchars((string) ($old['logo_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><input type="file" name="logo_image" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"><?php if (($formErrors['logo_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['logo_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block md:col-span-2"><span class="block text-sm text-gray-700 mb-2">Portada (URL)</span><input name="cover_url" type="text" value="<?= htmlspecialchars((string) ($old['cover_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"><?php if (($formErrors['cover_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['cover_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>

        <h3 class="md:col-span-2 text-lg font-semibold text-gray-900 mt-2">Dirección y ubicación</h3>

        <label class="block md:col-span-2"><span class="block text-sm text-gray-700 mb-2">Dirección</span><input id="business-address-line" name="address" required type="text" value="<?= htmlspecialchars(trim((string) ($old['street'] ?? '') . ' ' . (string) ($old['street_number'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Av. Rivadavia 1234" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php if (($formErrors['street'] ?? null) !== null || ($formErrors['street_number'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600">La dirección es obligatoria.</span><?php endif; ?></label>
        <input id="business-street-hidden" type="hidden" name="street" value="<?= htmlspecialchars((string) ($old['street'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input id="business-street-number-hidden" type="hidden" name="street_number" value="<?= htmlspecialchars((string) ($old['street_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="postal_code" value="">
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Municipio</span><select id="business-municipality-select" name="municipality" required class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php foreach (array_keys($municipalities) as $municipality): ?><option value="<?= htmlspecialchars((string) $municipality, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($old['municipality'] ?? '') === (string) $municipality ? 'selected' : '' ?>><?= htmlspecialchars((string) $municipality, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select><?php if (($formErrors['municipality'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['municipality'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Barrio / zona</span><select id="business-city-select" name="city" required class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"></select><?php if (($formErrors['city'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['city'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Provincia</span><select name="province" required class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"><?php foreach (($locationCatalog['provinces'] ?? ['Buenos Aires']) as $province): ?><option value="<?= htmlspecialchars((string) $province, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($old['province'] ?? '') === (string) $province ? 'selected' : '' ?>><?= htmlspecialchars((string) $province, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select><?php if (($formErrors['province'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['province'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></label>

        <div class="md:col-span-2">
            <p class="block text-sm text-gray-700 mb-2">Ubicación exacta en el mapa (arrastra el marcador)</p>
            <div id="business-address-map" class="h-72 rounded-2xl border border-gray-200 overflow-hidden"></div>
            <input id="business-address-lat" type="hidden" name="address_lat" value="<?= htmlspecialchars((string) ($old['address_lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <input id="business-address-lon" type="hidden" name="address_lon" value="<?= htmlspecialchars((string) ($old['address_lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            <p class="mt-2 text-xs text-gray-500">Latitud: <span id="business-address-lat-text"><?= htmlspecialchars((string) ($old['address_lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span> · Longitud: <span id="business-address-lon-text"><?= htmlspecialchars((string) ($old['address_lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></p>
            <?php if (($formErrors['address_lat'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['address_lat'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            <?php if (($formErrors['address_lon'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['address_lon'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </div>

        <button type="submit" class="md:col-span-2 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-3 font-semibold text-white shadow-lg shadow-red-600/20 transition-all hover:from-red-700 hover:to-rose-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-red-600/30">Guardar perfil comercial</button>
    </form>
</section>

<script>
    (() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        if (!window.L) {
            return;
        }

        const mapElement = document.getElementById('business-address-map');
        const latInput = document.getElementById('business-address-lat');
        const lonInput = document.getElementById('business-address-lon');
        const latText = document.getElementById('business-address-lat-text');
        const lonText = document.getElementById('business-address-lon-text');
        const municipalitySelect = document.getElementById('business-municipality-select');
        const citySelect = document.getElementById('business-city-select');
        const neighborhoodsByMunicipality = <?= json_encode($municipalities, JSON_UNESCAPED_UNICODE) ?>;

        if (!mapElement || !latInput || !lonInput || !latText || !lonText) {
            return;
        }
        const addressLineInput = document.getElementById('business-address-line');
        const streetHiddenInput = document.getElementById('business-street-hidden');
        const streetNumberHiddenInput = document.getElementById('business-street-number-hidden');
        const syncAddressFields = () => {
            if (!addressLineInput || !streetHiddenInput || !streetNumberHiddenInput) {
                return;
            }
            const rawValue = addressLineInput.value.trim();
            const match = rawValue.match(/^(.*?)(?:\s+(\d+\w*))?$/);
            streetHiddenInput.value = (match?.[1] || '').trim();
            streetNumberHiddenInput.value = (match?.[2] || '').trim();
        };
        addressLineInput?.addEventListener('input', syncAddressFields);
        syncAddressFields();

        const syncCities = () => {
            if (!municipalitySelect || !citySelect) {
                return;
            }

            const options = neighborhoodsByMunicipality[municipalitySelect.value] || [];
            const previous = citySelect.value;
            citySelect.innerHTML = options.map((city) => `<option value=\"${city}\">${city}</option>`).join('');
            if (options.includes(previous)) {
                citySelect.value = previous;
            }
        };
        syncCities();
        municipalitySelect?.addEventListener('change', syncCities);

        const startLat = Number.parseFloat(latInput.value || '<?= $defaultLat ?>');
        const startLon = Number.parseFloat(lonInput.value || '<?= $defaultLon ?>');
        const centerLat = Number.isFinite(startLat) ? startLat : <?= $defaultLat ?>;
        const centerLon = Number.isFinite(startLon) ? startLon : <?= $defaultLon ?>;

        const map = window.L.map(mapElement).setView([centerLat, centerLon], 14);
        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
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

        const marker = window.L.marker([centerLat, centerLon], {
            draggable: true,
            icon: redMarkerIcon,
        }).addTo(map);

        const syncCoordinateFields = (lat, lon) => {
            const formattedLat = Number(lat).toFixed(6);
            const formattedLon = Number(lon).toFixed(6);
            latInput.value = formattedLat;
            lonInput.value = formattedLon;
            latText.textContent = formattedLat;
            lonText.textContent = formattedLon;
        };

        syncCoordinateFields(centerLat, centerLon);

        marker.on('dragend', () => {
            const position = marker.getLatLng();
            syncCoordinateFields(position.lat, position.lng);
        });

        map.on('click', (event) => {
            marker.setLatLng(event.latlng);
            syncCoordinateFields(event.latlng.lat, event.latlng.lng);
        });

        window.setTimeout(() => map.invalidateSize(), 180);
    })();
</script>
