<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\PageAction;
use App\Application\Auth\AuthService;
use App\Domain\User\AccountRepository;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterSubmitAction extends PageAction
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
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $role = trim((string) ($data['role'] ?? 'user'));
        $businessName = trim((string) ($data['business_name'] ?? ''));
        $whatsapp = trim((string) ($data['whatsapp'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirmation = (string) ($data['password_confirmation'] ?? '');
        $street = trim((string) ($data['street'] ?? ''));
        $streetNumber = trim((string) ($data['street_number'] ?? ''));
        $postalCode = trim((string) ($data['postal_code'] ?? ''));
        $city = trim((string) ($data['city'] ?? ''));
        $municipality = trim((string) ($data['municipality'] ?? ''));
        $province = trim((string) ($data['province'] ?? ''));
        $addressLat = trim((string) ($data['address_lat'] ?? ''));
        $addressLon = trim((string) ($data['address_lon'] ?? ''));

        if (!in_array($role, ['business', 'user'], true)) {
            $role = 'user';
        }

        $errors = [];

        if ($role === 'business' && $businessName === '') {
            $errors['business_name'] = 'El nombre del local es obligatorio para registrar un negocio.';
        }

        if ($whatsapp === '') {
            $errors['whatsapp'] = 'El WhatsApp es obligatorio para publicar ofertas.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Ingresa un correo electrónico válido.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'La confirmación de la contraseña no coincide.';
        }

        if ($this->accountRepository->findByEmail($email) !== null) {
            $errors['email'] = 'Ya existe una cuenta con este correo electrónico.';
        }

        $addressFields = [
            'street' => ['value' => $street, 'label' => 'calle'],
            'street_number' => ['value' => $streetNumber, 'label' => 'número'],
            'postal_code' => ['value' => $postalCode, 'label' => 'código postal'],
            'city' => ['value' => $city, 'label' => 'ciudad'],
            'municipality' => ['value' => $municipality, 'label' => 'municipio'],
            'province' => ['value' => $province, 'label' => 'provincia'],
        ];

        if ($role === 'business') {
            foreach ($addressFields as $field => $config) {
                if ($config['value'] === '') {
                    $errors[$field] = sprintf('El campo %s es obligatorio para negocios.', $config['label']);
                }
            }

            if ($addressLat === '' || !is_numeric($addressLat)) {
                $errors['address_lat'] = 'Selecciona en el mapa la ubicación exacta del negocio.';
            }

            if ($addressLon === '' || !is_numeric($addressLon)) {
                $errors['address_lon'] = 'Selecciona en el mapa la ubicación exacta del negocio.';
            }
        }

        $old = [
            'email' => $email,
            'role' => $role,
            'business_name' => $businessName,
            'whatsapp' => $whatsapp,
            'street' => $street,
            'street_number' => $streetNumber,
            'postal_code' => $postalCode,
            'city' => $city,
            'municipality' => $municipality,
            'province' => $province,
            'address_lat' => $addressLat,
            'address_lon' => $addressLon,
        ];

        if ($errors !== []) {
            $this->flashFormErrors($errors, $old);

            return $this->redirect($response, '/register');
        }

        try {
            $account = $this->accountRepository->createBusinessAccount([
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
                'business_name' => $businessName !== '' ? $businessName : null,
                'whatsapp' => $whatsapp,
                'street' => $street !== '' ? $street : null,
                'street_number' => $streetNumber !== '' ? $streetNumber : null,
                'postal_code' => $postalCode !== '' ? $postalCode : null,
                'city' => $city !== '' ? $city : null,
                'municipality' => $municipality !== '' ? $municipality : null,
                'province' => $province !== '' ? $province : null,
                'address_lat' => $addressLat !== '' ? (float) $addressLat : null,
                'address_lon' => $addressLon !== '' ? (float) $addressLon : null,
            ]);
        } catch (PDOException) {
            $this->flashFormErrors([
                'general' => 'No pudimos crear la cuenta en este momento. Intenta nuevamente.',
            ], $old);

            return $this->redirect($response, '/register');
        }

        $this->authService->login($account);
        $this->flash('success', 'Tu cuenta ya está lista. Ahora puedes publicar ofertas desde tu panel.');

        return $this->redirect($response, '/panel');
    }
}
