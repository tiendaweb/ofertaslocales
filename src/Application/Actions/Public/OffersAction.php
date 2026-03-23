<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OffersAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $offers = $this->offerRepository->findActiveOffers();
        $categories = array_values(array_unique(array_map(
            static fn (array $offer): string => $offer['category'],
            $offers
        )));

        return $this->renderPage($response, 'pages/ofertas.php', [
            'pageTitle' => 'Ofertas activas | OfertasCerca',
            'currentRoute' => 'ofertas',
            'pageData' => [
                'offers' => array_map([$this, 'normalizeOffer'], $offers),
                'categories' => array_merge(['Todas'], $categories),
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
