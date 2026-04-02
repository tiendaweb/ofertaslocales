<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Application\Support\Whatsapp;
use App\Domain\Offer\OfferRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAdminOfferAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly Whatsapp $whatsappHelper
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $offerId = isset($args['id']) ? (int) $args['id'] : 0;
        if ($offerId <= 0) {
            $this->flash('error', 'No se encontró la oferta a editar.');

            return $this->redirect($response, '/admin');
        }

        $data = (array) $request->getParsedBody();
        $payload = [
            'category' => trim((string) ($data['category'] ?? '')),
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
            'location' => trim((string) ($data['location'] ?? '')),
        ];
        $payload['whatsapp'] = $this->whatsappHelper->normalize($payload['whatsapp']);

        foreach (['category', 'title', 'description', 'whatsapp', 'location'] as $field) {
            if ($payload[$field] === '') {
                $this->flash('error', 'Completa todos los campos para editar la oferta.');

                return $this->redirect($response, '/admin');
            }
        }

        if (!$this->whatsappHelper->isValid($payload['whatsapp'])) {
            $this->flash('error', 'El WhatsApp debe estar normalizado con formato internacional (ej: 54911XXXXXXXX).');

            return $this->redirect($response, '/admin');
        }

        $updated = $this->offerRepository->updateByAdmin($offerId, $payload);
        if (!$updated) {
            $this->flash('error', 'No se pudo actualizar la oferta seleccionada.');

            return $this->redirect($response, '/admin');
        }

        $this->flash('success', 'Oferta actualizada correctamente desde administración.');

        return $this->redirect($response, '/admin');
    }
}
