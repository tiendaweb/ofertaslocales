<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateOfferStatusAction extends PageAction
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
        $status = (string) (((array) $request->getParsedBody())['status'] ?? '');
        $allowedStatuses = ['pending', 'active', 'rejected', 'expired'];
        if (!in_array($status, $allowedStatuses, true)) {
            $this->flash('error', 'El estado seleccionado no es válido.');

            return $this->redirect($response, '/admin');
        }

        $offerId = isset($args['id']) ? (int) $args['id'] : 0;
        if ($offerId <= 0) {
            $this->flash('error', 'No se encontró la oferta a moderar.');

            return $this->redirect($response, '/admin');
        }

        $this->offerRepository->updateStatus($offerId, $status);
        $this->flash('success', 'El estado de la oferta fue actualizado.');

        return $this->redirect($response, '/admin');
    }
}
