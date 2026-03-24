<?php

declare(strict_types=1);

namespace App\Application\Actions\Business;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\OfferRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateBusinessOfferAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly OfferRepository $offerRepository,
        private readonly CategoryRepository $categoryRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $this->currentUser();
        $userId = (int) ($user['id'] ?? 0);
        $offerId = isset($args['id']) ? (int) $args['id'] : 0;
        if ($offerId <= 0 || $userId <= 0) {
            $this->flash('error', 'No se pudo identificar la oferta seleccionada.');

            return $this->redirect($response, '/panel');
        }

        $data = (array) $request->getParsedBody();
        $operation = (string) ($data['operation'] ?? '');

        return match ($operation) {
            'editar' => $this->handleEdit($response, $userId, $offerId, $data),
            'estado' => $this->handleStatusChange($response, $userId, $offerId, $data),
            'duplicar_renovar' => $this->handleDuplicateOrRenew($response, $userId, $offerId),
            default => $this->invalidOperation($response),
        };
    }

    private function handleEdit(Response $response, int $userId, int $offerId, array $data): Response
    {
        $payload = [
            'title' => trim((string) ($data['title'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'category' => trim((string) ($data['category'] ?? '')),
            'whatsapp' => trim((string) ($data['whatsapp'] ?? '')),
            'location' => trim((string) ($data['location'] ?? '')),
        ];

        foreach (['title', 'description', 'category', 'whatsapp'] as $requiredField) {
            if ($payload[$requiredField] === '') {
                $this->flash('error', 'Todos los campos de edición son obligatorios.');

                return $this->redirect($response, '/panel');
            }
        }

        if (!$this->categoryRepository->isApproved($payload['category'])) {
            $this->flash('error', 'La categoría seleccionada no está aprobada por administración.');

            return $this->redirect($response, '/panel');
        }

        $updated = $this->offerRepository->updateForUser($offerId, $userId, $payload);
        if ($updated !== true) {
            $this->flash('error', 'No tienes permisos para editar esta oferta o ya no existe.');

            return $this->redirect($response, '/panel');
        }

        $this->flash('success', 'La oferta se actualizó correctamente.');

        return $this->redirect($response, '/panel');
    }

    private function handleStatusChange(Response $response, int $userId, int $offerId, array $data): Response
    {
        $status = (string) ($data['status'] ?? '');
        $allowedStatuses = ['pending', 'active', 'rejected', 'expired'];
        if (!in_array($status, $allowedStatuses, true)) {
            $this->flash('error', 'El estado seleccionado no es válido.');

            return $this->redirect($response, '/panel');
        }

        $updated = $this->offerRepository->updateStatusForUser($offerId, $userId, $status);
        if ($updated !== true) {
            $this->flash('error', 'No tienes permisos para cambiar el estado de esta oferta.');

            return $this->redirect($response, '/panel');
        }

        $this->flash('success', 'El estado de la oferta fue actualizado.');

        return $this->redirect($response, '/panel');
    }

    private function handleDuplicateOrRenew(Response $response, int $userId, int $offerId): Response
    {
        $newOfferId = $this->offerRepository->duplicateForUser($offerId, $userId);
        if ($newOfferId === null) {
            $this->flash('error', 'No se pudo duplicar o renovar la oferta seleccionada.');

            return $this->redirect($response, '/panel');
        }

        $this->flash('success', 'Se creó una nueva oferta duplicada para renovar la publicación.');

        return $this->redirect($response, '/panel');
    }

    private function invalidOperation(Response $response): Response
    {
        $this->flash('error', 'La acción solicitada no es válida.');

        return $this->redirect($response, '/panel');
    }
}
