<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use App\Domain\Site\SeoRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OffersAction extends PageAction
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
        $queryParams = $request->getQueryParams();
        $selectedBusinessId = isset($queryParams['negocio']) && ctype_digit((string) $queryParams['negocio'])
            ? (int) $queryParams['negocio']
            : null;
        $catalog = $this->publicCatalogService->buildCatalog($selectedBusinessId);

        if ($selectedBusinessId !== null && $catalog['selectedBusiness'] === null) {
            $selectedBusinessId = null;
            $catalog = $this->publicCatalogService->buildCatalog();
        }

        $labels = $this->settingsRepository->findByKeys([
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
        ]);
        $seo = $this->seoRepository->findByPage('ofertas') ?? [];

        return $this->renderPage($response, 'pages/ofertas.php', [
            'pageTitle' => $seo['title'] ?? 'Ofertas activas | OfertasCerca',
            'metaDescription' => $seo['meta_description'] ?? null,
            'ogImage' => $seo['og_image'] ?? null,
            'currentRoute' => 'ofertas',
            'labels' => $labels,
            'offers' => $catalog['offers'],
            'totalOffers' => count($catalog['offers']),
            'selectedBusiness' => $catalog['selectedBusiness'],
            'pageData' => [
                'offers' => $catalog['offers'],
                'categories' => array_merge(['Todas'], $catalog['categories']),
                'selectedBusinessId' => $catalog['selectedBusinessId'],
            ],
        ]);
    }
}
