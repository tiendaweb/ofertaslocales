<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Offer\OfferRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteAdminOfferAction extends PageAction
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
        $offerId = isset($args['id']) ? (int) $args['id'] : 0;
        if ($offerId <= 0) {
            $this->flash('error', 'No se encontró la oferta a eliminar.');

            return $this->redirect($response, '/admin?tab=moderacion');
        }

        $deleted = $this->offerRepository->deleteByAdmin($offerId);

        if (!$deleted) {
            $this->flash('error', 'No fue posible eliminar la oferta seleccionada.');

            return $this->redirect($response, '/admin?tab=moderacion');
        }

        $this->flash('success', 'La oferta se eliminó correctamente.');

        return $this->redirect($response, '/admin?tab=moderacion');
    }
}
