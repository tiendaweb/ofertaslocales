<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\User\AccountRepository;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateAdminUserAction extends PageAction
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
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $role = (string) ($data['role'] ?? 'user');
        $businessName = trim((string) ($data['business_name'] ?? ''));
        $whatsapp = trim((string) ($data['whatsapp'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Debes ingresar un correo electrónico válido para crear el usuario.');

            return $this->redirect($response, '/admin/users');
        }

        if (!in_array($role, ['admin', 'business', 'user'], true)) {
            $this->flash('error', 'El rol indicado no es válido.');

            return $this->redirect($response, '/admin/users');
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'La contraseña inicial debe tener al menos 8 caracteres.');

            return $this->redirect($response, '/admin/users');
        }

        if ($this->accountRepository->findByEmail($email) !== null) {
            $this->flash('error', 'Ya existe un usuario con ese correo electrónico.');

            return $this->redirect($response, '/admin/users');
        }

        try {
            $this->accountRepository->create([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'business_name' => $businessName !== '' ? $businessName : null,
                'whatsapp' => $whatsapp !== '' ? $whatsapp : null,
                'status' => 'active',
            ]);
        } catch (PDOException) {
            $this->flash('error', 'No se pudo crear el usuario en este momento.');

            return $this->redirect($response, '/admin/users');
        }

        $this->flash('success', 'El usuario fue creado correctamente.');

        return $this->redirect($response, '/admin/users');
    }
}
