<?php

declare(strict_types=1);
?>
<?php $activeTab = in_array(($activeTab ?? 'moderacion'), ['moderacion', 'textos', 'logo', 'aplicacion', 'categorias', 'seo', 'usuarios', 'ajustes'], true) ? $activeTab : 'moderacion'; ?>
<section x-data="{ tab: '<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>' }" class="space-y-5 pb-28">
    <div class="rounded-[2.2rem] border border-red-100 bg-white/95 p-4 shadow-[0_24px_80px_rgba(239,68,68,0.12)] md:p-6">
        <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-red-500">Panel administrador</p>
                <h2 class="mt-2 text-3xl font-black text-gray-900">Control de OfertasLocales</h2>
                <p class="mt-1 text-sm text-gray-600">Todo tu flujo de revisión y configuración en una sola vista.</p>
            </div>
            <div class="grid grid-cols-2 gap-2 md:w-auto">
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-red-500">Pendientes</p>
                    <p class="text-2xl font-black text-red-700"><?= (int) $pendingOffers ?></p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Negocios</p>
                    <p class="text-2xl font-black text-gray-800"><?= (int) $businessCount ?></p>
                </div>
            </div>
        </div>

        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-8">
            <?php foreach ([
                'moderacion' => ['label' => 'Moderación', 'icon' => 'shield-check'],
                'textos' => ['label' => 'Textos', 'icon' => 'type'],
                'logo' => ['label' => 'Logo', 'icon' => 'image'],
                'aplicacion' => ['label' => 'Aplicación', 'icon' => 'smartphone'],
                'categorias' => ['label' => 'Categorías', 'icon' => 'tags'],
                'seo' => ['label' => 'SEO', 'icon' => 'search'],
                'usuarios' => ['label' => 'Usuarios', 'icon' => 'users'],
                'ajustes' => ['label' => 'Ajustes', 'icon' => 'settings'],
            ] as $tabKey => $tabItem) : ?>
                <button
                    type="button"
                    @click="tab = '<?= $tabKey ?>'"
                    :class="tab === '<?= $tabKey ?>' ? 'border-red-500 bg-red-600 text-white shadow-lg shadow-red-500/20' : 'border-red-100 bg-white text-gray-600 hover:border-red-300 hover:text-red-600'"
                    class="flex items-center justify-center gap-2 rounded-2xl border px-4 py-3 text-sm font-bold transition"
                >
                    <i data-lucide="<?= htmlspecialchars($tabItem['icon'], ENT_QUOTES, 'UTF-8') ?>" class="h-4 w-4"></i>
                    <span><?= htmlspecialchars($tabItem['label'], ENT_QUOTES, 'UTF-8') ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div x-show="tab === 'moderacion'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-xl font-black text-gray-900">Ofertas para revisar</h3>
            <div class="grid gap-4">
                <?php foreach ($offers as $offer) : ?>
                    <div class="rounded-3xl border border-red-100 bg-red-50/60 p-4 md:p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex min-w-0 flex-1 items-start gap-4">
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-red-100 bg-white">
                                    <?php if (trim((string) ($offer['image_url'] ?? '')) !== '') : ?>
                                        <img src="<?= htmlspecialchars((string) $offer['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Miniatura de oferta" class="h-full w-full object-cover">
                                    <?php else : ?>
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-red-100 to-rose-100 text-[10px] font-bold uppercase tracking-widest text-red-500">Sin imagen</div>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-red-500">
                                        <span><?= htmlspecialchars((string) $offer['business_name']) ?></span>
                                        <span class="h-1 w-1 rounded-full bg-red-300"></span>
                                        <span><?= htmlspecialchars((string) $offer['category']) ?></span>
                                    </div>
                                    <h4 class="truncate text-lg font-bold text-gray-900"><?= htmlspecialchars((string) $offer['title']) ?></h4>
                                    <p class="line-clamp-2 text-sm text-gray-600"><?= htmlspecialchars((string) $offer['description']) ?></p>
                                </div>
                            </div>
                            <form action="/admin/offers/<?= (int) $offer['id'] ?>/status" method="post" class="flex shrink-0 items-center gap-2 rounded-2xl border border-red-100 bg-white p-2">
                                <button name="status" value="active" title="Aprobar oferta" class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-600 text-white hover:bg-red-500 transition-colors">
                                    <i data-lucide="check" class="h-5 w-5"></i>
                                </button>
                                <button name="status" value="pending" title="Volver a pendiente" class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700 hover:bg-amber-200 transition-colors">
                                    <i data-lucide="clock-4" class="h-5 w-5"></i>
                                </button>
                                <button name="status" value="rejected" title="Rechazar oferta" class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                    <i data-lucide="x" class="h-5 w-5"></i>
                                </button>
                                <button name="status" value="expired" title="Marcar expirada" class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                    <i data-lucide="archive-x" class="h-5 w-5"></i>
                                </button>
                            </form>
                            <form action="/admin/offers/<?= (int) $offer['id'] ?>/delete" method="post" class="ml-2">
                                <button type="submit" title="Eliminar oferta" class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-600 text-white hover:bg-rose-500 transition-colors">
                                    <i data-lucide="trash-2" class="h-5 w-5"></i>
                                </button>
                            </form>
                        </div>
                        <details class="mt-3 rounded-2xl border border-red-100 bg-white p-3">
                            <summary class="cursor-pointer text-sm font-bold text-red-600">Editar oferta</summary>
                            <form action="/admin/offers/<?= (int) $offer['id'] ?>/update" method="post" class="mt-3 grid gap-3 md:grid-cols-2">
                                <label class="text-xs font-semibold text-gray-600">Categoría
                                    <input name="category" value="<?= htmlspecialchars((string) ($offer['category'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required class="mt-1 w-full rounded-xl border border-red-100 bg-red-50/30 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                                </label>
                                <label class="text-xs font-semibold text-gray-600">Título
                                    <input name="title" value="<?= htmlspecialchars((string) ($offer['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required class="mt-1 w-full rounded-xl border border-red-100 bg-red-50/30 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                                </label>
                                <label class="text-xs font-semibold text-gray-600">WhatsApp
                                    <input name="whatsapp" value="<?= htmlspecialchars((string) ($offer['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="54911XXXXXXXX" required class="mt-1 w-full rounded-xl border border-red-100 bg-red-50/30 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                                </label>
                                <label class="text-xs font-semibold text-gray-600">Ubicación
                                    <input name="location" value="<?= htmlspecialchars((string) ($offer['location'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required class="mt-1 w-full rounded-xl border border-red-100 bg-red-50/30 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                                </label>
                                <label class="md:col-span-2 text-xs font-semibold text-gray-600">Descripción
                                    <textarea name="description" rows="2" required class="mt-1 w-full rounded-xl border border-red-100 bg-red-50/30 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none"><?= htmlspecialchars((string) ($offer['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                </label>
                                <div class="md:col-span-2">
                                    <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white hover:bg-red-600">Guardar cambios</button>
                                </div>
                            </form>
                        </details>
                    </div>
                <?php endforeach; ?>
                <?php if ($offers === []) : ?>
                    <div class="rounded-2xl border border-dashed border-red-200 bg-red-50 px-4 py-5 text-sm text-red-700">No hay ofertas pendientes por revisar.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-xl font-black text-gray-900">Modo de aprobación</h3>
            <form action="/admin/approval-mode" method="post" class="grid gap-4 md:grid-cols-2">
                <label class="relative flex cursor-pointer flex-col rounded-3xl border border-red-200 bg-red-50 p-5">
                    <input type="radio" name="approval_mode" value="manual" <?= ($settings['approval_mode'] ?? 'manual') === 'manual' ? 'checked' : '' ?> class="absolute right-5 top-5 h-4 w-4 text-red-600 focus:ring-red-300">
                    <span class="text-base font-bold text-gray-900">Revisión manual</span>
                    <span class="mt-1 text-xs font-semibold uppercase tracking-wider text-red-500">Más control</span>
                    <p class="mt-3 text-sm text-gray-600">Las ofertas quedan pendientes hasta aprobarlas desde este panel.</p>
                </label>

                <label class="relative flex cursor-pointer flex-col rounded-3xl border border-gray-200 bg-gray-50 p-5">
                    <input type="radio" name="approval_mode" value="auto" <?= ($settings['approval_mode'] ?? 'manual') === 'auto' ? 'checked' : '' ?> class="absolute right-5 top-5 h-4 w-4 text-red-600 focus:ring-red-300">
                    <span class="text-base font-bold text-gray-900">Aprobación automática</span>
                    <span class="mt-1 text-xs font-semibold uppercase tracking-wider text-gray-500">Más velocidad</span>
                    <p class="mt-3 text-sm text-gray-600">La oferta se publica en el acto cuando el negocio la crea.</p>
                </label>
                <button type="submit" class="md:col-span-2 rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Guardar configuración</button>
            </form>
        </div>
    </div>

    <div x-show="tab === 'textos'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-xl font-black text-gray-900">Textos principales del sitio</h3>
            <form action="/admin/settings" method="post" class="grid gap-4">
                <?php
                $labels = [
                    'site_name' => 'Nombre del Sitio',
                    'hero_title' => 'Título Principal del Hero',
                    'hero_description' => 'Descripción de Bienvenida',
                    'hero_primary_cta' => 'Texto del Botón Principal',
                    'footer_tagline' => 'Frase del Pie de Página',
                ];
                foreach ($labels as $key => $label) : ?>
                    <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                        <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500"><?= $label ?></span>
                        <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars((string) ($settings[$key] ?? '')) ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                    </label>
                <?php endforeach; ?>
                <button type="submit" class="rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Aplicar cambios</button>
            </form>
        </div>
    </div>

    <div x-show="tab === 'logo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-xl font-black text-gray-900">Logo general del sitio</h3>
            <form action="/admin/settings" method="post" class="grid gap-4">
                <div class="rounded-2xl border border-red-100 bg-red-50/40 p-4">
                    <input id="admin-site-logo-file" type="file" accept="image/png,image/jpeg,image/webp" class="mb-3 block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-red-500">
                    <div class="grid gap-3 md:grid-cols-[180px_1fr] md:items-center">
                        <canvas id="admin-site-logo-crop-preview" width="180" height="180" class="h-[180px] w-[180px] rounded-2xl border border-red-100 bg-white object-cover"></canvas>
                        <div>
                            <label for="admin-site-logo-zoom" class="mb-2 block text-xs font-semibold text-gray-600">Zoom del recorte</label>
                            <input id="admin-site-logo-zoom" type="range" min="1" max="3" step="0.01" value="1" class="w-full accent-red-600">
                            <?php if (trim((string) ($settings['site_logo_url'] ?? '')) !== '') : ?>
                                <p class="mt-2 text-xs text-gray-500">Logo actual: <?= htmlspecialchars((string) $settings['site_logo_url'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <input id="admin-site-logo-image" type="hidden" name="site_logo_image" value="">
                    <input type="hidden" name="site_logo_url" value="<?= htmlspecialchars((string) ($settings['site_logo_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button type="submit" class="rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Guardar logo</button>
            </form>
        </div>
    </div>

    <div x-show="tab === 'aplicacion'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-5 text-xl font-black text-gray-900">Aplicación (PWA)</h3>
            <form action="/admin/settings" method="post" class="grid gap-4 md:grid-cols-2">
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Nombre de la app</span>
                    <input type="text" name="app_name" value="<?= htmlspecialchars((string) ($settings['app_name'] ?? 'OfertasLocales'), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Nombre corto</span>
                    <input type="text" name="short_name" value="<?= htmlspecialchars((string) ($settings['short_name'] ?? 'Ofertas'), ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Color de tema</span>
                    <input type="text" name="theme_color" value="<?= htmlspecialchars((string) ($settings['theme_color'] ?? '#dc2626'), ENT_QUOTES, 'UTF-8') ?>" placeholder="#dc2626" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Color de fondo</span>
                    <input type="text" name="background_color" value="<?= htmlspecialchars((string) ($settings['background_color'] ?? '#ffffff'), ENT_QUOTES, 'UTF-8') ?>" placeholder="#ffffff" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">URL de inicio</span>
                    <input type="text" name="start_url" value="<?= htmlspecialchars((string) ($settings['start_url'] ?? '/'), ENT_QUOTES, 'UTF-8') ?>" placeholder="/" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Modo de visualización</span>
                    <select name="display" class="w-full rounded-xl border border-red-100 bg-white p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                        <?php foreach (['standalone', 'fullscreen', 'minimal-ui', 'browser'] as $displayOption) : ?>
                            <option value="<?= $displayOption ?>" <?= (($settings['display'] ?? 'standalone') === $displayOption) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($displayOption, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Ícono 192x192</span>
                    <input type="text" name="icon_192" value="<?= htmlspecialchars((string) ($settings['icon_192'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="/uploads/icon-192.png o https://..." class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <label class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <span class="mb-1 block text-[11px] font-bold uppercase tracking-widest text-red-500">Ícono 512x512</span>
                    <input type="text" name="icon_512" value="<?= htmlspecialchars((string) ($settings['icon_512'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="/uploads/icon-512.png o https://..." class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                </label>
                <button type="submit" class="md:col-span-2 rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Guardar configuración de la app</button>
            </form>
        </div>
    </div>

    <div x-show="tab === 'categorias'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-xl font-black text-gray-900">Gestionar categorías</h3>
            <form action="/admin/categories" method="post" class="flex flex-col gap-3 md:flex-row">
                <input type="text" name="name" required placeholder="Nueva categoría" class="flex-1 rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <button type="submit" class="rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Agregar y aprobar</button>
            </form>
        </div>
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h4 class="mb-3 text-lg font-bold text-gray-900">Listado completo de categorías</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-red-100">
                    <thead class="bg-red-50/60">
                        <tr class="text-left text-[11px] font-bold uppercase tracking-widest text-red-500">
                            <th class="px-3 py-3">Nombre</th>
                            <th class="px-3 py-3">Estado</th>
                            <th class="px-3 py-3">Creada</th>
                            <th class="px-3 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-100 bg-white">
                        <?php foreach (($categories ?? []) as $category) : ?>
                            <tr class="align-top">
                                <td class="px-3 py-3">
                                    <form action="/admin/categories/<?= (int) $category['id'] ?>/update" method="post" class="flex items-center gap-2">
                                        <input type="text" name="name" value="<?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-full rounded-lg border border-red-100 bg-red-50/40 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none" required>
                                        <button type="submit" class="rounded-lg bg-white px-3 py-2 text-xs font-bold text-red-600 ring-1 ring-red-200 hover:bg-red-50">Guardar</button>
                                    </form>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider <?= (string) $category['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : ((string) $category['status'] === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-200 text-gray-700') ?>">
                                        <?= htmlspecialchars((string) $category['status'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-500"><?= htmlspecialchars((string) $category['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <?php if ((string) $category['status'] === 'pending') : ?>
                                            <form action="/admin/categories/<?= (int) $category['id'] ?>/status" method="post">
                                                <input type="hidden" name="status" value="approved">
                                                <button class="rounded-lg bg-red-600 px-3 py-2 text-xs font-bold text-white">Aprobar</button>
                                            </form>
                                            <form action="/admin/categories/<?= (int) $category['id'] ?>/status" method="post">
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="rounded-lg bg-gray-200 px-3 py-2 text-xs font-bold text-gray-700">Rechazar</button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="/admin/categories/<?= (int) $category['id'] ?>/delete" method="post">
                                            <button class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-bold text-white">Borrar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (($categories ?? []) === []) : ?>
                <p class="mt-4 text-sm text-gray-500">No hay categorías cargadas todavía.</p>
            <?php endif; ?>
        </div>
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h4 class="mb-3 text-lg font-bold text-gray-900">Jerarquía geográfica (Provincia > Ciudad > Municipio)</h4>
            <p class="mb-3 text-sm text-gray-600">Gestiona la jerarquía para alta/edición. Puedes agregar nuevas entradas cuando lo necesites.</p>
            <form action="/admin/settings" method="post" class="space-y-3">
                <textarea name="location_catalog_json" rows="10" class="w-full rounded-xl border border-red-100 bg-red-50/40 px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none"><?= htmlspecialchars((string) ($settings['location_catalog_json'] ?? json_encode([
                    'provinces' => ['Buenos Aires'],
                    'municipalities' => ['Tres de Febrero' => ['Ciudadela', 'Caseros', 'Santos Lugares', 'Villa Bosch', 'Martín Coronado']],
                    'hierarchy' => [
                        'Buenos Aires' => [
                            'Ciudadela' => ['Tres de Febrero'],
                            'Caseros' => ['Tres de Febrero'],
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)), ENT_QUOTES, 'UTF-8') ?></textarea>
                <button type="submit" class="rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Guardar catálogo de zonas</button>
            </form>
        </div>
    </div>

    <div x-show="tab === 'seo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="text-xl font-black text-gray-900">SEO y metadatos</h3>
            <p class="mb-5 mt-1 text-sm text-gray-600">Configurá cómo se muestran las páginas en buscadores y redes.</p>
            <div class="grid gap-4 xl:grid-cols-2">
                <?php foreach ($seoPages as $seo) : ?>
                    <form action="/admin/seo/<?= htmlspecialchars((string) $seo['page_name']) ?>" method="post" class="flex flex-col rounded-3xl border border-red-100 bg-red-50/60 p-4">
                        <span class="mb-3 text-[11px] font-bold uppercase tracking-widest text-red-500">Página: /<?= htmlspecialchars((string) $seo['page_name']) ?></span>
                        <label class="mb-3">
                            <span class="mb-1 block text-xs font-semibold text-gray-700">Meta título</span>
                            <input type="text" name="title" value="<?= htmlspecialchars((string) $seo['title']) ?>" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                        </label>
                        <label>
                            <span class="mb-1 block text-xs font-semibold text-gray-700">Meta descripción</span>
                            <textarea name="meta_description" rows="3" class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none"><?= htmlspecialchars((string) $seo['meta_description']) ?></textarea>
                        </label>
                        <label class="mt-3">
                            <span class="mb-1 block text-xs font-semibold text-gray-700">Imagen OpenGraph (URL)</span>
                            <input type="url" name="og_image" value="<?= htmlspecialchars((string) ($seo['og_image'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="https://..." class="w-full rounded-xl border border-red-100 bg-white px-3 py-2 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                            <span class="mt-1 block text-[11px] text-gray-500">Se conserva la imagen actual si no se modifica este campo.</span>
                        </label>
                        <button class="mt-4 rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-500">Guardar metadatos</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div x-show="tab === 'usuarios'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-xl font-black text-gray-900">Crear usuario</h3>
            <form id="admin-create-user-form" action="/admin/users" method="post" class="grid gap-3 md:grid-cols-2">
                <input type="email" name="email" placeholder="correo@ejemplo.com" required class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="password" name="password" placeholder="Contraseña inicial (mín. 8)" required class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="text" name="business_name" placeholder="Nombre comercial (opcional)" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="text" name="whatsapp" placeholder="WhatsApp (opcional)" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <div class="md:col-span-2 rounded-2xl border border-red-100 bg-red-50/40 p-4">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-red-500">Logo del negocio</p>
                    <input id="admin-logo-file" type="file" accept="image/png,image/jpeg,image/webp" class="mb-3 block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-red-600 file:px-3 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-red-500">
                    <div class="grid gap-3 md:grid-cols-[220px_1fr] md:items-center">
                        <canvas id="admin-logo-crop-preview" width="220" height="220" class="h-[220px] w-[220px] rounded-2xl border border-red-100 bg-white object-cover"></canvas>
                        <div>
                            <label for="admin-logo-zoom" class="mb-2 block text-xs font-semibold text-gray-600">Zoom del recorte</label>
                            <input id="admin-logo-zoom" type="range" min="1" max="3" step="0.01" value="1" class="w-full accent-red-600">
                            <p class="mt-2 text-xs text-gray-500">El recorte es cuadrado para encajar con las tarjetas del sitio.</p>
                        </div>
                    </div>
                    <input id="admin-logo-image" type="hidden" name="logo_image" value="">
                </div>
                <select name="role" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                    <option value="user">Usuario</option>
                    <option value="business">Negocio</option>
                    <option value="admin">Administrador</option>
                </select>
                <button type="submit" class="rounded-xl bg-red-600 p-3 text-sm font-bold text-white hover:bg-red-500">Crear usuario</button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="border-b border-gray-100 p-6">
        <h3 class="text-xl font-bold text-gray-900">Gestión de Usuarios</h3>
    </div>

    <?php $users = $usersPagination['items'] ?? []; ?>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50/50">
                <tr class="text-[11px] uppercase tracking-wider text-gray-500">
                    <th class="px-6 py-4 text-left font-semibold">Usuario</th>
                    <th class="px-6 py-4 text-left font-semibold">Rol</th>
                    <th class="px-6 py-4 text-left font-semibold">Estado</th>
                    <th class="px-6 py-4 text-right font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($users as $user) : ?>
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900"><?= htmlspecialchars((string) $user['email']) ?></span>
                                <span class="text-xs text-gray-500"><?= htmlspecialchars((string) ($user['business_name'] ?? 'Sin nombre comercial')) ?></span>
                                <?php if (!empty($user['whatsapp'])): ?>
                                    <span class="text-[10px] text-emerald-600 font-medium mt-1">WhatsApp: <?= htmlspecialchars($user['whatsapp']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 uppercase">
                                <?= htmlspecialchars((string) $user['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php $isSuspended = ($user['status'] ?? 'active') === 'suspended'; ?>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $isSuspended ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                <span class="mr-1.5 h-1.5 w-1.5 rounded-full <?= $isSuspended ? 'bg-amber-500' : 'bg-emerald-500' ?>"></span>
                                <?= ucfirst(htmlspecialchars((string) ($user['status'] ?? 'active'))) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-3">
                                <form action="/admin/users/<?= (int) $user['id'] ?>/impersonate" method="post" title="Ingresar como usuario">
                                    <button type="submit" class="text-gray-400 hover:text-gray-900 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                    </button>
                                </form>

                                <details class="relative">
                                    <summary class="list-none cursor-pointer text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </summary>
                                    <div class="absolute right-0 z-10 mt-2 w-[320px] rounded-xl border border-gray-200 bg-white p-3 shadow-xl">
                                        <form action="/admin/users/<?= (int) $user['id'] ?>" method="post" class="grid gap-2">
                                            <input type="email" name="email" value="<?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?>" required class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
                                            <input type="text" name="business_name" value="<?= htmlspecialchars((string) ($user['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Nombre negocio" class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
                                            <input type="text" name="whatsapp" value="<?= htmlspecialchars((string) ($user['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="WhatsApp" class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
                                            <select name="role" class="rounded-lg border border-gray-200 px-3 py-2 text-xs">
                                                <?php foreach (['user' => 'Usuario', 'business' => 'Negocio', 'admin' => 'Administrador'] as $roleValue => $roleLabel) : ?>
                                                    <option value="<?= $roleValue ?>" <?= (string) $user['role'] === $roleValue ? 'selected' : '' ?>><?= $roleLabel ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-bold text-white">Guardar</button>
                                        </form>
                                    </div>
                                </details>

                                <?php if ($isSuspended) : ?>
                                    <form action="/admin/users/<?= (int) $user['id'] ?>/unsuspend" method="post">
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs font-bold">Activar</button>
                                    </form>
                                <?php else : ?>
                                    <form action="/admin/users/<?= (int) $user['id'] ?>/suspend" method="post" class="flex items-center gap-1">
                                        <input type="text" name="reason" placeholder="Motivo" class="hidden md:block w-20 rounded border-gray-200 py-0.5 px-1 text-[10px] focus:ring-0">
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-xs font-bold">Suspender</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
        <?php
        $usersPage = (int) ($usersPagination['page'] ?? 1);
        $usersTotalPages = (int) ($usersPagination['total_pages'] ?? 1);
        $usersPerPage = (int) ($usersPagination['per_page'] ?? 10);
        ?>
        <?php if ($usersPage < $usersTotalPages) : ?>
            <div class="px-6 pb-6">
                <a href="/admin?tab=usuarios&page=<?= $usersPage + 1 ?>&per_page=<?= $usersPerPage ?>" class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                    Cargar más usuarios
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB: AJUSTES -->
    <div x-show="tab === 'ajustes'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-6 text-xl font-black text-gray-900">Ajustes Generales</h3>
            <form action="/admin/settings" method="post" class="space-y-8">

                <!-- WhatsApp de Contacto -->
                <div class="border-b border-gray-100 pb-6">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4 text-[#25D366]"></i> WhatsApp de Contacto
                    </h4>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Número de WhatsApp del sitio (sin espacios, ej: 5491112345678)</label>
                        <input type="text" name="contact_whatsapp" value="<?= htmlspecialchars((string) ($settings['contact_whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="5491112345678" class="block w-full max-w-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                        <p class="mt-1 text-xs text-gray-400">Se usa como enlace de contacto general del sitio.</p>
                    </div>
                </div>

                <!-- Modo Mantenimiento -->
                <div class="border-b border-gray-100 pb-6">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="wrench" class="w-4 h-4 text-amber-500"></i> Modo Mantenimiento
                    </h4>
                    <div class="flex items-center gap-3 mb-4" x-data="{ maintenance: '<?= htmlspecialchars((string) ($settings['maintenance_mode'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>' === '1' }">
                        <button type="button" @click="maintenance = !maintenance" :class="maintenance ? 'bg-red-600' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" role="switch">
                            <span :class="maintenance ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200"></span>
                        </button>
                        <input type="hidden" name="maintenance_mode" :value="maintenance ? '1' : '0'">
                        <span class="text-sm font-semibold" :class="maintenance ? 'text-red-600' : 'text-gray-500'" x-text="maintenance ? 'Sitio en mantenimiento (público ve página 503)' : 'Sitio activo'">Sitio activo</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mensaje de mantenimiento</label>
                        <textarea name="maintenance_message" rows="2" class="block w-full max-w-lg rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-none"><?= htmlspecialchars((string) ($settings['maintenance_message'] ?? 'Estamos realizando mejoras. Volvemos pronto.'), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                </div>

                <div class="border-b border-gray-100 pb-6" x-data="{ safeMode: '<?= htmlspecialchars((string) ($settings['safe_mode'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>' === '1' }">
                    <h4 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-gray-700">
                        <i data-lucide="shield" class="h-4 w-4 text-emerald-600"></i> Modo seguro de assets
                    </h4>
                    <div class="flex items-center gap-3 mb-2">
                        <button type="button" @click="safeMode = !safeMode" :class="safeMode ? 'bg-emerald-600' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" role="switch">
                            <span :class="safeMode ? 'translate-x-5' : 'translate-x-0'" class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-200"></span>
                        </button>
                        <input type="hidden" name="safe_mode" :value="safeMode ? '1' : '0'">
                        <span class="text-sm font-semibold" :class="safeMode ? 'text-emerald-700' : 'text-gray-500'" x-text="safeMode ? 'Modo seguro activo (bloquea CSS/JS personalizados)' : 'Modo seguro desactivado'">Modo seguro desactivado</span>
                    </div>
                    <p class="text-xs text-gray-500">Cuando está activo, el layout ignora temporalmente <code>custom_css_*</code> y <code>custom_js_*</code>.</p>
                </div>

                <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <p class="font-semibold">Advertencia de seguridad</p>
                    <p>Guardar código personalizado puede ejecutar scripts en navegador. Validá el origen antes de publicarlo.</p>
                </div>

                <!-- CSS y JS Personalizado - Frontend -->
                <div class="border-b border-gray-100 pb-6">
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="code" class="w-4 h-4 text-purple-500"></i> CSS y JS Personalizado — Sitio Público
                    </h4>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">CSS personalizado (sin etiqueta &lt;style&gt;)</label>
                            <textarea name="custom_css_frontend" rows="6" placeholder=".mi-clase { color: red; }" class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-mono text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-y"><?= htmlspecialchars((string) ($settings['custom_css_frontend'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">JS personalizado (sin etiqueta &lt;script&gt;)</label>
                            <textarea name="custom_js_frontend" rows="6" placeholder="console.log('Hola');" class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-mono text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-y"><?= htmlspecialchars((string) ($settings['custom_js_frontend'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- CSS y JS Personalizado - Panel -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-blue-500"></i> CSS y JS Personalizado — Panel de Usuarios
                    </h4>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">CSS personalizado del panel</label>
                            <textarea name="custom_css_panel" rows="6" placeholder=".mi-clase { color: red; }" class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-mono text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-y"><?= htmlspecialchars((string) ($settings['custom_css_panel'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">JS personalizado del panel</label>
                            <textarea name="custom_js_panel" rows="6" placeholder="console.log('Panel');" class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm font-mono text-gray-900 outline-none focus:border-red-500 focus:ring-2 focus:ring-red-500/20 resize-y"><?= htmlspecialchars((string) ($settings['custom_js_panel'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <h4 class="mb-3 text-sm font-bold uppercase tracking-widest text-gray-700">Auditoría reciente de ajustes críticos</h4>
                    <div class="space-y-2 text-sm">
                        <?php foreach (($settingsAuditLogs ?? []) as $log) : ?>
                            <div class="rounded-xl border border-gray-200 bg-white px-3 py-2">
                                <p class="font-semibold text-gray-800">
                                    <?= htmlspecialchars((string) ($log['setting_key'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    <span class="font-normal text-gray-500">· <?= htmlspecialchars((string) ($log['changed_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                </p>
                                <p class="text-xs text-gray-500">Por: <?= htmlspecialchars((string) (($log['changed_by_email'] ?? '') !== '' ? $log['changed_by_email'] : 'sistema'), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php if (($settingsAuditLogs ?? []) === []) : ?>
                            <p class="text-xs text-gray-500">Sin cambios auditados todavía.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="rounded-2xl bg-red-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-red-600/20 hover:bg-red-700 transition-colors">
                        Guardar Ajustes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-red-100 bg-white/95 px-3 py-2 shadow-[0_-10px_35px_rgba(0,0,0,0.08)] backdrop-blur supports-[backdrop-filter]:bg-white/80">
        <ul class="mx-auto grid max-w-5xl grid-cols-7 gap-2">
            <?php foreach ([
                'moderacion' => ['label' => 'Moderar', 'icon' => 'shield-check'],
                'textos' => ['label' => 'Textos', 'icon' => 'type'],
                'logo' => ['label' => 'Logo', 'icon' => 'image'],
                'categorias' => ['label' => 'Categorias', 'icon' => 'tags'],
                'seo' => ['label' => 'SEO', 'icon' => 'search'],
                'usuarios' => ['label' => 'Usuarios', 'icon' => 'users'],
                'ajustes' => ['label' => 'Ajustes', 'icon' => 'settings'],
            ] as $tabKey => $tabItem) : ?>
                <li>
                    <button
                        type="button"
                        @click="tab = '<?= $tabKey ?>'"
                        :class="tab === '<?= $tabKey ?>' ? 'bg-red-600 text-white' : 'bg-white text-gray-500 hover:bg-red-50 hover:text-red-600'"
                        class="flex w-full flex-col items-center justify-center gap-1 rounded-2xl border border-red-100 px-2 py-2 text-[11px] font-bold transition md:text-xs"
                    >
                        <i data-lucide="<?= htmlspecialchars($tabItem['icon'], ENT_QUOTES, 'UTF-8') ?>" class="h-4 w-4"></i>
                        <span><?= htmlspecialchars($tabItem['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</section>

<script>
    (() => {
        const initCropper = ({ fileInputId, zoomInputId, canvasId, hiddenInputId }) => {
            const fileInput = document.getElementById(fileInputId);
            const zoomInput = document.getElementById(zoomInputId);
            const canvas = document.getElementById(canvasId);
            const hiddenInput = document.getElementById(hiddenInputId);
            if (!fileInput || !zoomInput || !canvas || !hiddenInput) {
                return;
            }

            const context = canvas.getContext('2d');
            if (!context) {
                return;
            }

            const image = new Image();
            let zoom = 1;

            const drawPlaceholder = () => {
                context.fillStyle = '#fff1f2';
                context.fillRect(0, 0, canvas.width, canvas.height);
                context.fillStyle = '#be123c';
                context.font = 'bold 14px sans-serif';
                context.textAlign = 'center';
                context.fillText('Logo', canvas.width / 2, canvas.height / 2 - 4);
                context.font = '12px sans-serif';
                context.fillText('Selecciona una imagen', canvas.width / 2, canvas.height / 2 + 18);
                hiddenInput.value = '';
            };

            const redraw = () => {
                if (!image.src) {
                    drawPlaceholder();
                    return;
                }

                const cropSize = Math.min(image.naturalWidth, image.naturalHeight);
                const scaledCrop = cropSize / zoom;
                const sx = Math.max(0, (image.naturalWidth - scaledCrop) / 2);
                const sy = Math.max(0, (image.naturalHeight - scaledCrop) / 2);

                context.clearRect(0, 0, canvas.width, canvas.height);
                context.drawImage(image, sx, sy, scaledCrop, scaledCrop, 0, 0, canvas.width, canvas.height);
                hiddenInput.value = canvas.toDataURL('image/png', 0.92);
            };

            image.addEventListener('load', redraw);
            zoomInput.addEventListener('input', () => {
                zoom = Number(zoomInput.value || 1);
                redraw();
            });

            fileInput.addEventListener('change', (event) => {
                const [file] = event.target.files || [];
                if (!file) {
                    drawPlaceholder();
                    return;
                }

                const reader = new FileReader();
                reader.onload = (loadEvent) => {
                    image.src = String(loadEvent.target?.result || '');
                };
                reader.readAsDataURL(file);
            });

            drawPlaceholder();
        };

        initCropper({
            fileInputId: 'admin-logo-file',
            zoomInputId: 'admin-logo-zoom',
            canvasId: 'admin-logo-crop-preview',
            hiddenInputId: 'admin-logo-image',
        });
        initCropper({
            fileInputId: 'admin-site-logo-file',
            zoomInputId: 'admin-site-logo-zoom',
            canvasId: 'admin-site-logo-crop-preview',
            hiddenInputId: 'admin-site-logo-image',
        });
    })();
</script>
