<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicOfferService;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MapAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicOfferService $publicOfferService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $mapOffers = array_values(array_filter(array_map(
            [$this, 'normalizeMapOffer'],
            $this->publicOfferService->getActiveMapOffers()
        )));

        return $this->renderPage($response, 'pages/mapa.php', [
            'pageTitle' => 'Mapa de ofertas | OfertasCerca',
            'currentRoute' => 'mapa',
            'mapOffers' => $mapOffers,
            'coverageLabel' => count($mapOffers) > 0 ? 'Zona activa' : 'Sin datos',
            'pageData' => [
                'mapOffers' => $mapOffers,
                'defaultCenter' => $this->resolveDefaultCenter($mapOffers),
            ],
        ]);
    }

    private function normalizeMapOffer(array $offer): ?array
    {
        if ($offer['lat'] === null || $offer['lon'] === null) {
            return null;
        }

        $expiresAt = new DateTimeImmutable($offer['expires_at']);

        return [
            'id' => (int) $offer['id'],
            'business_name' => (string) $offer['business_name'],
            'category' => (string) $offer['category'],
            'title' => (string) $offer['title'],
            'description' => (string) $offer['description'],
            'image_url' => (string) $offer['image_url'],
            'whatsapp' => (string) $offer['whatsapp'],
            'location' => (string) $offer['location'],
            'lat' => (float) $offer['lat'],
            'lon' => (float) $offer['lon'],
            'expires_at' => $expiresAt->format(DATE_ATOM),
            'expires_label' => 'Vence ' . $expiresAt->format('d/m H:i'),
        ];
    }

    private function resolveDefaultCenter(array $mapOffers): array
    {
        if ($mapOffers === []) {
            return [-34.636, -58.536];
        }

        return [(float) $mapOffers[0]['lat'], (float) $mapOffers[0]['lon']];
    }
}
