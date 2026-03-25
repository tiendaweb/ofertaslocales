<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\User\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EditBusinessProfilePageAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly AccountRepository $accountRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->currentUser();
        if (!is_array($user) || !isset($user['id'])) {
            $this->flash('error', 'Necesitas iniciar sesión para editar tu negocio.');

            return $this->redirect($response, '/login');
        }

        $account = $this->accountRepository->findById((int) $user['id']);
        if ($account === null) {
            $this->flash('error', 'No encontramos tu cuenta comercial.');

            return $this->redirect($response, '/panel');
        }

        return $this->renderPage($response, 'pages/admin/business-edit.php', [
            'pageTitle' => 'Editar negocio | OfertasLocales',
            'currentRoute' => 'panel.negocio.editar',
            'account' => $account,
        ]);
    }
}
