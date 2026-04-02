<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use App\Domain\Site\SeoRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessesAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicCatalogService $publicCatalogService,
        private readonly SeoRepository $seoRepository,
        private readonly SettingsRepository $settingsRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $catalog = $this->publicCatalogService->buildCatalog();
        $businesses = $catalog['businesses'];
        $labels = $this->settingsRepository->findByKeys([
            'hero_badge',
            'hero_title',
            'hero_description',
            'hero_primary_cta',
            'hero_primary_cta_url',
            'site_logo_url',
            'merchant_badge',
            'merchant_title',
            'merchant_description',
            'offers_section_badge',
            'offers_section_title',
            'businesses_section_title',
            'businesses_section_description',
            'businesses_hero_badge',
            'businesses_hero_title',
            'businesses_hero_description',
            'footer_tagline',
            'footer_whatsapp_url',
            'footer_link_publish_url',
            'footer_link_login_url',
            'footer_link_map_url',
        ]);
        $seo = $this->seoRepository->findByPage('negocios') ?? [];

        return $this->renderPage($response, 'pages/negocios.php', [
            'pageTitle' => $seo['title'] ?? 'Negocios registrados | OfertasLocales',
            'metaDescription' => $seo['meta_description'] ?? null,
            'ogImage' => $seo['og_image'] ?? null,
            'currentRoute' => 'negocios',
            'labels' => $labels,
            'businesses' => $businesses,
            'summary' => [
                'totalBusinesses' => count($businesses),
                'activeOffers' => count($catalog['offers']),
            ],
        ]);
    }
}
