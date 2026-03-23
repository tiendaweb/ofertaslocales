<?php

declare(strict_types=1);
?>
<section class="glass rounded-3xl p-8">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Administración</p>
    <h2 class="text-3xl font-semibold text-white mb-6">Vista base para moderación y configuración</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
            <p class="text-sm text-slate-400 mb-2">Ofertas pendientes</p>
            <p class="text-4xl font-semibold text-white"><?= (int) $pendingOffers ?></p>
        </article>
        <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
            <p class="text-sm text-slate-400 mb-2">Administradores</p>
            <p class="text-4xl font-semibold text-white"><?= (int) $adminCount ?></p>
        </article>
        <article class="rounded-3xl bg-slate-900/70 p-6 border border-white/10">
            <p class="text-sm text-slate-400 mb-2">Negocios</p>
            <p class="text-4xl font-semibold text-white"><?= (int) $businessCount ?></p>
        </article>
    </div>
</section>
