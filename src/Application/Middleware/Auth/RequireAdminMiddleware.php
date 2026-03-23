<?php

declare(strict_types=1);

namespace App\Application\Middleware\Auth;

use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RequireAdminMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        if ($this->authService->hasRole('admin')) {
            return $handler->handle($request);
        }

        $_SESSION['flash']['error'] = 'Solo los administradores pueden acceder a esta sección.';

        $response = new \Slim\Psr7\Response();

        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
