<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction extends PageAction
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
        $metrics = $catalog['metrics'];

        return $this->renderPage($response, 'pages/index.php', [
            'pageTitle' => 'Ofertas Cerca | Ahorra hoy',
            'currentRoute' => 'inicio',
            'stats' => [
                [
                    'icon' => 'tag',
                    'value' => '+' . $metrics['activeOffers'],
                    'label' => 'Ofertas activas hoy',
                    'containerClass' => 'bg-red-50 text-red-500',
                ],
                [
                    'icon' => 'message-circle',
                    'value' => '+' . $metrics['estimatedContacts'],
                    'label' => 'Consultas estimadas en 24hs',
                    'containerClass' => 'bg-green-50 text-green-500',
                ],
                [
                    'icon' => 'store',
                    'value' => '+' . $metrics['activeBusinesses'],
                    'label' => 'Negocios con ofertas activas',
                    'containerClass' => 'bg-blue-50 text-blue-500',
                ],
            ],
            'howItWorks' => [
                [
                    'step' => 1,
                    'title' => 'Buscás',
                    'description' => 'Explorá el mapa o la lista de ofertas disponibles cerca de tu ubicación actual.',
                    'badgeClass' => 'bg-gray-900 text-white',
                ],
                [
                    'step' => 2,
                    'title' => 'Elegís',
                    'description' => 'Apurate, las ofertas tienen un tiempo límite real y desaparecen cuando expiran.',
                    'badgeClass' => 'bg-red-600 text-white',
                ],
                [
                    'step' => 3,
                    'title' => 'Contactás',
                    'description' => 'A un clic de distancia, escribile directo al dueño del negocio por WhatsApp.',
                    'badgeClass' => 'bg-green-500 text-white',
                ],
            ],
            'merchantBenefits' => [
                'Publicación 100% gratuita por tiempo limitado.',
                'Contacto directo sin intermediarios ni comisiones.',
                'La oferta se muestra con urgencia real según su fecha de vencimiento.',
            ],
            'pageData' => [
                'offers' => $catalog['offers'],
                'categories' => array_merge(['Todas'], $catalog['categories']),
                'mapOffers' => $catalog['mapOffers'],
            ],
        ]);
    }
}
