<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$flashOld = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$prefillOld = is_array($prefillOld ?? null) ? $prefillOld : [];
$old = $flashOld !== [] ? $flashOld : $prefillOld;
$selectedRole = in_array(($old['role'] ?? 'user'), ['user', 'business'], true) ? (string) $old['role'] : 'user';
$defaultLat = is_numeric($old['address_lat'] ?? null) ? (float) $old['address_lat'] : -34.6037;
$defaultLon = is_numeric($old['address_lon'] ?? null) ? (float) $old['address_lon'] : -58.3816;
?>
<section
    x-data="registerFlow(<?= htmlspecialchars(json_encode([
        'role' => $selectedRole,
        'lat' => $defaultLat,
        'lon' => $defaultLon,
        'startOpen' => $selectedRole === 'business',
    ], JSON_THROW_ON_ERROR), ENT_QUOTES, 'UTF-8') ?>)"
    class="max-w-3xl mx-auto rounded-[2rem] border border-red-100 bg-white p-6 md:p-8 shadow-xl shadow-red-900/10"
>
    <p class="text-sm uppercase tracking-[0.28em] text-red-600 mb-3">Registro</p>
    <h2 class="text-3xl font-semibold text-gray-900 mb-4">Crear cuenta para publicar ofertas</h2>
    <p class="text-gray-600 mb-6">Si elegís rol negocio, te guiamos en 4 pasos claros y enviamos todo junto al final.</p>

    <?php if (($formErrors['general'] ?? null) !== null) : ?>
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            <?= htmlspecialchars((string) $formErrors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="post" class="grid gap-4 md:grid-cols-2">
        <input type="hidden" name="draft_category" value="<?= htmlspecialchars((string) ($old['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="draft_title" value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="draft_location" value="<?= htmlspecialchars((string) ($old['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="draft_whatsapp" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="draft_description" value="<?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="draft_image_url" value="<?= htmlspecialchars((string) ($old['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <fieldset class="md:col-span-2">
            <legend class="block text-sm text-gray-700 mb-2">Tipo de cuenta</legend>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="cursor-pointer rounded-2xl border border-gray-200 bg-gray-50 p-4 hover:border-red-200 transition-colors">
                    <input x-model="role" type="radio" name="role" value="user" class="mr-2 accent-red-600">
                    <span class="font-semibold text-gray-900">Usuario común</span>
                    <p class="mt-1 text-sm text-gray-600">Registro rápido para explorar y publicar como particular.</p>
                </label>
                <label class="cursor-pointer rounded-2xl border border-gray-200 bg-gray-50 p-4 hover:border-red-200 transition-colors">
                    <input x-model="role" type="radio" name="role" value="business" class="mr-2 accent-red-600">
                    <span class="font-semibold text-gray-900">Negocio</span>
                    <p class="mt-1 text-sm text-gray-600">Flujo guiado con datos comerciales, dirección y branding.</p>
                </label>
            </div>
        </fieldset>

        <label class="block md:col-span-2">
            <span class="block text-sm text-gray-700 mb-2">Correo electrónico</span>
            <input name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="contacto@local.com" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            <?php if (($formErrors['email'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['email'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>

        <label class="block">
            <span class="block text-sm text-gray-700 mb-2">Contraseña</span>
            <input name="password" type="password" placeholder="Mínimo 8 caracteres" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            <?php if (($formErrors['password'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['password'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block">
            <span class="block text-sm text-gray-700 mb-2">Confirmar contraseña</span>
            <input name="password_confirmation" type="password" placeholder="Repite tu contraseña" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            <?php if (($formErrors['password_confirmation'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>

        <div class="md:col-span-2" x-show="role === 'business'" x-cloak>
            <button type="button" @click="openBusinessFlow()" class="w-full rounded-2xl bg-gray-900 px-4 py-3 text-sm font-bold text-white hover:bg-black">Abrir flujo guiado para negocio (4 pasos)</button>
            <p class="mt-2 text-xs text-gray-500">Paso 1: Cuenta · Paso 2: Datos comerciales · Paso 3: Dirección/mapa · Paso 4: Redes y branding.</p>
        </div>

        <div class="md:col-span-2" x-show="role === 'user'">
            <label class="block">
                <span class="block text-sm text-gray-700 mb-2">WhatsApp (opcional)</span>
                <input name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="+54 9 11 0000 0000" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-gray-900 outline-none transition-all focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20">
            </label>
        </div>

        <button type="submit" x-show="role === 'user'" class="md:col-span-2 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-3 font-semibold text-white shadow-lg shadow-red-600/20 transition-all hover:from-red-700 hover:to-rose-700 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-red-600/30">Crear cuenta</button>

        <div x-show="role === 'business'" x-cloak class="md:col-span-2 rounded-2xl border border-red-100 bg-red-50/60 px-4 py-3 text-sm text-red-700">
            El envío final para negocio ocurre dentro del paso 4 con un único POST a <strong>/register</strong>.
        </div>

        <div
            x-show="role === 'business' && modalOpen"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-black/60 p-0"
        >
            <div class="min-h-screen bg-white p-4 md:p-8">
                <div class="mx-auto max-w-5xl">
                    <div class="mb-6 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-red-500">Registro negocio</p>
                            <h3 class="text-2xl font-black text-gray-900">Flujo multipaso</h3>
                        </div>
                        <button type="button" @click="modalOpen = false" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-bold text-gray-600 hover:border-red-300 hover:text-red-600">Cerrar</button>
                    </div>

                    <div class="mb-6 grid gap-2 md:grid-cols-4">
                        <template x-for="(label, index) in steps" :key="index">
                            <div :class="currentStep === index + 1 ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-gray-200'" class="rounded-xl border px-3 py-2 text-xs font-bold uppercase tracking-wider">Paso <span x-text="index + 1"></span>: <span x-text="label"></span></div>
                        </template>
                    </div>

                    <div x-show="currentStep === 1" class="grid gap-4 md:grid-cols-2">
                        <label class="block md:col-span-2">
                            <span class="block text-sm text-gray-700 mb-2">Nombre visible del local</span>
                            <input x-bind:required="role === 'business'" name="business_name" type="text" value="<?= htmlspecialchars((string) ($old['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['business_name'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['business_name'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block md:col-span-2">
                            <span class="block text-sm text-gray-700 mb-2">WhatsApp</span>
                            <input x-bind:required="role === 'business'" name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                    </div>

                    <div x-show="currentStep === 2" class="grid gap-4 md:grid-cols-2">
                        <label class="block md:col-span-2">
                            <span class="block text-sm text-gray-700 mb-2">Bio corta</span>
                            <textarea name="bio" rows="3" maxlength="280" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3"><?= htmlspecialchars((string) ($old['bio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                            <?php if (($formErrors['bio'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['bio'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                    </div>

                    <div x-show="currentStep === 3" class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Calle</span>
                            <input x-bind:required="role === 'business'" name="street" type="text" value="<?= htmlspecialchars((string) ($old['street'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['street'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['street'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Número</span>
                            <input x-bind:required="role === 'business'" name="street_number" type="text" value="<?= htmlspecialchars((string) ($old['street_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['street_number'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['street_number'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Código postal</span>
                            <input x-bind:required="role === 'business'" name="postal_code" type="text" value="<?= htmlspecialchars((string) ($old['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['postal_code'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['postal_code'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Ciudad</span>
                            <input x-bind:required="role === 'business'" name="city" type="text" value="<?= htmlspecialchars((string) ($old['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['city'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['city'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Municipio</span>
                            <input x-bind:required="role === 'business'" name="municipality" type="text" value="<?= htmlspecialchars((string) ($old['municipality'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['municipality'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['municipality'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Provincia</span>
                            <input x-bind:required="role === 'business'" name="province" type="text" value="<?= htmlspecialchars((string) ($old['province'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['province'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['province'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <div class="md:col-span-2">
                            <p class="block text-sm text-gray-700 mb-2">Ubicación exacta en el mapa</p>
                            <div id="register-address-map" x-ref="mapContainer" class="h-72 rounded-2xl border border-gray-200 overflow-hidden"></div>
                            <input x-model="lat" type="hidden" name="address_lat" value="<?= htmlspecialchars((string) ($old['address_lat'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <input x-model="lon" type="hidden" name="address_lon" value="<?= htmlspecialchars((string) ($old['address_lon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <p class="mt-2 text-xs text-gray-500">Latitud: <span x-text="lat"></span> · Longitud: <span x-text="lon"></span></p>
                            <?php if (($formErrors['address_lat'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['address_lat'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                            <?php if (($formErrors['address_lon'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['address_lon'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div x-show="currentStep === 4" class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Instagram</span>
                            <input name="instagram_url" type="text" value="<?= htmlspecialchars((string) ($old['instagram_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="instagram.com/tu_negocio" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['instagram_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['instagram_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Facebook</span>
                            <input name="facebook_url" type="text" value="<?= htmlspecialchars((string) ($old['facebook_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="facebook.com/tu_negocio" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['facebook_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['facebook_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">TikTok</span>
                            <input name="tiktok_url" type="text" value="<?= htmlspecialchars((string) ($old['tiktok_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="tiktok.com/@tu_negocio" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['tiktok_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['tiktok_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block">
                            <span class="block text-sm text-gray-700 mb-2">Web</span>
                            <input name="website_url" type="text" value="<?= htmlspecialchars((string) ($old['website_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="www.tu-negocio.com" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['website_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['website_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block md:col-span-2">
                            <span class="block text-sm text-gray-700 mb-2">Logo (URL)</span>
                            <input name="logo_url" type="text" value="<?= htmlspecialchars((string) ($old['logo_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['logo_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['logo_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                        <label class="block md:col-span-2">
                            <span class="block text-sm text-gray-700 mb-2">Portada (URL)</span>
                            <input name="cover_url" type="text" value="<?= htmlspecialchars((string) ($old['cover_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                            <?php if (($formErrors['cover_url'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-600"><?= htmlspecialchars((string) $formErrors['cover_url'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </label>
                    </div>

                    <div class="mt-8 flex flex-wrap justify-between gap-2 border-t border-gray-100 pt-4">
                        <button type="button" @click="prevStep()" x-show="currentStep > 1" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-700">Anterior</button>
                        <div class="ml-auto flex gap-2">
                            <button type="button" @click="nextStep()" x-show="currentStep < 4" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white">Siguiente</button>
                            <button type="submit" x-show="currentStep === 4" class="rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-2 text-sm font-bold text-white">Crear cuenta negocio</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    function registerFlow(config) {
        return {
            role: config.role || 'user',
            modalOpen: Boolean(config.startOpen),
            currentStep: 1,
            steps: ['Cuenta', 'Datos comerciales', 'Dirección y mapa', 'Redes y branding'],
            lat: Number(config.lat || -34.6037).toFixed(6),
            lon: Number(config.lon || -58.3816).toFixed(6),
            map: null,
            marker: null,
            init() {
                this.$watch('role', (nextRole) => {
                    if (nextRole !== 'business') {
                        this.modalOpen = false;
                    }
                });

                this.$watch('modalOpen', (isOpen) => {
                    if (isOpen) {
                        this.initMap();
                    }
                });

                this.$watch('currentStep', (step) => {
                    if (step === 3 && this.map) {
                        setTimeout(() => this.map.invalidateSize(), 150);
                    }
                });

                if (this.modalOpen) {
                    this.initMap();
                }
            },
            openBusinessFlow() {
                this.currentStep = 1;
                this.modalOpen = true;
            },
            nextStep() {
                this.currentStep = Math.min(4, this.currentStep + 1);
            },
            prevStep() {
                this.currentStep = Math.max(1, this.currentStep - 1);
            },
            initMap() {
                if (!window.L || this.map) {
                    return;
                }

                const mapNode = this.$refs.mapContainer;
                if (!mapNode) {
                    return;
                }

                this.map = window.L.map(mapNode).setView([Number(this.lat), Number(this.lon)], 13);
                window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                const redMarkerIcon = window.L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41],
                });

                this.marker = window.L.marker([Number(this.lat), Number(this.lon)], {
                    draggable: true,
                    icon: redMarkerIcon,
                }).addTo(this.map);

                this.marker.on('dragend', () => {
                    const position = this.marker.getLatLng();
                    this.lat = Number(position.lat).toFixed(6);
                    this.lon = Number(position.lng).toFixed(6);
                });

                this.map.on('click', (event) => {
                    this.marker.setLatLng(event.latlng);
                    this.lat = Number(event.latlng.lat).toFixed(6);
                    this.lon = Number(event.latlng.lng).toFixed(6);
                });

                setTimeout(() => this.map.invalidateSize(), 200);
            },
        };
    }
</script>
