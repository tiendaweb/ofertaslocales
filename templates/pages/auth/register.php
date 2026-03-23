<?php

declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
$selectedRole = in_array(($old['role'] ?? 'user'), ['user', 'business'], true) ? (string) $old['role'] : 'user';
?>
<section x-data="{ role: '<?= htmlspecialchars($selectedRole, ENT_QUOTES, 'UTF-8') ?>' }" class="max-w-2xl mx-auto glass rounded-3xl p-8">
    <p class="text-sm uppercase tracking-[0.28em] text-blue-300 mb-3">Registro</p>
    <h2 class="text-3xl font-semibold text-white mb-4">Crear cuenta para publicar ofertas</h2>
    <p class="text-slate-300 mb-6">Puedes registrarte como usuario común o como negocio. En ambos casos podrás publicar ofertas desde tu panel.</p>

    <?php if (($formErrors['general'] ?? null) !== null) : ?>
        <div class="mb-4 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-rose-200">
            <?= htmlspecialchars((string) $formErrors['general'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form class="grid gap-4 md:grid-cols-2" action="/register" method="post">
        <fieldset class="md:col-span-2">
            <legend class="block text-sm text-slate-300 mb-2">Tipo de cuenta</legend>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="cursor-pointer rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                    <input x-model="role" type="radio" name="role" value="user" class="mr-2">
                    <span class="font-semibold text-white">Usuario común</span>
                    <p class="mt-1 text-sm text-slate-300">Ideal para revendedores o particulares.</p>
                </label>
                <label class="cursor-pointer rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                    <input x-model="role" type="radio" name="role" value="business" class="mr-2">
                    <span class="font-semibold text-white">Negocio</span>
                    <p class="mt-1 text-sm text-slate-300">Para locales y emprendimientos con marca propia.</p>
                </label>
            </div>
        </fieldset>

        <label class="block md:col-span-2">
            <span class="block text-sm text-slate-300 mb-2">Correo electrónico</span>
            <input name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="contacto@local.com" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['email'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['email'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">Contraseña</span>
            <input name="password" type="password" placeholder="Mínimo 8 caracteres" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['password'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['password'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block">
            <span class="block text-sm text-slate-300 mb-2">Confirmar contraseña</span>
            <input name="password_confirmation" type="password" placeholder="Repite tu contraseña" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['password_confirmation'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['password_confirmation'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block md:col-span-2">
            <span class="block text-sm text-slate-300 mb-2">WhatsApp</span>
            <input name="whatsapp" type="text" value="<?= htmlspecialchars((string) ($old['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="+54 9 11 0000 0000" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <?php if (($formErrors['whatsapp'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['whatsapp'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <label class="block md:col-span-2">
            <span class="block text-sm text-slate-300 mb-2">Nombre visible (solo para negocios)</span>
            <input name="business_name" type="text" value="<?= htmlspecialchars((string) ($old['business_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Tu negocio o emprendimiento" class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
            <p class="mt-2 text-xs text-slate-400" x-show="role === 'user'">Opcional: si lo dejas vacío, se usará tu correo como identificación.</p>
            <?php if (($formErrors['business_name'] ?? null) !== null) : ?><span class="mt-2 block text-sm text-rose-300"><?= htmlspecialchars((string) $formErrors['business_name'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </label>
        <button type="submit" class="md:col-span-2 rounded-2xl bg-emerald-400 px-4 py-3 font-semibold text-slate-950">Crear cuenta</button>
    </form>
</section>
