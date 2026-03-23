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
    private const SETTINGS_KEYS = [
        'hero_badge',
        'hero_title',
        'hero_description',
        'hero_primary_cta',
        'hero_primary_cta_url',
        'merchant_badge',
        'merchant_title',
        'merchant_description',
        'footer_tagline',
        'footer_whatsapp_url',
        'footer_link_publish_url',
        'footer_link_login_url',
        'footer_link_map_url',
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

        foreach ($fields as $key => $value) {
            $normalizedValue = trim((string) $value);

            if (in_array($key, self::SETTINGS_KEYS, true)) {
                $settingsPayload[$key] = $normalizedValue;
                continue;
            }

            if (isset(self::SEO_KEYS[$key])) {
                $seoDefinition = self::SEO_KEYS[$key];
                $seoPayload[$seoDefinition['page']][$seoDefinition['field']] = $normalizedValue;
            }
        }

        if ($settingsPayload === [] && $seoPayload === []) {
            return $this->respondJson($response, [
                'ok' => false,
                'message' => 'Las claves enviadas no están permitidas para edición inline.',
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
            'ok' => true,
            'message' => 'Cambios guardados correctamente.',
            'updated' => array_keys($fields),
        ]);
    }

    private function respondJson(Response $response, array $payload, int $status = 200): Response
    {
        $response->getBody()->write((string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
