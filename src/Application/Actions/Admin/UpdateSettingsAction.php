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
    ];

    private const ALLOWED_PWA_DISPLAY = ['standalone', 'fullscreen', 'minimal-ui', 'browser'];

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
            $this->settingsRepository->updateMany($payload);
            try {
                $this->pwaManifestManager->generateFromSettings();
            } catch (\RuntimeException) {
                $this->flash('error', 'Los ajustes se guardaron, pero no se pudo regenerar el manifest de la app.');

                return $this->redirect($response, '/admin?tab=aplicacion');
            }
        }
        $this->flash('success', 'Los ajustes del sitio fueron actualizados.');

        return $this->redirect($response, '/admin');
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
