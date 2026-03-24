<?php
declare(strict_types=1);

$formErrors = is_array($flash['form_errors'] ?? null) ? $flash['form_errors'] : [];
$old = is_array($flash['old'] ?? null) ? $flash['old'] : [];
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <section class="max-w-md w-full bg-white rounded-[2.5rem] border border-red-100 p-8 md:p-10 shadow-xl shadow-red-900/10">
        <div class="text-center mb-8">
            <span class="inline-block py-1 px-3 rounded-full border border-red-100 bg-red-50 text-red-700 text-xs font-bold uppercase tracking-widest mb-4">
                Acceso Seguro
            </span>
            <h2 class="text-3xl font-black text-gray-900 mb-2">¡Hola de nuevo!</h2>
            <p class="text-gray-500">Ingresa tus credenciales para gestionar tus ofertas.</p>
        </div>

        <?php if (($formErrors['general'] ?? null) !== null) : ?>
            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-rose-700 text-sm animate-pulse">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <p><?= htmlspecialchars((string) $formErrors['general'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <form class="space-y-5" action="/login" method="post">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 ml-1">Correo electrónico</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input
                        name="email" 
                        type="email" 
                        value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" 
                        placeholder="ejemplo@correo.com" 
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 pl-12 pr-4 py-3.5 text-gray-900 transition-all outline-none focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"
                    >
                </div>
                <?php if (($formErrors['email'] ?? null) !== null) : ?>
                    <span class="mt-2 block text-xs font-semibold text-rose-500 ml-1 italic">
                        * <?= htmlspecialchars((string) $formErrors['email'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2 ml-1">
                    <label class="text-sm font-bold text-gray-700">Contraseña</label>
                    <a href="#" class="text-xs font-semibold text-red-600 hover:text-red-700">¿Olvidaste tu clave?</a>
                </div>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input
                        name="password" 
                        type="password" 
                        placeholder="••••••••" 
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 pl-12 pr-4 py-3.5 text-gray-900 transition-all outline-none focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/20"
                    >
                </div>
                <?php if (($formErrors['password'] ?? null) !== null) : ?>
                    <span class="mt-2 block text-xs font-semibold text-rose-500 ml-1 italic">
                        * <?= htmlspecialchars((string) $formErrors['password'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 px-4 py-4 font-bold text-white shadow-lg shadow-red-600/25 transition-all focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-red-600/35 active:scale-[0.98]">
                Entrar al panel
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            ¿No tienes cuenta? 
            <a href="/register" class="font-bold text-red-600 hover:underline">Regístrate gratis</a>
        </p>

        <div class="mt-8 rounded-2xl border border-gray-100 bg-gray-50/50 p-5">
            <div class="flex items-center gap-2 mb-3 text-gray-400">
                <i data-lucide="info" class="w-4 h-4 text-red-400"></i>
                <span class="text-xs font-bold uppercase tracking-wider">Acceso Demo</span>
            </div>
            <div class="space-y-3">
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Administrador</span>
                    <code class="text-xs text-gray-700">admin@admin.com / admin@admin.com</code>
                </div>
                <div class="flex flex-col border-t border-gray-100 pt-2">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Negocio</span>
                    <code class="text-xs text-gray-700">panaderia@barrio.test / 123</code>
                </div>
            </div>
        </div>
    </section>
</div>
