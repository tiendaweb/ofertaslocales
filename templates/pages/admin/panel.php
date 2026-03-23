<?php

declare(strict_types=1);
?>
<section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
    <article class="glass rounded-3xl p-8">
        <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Panel privado</p>
        <h2 class="text-3xl font-semibold text-white mb-4">Resumen inicial para negocios</h2>
        <div class="space-y-3 text-slate-300">
            <p>Solicitudes pendientes de revisión: <strong class="text-white"><?= (int) $pendingOffers ?></strong></p>
            <p>Negocios disponibles en el repositorio: <strong class="text-white"><?= count($businesses) ?></strong></p>
        </div>
    </article>
    <article class="glass rounded-3xl p-8">
        <h3 class="text-xl font-semibold text-white mb-4">Cuentas visibles para el panel</h3>
        <div class="space-y-3">
            <?php foreach ($businesses as $business): ?>
                <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                    <p class="font-medium text-white"><?= htmlspecialchars($business['business_name'] ?: 'Sin nombre comercial', ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-sm text-slate-400"><?= htmlspecialchars($business['email'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>
