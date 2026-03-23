<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Application\Auth\AuthService;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ImpersonateAdminUserAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository,
        private readonly AuthService $authService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $currentUser = $this->currentUser();

        if ($id <= 0 || $currentUser === null) {
            $this->flash('error', 'No se pudo iniciar la suplantación.');

            return $this->redirect($response, '/admin/users');
        }

        if ((int) $currentUser['id'] === $id) {
            $this->flash('error', 'Ya estás usando esta misma cuenta.');

            return $this->redirect($response, '/admin/users');
        }

        $account = $this->accountRepository->findById($id);
        if ($account === null) {
            $this->flash('error', 'La cuenta indicada no existe.');

            return $this->redirect($response, '/admin/users');
        }

        if (($account['status'] ?? 'active') !== 'active' || (int) ($account['is_suspended'] ?? 0) === 1) {
            $this->flash('error', 'No puedes ingresar como una cuenta suspendida.');

            return $this->redirect($response, '/admin/users');
        }

        $this->authService->impersonate($account, (int) $currentUser['id']);
        $this->flash('success', 'Ahora estás navegando como el usuario seleccionado.');

        return $this->redirect($response, '/');
    }
}
