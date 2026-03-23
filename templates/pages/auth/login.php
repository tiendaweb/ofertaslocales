<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
?>
<section class="max-w-xl mx-auto glass rounded-3xl p-8 neon-ring">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Autenticación</p>
    <h2 class="text-3xl font-semibold text-white mb-2">Ingresar a tu cuenta</h2>
    <p class="text-slate-300 mb-6">Usa tus credenciales de negocio o administrador para acceder a tu panel.</p>

    <?php if (($formErrors['general'] ?? null) !== null) : ?>
        <div class="mb-4 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-rose-200">
            <?= htmlspecialchars((string) $formErrors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form class="space-y-4" action="/login" method="post">
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">Correo electrónico</span>
            <input name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="nombre@negocio.com" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['email'] ?? null) !== null) : ?>
                <span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['email'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </label>
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">Contraseña</span>
            <input name="password" type="password" placeholder="********" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['password'] ?? null) !== null) : ?>
                <span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['password'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </label>
        <button type="submit" class="w-full rounded-2xl bg-blue-500 px-4 py-3 font-semibold text-slate-950">Continuar</button>
    </form>

    <div class="mt-6 rounded-2xl border border-white/10 bg-slate-900/50 p-4 text-sm text-slate-300">
        <p class="font-semibold text-white mb-2">Credenciales demo iniciales</p>
        <ul class="space-y-1">
            <li>Admin: <code>admin@ofertascerca.test</code> / <code>admin12345</code></li>
            <li>Negocio: <code>panaderia@barrio.test</code> / <code>negocio123</code></li>
        </ul>
    </div>
</section>
