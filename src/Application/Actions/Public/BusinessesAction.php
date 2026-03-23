<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use App\Domain\Site\SeoRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessesAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicCatalogService $publicCatalogService,
        private readonly SeoRepository $seoRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $catalog = $this->publicCatalogService->buildCatalog();
        $businesses = $catalog['businesses'];
        $seo = $this->seoRepository->findByPage('negocios') ?? [];

        return $this->renderPage($response, 'pages/negocios.php', [
            'pageTitle' => $seo['title'] ?? 'Negocios registrados | OfertasCerca',
            'metaDescription' => $seo['meta_description'] ?? null,
            'ogImage' => $seo['og_image'] ?? null,
            'currentRoute' => 'negocios',
            'businesses' => $businesses,
            'summary' => [
                'totalBusinesses' => count($businesses),
                'activeOffers' => count($catalog['offers']),
            ],
        ]);
    }
}
