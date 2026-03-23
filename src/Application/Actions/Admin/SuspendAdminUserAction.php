<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SuspendAdminUserAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $adminId = (int) ($this->currentUser()['id'] ?? 0);

        if ($id <= 0 || $adminId <= 0) {
            $this->flash('error', 'No pudimos procesar la suspensión solicitada.');

            return $this->redirect($response, '/admin/users');
        }

        if ($id === $adminId) {
            $this->flash('error', 'No puedes suspender tu propia cuenta de administrador.');

            return $this->redirect($response, '/admin/users');
        }

        $reason = trim((string) (((array) $request->getParsedBody())['reason'] ?? ''));
        $this->accountRepository->suspend($id, $adminId, $reason !== '' ? $reason : null);

        $this->flash('success', 'La cuenta fue suspendida.');

        return $this->redirect($response, '/admin/users');
    }
}
