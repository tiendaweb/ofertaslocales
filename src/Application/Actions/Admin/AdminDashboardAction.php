<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminDashboardAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly AccountRepository $accountRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $this->renderPage($response, 'pages/admin/admin.php', [
            'pageTitle' => 'Administración | OfertasCerca',
            'currentRoute' => 'admin',
            'pendingOffers' => $this->offerRepository->countPendingOffers(),
            'adminCount' => $this->accountRepository->countByRole('admin'),
            'businessCount' => $this->accountRepository->countByRole('business'),
        ]);
    }
}
