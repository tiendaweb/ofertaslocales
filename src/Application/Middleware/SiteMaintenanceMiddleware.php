<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use PDO;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SiteMaintenanceMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly ResponseFactoryInterface $responseFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $isExcludedPath = str_starts_with($path, '/admin')
            || str_starts_with($path, '/login')
            || str_starts_with($path, '/logout')
            || str_starts_with($path, '/assets')
            || str_starts_with($path, '/uploads')
            || str_starts_with($path, '/manifest.webmanifest')
            || str_starts_with($path, '/sw.js');

        if ($isExcludedPath) {
            return $handler->handle($request);
        }

        $mode = $this->findSetting('maintenance_mode');
        if ($mode !== '1') {
            return $handler->handle($request);
        }

        $currentUser = $_SESSION['auth']['user'] ?? null;
        if (is_array($currentUser) && (($currentUser['role'] ?? '') === 'admin')) {
            return $handler->handle($request);
        }

        $message = $this->findSetting('maintenance_message');
        $safeMessage = htmlspecialchars(
            $message !== '' ? $message : 'Estamos realizando tareas de mantenimiento. Volvé a intentar en unos minutos.',
            ENT_QUOTES,
            'UTF-8'
        );

        $response = $this->responseFactory->createResponse(503);
        $response->getBody()->write(
            '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Modo mantenimiento</title><style>body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#fff7f7;color:#111827;display:grid;place-items:center;min-height:100vh}main{max-width:560px;background:#fff;border:1px solid #fee2e2;border-radius:24px;padding:28px;box-shadow:0 12px 42px rgba(220,38,38,.12)}h1{margin:0 0 10px;font-size:1.7rem;color:#b91c1c}p{margin:0;line-height:1.6}</style></head><body><main><h1>Estamos mejorando OfertasLocales</h1><p>'
            . $safeMessage .
            '</p></main></body></html>'
        );

        return $response;
    }

    private function findSetting(string $key): string
    {
        $statement = $this->pdo->prepare('SELECT value FROM settings WHERE key = :key LIMIT 1');
        $statement->execute(['key' => $key]);

        $value = $statement->fetchColumn();

        return is_string($value) ? $value : '';
    }
}
