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
                session_set_cookie_params([
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }

            session_start();
        }

        $_SESSION = $_SESSION ?? [];

        return $handler->handle($request->withAttribute('session', $_SESSION));
    }
}
