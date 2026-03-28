<?php

declare(strict_types=1);

$account = is_array($account ?? null) ? $account : [];
$savedLocationsRaw = json_decode((string) ($account['saved_locations'] ?? ''), true);
$savedLocationsText = is_array($savedLocationsRaw) ? implode("\n", array_map('strval', $savedLocationsRaw)) : '';
$locationCatalog = is_array($locationCatalog ?? null) ? $locationCatalog : ['provinces' => ['Buenos Aires'], 'municipalities' => ['Tres de Febrero' => ['Ciudadela']]];
$defaultLat = is_numeric($account['address_lat'] ?? null) ? (float) $account['address_lat'] : -34.6037;
$defaultLon = is_numeric($account['address_lon'] ?? null) ? (float) $account['address_lon'] : -58.3816;
?>

<section class="max-w-5xl mx-auto rounded-[2rem] border border-red-100 bg-white p-6 md:p-8 shadow-xl shadow-red-900/10 space-y-6" x-data='userProfileMap(<?= htmlspecialchars(json_encode([
    'lat' => $defaultLat,
    'lon' => $defaultLon,
], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>)'>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.28em] text-red-600">Panel usuario</p>
            <h2 class="text-3xl font-semibold text-gray-900">Perfil y ubicaciones guardadas</h2>
        </div>
    </div>

    <form class="grid gap-4 md:grid-cols-2" action="/panel/perfil" method="post">
        <label class="block md:col-span-2"><span class="block text-sm text-gray-700 mb-2">Dirección</span><input id="user-address-line" name="address" type="text" value="<?= htmlspecialchars(trim((string) ($account['street'] ?? '') . ' ' . (string) ($account['street_number'] ?? '')), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"></label>
        <input id="user-street-hidden" type="hidden" name="street" value="<?= htmlspecialchars((string) ($account['street'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input id="user-street-number-hidden" type="hidden" name="street_number" value="<?= htmlspecialchars((string) ($account['street_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <?php $municipalities = is_array($locationCatalog['municipalities'] ?? null) ? $locationCatalog['municipalities'] : []; ?>
        <label class="block">
            <span class="block text-sm text-gray-700 mb-2">Municipio</span>
            <select name="municipality" x-model="municipality" @change="syncNeighborhoods" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                <?php foreach (array_keys($municipalities) as $municipality) : ?>
                    <option value="<?= htmlspecialchars((string) $municipality, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($account['municipality'] ?? '') === (string) $municipality ? 'selected' : '' ?>><?= htmlspecialchars((string) $municipality, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="block">
            <span class="block text-sm text-gray-700 mb-2">Barrio / zona</span>
            <select name="city" x-model="city" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                <template x-for="zone in neighborhoods" :key="zone">
                    <option :value="zone" x-text="zone"></option>
                </template>
            </select>
        </label>
        <label class="block">
            <span class="block text-sm text-gray-700 mb-2">Provincia</span>
            <select name="province" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                <?php foreach (($locationCatalog['provinces'] ?? ['Buenos Aires']) as $province) : ?>
                    <option value="<?= htmlspecialchars((string) $province, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($account['province'] ?? '') === (string) $province ? 'selected' : '' ?>><?= htmlspecialchars((string) $province, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <input type="hidden" name="postal_code" value="">

        <div class="md:col-span-2">
            <p class="mb-2 text-sm font-semibold text-gray-700">Ubicación en el mapa</p>
            <div id="user-profile-map" class="h-72 rounded-2xl border border-gray-200"></div>
            <input id="user-profile-lat" type="hidden" name="address_lat" x-model="lat">
            <input id="user-profile-lon" type="hidden" name="address_lon" x-model="lon">
        </div>

        <label class="md:col-span-2 block"><span class="block text-sm text-gray-700 mb-2">Zonas guardadas (una por línea)</span><textarea name="saved_locations" rows="4" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"><?= htmlspecialchars($savedLocationsText, ENT_QUOTES, 'UTF-8') ?></textarea></label>

        <label class="block"><span class="block text-sm text-gray-700 mb-2">Nueva contraseña</span><input name="new_password" type="password" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"></label>
        <label class="block"><span class="block text-sm text-gray-700 mb-2">Repetir nueva contraseña</span><input name="new_password_confirmation" type="password" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"></label>

        <button type="submit" class="md:col-span-2 rounded-2xl bg-red-600 px-4 py-3 font-semibold text-white">Guardar perfil</button>
    </form>
</section>

<script>
function userProfileMap(config) {
    return {
        lat: Number(config.lat).toFixed(6),
        lon: Number(config.lon).toFixed(6),
        municipality: <?= json_encode((string) ($account['municipality'] ?? array_key_first($municipalities) ?? '')) ?>,
        city: <?= json_encode((string) ($account['city'] ?? '')) ?>,
        neighborhoodsByMunicipality: <?= json_encode($municipalities, JSON_UNESCAPED_UNICODE) ?>,
        neighborhoods: [],
        syncNeighborhoods() {
            const list = this.neighborhoodsByMunicipality[this.municipality] || [];
            this.neighborhoods = list;
            if (!list.includes(this.city)) {
                this.city = list[0] || '';
            }
        },
        init() {
            const addressLine = document.getElementById('user-address-line');
            const streetHidden = document.getElementById('user-street-hidden');
            const numberHidden = document.getElementById('user-street-number-hidden');
            const syncAddress = () => {
                if (!addressLine || !streetHidden || !numberHidden) {
                    return;
                }
                const rawValue = addressLine.value.trim();
                const match = rawValue.match(/^(.*?)(?:\s+(\d+\w*))?$/);
                streetHidden.value = (match?.[1] || '').trim();
                numberHidden.value = (match?.[2] || '').trim();
            };
            addressLine?.addEventListener('input', syncAddress);
            syncAddress();
            this.syncNeighborhoods();
            if (!window.L) return;
            const map = window.L.map('user-profile-map').setView([Number(this.lat), Number(this.lon)], 15);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            const marker = window.L.marker([Number(this.lat), Number(this.lon)], { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                this.lat = Number(pos.lat).toFixed(6);
                this.lon = Number(pos.lng).toFixed(6);
            });
            map.on('click', (event) => {
                marker.setLatLng(event.latlng);
                this.lat = Number(event.latlng.lat).toFixed(6);
                this.lon = Number(event.latlng.lng).toFixed(6);
            });
            setTimeout(() => map.invalidateSize(), 120);
        },
    };
}
</script>
