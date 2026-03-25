<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Settings\SettingsInterface;
use App\Domain\Site\SettingsRepository;
use RuntimeException;

class PwaManifestManager
{
    private const SETTINGS_KEYS = [
        'app_name',
        'short_name',
        'theme_color',
        'background_color',
        'start_url',
        'display',
        'icon_192',
        'icon_512',
    ];

    private const ALLOWED_DISPLAY = ['standalone', 'fullscreen', 'minimal-ui', 'browser'];

    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly SettingsInterface $settings
    ) {
    }

    public function generateFromSettings(): void
    {
        $stored = $this->settingsRepository->findByKeys(self::SETTINGS_KEYS);
        $manifest = $this->buildManifest($stored);
        $manifestPath = rtrim((string) $this->settings->get('paths')['public'], DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . 'manifest.webmanifest';

        $result = file_put_contents(
            $manifestPath,
            (string) json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        if ($result === false) {
            throw new RuntimeException('No se pudo guardar el manifest de la aplicación.');
        }
    }

    /**
     * @param array<string, string> $stored
     *
     * @return array<string, mixed>
     */
    private function buildManifest(array $stored): array
    {
        $appName = trim((string) ($stored['app_name'] ?? 'OfertasLocales'));
        $shortName = trim((string) ($stored['short_name'] ?? 'Ofertas'));
        $themeColor = $this->sanitizeColor((string) ($stored['theme_color'] ?? '#dc2626'), '#dc2626');
        $backgroundColor = $this->sanitizeColor((string) ($stored['background_color'] ?? '#ffffff'), '#ffffff');
        $startUrl = $this->sanitizeStartUrl((string) ($stored['start_url'] ?? '/'));
        $display = in_array((string) ($stored['display'] ?? 'standalone'), self::ALLOWED_DISPLAY, true)
            ? (string) $stored['display']
            : 'standalone';
        $icon192 = $this->sanitizeIconPath((string) ($stored['icon_192'] ?? ''));
        $icon512 = $this->sanitizeIconPath((string) ($stored['icon_512'] ?? ''));
        $icons = [];

        if ($icon192 !== '') {
            $icons[] = [
                'src' => $icon192,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ];
        }
        if ($icon512 !== '') {
            $icons[] = [
                'src' => $icon512,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ];
        }

        return [
            'name' => $appName !== '' ? $appName : 'OfertasLocales',
            'short_name' => $shortName !== '' ? $shortName : 'Ofertas',
            'start_url' => $startUrl,
            'display' => $display,
            'theme_color' => $themeColor,
            'background_color' => $backgroundColor,
            'lang' => 'es-AR',
            'icons' => $icons,
        ];
    }

    private function sanitizeColor(string $color, string $fallback): string
    {
        $normalized = trim($color);

        return preg_match('/^#[0-9a-fA-F]{6}$/', $normalized) === 1 ? strtolower($normalized) : $fallback;
    }

    private function sanitizeStartUrl(string $startUrl): string
    {
        $normalized = trim($startUrl);

        if ($normalized === '' || str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return '/';
        }

        return str_starts_with($normalized, '/') ? $normalized : '/' . $normalized;
    }

    private function sanitizeIconPath(string $iconPath): string
    {
        $normalized = trim($iconPath);
        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $normalized;
        }

        return str_starts_with($normalized, '/') ? $normalized : '/' . $normalized;
    }
}
