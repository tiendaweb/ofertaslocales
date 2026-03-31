<?php

declare(strict_types=1);

namespace App\Application\Actions\Auth;

use App\Application\Actions\PageAction;
use App\Application\Auth\AuthService;
use App\Application\Service\OfferPublishPolicy;
use App\Domain\User\AccountRepository;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SettingsRepository;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterSubmitAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository,
        private readonly AuthService $authService,
        private readonly OfferRepository $offerRepository,
        private readonly SettingsRepository $settingsRepository,
        private readonly OfferPublishPolicy $offerPublishPolicy
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $role = 'business';
        $businessType = trim((string) ($data['business_type'] ?? 'comercio'));
        $businessName = trim((string) ($data['business_name'] ?? ''));
        $whatsapp = trim((string) ($data['whatsapp'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $passwordConfirmation = (string) ($data['password_confirmation'] ?? '');
        $street = trim((string) ($data['street'] ?? ''));
        $streetNumber = trim((string) ($data['street_number'] ?? ''));
        $postalCode = trim((string) ($data['postal_code'] ?? ''));
        $betweenStreets = trim((string) ($data['between_streets'] ?? ''));
        $city = trim((string) ($data['city'] ?? ''));
        $municipality = trim((string) ($data['municipality'] ?? ''));
        $province = trim((string) ($data['province'] ?? ''));
        $addressLat = trim((string) ($data['address_lat'] ?? ''));
        $addressLon = trim((string) ($data['address_lon'] ?? ''));
        $bio = trim((string) ($data['bio'] ?? ''));
        $instagramUrl = trim((string) ($data['instagram_url'] ?? ''));
        $facebookUrl = trim((string) ($data['facebook_url'] ?? ''));
        $tiktokUrl = trim((string) ($data['tiktok_url'] ?? ''));
        $websiteUrl = trim((string) ($data['website_url'] ?? ''));
        $logoUrl = trim((string) ($data['logo_url'] ?? ''));
        $coverUrl = trim((string) ($data['cover_url'] ?? ''));
        $offerDraft = [
            'category' => trim((string) ($data['draft_category'] ?? ($data['category'] ?? ''))),
            'title' => trim((string) ($data['draft_title'] ?? ($data['title'] ?? ''))),
            'description' => trim((string) ($data['draft_description'] ?? ($data['description'] ?? ''))),
            'whatsapp' => trim((string) ($data['draft_whatsapp'] ?? '')),
            'location' => trim((string) ($data['draft_location'] ?? ($data['location'] ?? ''))),
            'business_name' => $businessName,
            'image_url' => trim((string) ($data['draft_image_url'] ?? '')),
        ];

        if (!in_array($businessType, ['comercio', 'emprendedor', 'servicio'], true)) {
            $businessType = 'comercio';
        }

        $errors = [];

        if ($businessName === '') {
            $errors['business_name'] = 'El nombre del local es obligatorio para registrar un negocio.';
        }

        if ($whatsapp === '') {
            $errors['whatsapp'] = 'El WhatsApp es obligatorio para registrar un negocio.';
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
            'city' => ['value' => $city, 'label' => 'ciudad'],
            'municipality' => ['value' => $municipality, 'label' => 'municipio'],
            'province' => ['value' => $province, 'label' => 'provincia'],
        ];

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

        if ($bio !== '' && mb_strlen($bio) > 280) {
            $errors['bio'] = 'La bio corta no puede superar los 280 caracteres.';
        }

        $socialUrlMap = [
            'instagram_url' => ['label' => 'Instagram', 'value' => $instagramUrl],
            'facebook_url' => ['label' => 'Facebook', 'value' => $facebookUrl],
            'tiktok_url' => ['label' => 'TikTok', 'value' => $tiktokUrl],
            'website_url' => ['label' => 'sitio web', 'value' => $websiteUrl],
            'logo_url' => ['label' => 'logo', 'value' => $logoUrl],
            'cover_url' => ['label' => 'portada', 'value' => $coverUrl],
        ];
        $sanitizedUrls = [];
        foreach ($socialUrlMap as $field => $config) {
            $normalized = $this->normalizeUrl((string) $config['value']);
            if ($normalized === false) {
                $errors[$field] = sprintf('La URL de %s no es válida.', $config['label']);
                continue;
            }

            $sanitizedUrls[$field] = $normalized;
        }

        $old = [
            'email' => $email,
            'role' => $role,
            'business_type' => $businessType,
            'business_name' => $businessName,
            'whatsapp' => $whatsapp,
            'street' => $street,
            'street_number' => $streetNumber,
            'postal_code' => $postalCode,
            'between_streets' => $betweenStreets,
            'city' => $city,
            'municipality' => $municipality,
            'province' => $province,
            'address_lat' => $addressLat,
            'address_lon' => $addressLon,
            'bio' => $bio,
            'instagram_url' => $instagramUrl,
            'facebook_url' => $facebookUrl,
            'tiktok_url' => $tiktokUrl,
            'website_url' => $websiteUrl,
            'logo_url' => $logoUrl,
            'cover_url' => $coverUrl,
            'category' => $offerDraft['category'],
            'title' => $offerDraft['title'],
            'description' => $offerDraft['description'],
            'location' => $offerDraft['location'],
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
                'business_type' => $businessType,
                'business_name' => $businessName !== '' ? $businessName : null,
                'whatsapp' => $whatsapp,
                'street' => $street !== '' ? $street : null,
                'street_number' => $streetNumber !== '' ? $streetNumber : null,
                'postal_code' => $postalCode !== '' ? $postalCode : null,
                'between_streets' => $betweenStreets !== '' ? $betweenStreets : null,
                'city' => $city !== '' ? $city : null,
                'municipality' => $municipality !== '' ? $municipality : null,
                'province' => $province !== '' ? $province : null,
                'address_lat' => $addressLat !== '' ? (float) $addressLat : null,
                'address_lon' => $addressLon !== '' ? (float) $addressLon : null,
                'bio' => $bio !== '' ? $bio : null,
                'instagram_url' => $sanitizedUrls['instagram_url'] ?? null,
                'facebook_url' => $sanitizedUrls['facebook_url'] ?? null,
                'tiktok_url' => $sanitizedUrls['tiktok_url'] ?? null,
                'website_url' => $sanitizedUrls['website_url'] ?? null,
                'logo_url' => $sanitizedUrls['logo_url'] ?? null,
                'cover_url' => $sanitizedUrls['cover_url'] ?? null,
            ]);
        } catch (PDOException) {
            $this->flashFormErrors([
                'general' => 'No pudimos crear la cuenta en este momento. Intenta nuevamente.',
            ], $old);

            return $this->redirect($response, '/register');
        }

        $this->authService->login($account);
        $offerDraft = $this->hydrateOfferDraftFromSession($offerDraft);
        $_SESSION['offer_draft'] = $offerDraft;
        $this->flash('success', '¡Tu cuenta de negocio está lista! Ahora podés publicar tu primera oferta.');

        return $this->redirect($response, '/panel?open_offer_wizard=1');
    }

    private function isPublishableDraft(array $offerDraft): bool
    {
        foreach (['category', 'title', 'description', 'whatsapp', 'location'] as $field) {
            if (trim((string) ($offerDraft[$field] ?? '')) === '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeUrl(string $url): string|false|null
    {
        if ($url === '') {
            return null;
        }

        $normalized = preg_match('#^https?://#i', $url) === 1
            ? $url
            : 'https://' . ltrim($url, '/');

        if (filter_var($normalized, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($normalized);
        if (!is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        return $normalized;
    }

    private function hydrateOfferDraftFromSession(array $offerDraft): array
    {
        $sessionDraft = is_array($_SESSION['offer_draft'] ?? null) ? $_SESSION['offer_draft'] : [];
        if ($sessionDraft === []) {
            return $offerDraft;
        }

        foreach (['category', 'title', 'description', 'whatsapp', 'location', 'business_name', 'image_url'] as $field) {
            if (($offerDraft[$field] ?? '') === '' && isset($sessionDraft[$field])) {
                $offerDraft[$field] = trim((string) $sessionDraft[$field]);
            }
        }

        return $offerDraft;
    }

    private function publishDraftOffer(int $userId, array $account, array $offerDraft): bool
    {
        $settings = $this->settingsRepository->findByKeys(['approval_mode', 'default_user_publish_mode']);
        $policy = $this->offerPublishPolicy->resolve($account, $settings);
        if (($policy['can_publish'] ?? false) !== true) {
            return false;
        }

        $this->offerRepository->createForUser($userId, [
            'category' => trim((string) $offerDraft['category']),
            'title' => trim((string) $offerDraft['title']),
            'description' => trim((string) $offerDraft['description']),
            'image_url' => trim((string) ($offerDraft['image_url'] ?? '')) ?: null,
            'whatsapp' => trim((string) $offerDraft['whatsapp']),
            'location' => trim((string) $offerDraft['location']),
            'lat' => null,
            'lon' => null,
            'status' => (string) ($policy['status'] ?? 'pending'),
            'expires_at' => gmdate('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);

        return true;
    }
}
