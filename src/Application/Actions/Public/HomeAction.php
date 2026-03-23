<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\HomeMetricsService;
use App\Application\Service\PublicOfferService;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicOfferService $publicOfferService,
        private readonly HomeMetricsService $homeMetricsService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $offers = $this->publicOfferService->getActiveOffers();
        $metrics = $this->homeMetricsService->getMetrics();

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
                'offers' => array_map([$this, 'normalizeOffer'], $offers),
                'categories' => array_merge(['Todas'], $this->publicOfferService->getActiveCategories()),
                'mapOffers' => array_map([$this, 'normalizeMapOffer'], $this->publicOfferService->getActiveMapOffers()),
            ],
        ]);
    }

    private function normalizeOffer(array $offer): array
    {
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
            'expires_at' => $expiresAt->format(DATE_ATOM),
            'badge' => $this->resolveBadge($offer['category'], $expiresAt),
        ];
    }

    private function normalizeMapOffer(array $offer): array
    {
        return [
            'id' => (int) $offer['id'],
            'business_name' => (string) $offer['business_name'],
            'title' => (string) $offer['title'],
            'description' => (string) $offer['description'],
            'image_url' => (string) $offer['image_url'],
            'location' => (string) $offer['location'],
            'lat' => (float) $offer['lat'],
            'lon' => (float) $offer['lon'],
        ];
    }

    private function resolveBadge(string $category, DateTimeImmutable $expiresAt): string
    {
        $remainingSeconds = $expiresAt->getTimestamp() - time();

        if ($remainingSeconds <= 14_400) {
            return '⏳ ÚLTIMAS HORAS';
        }

        return match ($category) {
            'Gastronomía' => '🍕 IDEAL CENA',
            'Ferretería' => '🔥 MÁS VENDIDO',
            'Estética' => '✂️ TENDENCIA',
            default => '✨ RECOMENDADA',
        };
    }
}
