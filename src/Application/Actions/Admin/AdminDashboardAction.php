<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SeoRepository;
use App\Domain\Site\SettingsRepository;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminDashboardAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly AccountRepository $accountRepository,
        private readonly SettingsRepository $settingsRepository,
        private readonly SeoRepository $seoRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();
        $page = max(1, (int) ($queryParams['page'] ?? 1));
        $perPage = max(1, min(30, (int) ($queryParams['per_page'] ?? 10)));

        return $this->renderPage($response, 'pages/admin/admin.php', [
            'pageTitle' => 'Administración | OfertasLocales',
            'currentRoute' => 'admin',
            'activeTab' => (string) ($queryParams['tab'] ?? 'moderacion'),
            'pendingOffers' => $this->offerRepository->countPendingOffers(),
            'adminCount' => $this->accountRepository->countByRole('admin'),
            'businessCount' => $this->accountRepository->countByRole('business'),
            'offers' => $this->offerRepository->findForModeration(),
            'settings' => $this->settingsRepository->findAll(),
            'settingsAuditLogs' => $this->settingsRepository->findAuditLogs([
                'contact_whatsapp',
                'site_name',
                'maintenance_mode',
                'custom_css_frontend',
                'custom_js_frontend',
                'custom_css_panel',
                'custom_js_panel',
            ], 25),
            'seoPages' => $this->seoRepository->findAll(),
            'usersPagination' => $this->accountRepository->findAllPaginated($page, $perPage),
            'categories' => $this->categoryRepository->findAll(),
            'pendingCategories' => $this->categoryRepository->findPending(),
        ]);
    }
}
