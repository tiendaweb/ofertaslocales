<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateUserProfileAction extends PageAction
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
        $user = $this->currentUser();
        if (!is_array($user) || !isset($user['id'])) {
            $this->flash('error', 'Debes iniciar sesión para actualizar tu perfil.');

            return $this->redirect($response, '/login');
        }

        $data = (array) $request->getParsedBody();
        $savedLocations = array_filter(array_map('trim', explode("\n", (string) ($data['saved_locations'] ?? ''))));

        $this->accountRepository->update((int) $user['id'], [
            'street' => trim((string) ($data['street'] ?? '')),
            'street_number' => trim((string) ($data['street_number'] ?? '')),
            'city' => trim((string) ($data['city'] ?? '')),
            'municipality' => trim((string) ($data['municipality'] ?? '')),
            'province' => trim((string) ($data['province'] ?? '')),
            'postal_code' => trim((string) ($data['postal_code'] ?? '')),
            'address_lat' => is_numeric((string) ($data['address_lat'] ?? '')) ? (float) $data['address_lat'] : null,
            'address_lon' => is_numeric((string) ($data['address_lon'] ?? '')) ? (float) $data['address_lon'] : null,
            'saved_locations' => $savedLocations !== [] ? json_encode(array_values($savedLocations), JSON_UNESCAPED_UNICODE) : null,
        ]);

        $newPassword = (string) ($data['new_password'] ?? '');
        $newPasswordConfirmation = (string) ($data['new_password_confirmation'] ?? '');
        if ($newPassword !== '' || $newPasswordConfirmation !== '') {
            if (strlen($newPassword) < 8 || $newPassword !== $newPasswordConfirmation) {
                $this->flash('error', 'La contraseña debe tener al menos 8 caracteres y coincidir en ambos campos.');

                return $this->redirect($response, '/panel/perfil');
            }

            $this->accountRepository->updatePassword((int) $user['id'], password_hash($newPassword, PASSWORD_DEFAULT));
        }

        $this->flash('success', 'Tu perfil fue actualizado correctamente.');

        return $this->redirect($response, '/panel/perfil');
    }
}
