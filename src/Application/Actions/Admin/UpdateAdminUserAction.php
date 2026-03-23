<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAdminUserAction extends PageAction
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
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $role = trim((string) ($data['role'] ?? ''));

        if ($id <= 0) {
            $this->flash('error', 'No se encontró el usuario a editar.');

            return $this->redirect($response, '/admin/users');
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'El correo electrónico indicado no es válido.');

            return $this->redirect($response, '/admin/users');
        }

        if (!in_array($role, ['admin', 'business', 'user'], true)) {
            $this->flash('error', 'El rol indicado no es válido.');

            return $this->redirect($response, '/admin/users');
        }

        $updated = $this->accountRepository->update($id, [
            'email' => $email,
            'role' => $role,
            'business_name' => trim((string) ($data['business_name'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
        ]);

        if ($updated === null) {
            $this->flash('error', 'No existe el usuario que intentaste modificar.');

            return $this->redirect($response, '/admin/users');
        }

        $this->flash('success', 'Los datos del usuario fueron actualizados.');

        return $this->redirect($response, '/admin/users');
    }
}
