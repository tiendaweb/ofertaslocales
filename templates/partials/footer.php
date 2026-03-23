<?php

declare(strict_types=1);
?>
<footer class="border-t border-white/10 mt-10">
    <div class="max-w-6xl mx-auto px-4 py-6 md:px-6 text-sm text-slate-400 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <p>Base pública y privada inicial para OfertasCerca. Todo el contenido visible está en español.</p>
        <p>© <?= htmlspecialchars((string) $currentYear, ENT_QUOTES, 'UTF-8') ?> · Slim 4 + SQLite + Tailwind CSS</p>
    </div>
</footer>
