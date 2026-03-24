<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\OfferPublishPolicy;
use App\Application\Settings\SettingsInterface;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class SubmitPublicOfferAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly SettingsRepository $settingsRepository,
        private readonly SettingsInterface $settings,
        private readonly OfferPublishPolicy $offerPublishPolicy
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();

        $payload = [
            'business_name' => trim((string) ($data['business_name'] ?? '')),
            'category' => trim((string) ($data['category'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
            'location' => trim((string) ($data['location'] ?? '')),
        ];

        if ($payload['description'] === '') {
            $payload['description'] = $payload['title'] !== ''
                ? sprintf('Promoción destacada: %s.', $payload['title'])
                : '';
        }

        $errors = [];
        foreach (
            [
                'business_name' => 'nombre del negocio',
                'category' => 'categoría',
                'title' => 'título de la oferta',
                'whatsapp' => 'WhatsApp',
                'location' => 'ubicación',
            ] as $field => $label
        ) {
            if ($payload[$field] === '') {
                $errors[$field] = sprintf('El campo %s es obligatorio.', $label);
            }
        }

        $uploadedFiles = $request->getUploadedFiles();
        $image = $uploadedFiles['image'] ?? null;
        $imageUrl = null;

        if ($image instanceof UploadedFileInterface && $image->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeImage($image);
            if (($uploadResult['error'] ?? null) !== null) {
                $errors['image'] = (string) $uploadResult['error'];
            } else {
                $imageUrl = $uploadResult['path'] ?? null;
            }
        }

        $_SESSION['offer_draft'] = [
            'business_name' => $payload['business_name'],
            'category' => $payload['category'],
            'title' => $payload['title'],
            'description' => $payload['description'],
            'whatsapp' => $payload['whatsapp'],
            'location' => $payload['location'],
            'image_url' => $imageUrl,
        ];

        if ($errors !== []) {
            $this->flashFormErrors($errors, $payload);

            return $this->redirect($response, '/register');
        }

        $user = $this->currentUser();
        if ($user === null) {
            $this->flash('success', 'Te guardamos la oferta. Completa tu registro para publicarla.');

            return $this->redirect($response, '/register');
        }

        if ((string) ($user['role'] ?? '') !== 'business') {
            $this->flash('error', 'Tu cuenta actual no puede publicar ofertas. Ingresa con una cuenta de negocio.');

            return $this->redirect($response, '/panel');
        }

        $settings = $this->settingsRepository->findByKeys(['approval_mode', 'default_user_publish_mode']);
        $policy = $this->offerPublishPolicy->resolve($user, $settings);

        if (($policy['can_publish'] ?? false) !== true) {
            $this->flash('error', (string) ($policy['blocked_reason'] ?? 'No tienes permisos para publicar ofertas.'));

            return $this->redirect($response, '/panel');
        }

        $this->offerRepository->createForUser((int) $user['id'], [
            'category' => $payload['category'],
            'title' => $payload['title'],
            'description' => $payload['description'],
            'image_url' => $imageUrl,
            'whatsapp' => $payload['whatsapp'],
            'location' => $payload['location'],
            'lat' => null,
            'lon' => null,
            'status' => (string) ($policy['status'] ?? 'pending'),
            'expires_at' => gmdate('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);

        unset($_SESSION['offer_draft']);
        $this->flash('success', 'Oferta recibida correctamente. Ya puedes gestionarla desde tu panel.');

        return $this->redirect($response, '/panel');
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
            $this->logger->error('Error al guardar imagen de oferta desde Home.', [
                'message' => $exception->getMessage(),
            ]);

            return ['error' => 'No se pudo guardar la imagen. Verifica permisos e intenta nuevamente.'];
        }

        return ['path' => '/uploads/' . $filename];
    }
}
