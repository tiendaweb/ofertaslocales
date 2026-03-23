<?php

declare(strict_types=1);
?>
<section class="glass rounded-3xl p-8 mb-6">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Mapa</p>
    <h2 class="text-3xl font-semibold text-white mb-4">Preparación del mapa público con OpenStreetMap</h2>
    <p class="text-slate-300 leading-7 mb-4">La ruta ya centraliza la data que luego consumirá Leaflet. Por ahora se muestra un resumen de marcadores listos para migrar al mapa interactivo.</p>
    <div class="rounded-3xl border border-dashed border-blue-400/30 bg-slate-900/60 p-5">
        <pre class="text-xs text-slate-300 whitespace-pre-wrap"><?= htmlspecialchars(json_encode($mapOffers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?></pre>
    </div>
</section>
