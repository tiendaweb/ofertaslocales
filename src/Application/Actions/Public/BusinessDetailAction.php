<?php

declare(strict_types=1);

namespace App\Application\Actions\Public;

use App\Application\Actions\PageAction;
use App\Application\Service\PublicCatalogService;
use App\Domain\Site\SeoRepository;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessDetailAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly PublicCatalogService $publicCatalogService,
        private readonly AccountRepository $accountRepository,
        private readonly SeoRepository $seoRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $businessId = (int) ($args['id'] ?? 0);
        if ($businessId <= 0) {
            return $response->withStatus(404);
        }

        $catalog = $this->publicCatalogService->buildCatalog($businessId);
        $account = $this->accountRepository->findById($businessId);

        if ($account === null || ($account['role'] ?? '') !== 'business') {
            return $response->withStatus(404);
        }

        $selectedBusiness = $catalog['selectedBusiness'];
        $businessName = $selectedBusiness['business_name'] ?? ($account['business_name'] ?: $account['email']);
        $business = [
            'id' => $businessId,
            'business_name' => $businessName,
            'whatsapp' => $selectedBusiness['whatsapp'] ?? ($account['whatsapp'] ?? ''),
            'location' => $selectedBusiness['location'] ?? $this->formatAddress($account),
            'active_offers' => $selectedBusiness['active_offers'] ?? 0,
            'bio' => $selectedBusiness['bio'] ?? ($account['bio'] ?? null),
            'logo_url' => $selectedBusiness['logo_url'] ?? ($account['logo_url'] ?? null),
            'cover_url' => $selectedBusiness['cover_url'] ?? ($account['cover_url'] ?? null),
            'instagram_url' => $selectedBusiness['instagram_url'] ?? ($account['instagram_url'] ?? null),
            'facebook_url' => $selectedBusiness['facebook_url'] ?? ($account['facebook_url'] ?? null),
            'tiktok_url' => $selectedBusiness['tiktok_url'] ?? ($account['tiktok_url'] ?? null),
            'website_url' => $selectedBusiness['website_url'] ?? ($account['website_url'] ?? null),
            'between_streets' => $account['between_streets'] ?? null,
            'business_type' => $account['business_type'] ?? 'comercio',
        ];

        $seo = $this->seoRepository->findByPage('negocios') ?? [];

        return $this->renderPage($response, 'pages/negocio-detalle.php', [
            'pageTitle' => sprintf('%s | Negocio local', $businessName),
            'metaDescription' => $seo['meta_description'] ?? null,
            'ogImage' => $seo['og_image'] ?? null,
            'currentRoute' => 'negocios',
            'business' => $business,
            'activeOffers' => $catalog['offers'],
        ]);
    }

    private function formatAddress(array $account): string
    {
        $betweenStreets = trim((string) ($account['between_streets'] ?? ''));
        $postalCode = trim((string) ($account['postal_code'] ?? ''));
        $segments = [
            trim(sprintf('%s %s', (string) ($account['street'] ?? ''), (string) ($account['street_number'] ?? ''))),
            $betweenStreets !== '' ? 'Entre ' . $betweenStreets : '',
            $postalCode !== '' ? 'CP ' . $postalCode : '',
            (string) ($account['city'] ?? ''),
            (string) ($account['province'] ?? ''),
        ];

        $address = implode(', ', array_values(array_filter($segments, static fn (string $segment): bool => $segment !== '')));

        return $address !== '' ? $address : 'Dirección no informada';
    }
}
