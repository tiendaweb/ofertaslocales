<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteBusinessOfferAction extends PageAction
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
        $user = $this->currentUser();
        $userId = (int) ($user['id'] ?? 0);
        $offerId = isset($args['id']) ? (int) $args['id'] : 0;

        if ($offerId <= 0 || $userId <= 0) {
            $this->flash('error', 'No se pudo identificar la oferta a eliminar.');

            return $this->redirect($response, '/panel');
        }

        $deleted = $this->offerRepository->softDeleteForUser($offerId, $userId);
        if ($deleted !== true) {
            $this->flash('error', 'No tienes permisos para eliminar esta oferta o ya fue eliminada.');

            return $this->redirect($response, '/panel');
        }

        $this->flash('success', 'La oferta se eliminó del panel.');

        return $this->redirect($response, '/panel');
    }
}
