<?php

declare(strict_types=1);
?>
<?php $activeTab = in_array(($activeTab ?? 'moderacion'), ['moderacion', 'textos', 'seo', 'usuarios'], true) ? $activeTab : 'moderacion'; ?>
<section x-data="{ tab: '<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>' }" class="space-y-5 pb-28">
    <div class="rounded-[2.2rem] border border-red-100 bg-white/95 p-4 shadow-[0_24px_80px_rgba(239,68,68,0.12)] md:p-6">
        <div class="mb-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-red-500">Panel administrador</p>
                <h2 class="mt-2 text-3xl font-black text-gray-900">Control de OfertasCerca</h2>
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

        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
            <?php foreach ([
                'moderacion' => ['label' => 'Moderación', 'icon' => 'shield-check'],
                'textos' => ['label' => 'Textos', 'icon' => 'type'],
                'seo' => ['label' => 'SEO', 'icon' => 'search'],
                'usuarios' => ['label' => 'Usuarios', 'icon' => 'users'],
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
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-red-500">
                                    <span><?= htmlspecialchars((string) $offer['business_name']) ?></span>
                                    <span class="h-1 w-1 rounded-full bg-red-300"></span>
                                    <span><?= htmlspecialchars((string) $offer['category']) ?></span>
                                </div>
                                <h4 class="truncate text-lg font-bold text-gray-900"><?= htmlspecialchars((string) $offer['title']) ?></h4>
                                <p class="line-clamp-2 text-sm text-gray-600"><?= htmlspecialchars((string) $offer['description']) ?></p>
                            </div>
                            <form action="/admin/offers/<?= (int) $offer['id'] ?>/status" method="post" class="flex shrink-0 items-center gap-2 rounded-2xl border border-red-100 bg-white p-2">
                                <button name="status" value="active" title="Aprobar oferta" class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-600 text-white hover:bg-red-500 transition-colors">
                                    <i data-lucide="check" class="h-5 w-5"></i>
                                </button>
                                <button name="status" value="rejected" title="Rechazar oferta" class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                                    <i data-lucide="x" class="h-5 w-5"></i>
                                </button>
                            </form>
                        </div>
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
                <div class="rounded-2xl border border-red-100 bg-red-50/50 p-3">
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-red-500">Regla para usuarios (rol user)</label>
                    <select name="default_user_publish_mode" class="w-full rounded-xl border border-red-100 bg-white p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                        <option value="direct" <?= ($settings['default_user_publish_mode'] ?? 'review') === 'direct' ? 'selected' : '' ?>>Publicación inmediata</option>
                        <option value="review" <?= ($settings['default_user_publish_mode'] ?? 'review') === 'review' ? 'selected' : '' ?>>Publicación bajo revisión</option>
                        <option value="profile_required" <?= ($settings['default_user_publish_mode'] ?? 'review') === 'profile_required' ? 'selected' : '' ?>>Solo tras completar perfil</option>
                    </select>
                    <p class="mt-2 text-xs text-gray-600">Define cómo publican las cuentas generales. Las cuentas negocio/admin mantienen su flujo comercial.</p>
                </div>
                <button type="submit" class="rounded-2xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500">Aplicar cambios</button>
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
                        <button class="mt-4 rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-500">Guardar metadatos</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div x-show="tab === 'usuarios'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-xl font-black text-gray-900">Crear usuario</h3>
            <form action="/admin/users" method="post" class="grid gap-3 md:grid-cols-2">
                <input type="email" name="email" placeholder="correo@ejemplo.com" required class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="password" name="password" placeholder="Contraseña inicial (mín. 8)" required class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="text" name="business_name" placeholder="Nombre comercial (opcional)" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <input type="text" name="whatsapp" placeholder="WhatsApp (opcional)" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                <select name="role" class="rounded-xl border border-red-100 bg-red-50/40 p-3 text-sm text-gray-800 focus:border-red-400 focus:outline-none">
                    <option value="user">Usuario</option>
                    <option value="business">Negocio</option>
                    <option value="admin">Administrador</option>
                </select>
                <button type="submit" class="rounded-xl bg-red-600 p-3 text-sm font-bold text-white hover:bg-red-500">Crear usuario</button>
            </form>
        </div>

        <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-xl font-black text-gray-900">Gestión de usuarios</h3>
            <?php $users = $usersPagination['items'] ?? []; ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs text-gray-700">
                    <thead>
                        <tr class="border-b border-red-100 text-[11px] uppercase tracking-widest text-red-500">
                            <th class="px-2 py-3 text-left">Cuenta</th>
                            <th class="px-2 py-3 text-left">Rol</th>
                            <th class="px-2 py-3 text-left">Estado</th>
                            <th class="px-2 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr class="border-t border-red-50 align-top">
                                <td class="px-2 py-3">
                                    <p class="font-semibold text-gray-900"><?= htmlspecialchars((string) $user['email']) ?></p>
                                    <p class="text-gray-600"><?= htmlspecialchars((string) ($user['business_name'] ?? 'Sin nombre comercial')) ?></p>
                                </td>
                                <td class="px-2 py-3">
                                    <span class="rounded-full border border-red-100 bg-red-50 px-3 py-1 font-semibold text-red-700"><?= htmlspecialchars((string) $user['role']) ?></span>
                                </td>
                                <td class="px-2 py-3">
                                    <span class="rounded-full px-3 py-1 font-semibold <?= ($user['status'] ?? 'active') === 'suspended' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                        <?= htmlspecialchars((string) ($user['status'] ?? 'active')) ?>
                                    </span>
                                </td>
                                <td class="px-2 py-3">
                                    <div class="space-y-2">
                                        <form action="/admin/users/<?= (int) $user['id'] ?>" method="post" class="grid gap-2 md:grid-cols-4">
                                            <input type="email" name="email" value="<?= htmlspecialchars((string) $user['email']) ?>" required class="rounded-lg border border-red-100 bg-red-50/40 px-2 py-1 text-xs text-gray-800 focus:border-red-400 focus:outline-none">
                                            <input type="text" name="business_name" value="<?= htmlspecialchars((string) ($user['business_name'] ?? '')) ?>" class="rounded-lg border border-red-100 bg-red-50/40 px-2 py-1 text-xs text-gray-800 focus:border-red-400 focus:outline-none">
                                            <input type="text" name="whatsapp" value="<?= htmlspecialchars((string) ($user['whatsapp'] ?? '')) ?>" class="rounded-lg border border-red-100 bg-red-50/40 px-2 py-1 text-xs text-gray-800 focus:border-red-400 focus:outline-none">
                                            <select name="role" class="rounded-lg border border-red-100 bg-red-50/40 px-2 py-1 text-xs text-gray-800 focus:border-red-400 focus:outline-none">
                                                <?php foreach (['user' => 'Usuario', 'business' => 'Negocio', 'admin' => 'Administrador'] as $roleValue => $roleLabel) : ?>
                                                    <option value="<?= $roleValue ?>" <?= ($user['role'] ?? '') === $roleValue ? 'selected' : '' ?>><?= $roleLabel ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="rounded-lg bg-red-600 px-2 py-1 font-semibold text-white hover:bg-red-500 md:col-span-4">Guardar cambios</button>
                                        </form>
                                        <div class="flex flex-wrap gap-2">
                                            <?php if (($user['status'] ?? 'active') === 'suspended') : ?>
                                                <form action="/admin/users/<?= (int) $user['id'] ?>/unsuspend" method="post">
                                                    <button type="submit" class="rounded-lg bg-emerald-500 px-3 py-1 font-semibold text-white hover:bg-emerald-400">Reactivar</button>
                                                </form>
                                            <?php else : ?>
                                                <form action="/admin/users/<?= (int) $user['id'] ?>/suspend" method="post" class="flex items-center gap-2">
                                                    <input type="text" name="reason" placeholder="Motivo (opcional)" class="rounded-lg border border-red-100 bg-red-50/40 px-2 py-1 text-xs text-gray-800 focus:border-red-400 focus:outline-none">
                                                    <button type="submit" class="rounded-lg bg-rose-600 px-3 py-1 font-semibold text-white hover:bg-rose-500">Suspender</button>
                                                </form>
                                            <?php endif; ?>
                                            <form action="/admin/users/<?= (int) $user['id'] ?>/impersonate" method="post">
                                                <button type="submit" class="rounded-lg bg-gray-800 px-3 py-1 font-semibold text-white hover:bg-gray-700">Ingresar como</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-red-100 bg-white/95 px-3 py-2 shadow-[0_-10px_35px_rgba(0,0,0,0.08)] backdrop-blur supports-[backdrop-filter]:bg-white/80">
        <ul class="mx-auto grid max-w-5xl grid-cols-4 gap-2">
            <?php foreach ([
                'moderacion' => ['label' => 'Moderar', 'icon' => 'shield-check'],
                'textos' => ['label' => 'Textos', 'icon' => 'type'],
                'seo' => ['label' => 'SEO', 'icon' => 'search'],
                'usuarios' => ['label' => 'Usuarios', 'icon' => 'users'],
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
