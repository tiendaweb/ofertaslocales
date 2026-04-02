<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Site\SeoRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateInlineContentAction extends PageAction
{
    private const SETTINGS_FIELDS = [
        'hero_badge' => 'text',
        'hero_title' => 'textarea',
        'hero_description' => 'textarea',
        'hero_primary_cta' => 'text',
        'hero_primary_cta_url' => 'url',
        'merchant_badge' => 'text',
        'merchant_title' => 'text',
        'merchant_description' => 'textarea',
        'offers_section_badge' => 'text',
        'offers_section_title' => 'text',
        'businesses_section_title' => 'text',
        'businesses_section_description' => 'textarea',
        'businesses_hero_badge' => 'text',
        'businesses_hero_title' => 'textarea',
        'businesses_hero_description' => 'textarea',
        'footer_tagline' => 'text',
        'footer_whatsapp_url' => 'url',
        'footer_link_publish_url' => 'url',
        'footer_link_login_url' => 'url',
        'footer_link_map_url' => 'url',
    ];

    private const SEO_KEYS = [
        'seo.home.og_image' => ['page' => 'home', 'field' => 'og_image'],
        'seo.ofertas.og_image' => ['page' => 'ofertas', 'field' => 'og_image'],
        'seo.negocios.og_image' => ['page' => 'negocios', 'field' => 'og_image'],
        'seo.mapa.og_image' => ['page' => 'mapa', 'field' => 'og_image'],
    ];

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SettingsRepository $settingsRepository,
        private readonly SeoRepository $seoRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = (array) $request->getParsedBody();
        $fields = $data['fields'] ?? [];

        if (!is_array($fields) || $fields === []) {
            return $this->respondJson($response, [
                'ok' => false,
                'message' => 'No se enviaron cambios para guardar.',
            ], 422);
        }

        $settingsPayload = [];
        $seoPayload = [];
        $updated = [];
        $rejected = [];

        foreach ($fields as $key => $value) {
            $normalizedValue = trim((string) $value);

            if (isset(self::SETTINGS_FIELDS[$key])) {
                $validationError = $this->validateByType($normalizedValue, self::SETTINGS_FIELDS[$key]);
                if ($validationError !== null) {
                    $rejected[$key] = $validationError;
                    continue;
                }

                $settingsPayload[$key] = $normalizedValue;
                $updated[] = $key;
                continue;
            }

            if (isset(self::SEO_KEYS[$key])) {
                $validationError = $this->validateByType($normalizedValue, 'url');
                if ($validationError !== null) {
                    $rejected[$key] = $validationError;
                    continue;
                }

                $seoDefinition = self::SEO_KEYS[$key];
                $seoPayload[$seoDefinition['page']][$seoDefinition['field']] = $normalizedValue;
                $updated[] = $key;
                continue;
            }

            $rejected[$key] = 'Clave no permitida para edición inline.';
        }

        if ($settingsPayload === [] && $seoPayload === []) {
            return $this->respondJson($response, [
                'ok' => false,
                'message' => 'Las claves enviadas no están permitidas para edición inline.',
                'updated' => [],
                'rejected' => $rejected,
            ], 422);
        }

        if ($settingsPayload !== []) {
            $this->settingsRepository->updateMany($settingsPayload);
        }

        foreach ($seoPayload as $pageName => $seoChanges) {
            $currentSeo = $this->seoRepository->findByPage($pageName) ?? [];
            $this->seoRepository->updatePage($pageName, [
                'title' => (string) ($currentSeo['title'] ?? ''),
                'meta_description' => (string) ($currentSeo['meta_description'] ?? ''),
                'og_image' => (string) ($seoChanges['og_image'] ?? $currentSeo['og_image'] ?? ''),
            ]);
        }

        return $this->respondJson($response, [
            'ok' => $rejected === [],
            'message' => $rejected === []
                ? 'Cambios guardados correctamente.'
                : 'Se guardaron cambios parciales. Revisá los campos rechazados.',
            'updated' => array_values(array_unique($updated)),
            'rejected' => $rejected,
        ]);
    }

    private function validateByType(string $value, string $type): ?string
    {
        if ($value === '') {
            return 'El valor no puede estar vacío.';
        }

        if ($type !== 'url') {
            return null;
        }

        if (str_starts_with($value, '/')) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return 'La URL no es válida.';
        }

        return null;
    }

    private function respondJson(Response $response, array $payload, int $status = 200): Response
    {
        $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
