<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MapAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicCatalogService $publicCatalogService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $catalog = $this->publicCatalogService->buildCatalog();
        $mapOffers = $catalog['mapOffers'];

        return $this->renderPage($response, 'pages/mapa.php', [
            'pageTitle' => 'Mapa de ofertas | OfertasCerca',
            'currentRoute' => 'mapa',
            'mapOffers' => $mapOffers,
            'coverageLabel' => count($mapOffers) > 0 ? 'Zona activa' : 'Sin datos',
            'pageData' => [
                'mapOffers' => $mapOffers,
                'defaultCenter' => $catalog['defaultCenter'],
            ],
        ]);
    }
}
