<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$flashOld = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$prefillOld = is_array($prefillOld ?? null) ? $prefillOld : [];
$old = $flashOld !== [] ? $flashOld : $prefillOld;
$selectedRole = in_array(($old['role'] ?? 'user'), ['user', 'business'], true) ? (string) $old['role'] : 'user';

// Coordenadas por defecto: Ciudadela, Buenos Aires
$defaultLat = is_numeric($old['address_lat'] ?? null) ? (float) $old['address_lat'] : -34.6416;
$defaultLon = is_numeric($old['address_lon'] ?? null) ? (float) $old['address_lon'] : -58.5430;
?>

<style>
    /* Previene el parpadeo de Alpine.js al cargar la página */
    [x-cloak] { display: none !important; }
    
    /* Scrollbar personalizada para el modal */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 10px; }
</style>

<section
    x-data="registerFlow(<?= htmlspecialchars(json_encode([
        'role' => $selectedRole,
        'lat' => $defaultLat,
        'lon' => $defaultLon,
        'startOpen' => $selectedRole === 'business',
    ], JSON_THROW_ON_ERROR), ENT_QUOTES, 'UTF-8') ?>)"
    class="max-w-xl mx-auto rounded-[2rem] border border-gray-100 bg-white shadow-2xl shadow-gray-200/50 overflow-hidden relative"
>
    <!-- Header decorativo -->
    <div class="bg-gradient-to-br from-red-600 to-rose-700 px-6 pt-8 pb-12 text-center rounded-b-[2.5rem] relative">
        <div class="absolute inset-0 overflow-hidden rounded-b-[2.5rem]">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute top-12 -left-12 w-32 h-32 bg-rose-400/20 rounded-full blur-xl"></div>
        </div>
        <div class="relative z-10 text-white">
            <p class="text-xs uppercase tracking-[0.3em] text-red-100 mb-2 font-semibold">Registro</p>
            <h2 class="text-3xl font-bold mb-2">Crear cuenta</h2>
            <p class="text-red-100 text-sm">Únete para publicar y gestionar tus ofertas</p>
        </div>
    </div>

    <!-- Contenedor del formulario -->
    <div class="px-6 pb-8 pt-0 -mt-6 relative z-20">
        <div class="bg-white rounded-3xl shadow-lg border border-gray-50 p-6">
            <?php if (($formErrors['general'] ?? null) !== null) : ?>
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-medium"><?= htmlspecialchars((string) $formErrors['general'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form action="/register" method="post" class="space-y-5">
                <!-- Campos ocultos de draft -->
                <input type="hidden" name="draft_category" value="<?= htmlspecialchars((string) ($old['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="draft_title" value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="draft_location" value="<?= htmlspecialchars((string) ($old['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="draft_whatsapp" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="draft_description" value="<?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="draft_image_url" value="<?= htmlspecialchars((string) ($old['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                
                <!-- Input de rol mapeado al toggle -->
                <input type="hidden" name="role" :value="isBusiness ? 'business' : 'user'">

                <!-- Toggle Tipo de Cuenta (Usuario / Negocio) -->
                <div class="group relative flex items-center justify-between rounded-2xl border border-gray-200 bg-gray-50 p-4 transition-colors hover:border-red-200 cursor-pointer" @click="isBusiness = !isBusiness">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl transition-colors" :class="isBusiness ? 'bg-red-100 text-red-600' : 'bg-gray-200 text-gray-500'">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-gray-900">Soy un Negocio / Local</span>
                            <span class="block text-xs text-gray-500">Habilita opciones comerciales</span>
                        </div>
                    </div>
                    <button type="button" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2" :class="isBusiness ? 'bg-red-600' : 'bg-gray-300'" role="switch" :aria-checked="isBusiness">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="isBusiness ? 'translate-x-5' : 'translate-x-0'"></span>
                    </button>
                </div>

                <!-- Email -->
                <div>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Correo electrónico" class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition-all focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10">
                    </div>
                    <?php if (($formErrors['email'] ?? null) !== null) : ?><span class="mt-1.5 block text-xs font-medium text-rose-500 pl-2"><?= htmlspecialchars((string) $formErrors['email'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                </div>

                <!-- Contraseña -->
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input name="password" type="password" placeholder="Contraseña" class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition-all focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10">
                        </div>
                        <?php if (($formErrors['password'] ?? null) !== null) : ?><span class="mt-1.5 block text-xs font-medium text-rose-500 pl-2"><?= htmlspecialchars((string) $formErrors['password'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input name="password_confirmation" type="password" placeholder="Confirmar" class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition-all focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10">
                        </div>
                        <?php if (($formErrors['password_confirmation'] ?? null) !== null) : ?><span class="mt-1.5 block text-xs font-medium text-rose-500 pl-2"><?= htmlspecialchars((string) $formErrors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                    </div>
                </div>

                <!-- Flujo Usuario Común -->
                <div x-show="!isBusiness" x-collapse>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <input name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="WhatsApp (Ej: +54 9 11 0000 0000)" class="block w-full rounded-2xl border border-gray-200 bg-gray-50 py-3.5 pl-11 pr-4 text-sm text-gray-900 outline-none transition-all focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10">
                    </div>
                </div>

                <!-- Botones de Acción Principales -->
                <div class="pt-2">
                    <button type="submit" x-show="!isBusiness" class="w-full rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-red-600/20 transition-all hover:from-red-700 hover:to-rose-700 hover:shadow-xl hover:shadow-red-600/30 focus:outline-none focus:ring-4 focus:ring-red-600/30 active:scale-[0.98]">
                        Crear Mi Cuenta
                    </button>
                    
                    <button type="button" x-show="isBusiness" @click="openBusinessFlow()" class="w-full flex items-center justify-center gap-2 rounded-2xl bg-gray-900 py-3.5 px-4 text-sm font-bold text-white shadow-lg shadow-gray-900/20 transition-all hover:bg-black hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-gray-900/30 active:scale-[0.98]" x-cloak>
                        <span>Completar Perfil del Negocio</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Fullscreen de Flujo de Negocio -->
    <template x-teleport="body">
        <div x-show="isBusiness && modalOpen" x-transition.opacity x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-0 md:p-6">
            
            <div x-show="isBusiness && modalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 md:translate-y-4 md:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 md:translate-y-4 md:scale-95"
                 class="w-full h-full md:h-auto md:max-h-[90vh] md:max-w-3xl bg-white md:rounded-3xl shadow-2xl flex flex-col overflow-hidden relative">
                 
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white shrink-0 z-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-100 p-2 rounded-lg text-red-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 leading-tight">Perfil de Negocio</h3>
                            <p class="text-xs text-gray-500 font-medium tracking-wide uppercase">Paso <span x-text="currentStep"></span> de 4</p>
                        </div>
                    </div>
                    <button type="button" @click="modalOpen = false" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div class="h-1.5 w-full bg-gray-100 shrink-0">
                    <div class="h-full bg-red-600 transition-all duration-300 ease-out" :style="'width: ' + ((currentStep / 4) * 100) + '%'"></div>
                </div>

                <!-- Modal Body (Desplazable) -->
                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-gray-50/50">
                    
                    <!-- PASO 1: INFO BÁSICA -->
                    <div x-show="currentStep === 1" x-transition.opacity.duration.300ms class="space-y-5 max-w-xl mx-auto">
                        <div class="text-center mb-6">
                            <h4 class="text-xl font-bold text-gray-900">Datos principales</h4>
                            <p class="text-sm text-gray-500 mt-1">¿Cómo quieres que te encuentren los clientes?</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nombre visible del local</label>
                            <input :required="isBusiness" form="register-form" name="business_name" type="text" value="<?= htmlspecialchars((string) ($old['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ej: Pizzería Don Carlos" class="block w-full rounded-2xl border border-gray-200 bg-white py-3 px-4 text-gray-900 outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 shadow-sm">
                            <?php if (($formErrors['business_name'] ?? null) !== null) : ?><span class="mt-1 block text-xs text-rose-500"><?= htmlspecialchars((string) $formErrors['business_name'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">WhatsApp de contacto</label>
                            <input :required="isBusiness" form="register-form" name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="+54 9 11 0000 0000" class="block w-full rounded-2xl border border-gray-200 bg-white py-3 px-4 text-gray-900 outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 shadow-sm">
                            <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-1 block text-xs text-rose-500"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>

                    <!-- PASO 2: BIO -->
                    <div x-show="currentStep === 2" x-transition.opacity.duration.300ms class="space-y-5 max-w-xl mx-auto" x-cloak>
                        <div class="text-center mb-6">
                            <h4 class="text-xl font-bold text-gray-900">Descripción</h4>
                            <p class="text-sm text-gray-500 mt-1">Cuéntale al público qué ofreces en pocas palabras.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Biografía corta</label>
                            <textarea form="register-form" name="bio" rows="4" maxlength="280" placeholder="Somos un local dedicado a... (Máx 280 caracteres)" class="block w-full rounded-2xl border border-gray-200 bg-white py-3 px-4 text-gray-900 outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/10 shadow-sm resize-none"><?= htmlspecialchars((string) ($old['bio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                            <?php if (($formErrors['bio'] ?? null) !== null) : ?><span class="mt-1 block text-xs text-rose-500"><?= htmlspecialchars((string) $formErrors['bio'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                    </div>

                    <!-- PASO 3: DIRECCIÓN -->
                    <div x-show="currentStep === 3" x-transition.opacity.duration.300ms class="space-y-5" x-cloak>
                        <div class="text-center mb-4">
                            <h4 class="text-xl font-bold text-gray-900">Ubicación</h4>
                            <p class="text-sm text-gray-500 mt-1">Ayuda a los clientes a encontrar tu local fácilmente.</p>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2 flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Calle</label>
                                    <input @input.debounce.1000ms="updateMapFromAddress" x-model="addressData.street" :required="isBusiness" form="register-form" name="street" type="text" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                                </div>
                                <div class="w-24 shrink-0">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Número</label>
                                    <input @input.debounce.1000ms="updateMapFromAddress" x-model="addressData.street_number" :required="isBusiness" form="register-form" name="street_number" type="text" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                                </div>
                            </div>
                           <div x-data="{ 
    addressData: {
        municipality: 'Tres de Febrero',
        city: 'Ciudadela'
    },
    neighborhoods: {
        'Tres de Febrero': ['Ciudadela', 'Caseros', 'Santos Lugares', 'Villa Bosch', 'Martin Coronado'],
        'Moron': ['Morón Centro', 'Castelar', 'Haedo', 'El Palomar', 'Villa Sarmiento']
    },
    updateMapFromAddress() {
        // Tu lógica existente para actualizar el mapa
        console.log('Actualizando mapa para:', this.addressData.city, this.addressData.municipality);
    }
}">

    <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Municipio</label>
        <select 
            x-model="addressData.municipality" 
            @change="addressData.city = neighborhoods[addressData.municipality][0]; updateMapFromAddress()" 
            :required="isBusiness" 
            form="register-form" 
            name="municipality" 
            class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
        >
            <option value="Tres de Febrero">Tres de Febrero</option>
            <option value="Moron">Morón</option>
        </select>
    </div>

    <div class="mt-4">
        <label class="block text-xs font-semibold text-gray-700 mb-1">Barrio / Zona</label>
        <select 
            x-model="addressData.city" 
            @change="updateMapFromAddress" 
            :required="isBusiness" 
            form="register-form" 
            name="city" 
            class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
        >
            <template x-for="barrio in neighborhoods[addressData.municipality]" :key="barrio">
                <option :value="barrio" x-text="barrio"></option>
            </template>
        </select>
    </div>
</div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Provincia</label>
                                <select @change="updateMapFromAddress" x-model="addressData.province" :required="isBusiness" form="register-form" name="province" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                                    <option value="Buenos Aires">Buenos Aires</option>
                                </select>
                            </div>
                            
                            <!-- Código Postal Oculto -->
                            <input form="register-form" name="postal_code" type="hidden" value="<?= htmlspecialchars((string) ($old['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            
                            <div class="md:col-span-2 mt-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Pin en el mapa (Se mueve automáticamente al escribir)
                                </label>
                                <div id="register-address-map" x-ref="mapContainer" class="h-48 md:h-64 rounded-2xl border-2 border-gray-200 overflow-hidden shadow-inner z-0"></div>
                                <input x-model="lat" form="register-form" type="hidden" name="address_lat">
                                <input x-model="lon" form="register-form" type="hidden" name="address_lon">
                            </div>
                        </div>
                    </div>

                    <!-- PASO 4: REDES -->
                    <div x-show="currentStep === 4" x-transition.opacity.duration.300ms class="space-y-4 max-w-xl mx-auto" x-cloak>
                        <div class="text-center mb-6">
                            <h4 class="text-xl font-bold text-gray-900">Presencia Digital</h4>
                            <p class="text-sm text-gray-500 mt-1">Agrega enlaces y personaliza el aspecto de tu negocio.</p>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </div>
                                <input form="register-form" name="instagram_url" type="text" value="<?= htmlspecialchars((string) ($old['instagram_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Usuario de Instagram" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                </div>
                                <input form="register-form" name="facebook_url" type="text" value="<?= htmlspecialchars((string) ($old['facebook_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Facebook URL" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.63-1.36 5.17-3.41 6.81-2.04 1.63-4.81 2.4-7.44 2.1-2.53-.29-4.88-1.54-6.52-3.41-1.63-1.87-2.48-4.36-2.31-6.88.16-2.45 1.34-4.74 3.16-6.42 1.83-1.67 4.34-2.57 6.86-2.48.01 1.39-.01 2.78.01 4.17-1.12-.04-2.28.18-3.23.77-.96.59-1.63 1.55-1.87 2.64-.24 1.09-.07 2.27.46 3.22.52.95 1.39 1.63 2.43 1.94 1.03.31 2.17.22 3.14-.23.97-.45 1.73-1.25 2.08-2.26.35-1.02.39-2.14.39-3.23.02-4.62.01-9.24.01-13.85z"/></svg>
                                </div>
                                <input form="register-form" name="tiktok_url" type="text" value="<?= htmlspecialchars((string) ($old['tiktok_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="TikTok URL" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </div>
                                <input form="register-form" name="website_url" type="text" value="<?= htmlspecialchars((string) ($old['website_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Sitio Web (Opcional)" class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                            </div>
                            <div class="md:col-span-2 relative mt-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">URL del Logo (Opcional)</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input form="register-form" name="logo_url" type="text" value="<?= htmlspecialchars((string) ($old['logo_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                                </div>
                            </div>
                            <div class="md:col-span-2 relative">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">URL de Portada (Opcional)</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </div>
                                    <input form="register-form" name="cover_url" type="text" value="<?= htmlspecialchars((string) ($old['cover_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="block w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer (Fijo abajo) -->
                <div class="px-6 py-4 bg-white border-t border-gray-100 shrink-0 flex items-center justify-between gap-3">
                    <button type="button" @click="prevStep()" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900" :class="currentStep === 1 ? 'invisible' : 'visible'">
                        Atrás
                    </button>
                    
                    <button type="button" x-show="currentStep < 4" @click="nextStep()" class="rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-bold text-white shadow-md transition-all hover:bg-black active:scale-[0.98]">
                        Continuar
                    </button>
                    
                    <button type="submit" form="register-form" x-show="currentStep === 4" class="rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-600/20 transition-all hover:from-red-700 hover:to-rose-700 active:scale-[0.98]">
                        Finalizar Registro
                    </button>
                </div>
            </div>
        </div>
    </template>
    
    <script>
        document.querySelector('form').id = 'register-form';
    </script>
</section>

<script>
    function registerFlow(config) {
        return {
            isBusiness: config.role === 'business',
            modalOpen: Boolean(config.startOpen),
            currentStep: 1,
            lat: Number(config.lat).toFixed(6),
            lon: Number(config.lon).toFixed(6),
            map: null,
            marker: null,
            // Estado de la dirección para geocodificación
            addressData: {
                street: "<?= htmlspecialchars((string) ($old['street'] ?? '')) ?>",
                street_number: "<?= htmlspecialchars((string) ($old['street_number'] ?? '')) ?>",
                city: "<?= htmlspecialchars((string) ($old['city'] ?? '')) ?>",
                municipality: "<?= htmlspecialchars((string) ($old['municipality'] ?? 'Tres de Febrero')) ?>",
                province: "<?= htmlspecialchars((string) ($old['province'] ?? 'Buenos Aires')) ?>"
            },
            init() {
                this.$watch('isBusiness', (val) => {
                    if (!val) {
                        this.modalOpen = false;
                    }
                });

                this.$watch('modalOpen', (isOpen) => {
                    if (isOpen) {
                        setTimeout(() => this.initMap(), 100);
                    }
                });

                this.$watch('currentStep', (step) => {
                    if (step === 3 && this.map) {
                        setTimeout(() => this.map.invalidateSize(), 150);
                    }
                });

                if (this.modalOpen) {
                    setTimeout(() => this.initMap(), 100);
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
            async updateMapFromAddress() {
                const { street, street_number, city, municipality, province } = this.addressData;
                
                // Solo buscamos si tenemos calle y número
                if (!street || !street_number) return;

                const query = `${street} ${street_number}, ${city}, ${municipality}, ${province}, Argentina`;
                
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                    const data = await response.json();

                    if (data && data.length > 0) {
                        const newLat = parseFloat(data[0].lat);
                        const newLon = parseFloat(data[0].lon);
                        
                        this.lat = newLat.toFixed(6);
                        this.lon = newLon.toFixed(6);

                        if (this.map && this.marker) {
                            this.marker.setLatLng([newLat, newLon]);
                            this.map.flyTo([newLat, newLon], 16);
                        }
                    }
                } catch (error) {
                    console.error("Error al geocodificar:", error);
                }
            },
            initMap() {
                if (!window.L) return;

                const mapNode = this.$refs.mapContainer;
                if (!mapNode || this.map) return;

                this.map = window.L.map(mapNode).setView([Number(this.lat), Number(this.lon)], 15);
                
                window.L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                }).addTo(this.map);

                const redMarkerIcon = window.L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41],
                });
bar
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
