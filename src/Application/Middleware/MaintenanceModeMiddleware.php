<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class MaintenanceModeMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SettingsRepository $settingsRepository)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $path = $request->getUri()->getPath();

        // Never apply maintenance to admin, login, or static asset paths
        $excluded = ['/admin', '/login', '/register', '/logout', '/uploads'];
        foreach ($excluded as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $handler->handle($request);
            }
        }

        $settings = $this->settingsRepository->findByKeys(['maintenance_mode', 'maintenance_message']);
        if (($settings['maintenance_mode'] ?? '0') !== '1') {
            return $handler->handle($request);
        }

        // Allow admins through even during maintenance
        $currentUser = $_SESSION['auth']['user'] ?? null;
        if (is_array($currentUser) && (($currentUser['role'] ?? '') === 'admin')) {
            return $handler->handle($request);
        }

        $message = htmlspecialchars(
            (string) ($settings['maintenance_message'] ?? 'Estamos realizando mejoras. Volvemos pronto.'),
            ENT_QUOTES,
            'UTF-8'
        );

        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en Mantenimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-red-600 to-rose-800 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-10 text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
        </div>
        <h1 class="text-2xl font-black text-gray-900 mb-3">Sitio en Mantenimiento</h1>
        <p class="text-gray-500 text-base leading-relaxed mb-8">{$message}</p>
        <div class="text-xs text-gray-400 font-semibold uppercase tracking-widest">Volvemos pronto &mdash; OfertasLocales</div>
    </div>
</body>
</html>
HTML;

        $response = new SlimResponse(503);
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withHeader('Retry-After', '3600');
    }
}
