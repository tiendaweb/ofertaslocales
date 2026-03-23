<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'OfertasCerca', ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background: radial-gradient(circle at top, #0f172a 0%, #020617 55%, #01030b 100%); }
        .glass { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(18px); border: 1px solid rgba(148, 163, 184, 0.18); }
        .neon-ring { box-shadow: 0 0 24px rgba(59, 130, 246, 0.18); }
        .chip { border: 1px solid rgba(96, 165, 250, 0.25); background: rgba(30, 41, 59, 0.7); }
    </style>
</head>
<body class="text-slate-100 min-h-screen">
    <?php include __DIR__ . '/partials/header.php'; ?>
    <main class="max-w-6xl mx-auto px-4 py-8 md:px-6 md:py-10">
        <?php include $contentTemplate; ?>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
