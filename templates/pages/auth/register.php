<?php

declare(strict_types=1);
?>
<section class="max-w-2xl mx-auto glass rounded-3xl p-8">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Registro</p>
    <h2 class="text-3xl font-semibold text-white mb-4">Crear cuenta para publicar ofertas</h2>
    <form class="grid gap-4 md:grid-cols-2">
        <label class="block md:col-span-2">
            <span class="block text-sm text-slate-300 mb-2">Correo electrónico</span>
            <input type="email" placeholder="contacto@local.com" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </label>
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">Contraseña</span>
            <input type="password" placeholder="Mínimo 8 caracteres" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </label>
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">WhatsApp</span>
            <input type="text" placeholder="+54 9 11 0000 0000" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </label>
        <label class="block md:col-span-2">
            <span class="block text-sm text-slate-300 mb-2">Nombre del local</span>
            <input type="text" placeholder="Tu negocio o emprendimiento" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </label>
        <button type="button" class="md:col-span-2 rounded-2xl bg-emerald-400 px-4 py-3 font-semibold text-slate-950">Crear cuenta</button>
    </form>
</section>
