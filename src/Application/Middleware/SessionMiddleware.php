<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class SessionMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (PHP_SAPI !== 'cli' && session_status() !== PHP_SESSION_ACTIVE) {
            if (!headers_sent()) {
                session_name('ofertascerca_session');
                session_set_cookie_params([
                    'lifetime' => 60 * 60 * 24 * 30,
                    'path' => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                    'secure' => $this->isSecureRequest($request),
                ]);
                ini_set('session.use_strict_mode', '1');
                ini_set('session.cookie_httponly', '1');
                ini_set('session.cookie_samesite', 'Lax');
            }

            session_start();
        }

        $_SESSION = is_array($_SESSION ?? null) ? $_SESSION : [];

        return $handler->handle($request->withAttribute('session', $_SESSION));
    }

    private function isSecureRequest(Request $request): bool
    {
        $uri = $request->getUri();
        if ($uri->getScheme() === 'https') {
            return true;
        }

        $forwardedProto = $request->getHeaderLine('X-Forwarded-Proto');

        return strtolower($forwardedProto) === 'https';
    }
}
