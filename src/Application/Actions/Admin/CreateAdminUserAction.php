<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Application\Settings\SettingsInterface;
use App\Domain\User\AccountRepository;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateAdminUserAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository,
        private readonly SettingsInterface $settings
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
        $logoImage = trim((string) ($data['logo_image'] ?? ''));

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

        $logoUrl = null;
        if ($logoImage !== '') {
            $savedLogo = $this->storeLogoImage($logoImage);
            if ($savedLogo === null) {
                $this->flash('error', 'No se pudo guardar el logo recortado. Intenta con otra imagen.');

                return $this->redirect($response, '/admin/users');
            }

            $logoUrl = $savedLogo;
        }

        try {
            $this->accountRepository->create([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'business_name' => $businessName !== '' ? $businessName : null,
                'whatsapp' => $whatsapp !== '' ? $whatsapp : null,
                'logo_url' => $logoUrl,
                'status' => 'active',
            ]);
        } catch (PDOException) {
            $this->flash('error', 'No se pudo crear el usuario en este momento.');

            return $this->redirect($response, '/admin/users');
        }

        $this->flash('success', 'El usuario fue creado correctamente.');

        return $this->redirect($response, '/admin/users');
    }

    private function storeLogoImage(string $logoImage): ?string
    {
        if (!preg_match('#^data:image/(png|jpeg|webp);base64,#', $logoImage, $matches)) {
            return null;
        }

        $encoded = substr($logoImage, strpos($logoImage, ',') + 1);
        $binary = base64_decode($encoded, true);
        if ($binary === false) {
            return null;
        }

        $uploadPath = $this->settings->get('paths')['uploads'];
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return null;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $filename = sprintf('logo-%s.%s', bin2hex(random_bytes(8)), $extension);
        $destination = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        return file_put_contents($destination, $binary) !== false ? '/uploads/' . $filename : null;
    }
}
