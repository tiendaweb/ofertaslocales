<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Application\Settings\SettingsInterface;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SettingsRepository;
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
        private readonly SettingsInterface $settings
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
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? ($user['whatsapp'] ?? ''))),
            'location' => trim((string) ($data['location'] ?? '')),
            'lat' => $this->normalizeCoordinate($data['lat'] ?? null),
            'lon' => $this->normalizeCoordinate($data['lon'] ?? null),
            'expires_at' => trim((string) ($data['expires_at'] ?? '')),
        ];

        foreach (
            [
                'category' => 'categoría',
                'title' => 'título',
                'description' => 'descripción',
                'whatsapp' => 'WhatsApp',
                'location' => 'ubicación',
            ] as $field => $label
        ) {
            if ($payload[$field] === '') {
                $errors[$field] = sprintf('La %s es obligatoria.', $label);
            }
        }

        if ($payload['lat'] === null xor $payload['lon'] === null) {
            $errors['coordinates'] = 'Debes completar ambas coordenadas o dejarlas vacías.';
        }

        if ($payload['expires_at'] === '') {
            $payload['expires_at'] = gmdate('Y-m-d\TH:i', strtotime('+24 hours'));
        }

        $expiresAt = date_create($payload['expires_at']);
        if ($expiresAt === false) {
            $errors['expires_at'] = 'Ingresa una fecha de expiración válida.';
        } elseif ($expiresAt <= new \DateTimeImmutable('now')) {
            $errors['expires_at'] = 'La fecha de expiración debe ser futura.';
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
            $this->flashFormErrors($errors, $payload);

            return $this->redirect($response, '/panel');
        }

        $approvalMode = $this->settingsRepository->findByKeys(['approval_mode'])['approval_mode'] ?? 'manual';
        $status = $approvalMode === 'auto' ? 'active' : 'pending';

        $this->offerRepository->createForUser((int) $user['id'], [
            'category' => $payload['category'],
            'title' => $payload['title'],
            'description' => $payload['description'],
            'image_url' => $imageUrl,
            'whatsapp' => $payload['whatsapp'],
            'location' => $payload['location'],
            'lat' => $payload['lat'],
            'lon' => $payload['lon'],
            'status' => $status,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);

        $this->flash('success', $status === 'active'
            ? 'La oferta fue publicada y ya está visible.'
            : 'La oferta fue creada y quedó pendiente de revisión.');

        return $this->redirect($response, '/panel');
    }

    private function normalizeCoordinate(mixed $value): ?float
    {
        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return null;
        }

        return is_numeric($stringValue) ? (float) $stringValue : null;
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
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        $filename = sprintf('oferta-%s.%s', bin2hex(random_bytes(8)), $allowedExtensions[$mimeType]);
        $image->moveTo($uploadPath . DIRECTORY_SEPARATOR . $filename);

        return ['path' => '/uploads/' . $filename];
    }
}
