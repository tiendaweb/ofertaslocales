<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Application\Service\PwaManifestManager;
use App\Application\Settings\SettingsInterface;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateSettingsAction extends PageAction
{
    private const EDITABLE_KEYS = [
        'site_name',
        'site_logo_url',
        'hero_badge',
        'hero_title',
        'hero_description',
        'hero_primary_cta',
        'merchant_badge',
        'merchant_title',
        'merchant_description',
        'footer_tagline',
        'app_name',
        'short_name',
        'theme_color',
        'background_color',
        'start_url',
        'display',
        'icon_192',
        'icon_512',
        'location_catalog_json',
        'contact_whatsapp',
        'maintenance_mode',
        'maintenance_message',
        'custom_css_frontend',
        'custom_js_frontend',
        'custom_css_panel',
        'custom_js_panel',
        'safe_mode',
    ];

    private const ALLOWED_PWA_DISPLAY = ['standalone', 'fullscreen', 'minimal-ui', 'browser'];

    private const MAX_LENGTH_BY_KEY = [
        'site_name' => 120,
        'contact_whatsapp' => 25,
        'maintenance_message' => 400,
        'custom_css_frontend' => 12000,
        'custom_js_frontend' => 12000,
        'custom_css_panel' => 12000,
        'custom_js_panel' => 12000,
    ];

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SettingsRepository $settingsRepository,
        private readonly SettingsInterface $settings,
        private readonly PwaManifestManager $pwaManifestManager
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $payload = [];

        foreach (self::EDITABLE_KEYS as $key) {
            if (array_key_exists($key, $data)) {
                $payload[$key] = trim((string) $data[$key]);
            }
        }

        $validationError = $this->validateContentRules($payload);
        if ($validationError !== null) {
            $this->flash('error', $validationError);

            return $this->redirect($response, '/admin?tab=ajustes');
        }

        $siteLogoImage = trim((string) ($data['site_logo_image'] ?? ''));
        if ($siteLogoImage !== '') {
            $savedLogo = $this->storeLogoImage($siteLogoImage);
            if ($savedLogo === null) {
                $this->flash('error', 'No se pudo guardar el logo general del sitio.');

                return $this->redirect($response, '/admin?tab=textos');
            }

            $payload['site_logo_url'] = $savedLogo;
        }

        if (array_key_exists('display', $payload) && !in_array($payload['display'], self::ALLOWED_PWA_DISPLAY, true)) {
            $payload['display'] = 'standalone';
        }
        if (array_key_exists('maintenance_mode', $payload) && !in_array($payload['maintenance_mode'], ['0', '1'], true)) {
            $payload['maintenance_mode'] = '0';
        }
        if (array_key_exists('safe_mode', $payload) && !in_array($payload['safe_mode'], ['0', '1'], true)) {
            $payload['safe_mode'] = '0';
        }
        if (array_key_exists('theme_color', $payload) && preg_match('/^#[0-9a-fA-F]{6}$/', $payload['theme_color']) !== 1) {
            $payload['theme_color'] = '#dc2626';
        }
        if (array_key_exists('background_color', $payload) && preg_match('/^#[0-9a-fA-F]{6}$/', $payload['background_color']) !== 1) {
            $payload['background_color'] = '#ffffff';
        }
        if (array_key_exists('start_url', $payload) && str_starts_with($payload['start_url'], '/')) {
            // válido
        } elseif (array_key_exists('start_url', $payload)) {
            $payload['start_url'] = '/' . ltrim($payload['start_url'], '/');
        }
        if (array_key_exists('location_catalog_json', $payload) && trim($payload['location_catalog_json']) !== '') {
            $decoded = json_decode($payload['location_catalog_json'], true);
            if (!is_array($decoded) || !isset($decoded['provinces'], $decoded['municipalities'])) {
                $this->flash('error', 'El catálogo de ubicaciones debe ser un JSON válido con provinces y municipalities.');

                return $this->redirect($response, '/admin?tab=categorias');
            }
        }

        if ($payload !== []) {
            $currentUser = $this->currentUser() ?? [];
            $this->settingsRepository->updateMany($payload, [
                'user_id' => isset($currentUser['id']) ? (int) $currentUser['id'] : null,
                'email' => isset($currentUser['email']) ? (string) $currentUser['email'] : null,
            ]);
            try {
                $this->pwaManifestManager->generateFromSettings();
            } catch (\RuntimeException) {
                $this->flash('error', 'Los ajustes se guardaron, pero no se pudo regenerar el manifest de la app.');

                return $this->redirect($response, '/admin?tab=aplicacion');
            }
        }

        if ($this->payloadContainsCustomScript($payload)) {
            $this->flash('success', 'Ajustes guardados. Atención: hay código personalizado activo en el sitio o panel.');
        } else {
            $this->flash('success', 'Los ajustes del sitio fueron actualizados.');
        }

        return $this->redirect($response, '/admin?tab=ajustes');
    }

    private function validateContentRules(array $payload): ?string
    {
        foreach (self::MAX_LENGTH_BY_KEY as $key => $maxLength) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }

            $value = (string) $payload[$key];
            if (mb_strlen($value) > $maxLength) {
                return sprintf('El campo "%s" supera el máximo permitido de %d caracteres.', $key, $maxLength);
            }
        }

        foreach (['custom_css_frontend', 'custom_css_panel'] as $cssKey) {
            if (!array_key_exists($cssKey, $payload)) {
                continue;
            }

            $css = (string) $payload[$cssKey];
            if (preg_match('/<\/?script\b/i', $css) === 1 || str_contains($css, '<?')) {
                return 'El CSS personalizado no puede contener etiquetas <script> ni bloques PHP.';
            }
        }

        foreach (['custom_js_frontend', 'custom_js_panel'] as $jsKey) {
            if (!array_key_exists($jsKey, $payload)) {
                continue;
            }

            $js = (string) $payload[$jsKey];
            if (preg_match('/<\/?script\b/i', $js) === 1 || str_contains($js, '<?')) {
                return 'El JavaScript personalizado no puede contener etiquetas <script> ni bloques PHP.';
            }
        }

        return null;
    }

    private function payloadContainsCustomScript(array $payload): bool
    {
        foreach (['custom_css_frontend', 'custom_js_frontend', 'custom_css_panel', 'custom_js_panel'] as $key) {
            if (isset($payload[$key]) && trim((string) $payload[$key]) !== '') {
                return true;
            }
        }

        return false;
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
        $filename = sprintf('logo-sitio-%s.%s', bin2hex(random_bytes(8)), $extension);
        $destination = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        return file_put_contents($destination, $binary) !== false ? '/uploads/' . $filename : null;
    }
}
