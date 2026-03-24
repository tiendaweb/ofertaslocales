<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
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
        'default_user_publish_mode',
    ];

    private const ALLOWED_USER_PUBLISH_MODES = ['direct', 'review', 'profile_required'];

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SettingsRepository $settingsRepository,
        private readonly SettingsInterface $settings
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $payload = [];

        foreach (self::EDITABLE_KEYS as $key) {
            $payload[$key] = trim((string) ($data[$key] ?? ''));
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

        if (!in_array($payload['default_user_publish_mode'], self::ALLOWED_USER_PUBLISH_MODES, true)) {
            $payload['default_user_publish_mode'] = 'review';
        }

        $this->settingsRepository->updateMany($payload);
        $this->flash('success', 'Los textos y reglas de publicación fueron actualizados.');

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
