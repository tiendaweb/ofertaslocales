<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\BusinessDirectoryService;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessesAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly BusinessDirectoryService $businessDirectoryService
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $businesses = array_map(function (array $business): array {
            $business['next_expiration_label'] = $this->formatNextExpiration($business['next_expiration']);

            return $business;
        }, $this->businessDirectoryService->getBusinessesWithActiveOffers());

        return $this->renderPage($response, 'pages/negocios.php', [
            'pageTitle' => 'Negocios registrados | OfertasCerca',
            'currentRoute' => 'negocios',
            'businesses' => $businesses,
            'summary' => [
                'totalBusinesses' => count($businesses),
                'activeOffers' => array_sum(array_map(
                    static fn (array $business): int => (int) $business['active_offers'],
                    $businesses
                )),
            ],
        ]);
    }

    private function formatNextExpiration(?string $nextExpiration): string
    {
        if ($nextExpiration === null || $nextExpiration === '') {
            return 'Sin fecha activa';
        }

        return (new DateTimeImmutable($nextExpiration))->format('d/m H:i');
    }
}
