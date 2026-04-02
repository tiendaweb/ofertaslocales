<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Application\Service\OfferPublishPolicy;
use App\Application\Support\Whatsapp;
use App\Application\Settings\SettingsInterface;
use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SettingsRepository;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class CreateOfferAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly SettingsRepository $settingsRepository,
        private readonly SettingsInterface $settings,
        private readonly OfferPublishPolicy $offerPublishPolicy,
        private readonly CategoryRepository $categoryRepository,
        private readonly AccountRepository $accountRepository,
        private readonly Whatsapp $whatsappHelper
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $user = $this->currentUser();
        $errors = [];

        $payload = [
            'category' => trim((string) ($data['category'] ?? '')),
            'requested_category' => trim((string) ($data['requested_category'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? ($user['whatsapp'] ?? ''))),
            'expires_at' => gmdate('Y-m-d\TH:i', strtotime('+24 hours')),
        ];
        $normalizedWhatsapp = $this->whatsappHelper->normalize($payload['whatsapp']);
        $payload['whatsapp'] = $normalizedWhatsapp;

        foreach (
            [
                'category' => 'categoría',
                'title' => 'título',
                'description' => 'descripción',
                'whatsapp' => 'WhatsApp',
            ] as $field => $label
        ) {
            if ($payload[$field] === '') {
                $errors[$field] = sprintf('La %s es obligatoria.', $label);
            }
        }

        if ($normalizedWhatsapp !== '' && !$this->whatsappHelper->isValid($normalizedWhatsapp)) {
            $errors['whatsapp'] = 'Ingresa un WhatsApp válido en formato internacional (ej: 54911XXXXXXXX).';
        }

        $expiresAt = date_create($payload['expires_at']);
        if ($expiresAt === false) {
            $errors['expires_at'] = 'Ingresa una fecha de expiración válida.';
        } elseif ($expiresAt <= new \DateTimeImmutable('now')) {
            $errors['expires_at'] = 'La fecha de expiración debe ser futura.';
        }

        if (!$this->categoryRepository->isApproved($payload['category'])) {
            $errors['category'] = 'Selecciona una categoría aprobada por administración.';
        }

        $businessProfile = $this->accountRepository->findById((int) ($user['id'] ?? 0));
        $location = $this->buildBusinessLocation($businessProfile ?? []);
        $lat = $businessProfile !== null && $businessProfile['address_lat'] !== null ? (float) $businessProfile['address_lat'] : null;
        $lon = $businessProfile !== null && $businessProfile['address_lon'] !== null ? (float) $businessProfile['address_lon'] : null;

        if ($location === '') {
            $errors['location'] = 'Tu negocio debe tener una dirección registrada para publicar ofertas.';
        }

        if ($lat === null || $lon === null) {
            $errors['coordinates'] = 'Tu negocio necesita coordenadas registradas. Completa tu perfil comercial.';
        }

        $imageUrl = null;
        $uploadedFiles = $request->getUploadedFiles();
        $image = $uploadedFiles['image'] ?? null;

        if ($image instanceof UploadedFileInterface && $image->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeImage($image);
            if (($uploadResult['error'] ?? null) !== null) {
                $errors['image'] = (string) $uploadResult['error'];
            } else {
                $imageUrl = $uploadResult['path'] ?? null;
            }
        }

        if ($errors !== []) {
            $_SESSION['offer_draft'] = [
                'category' => $payload['category'],
                'title' => $payload['title'],
                'description' => $payload['description'],
                'whatsapp' => $payload['whatsapp'],
            ];

            if ($payload['requested_category'] !== '') {
                $this->categoryRepository->requestCategory($payload['requested_category'], (int) ($user['id'] ?? 0));
                $this->flash('success', 'La nueva categoría fue enviada para aprobación del administrador.');
            }

            $this->flashFormErrors($errors, $payload);

            return $this->redirect($response, '/panel');
        }

        $settings = $this->settingsRepository->findByKeys(['approval_mode']);
        $policy = $this->offerPublishPolicy->resolve($user, $settings);

        if (($policy['can_publish'] ?? false) !== true) {
            $this->flash('error', (string) ($policy['blocked_reason'] ?? 'No tienes permisos para publicar ofertas.'));

            return $this->redirect($response, '/panel');
        }

        $status = (string) ($policy['status'] ?? 'pending');

        $this->offerRepository->createForUser((int) $user['id'], [
            'category' => $payload['category'],
            'title' => $payload['title'],
            'description' => $payload['description'],
            'image_url' => $imageUrl,
            'whatsapp' => $payload['whatsapp'],
            'location' => $location,
            'lat' => $lat,
            'lon' => $lon,
            'status' => $status,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        $this->flash('success', $status === 'active'
            ? 'La oferta fue publicada y ya está visible.'
            : 'La oferta fue creada y quedó pendiente de revisión.');
        unset($_SESSION['offer_draft']);

        return $this->redirect($response, '/panel');
    }

    private function buildBusinessLocation(array $profile): string
    {
        $street = trim((string) ($profile['street'] ?? ''));
        $streetNumber = trim((string) ($profile['street_number'] ?? ''));
        $betweenStreets = trim((string) ($profile['between_streets'] ?? ''));
        $postalCode = trim((string) ($profile['postal_code'] ?? ''));
        $city = trim((string) ($profile['city'] ?? ''));
        $province = trim((string) ($profile['province'] ?? ''));

        $segments = array_filter([
            trim($street . ' ' . $streetNumber),
            $betweenStreets !== '' ? 'Entre calles: ' . $betweenStreets : '',
            $postalCode !== '' ? 'CP ' . $postalCode : '',
            $city,
            $province,
        ], static fn (string $segment): bool => $segment !== '');

        return implode(', ', $segments);
    }

    private function storeImage(UploadedFileInterface $image): array
    {
        if ($image->getError() !== UPLOAD_ERR_OK) {
            return ['error' => 'No se pudo subir la imagen seleccionada.'];
        }

        $mimeType = $image->getClientMediaType() ?? '';
        $allowedExtensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($allowedExtensions[$mimeType])) {
            return ['error' => 'La imagen debe estar en formato JPG, PNG o WEBP.'];
        }

        $uploadPath = $this->settings->get('paths')['uploads'];
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return ['error' => 'No se pudo preparar la carpeta de imágenes.'];
        }

        if (!is_writable($uploadPath)) {
            return ['error' => 'La carpeta de imágenes no tiene permisos de escritura.'];
        }

        try {
            $filename = sprintf('oferta-%s.%s', bin2hex(random_bytes(8)), $allowedExtensions[$mimeType]);
            $image->moveTo($uploadPath . DIRECTORY_SEPARATOR . $filename);
        } catch (\Throwable $exception) {
            $this->logger->error('Error al guardar imagen de oferta.', [
                'message' => $exception->getMessage(),
            ]);

            return ['error' => 'No se pudo guardar la imagen. Verifica permisos e intenta nuevamente.'];
        }

        return ['path' => '/uploads/' . $filename];
    }
}
