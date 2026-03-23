<?php

declare(strict_types=1);

?>
<header class="bg-gradient-to-br from-red-600 to-red-800 text-white pt-16 pb-20 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block py-1 px-3 rounded-full bg-red-500/50 text-sm font-medium mb-4 backdrop-blur-sm border border-red-400/30">
            📍 Descubrí tu zona
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
            Ofertas cerca tuyo que te hacen <span class="text-yellow-300">ahorrar HOY</span>
        </h1>
        <p class="text-lg md:text-xl text-red-100 mb-8 max-w-2xl mx-auto font-light">
            Encontrá descuentos reales, contactá directo al vendedor por WhatsApp y asegurá tu precio antes de que el reloj llegue a cero.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="/ofertas" class="bg-yellow-400 text-yellow-900 px-8 py-4 rounded-xl font-bold text-lg hover:bg-yellow-300 transition shadow-lg flex items-center justify-center gap-2">
                <i data-lucide="search" class="w-5 h-5"></i>
                Ver descuentos ahora
            </a>
        </div>
    </div>
</header>

<div class="max-w-6xl mx-auto px-4 -mt-10 relative z-10">
    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 flex flex-col md:flex-row justify-around gap-6 text-center border border-gray-100">
        <?php foreach ($stats as $index => $stat) : ?>
            <div class="flex flex-col items-center">
                <div class="<?= htmlspecialchars($stat['containerClass'], ENT_QUOTES, 'UTF-8') ?> p-3 rounded-full mb-3">
                    <i data-lucide="<?= htmlspecialchars($stat['icon'], ENT_QUOTES, 'UTF-8') ?>" class="w-7 h-7"></i>
                </div>
                <div class="text-3xl font-black text-gray-800"><?= htmlspecialchars($stat['value'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-sm text-gray-500 font-medium"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php if ($index < count($stats) - 1) : ?>
                <div class="hidden md:block w-px bg-gray-100"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-16">
    <section class="mb-16 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-8">¿Cómo funciona?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($howItWorks as $step) : ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="<?= htmlspecialchars($step['badgeClass'], ENT_QUOTES, 'UTF-8') ?> w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4"><?= (int) $step['step'] ?></div>
                    <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="text-gray-600 text-sm"><?= htmlspecialchars($step['description'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include __DIR__ . '/ofertas.php'; ?>

    <section class="mb-16">
        <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100">
            <div class="p-6 md:p-8 md:w-2/3">
                <h2 class="text-2xl md:text-3xl font-bold mb-2">📍 Ofertas activas en el mapa</h2>
                <p class="text-gray-600 mb-6">Encontrá los negocios que están listos para atenderte ahora mismo a pocas cuadras de tu ubicación.</p>
                <a href="/mapa" class="inline-flex items-center gap-2 bg-gray-900 text-white px-5 py-3 rounded-xl font-semibold hover:bg-gray-800 transition">
                    <i data-lucide="map" class="w-5 h-5"></i>
                    Abrir mapa interactivo
                </a>
            </div>
            <div class="h-80 w-full bg-gray-200">
                <div class="h-full w-full" id="home-map-preview"></div>
            </div>
        </div>
    </section>

    <section id="publicar" class="mb-10">
        <div class="bg-gray-900 text-white rounded-3xl overflow-hidden shadow-2xl relative">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-8 md:p-12 flex flex-col justify-center">
                    <div class="inline-block bg-red-600/20 text-red-400 font-semibold px-3 py-1 rounded-full text-sm w-max mb-6">
                        Para Comerciantes
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
                        Conseguí más clientes <span class="text-yellow-400">hoy mismo</span>
                    </h2>
                    <p class="text-gray-300 text-lg mb-8">
                        Publicá tu oferta GRATIS. Llega a miles de vecinos en tu zona y empezá a recibir consultas directo en tu WhatsApp.
                    </p>

                    <ul class="space-y-4 mb-8">
                        <?php foreach ($merchantBenefits as $benefit) : ?>
                            <li class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="text-green-400 mt-0.5 w-5 h-5"></i>
                                <span class="text-gray-200"><?= htmlspecialchars($benefit, ENT_QUOTES, 'UTF-8') ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="bg-white text-gray-800 p-6 md:p-8 m-4 md:m-8 rounded-2xl">
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <h3 class="text-xl font-bold">Crear nueva oferta</h3>
                        <span class="text-xs bg-red-50 text-red-600 font-semibold px-3 py-1 rounded-full">Paso visual</span>
                    </div>

                    <form id="offerForm" class="space-y-4" action="/register" method="get">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del negocio</label>
                                <input required type="text" id="inputBusiness" name="negocio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" placeholder="Ej: Ferretería Juan">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                <select id="inputCategory" name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"></select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex justify-between">
                                <span>¿Qué ofreces? (La oferta estrella)</span>
                                <span id="charCount" class="text-xs text-gray-500">0/40</span>
                            </label>
                            <input required type="text" id="inputOffer" name="oferta" maxlength="40" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all text-red-600 font-medium" placeholder="Ej: 2x1 en Pinturas Alba hoy">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación (Barrio/Ciudad)</label>
                                <input required type="text" id="inputLocation" name="ubicacion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" placeholder="Ej: Ciudadela">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de WhatsApp</label>
                                <input required type="tel" id="inputWhatsapp" name="whatsapp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all" placeholder="54911...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto atractiva (Atrae 3x más clicks)</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors relative cursor-pointer">
                                <input type="file" id="inputImage" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div id="imagePreviewContainer" class="hidden h-32 w-full rounded-lg overflow-hidden">
                                    <img id="imagePreview" src="" alt="Vista previa de la imagen seleccionada" class="w-full h-full object-cover">
                                </div>
                                <div id="imagePlaceholder" class="flex flex-col items-center justify-center py-4">
                                    <i data-lucide="camera" class="text-gray-400 mb-2 w-8 h-8"></i>
                                    <span class="text-sm font-medium text-gray-600">Toca para subir foto</span>
                                    <span class="text-xs text-gray-400 mt-1">PNG, JPG hasta 5MB</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-md flex justify-center items-center gap-2 mt-4">
                            <i data-lucide="flame" class="w-5 h-5"></i>
                            <span id="submitText">🔥 Publicar mi oferta gratis por 24hs</span>
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-3">Al publicar aceptas nuestros términos y condiciones y continúas al registro del comercio.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
