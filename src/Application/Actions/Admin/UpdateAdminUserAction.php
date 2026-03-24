<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Application\Settings\SettingsInterface;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAdminUserAction extends PageAction
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
        $id = (int) ($args['id'] ?? 0);
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $role = trim((string) ($data['role'] ?? ''));
        $logoImage = trim((string) ($data['logo_image'] ?? ''));

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

        $payload = [
            'email' => $email,
            'role' => $role,
            'business_name' => trim((string) ($data['business_name'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
        ];

        if ($logoImage !== '') {
            $savedLogo = $this->storeLogoImage($logoImage);
            if ($savedLogo === null) {
                $this->flash('error', 'No se pudo guardar el logo recortado. Intenta nuevamente.');

                return $this->redirect($response, '/admin/users');
            }

            $payload['logo_url'] = $savedLogo;
        }

        $updated = $this->accountRepository->update($id, $payload);

        if ($updated === null) {
            $this->flash('error', 'No existe el usuario que intentaste modificar.');

            return $this->redirect($response, '/admin/users');
        }

        $this->flash('success', 'Los datos del usuario fueron actualizados.');

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
