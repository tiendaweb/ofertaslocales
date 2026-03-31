<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Application\Settings\SettingsInterface;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class UpdateBusinessProfileAction extends PageAction
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
        $user = $this->currentUser();
        if (!is_array($user) || !isset($user['id'])) {
            $this->flash('error', 'Necesitas iniciar sesión para actualizar tu negocio.');

            return $this->redirect($response, '/login');
        }

        $id = (int) $user['id'];
        $data = (array) $request->getParsedBody();

        $businessName = trim((string) ($data['business_name'] ?? ''));
        $whatsapp = trim((string) ($data['whatsapp'] ?? ''));
        $bio = trim((string) ($data['bio'] ?? ''));
        $street = trim((string) ($data['street'] ?? ''));
        $streetNumber = trim((string) ($data['street_number'] ?? ''));
        $postalCode = trim((string) ($data['postal_code'] ?? ''));
        $betweenStreets = trim((string) ($data['between_streets'] ?? ''));
        $city = trim((string) ($data['city'] ?? ''));
        $municipality = trim((string) ($data['municipality'] ?? ''));
        $province = trim((string) ($data['province'] ?? ''));
        $addressLat = trim((string) ($data['address_lat'] ?? ''));
        $addressLon = trim((string) ($data['address_lon'] ?? ''));

        $socialUrlMap = [
            'instagram_url' => ['label' => 'Instagram', 'value' => trim((string) ($data['instagram_url'] ?? ''))],
            'facebook_url' => ['label' => 'Facebook', 'value' => trim((string) ($data['facebook_url'] ?? ''))],
            'tiktok_url' => ['label' => 'TikTok', 'value' => trim((string) ($data['tiktok_url'] ?? ''))],
            'website_url' => ['label' => 'sitio web', 'value' => trim((string) ($data['website_url'] ?? ''))],
            'logo_url' => ['label' => 'logo', 'value' => trim((string) ($data['logo_url'] ?? ''))],
            'cover_url' => ['label' => 'portada', 'value' => trim((string) ($data['cover_url'] ?? ''))],
        ];
        $errors = [];
        $uploadedFiles = $request->getUploadedFiles();
        $logoUpload = $uploadedFiles['logo_image'] ?? null;
        if ($logoUpload instanceof UploadedFileInterface && $logoUpload->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeImage($logoUpload, 'negocio-logo');
            if (($uploadResult['error'] ?? null) !== null) {
                $errors['logo_url'] = (string) $uploadResult['error'];
            } else {
                $socialUrlMap['logo_url']['value'] = (string) ($uploadResult['path'] ?? '');
            }
        }

        $coverUpload = $uploadedFiles['cover_image'] ?? null;
        if ($coverUpload instanceof UploadedFileInterface && $coverUpload->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->storeImage($coverUpload, 'negocio-cover');
            if (($uploadResult['error'] ?? null) !== null) {
                $errors['cover_url'] = (string) $uploadResult['error'];
            } else {
                $socialUrlMap['cover_url']['value'] = (string) ($uploadResult['path'] ?? '');
            }
        }

        if ($businessName === '') {
            $errors['business_name'] = 'El nombre del negocio es obligatorio.';
        }

        if ($whatsapp === '') {
            $errors['whatsapp'] = 'El WhatsApp del negocio es obligatorio.';
        }

        if ($bio !== '' && mb_strlen($bio) > 280) {
            $errors['bio'] = 'La bio corta no puede superar los 280 caracteres.';
        }

        foreach ([
            'street' => ['value' => $street, 'label' => 'calle'],
            'street_number' => ['value' => $streetNumber, 'label' => 'número'],
            'postal_code' => ['value' => $postalCode, 'label' => 'código postal'],
            'city' => ['value' => $city, 'label' => 'ciudad'],
            'municipality' => ['value' => $municipality, 'label' => 'municipio'],
            'province' => ['value' => $province, 'label' => 'provincia'],
        ] as $field => $fieldConfig) {
            if ($fieldConfig['value'] === '') {
                $errors[$field] = sprintf('El campo %s es obligatorio para tu negocio.', $fieldConfig['label']);
            }
        }

        if ($addressLat === '' || !is_numeric($addressLat)) {
            $errors['address_lat'] = 'Selecciona en el mapa una latitud válida.';
        }

        if ($addressLon === '' || !is_numeric($addressLon)) {
            $errors['address_lon'] = 'Selecciona en el mapa una longitud válida.';
        }

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
            'business_name' => $businessName,
            'whatsapp' => $whatsapp,
            'bio' => $bio,
            'street' => $street,
            'street_number' => $streetNumber,
            'postal_code' => $postalCode,
            'between_streets' => $betweenStreets,
            'city' => $city,
            'municipality' => $municipality,
            'province' => $province,
            'address_lat' => $addressLat,
            'address_lon' => $addressLon,
            'instagram_url' => $socialUrlMap['instagram_url']['value'],
            'facebook_url' => $socialUrlMap['facebook_url']['value'],
            'tiktok_url' => $socialUrlMap['tiktok_url']['value'],
            'website_url' => $socialUrlMap['website_url']['value'],
            'logo_url' => $socialUrlMap['logo_url']['value'],
            'cover_url' => $socialUrlMap['cover_url']['value'],
        ];

        if ($errors !== []) {
            $this->flashFormErrors($errors, $old);

            return $this->redirect($response, '/panel/negocio/editar');
        }

        $updated = $this->accountRepository->update($id, [
            'business_name' => $businessName,
            'whatsapp' => $whatsapp,
            'bio' => $bio !== '' ? $bio : null,
            'street' => $street,
            'street_number' => $streetNumber,
            'postal_code' => $postalCode,
            'between_streets' => $betweenStreets !== '' ? $betweenStreets : null,
            'city' => $city,
            'municipality' => $municipality,
            'province' => $province,
            'address_lat' => (float) $addressLat,
            'address_lon' => (float) $addressLon,
            'instagram_url' => $sanitizedUrls['instagram_url'] ?? null,
            'facebook_url' => $sanitizedUrls['facebook_url'] ?? null,
            'tiktok_url' => $sanitizedUrls['tiktok_url'] ?? null,
            'website_url' => $sanitizedUrls['website_url'] ?? null,
            'logo_url' => $sanitizedUrls['logo_url'] ?? null,
            'cover_url' => $sanitizedUrls['cover_url'] ?? null,
        ]);

        if ($updated === null) {
            $this->flash('error', 'No pudimos actualizar tu perfil comercial.');

            return $this->redirect($response, '/panel/negocio/editar');
        }

        $_SESSION['auth']['user']['business_name'] = $updated['business_name'] !== null ? (string) $updated['business_name'] : null;
        $_SESSION['auth']['user']['whatsapp'] = $updated['whatsapp'] !== null ? (string) $updated['whatsapp'] : null;

        $this->flash('success', 'Tu perfil comercial fue actualizado correctamente.');

        return $this->redirect($response, '/panel/negocio/editar');
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

    private function storeImage(UploadedFileInterface $image, string $prefix): array
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
            $filename = sprintf('%s-%s.%s', $prefix, bin2hex(random_bytes(8)), $allowedExtensions[$mimeType]);
            $image->moveTo($uploadPath . DIRECTORY_SEPARATOR . $filename);
        } catch (\Throwable $exception) {
            $this->logger->error('Error al guardar imagen del perfil comercial.', [
                'message' => $exception->getMessage(),
            ]);

            return ['error' => 'No se pudo guardar la imagen. Intenta nuevamente.'];
        }

        return ['path' => '/uploads/' . $filename];
    }
}
