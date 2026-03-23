<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\PageAction;
use App\Application\Auth\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginSubmitAction extends PageAction
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
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ingresa un correo electrónico válido.';
        }

        if ($password === '') {
            $errors['password'] = 'Ingresa tu contraseña.';
        }

        if ($errors !== []) {
            $this->flashFormErrors($errors, ['email' => $email]);

            return $this->redirect($response, '/login');
        }

        $account = $this->authService->attempt($email, $password);
        if ($account === null) {
            $this->flashFormErrors([
                'general' => 'Las credenciales ingresadas no son válidas.',
            ], ['email' => $email]);

            return $this->redirect($response, '/login');
        }

        $this->authService->login($account);
        $this->flash('success', 'Sesión iniciada correctamente.');

        $redirectTo = $account['role'] === 'admin' ? '/admin' : ($account['role'] === 'business' ? '/panel' : '/');

        return $this->redirect($response, $redirectTo);
    }
}
