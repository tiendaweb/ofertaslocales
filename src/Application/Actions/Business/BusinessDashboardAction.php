<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BusinessDashboardAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly SettingsRepository $settingsRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->currentUser();
        $offers = $this->offerRepository->findByUserId((int) $user['id']);
        $settings = $this->settingsRepository->findByKeys(['approval_mode']);

        return $this->renderPage($response, 'pages/admin/panel.php', [
            'pageTitle' => 'Panel del negocio | OfertasCerca',
            'currentRoute' => 'panel',
            'offers' => $offers,
            'approvalMode' => $settings['approval_mode'] ?? 'manual',
        ]);
    }
}
