<?php

declare(strict_types=1);
?>
<?php $activeTab = in_array(($activeTab ?? 'moderacion'), ['moderacion', 'textos', 'seo', 'usuarios'], true) ? $activeTab : 'moderacion'; ?>
<section x-data="{ tab: '<?= htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8') ?>' }" class="flex flex-col lg:flex-row min-h-[800px] w-full gap-6 overflow-hidden rounded-[3rem] border border-white/10 bg-slate-950/40 p-4 backdrop-blur-3xl text-slate-200">
    
    <aside class="flex w-full lg:w-80 flex-col rounded-[2.5rem] bg-white/[0.03] p-6 border border-white/5 shadow-2xl">
        <div class="mb-10 px-4">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-blue-500 flex items-center justify-center shadow-[0_0_20px_rgba(59,130,246,0.5)]">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <h2 class="text-xl font-bold tracking-tight text-white">Panel <span class="text-blue-400 font-light">PRO</span></h2>
            </div>
            <p class="mt-2 text-[10px] uppercase tracking-[0.2em] text-slate-500 font-bold">Administración Central</p>
        </div>

        <nav class="flex flex-col gap-2 flex-1">
            <button type="button" @click="tab = 'moderacion'" :class="tab === 'moderacion' ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="flex items-center gap-4 rounded-2xl px-5 py-4 transition-all duration-300 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span class="text-sm font-semibold">Moderación</span>
            </button>

            <button type="button" @click="tab = 'textos'" :class="tab === 'textos' ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="flex items-center gap-4 rounded-2xl px-5 py-4 transition-all duration-300 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span class="text-sm font-semibold">Textos de la Web</span>
            </button>

            <button type="button" @click="tab = 'seo'" :class="tab === 'seo' ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="flex items-center gap-4 rounded-2xl px-5 py-4 transition-all duration-300 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <span class="text-sm font-semibold">SEO & Buscadores</span>
            </button>

            <button type="button" @click="tab = 'usuarios'" :class="tab === 'usuarios' ? 'bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'" class="flex items-center gap-4 rounded-2xl px-5 py-4 transition-all duration-300 group">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m12 0H7m6-12a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span class="text-sm font-semibold">Usuarios</span>
            </button>
        </nav>

        <div class="mt-10 rounded-3xl bg-slate-900/40 p-5 border border-white/5">
            <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest mb-3">Tu sesión</p>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-600 to-emerald-400 p-[2px]">
                    <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center text-xs font-bold text-white uppercase">AD</div>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">Admin Principal</p>
                    <p class="text-[10px] text-emerald-400 flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
        
        <header class="mb-8 flex flex-col md:flex-row items-center justify-between px-4 pt-4 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">¡Buenas, <span x-text="tab.charAt(0).toUpperCase() + tab.slice(1)"></span>!</h1>
                <p class="text-slate-400 text-sm">Gestioná los laburos y la presencia del sitio desde acá.</p>
            </div>
            
            <div class="flex gap-3">
                <div class="rounded-2xl border border-white/5 bg-white/[0.02] px-5 py-3 shadow-inner">
                    <p class="text-[10px] uppercase font-black text-slate-500 tracking-tighter leading-none mb-1">Pendientes</p>
                    <p class="text-xl font-bold text-amber-400 tabular-nums leading-none"><?= (int) $pendingOffers ?></p>
                </div>
                <div class="rounded-2xl border border-white/5 bg-white/[0.02] px-5 py-3 shadow-inner">
                    <p class="text-[10px] uppercase font-black text-slate-500 tracking-tighter leading-none mb-1">Total Negocios</p>
                    <p class="text-xl font-bold text-white tabular-nums leading-none"><?= (int) $businessCount ?></p>
                </div>
            </div>
        </header>

        <div class="px-2">
            
            <div x-show="tab === 'moderacion'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/20 p-8 backdrop-blur-xl shadow-2xl">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Ofertas para revisar
                    </h3>
                    
                    <div class="grid gap-4">
                        <?php foreach ($offers as $offer) : ?>
                        <div class="group relative flex flex-col md:flex-row items-start md:items-center gap-6 rounded-[2rem] border border-white/5 bg-slate-950/40 p-6 transition-all duration-300 hover:bg-slate-900/60 hover:border-blue-500/30">
                            <div class="h-20 w-20 shrink-0 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 border border-white/10 flex items-center justify-center text-3xl shadow-lg">
                                🖼️
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="mb-1 flex items-center gap-3 flex-wrap">
                                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.15em]"><?= htmlspecialchars($offer['business_name']) ?></span>
                                    <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                                    <span class="text-[10px] font-medium text-slate-500 uppercase"><?= htmlspecialchars($offer['category']) ?></span>
                                </div>
                                <h4 class="text-lg font-bold text-white truncate"><?= htmlspecialchars($offer['title']) ?></h4>
                                <p class="text-sm text-slate-400 line-clamp-1 italic font-light"><?= htmlspecialchars($offer['description']) ?></p>
                            </div>

                            <form action="/admin/offers/<?= (int) $offer['id'] ?>/status" method="post" class="flex items-center gap-3 bg-slate-900/50 p-2 rounded-2xl border border-white/5">
                                <button name="status" value="active" title="Aprobar laburo" class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-colors shadow-lg shadow-emerald-900/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                <button name="status" value="rejected" title="Rechazar" class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/5 text-white hover:bg-rose-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/40 p-8">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Seguridad de las Ofertas
                    </h3>
                    <form action="/admin/approval-mode" method="post" class="grid md:grid-cols-2 gap-4">
                        <label class="group relative flex cursor-pointer flex-col rounded-3xl border border-white/5 bg-slate-950/40 p-6 transition-all hover:border-blue-500/50">
                            <input type="radio" name="approval_mode" value="manual" <?= ($settings['approval_mode'] ?? 'manual') === 'manual' ? 'checked' : '' ?> class="absolute top-6 right-6 h-5 w-5 text-blue-500 bg-transparent border-white/20 focus:ring-0">
                            <span class="text-base font-bold text-white">Revisión Manual</span>
                            <span class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">Modo Seguro</span>
                            <p class="mt-4 text-xs text-slate-400 leading-relaxed">Las ofertas quedan "en espera" hasta que vos les des el OK.</p>
                        </label>
                        
                        <label class="group relative flex cursor-pointer flex-col rounded-3xl border border-white/5 bg-slate-950/40 p-6 transition-all hover:border-emerald-500/50">
                            <input type="radio" name="approval_mode" value="auto" <?= ($settings['approval_mode'] ?? 'manual') === 'auto' ? 'checked' : '' ?> class="absolute top-6 right-6 h-5 w-5 text-emerald-500 bg-transparent border-white/20 focus:ring-0">
                            <span class="text-base font-bold text-white">Aprobación Directa</span>
                            <span class="text-xs text-slate-500 mt-1 uppercase tracking-widest font-bold">Modo Rápido</span>
                            <p class="mt-4 text-xs text-slate-400 leading-relaxed">Apenas el negocio publica, la oferta sale al aire sin filtros.</p>
                        </label>
                        <button type="submit" class="md:col-span-2 mt-2 w-full rounded-2xl bg-blue-600 py-4 font-bold text-white shadow-lg shadow-blue-900/40 hover:bg-blue-500 transition-all">Guardar Configuración</button>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'textos'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/20 p-8 backdrop-blur-xl">
                    <h3 class="text-xl font-bold text-white mb-8">Personalización de la Marca</h3>
                    <form action="/admin/settings" method="post" class="grid gap-6">
                        <?php 
                        $labels = [
                            'site_name' => 'Nombre del Sitio',
                            'hero_title' => 'Título Principal del Hero',
                            'hero_description' => 'Descripción de Bienvenida',
                            'hero_primary_cta' => 'Texto del Botón Principal',
                            'footer_tagline' => 'Frase del Pie de Página'
                        ];
                        foreach ($labels as $key => $label) : ?>
                        <div class="relative rounded-2xl border border-white/5 bg-slate-950/60 p-2 focus-within:border-blue-500/50 transition-colors shadow-inner">
                            <label class="block px-3 pt-1 text-[10px] font-black uppercase tracking-widest text-slate-500"><?= $label ?></label>
                            <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars((string) ($settings[$key] ?? '')) ?>" class="w-full border-none bg-transparent px-3 pb-3 text-sm text-white focus:ring-0">
                        </div>
                        <?php endforeach; ?>
                        <button type="submit" class="w-full rounded-2xl bg-emerald-500 py-4 font-black text-slate-950 shadow-lg shadow-emerald-900/20 hover:bg-emerald-400 transition-all uppercase tracking-widest">Aplicar Cambios</button>
                    </form>
                </div>
            </div>

            <div x-show="tab === 'seo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/20 p-8 backdrop-blur-xl">
                    <h3 class="text-xl font-bold text-white mb-4 italic">Optimización para Buscadores</h3>
                    <p class="text-sm text-slate-400 mb-8 font-light">Configurá cómo te ven Google y las redes sociales.</p>
                    
                    <div class="grid gap-6 xl:grid-cols-2">
                        <?php foreach ($seoPages as $seo) : ?>
                        <form action="/admin/seo/<?= htmlspecialchars($seo['page_name']) ?>" method="post" class="flex flex-col rounded-[2rem] border border-white/5 bg-slate-950/40 p-6 transition-all hover:bg-slate-950/80">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black uppercase tracking-widest text-blue-400">Página: /<?= $seo['page_name'] ?></span>
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            </div>
                            
                            <div class="space-y-4 flex-1">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-500 ml-1">Meta Título</span>
                                    <input type="text" name="title" value="<?= htmlspecialchars($seo['title']) ?>" class="w-full rounded-xl border-none bg-white/5 p-3 text-xs text-white focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] font-bold text-slate-500 ml-1">Meta Descripción</span>
                                    <textarea name="meta_description" rows="3" class="w-full rounded-xl border-none bg-white/5 p-3 text-xs text-white focus:ring-1 focus:ring-blue-500 leading-relaxed"><?= htmlspecialchars($seo['meta_description']) ?></textarea>
                                </div>
                            </div>
                            
                            <button class="mt-6 w-full rounded-xl bg-white/5 py-3 text-[10px] font-black uppercase tracking-widest text-white hover:bg-white/10 border border-white/10 transition-all">Guardar Metadatos</button>
                        </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div x-show="tab === 'usuarios'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/20 p-8 backdrop-blur-xl">
                    <h3 class="text-xl font-bold text-white mb-6">Crear usuario</h3>
                    <form action="/admin/users" method="post" class="grid gap-4 md:grid-cols-2">
                        <input type="email" name="email" placeholder="correo@ejemplo.com" required class="rounded-xl border-none bg-white/5 p-3 text-sm text-white focus:ring-1 focus:ring-blue-500">
                        <input type="password" name="password" placeholder="Contraseña inicial (mín. 8)" required class="rounded-xl border-none bg-white/5 p-3 text-sm text-white focus:ring-1 focus:ring-blue-500">
                        <input type="text" name="business_name" placeholder="Nombre comercial (opcional)" class="rounded-xl border-none bg-white/5 p-3 text-sm text-white focus:ring-1 focus:ring-blue-500">
                        <input type="text" name="whatsapp" placeholder="WhatsApp (opcional)" class="rounded-xl border-none bg-white/5 p-3 text-sm text-white focus:ring-1 focus:ring-blue-500">
                        <select name="role" class="rounded-xl border-none bg-white/5 p-3 text-sm text-white focus:ring-1 focus:ring-blue-500">
                            <option value="user" class="text-slate-900">Usuario</option>
                            <option value="business" class="text-slate-900">Negocio</option>
                            <option value="admin" class="text-slate-900">Administrador</option>
                        </select>
                        <button type="submit" class="rounded-xl bg-emerald-500 p-3 text-sm font-bold text-slate-950 hover:bg-emerald-400">Crear usuario</button>
                    </form>
                </div>

                <div class="rounded-[2.5rem] border border-white/10 bg-slate-900/20 p-8 backdrop-blur-xl">
                    <h3 class="text-xl font-bold text-white mb-6">Gestión de usuarios</h3>
                    <?php $users = $usersPagination['items'] ?? []; ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-slate-300">
                            <thead>
                                <tr class="text-slate-500 uppercase tracking-wider">
                                    <th class="text-left px-2 py-2">Cuenta</th>
                                    <th class="text-left px-2 py-2">Rol</th>
                                    <th class="text-left px-2 py-2">Estado</th>
                                    <th class="text-left px-2 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user) : ?>
                                    <tr class="border-t border-white/5 align-top">
                                        <td class="px-2 py-3">
                                            <p class="font-semibold text-white"><?= htmlspecialchars((string) $user['email']) ?></p>
                                            <p class="text-slate-400"><?= htmlspecialchars((string) ($user['business_name'] ?? 'Sin nombre comercial')) ?></p>
                                        </td>
                                        <td class="px-2 py-3">
                                            <span class="rounded-full bg-white/10 px-3 py-1"><?= htmlspecialchars((string) $user['role']) ?></span>
                                        </td>
                                        <td class="px-2 py-3">
                                            <span class="rounded-full px-3 py-1 <?= ($user['status'] ?? 'active') === 'suspended' ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' ?>">
                                                <?= htmlspecialchars((string) ($user['status'] ?? 'active')) ?>
                                            </span>
                                        </td>
                                        <td class="px-2 py-3">
                                            <div class="space-y-2">
                                                <form action="/admin/users/<?= (int) $user['id'] ?>" method="post" class="grid gap-2 md:grid-cols-4">
                                                    <input type="email" name="email" value="<?= htmlspecialchars((string) $user['email']) ?>" required class="rounded-lg border-none bg-white/5 px-2 py-1 text-xs text-white">
                                                    <input type="text" name="business_name" value="<?= htmlspecialchars((string) ($user['business_name'] ?? '')) ?>" class="rounded-lg border-none bg-white/5 px-2 py-1 text-xs text-white">
                                                    <input type="text" name="whatsapp" value="<?= htmlspecialchars((string) ($user['whatsapp'] ?? '')) ?>" class="rounded-lg border-none bg-white/5 px-2 py-1 text-xs text-white">
                                                    <select name="role" class="rounded-lg border-none bg-white/5 px-2 py-1 text-xs text-white">
                                                        <?php foreach (['user' => 'Usuario', 'business' => 'Negocio', 'admin' => 'Administrador'] as $roleValue => $roleLabel) : ?>
                                                            <option value="<?= $roleValue ?>" class="text-slate-900" <?= ($user['role'] ?? '') === $roleValue ? 'selected' : '' ?>><?= $roleLabel ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="rounded-lg bg-blue-600 px-2 py-1 font-semibold text-white hover:bg-blue-500 md:col-span-4">Guardar cambios</button>
                                                </form>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if (($user['status'] ?? 'active') === 'suspended') : ?>
                                                        <form action="/admin/users/<?= (int) $user['id'] ?>/unsuspend" method="post">
                                                            <button type="submit" class="rounded-lg bg-emerald-500 px-3 py-1 font-semibold text-slate-950 hover:bg-emerald-400">Reactivar</button>
                                                        </form>
                                                    <?php else : ?>
                                                        <form action="/admin/users/<?= (int) $user['id'] ?>/suspend" method="post" class="flex items-center gap-2">
                                                            <input type="text" name="reason" placeholder="Motivo (opcional)" class="rounded-lg border-none bg-white/5 px-2 py-1 text-xs text-white">
                                                            <button type="submit" class="rounded-lg bg-rose-500 px-3 py-1 font-semibold text-white hover:bg-rose-400">Suspender</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form action="/admin/users/<?= (int) $user['id'] ?>/impersonate" method="post">
                                                        <button type="submit" class="rounded-lg bg-indigo-500 px-3 py-1 font-semibold text-white hover:bg-indigo-400">Ingresar como</button>
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

        </div>
    </main>
</section>
