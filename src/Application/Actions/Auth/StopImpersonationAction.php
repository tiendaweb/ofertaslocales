<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\PageAction;
use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StopImpersonationAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AuthService $authService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        if (!$this->authService->isImpersonating()) {
            $this->flash('error', 'No hay una suplantación activa para finalizar.');

            return $this->redirect($response, '/');
        }

        $this->authService->stopImpersonation();
        $this->flash('success', 'Volviste a tu sesión de administrador.');

        return $this->redirect($response, '/admin/users');
    }
}
