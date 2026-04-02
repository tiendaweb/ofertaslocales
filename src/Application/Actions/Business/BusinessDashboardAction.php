<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Application\Service\OfferPublishPolicy;
use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\OfferRepository;
use App\Domain\User\AccountRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessDashboardAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly SettingsRepository $settingsRepository,
        private readonly OfferPublishPolicy $offerPublishPolicy,
        private readonly CategoryRepository $categoryRepository,
        private readonly AccountRepository $accountRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->currentUser();
        if (($user['role'] ?? 'user') === 'user') {
            return $this->redirect($response, '/panel/perfil');
        }

        $offers = $this->offerRepository->findByUserId((int) $user['id']);
        $settings = $this->settingsRepository->findByKeys(['approval_mode']);
        $publishPolicy = $this->offerPublishPolicy->resolve($user, $settings);
        $offerDraft = is_array($_SESSION['offer_draft'] ?? null) ? $_SESSION['offer_draft'] : [];
        $query = $request->getQueryParams();
        $openOfferWizard = (($query['open_offer_wizard'] ?? null) === '1');

        $businessProfile = $this->accountRepository->findById((int) $user['id']) ?? [];

        return $this->renderPage($response, 'pages/admin/panel.php', [
            'pageTitle' => 'Panel del negocio | OfertasLocales',
            'currentRoute' => 'panel',
            'offers' => $offers,
            'approvalMode' => $settings['approval_mode'] ?? 'manual',
            'publishPolicy' => $publishPolicy,
            'offerDraft' => $offerDraft,
            'openOfferWizard' => $openOfferWizard,
            'approvedCategories' => $this->categoryRepository->findApprovedNames(),
            'businessProfile' => $businessProfile,
        ]);
    }
}
