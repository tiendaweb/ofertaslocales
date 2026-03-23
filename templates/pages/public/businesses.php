<?php

declare(strict_types=1);
?>
<section class="glass rounded-3xl p-8 mb-6">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Negocios</p>
    <h2 class="text-3xl font-semibold text-white mb-4">Directorio inicial de comercios registrados</h2>
    <p class="text-slate-300 leading-7">Esta vista queda preparada para consolidar el directorio de negocios con sus ofertas activas y próximos vencimientos.</p>
</section>

<div class="grid gap-4 md:grid-cols-2">
    <?php foreach ($businesses as $business): ?>
        <article class="glass rounded-3xl p-6 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-xl font-semibold text-white"><?= htmlspecialchars($business['business_name'] ?: 'Sin nombre comercial', ENT_QUOTES, 'UTF-8') ?></h3>
                <span class="chip rounded-full px-3 py-1 text-xs uppercase tracking-[0.22em]"><?= htmlspecialchars($business['role'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <p class="text-slate-400 text-sm"><?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="flex items-center justify-between text-sm text-slate-300">
                <span>Ofertas activas: <?= (int) $business['active_offers'] ?></span>
                <span>Próximo cierre: <?= htmlspecialchars((string) ($business['next_expiration'] ?? 'Sin fecha'), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </article>
    <?php endforeach; ?>
</div>
