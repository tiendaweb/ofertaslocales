<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAdminCategoryStatusAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly CategoryRepository $categoryRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $categoryId = isset($args['id']) ? (int) $args['id'] : 0;
        $status = trim((string) (((array) $request->getParsedBody())['status'] ?? ''));
        $adminId = (int) ($this->currentUser()['id'] ?? 0);

        if ($categoryId <= 0 || !in_array($status, ['approved', 'rejected'], true)) {
            $this->flash('error', 'La actualización de categoría no es válida.');

            return $this->redirect($response, '/admin?tab=categorias');
        }

        $updated = $this->categoryRepository->updateStatus($categoryId, $status, $adminId);
        $this->flash($updated ? 'success' : 'error', $updated
            ? 'El estado de la categoría fue actualizado.'
            : 'No se pudo actualizar la categoría seleccionada.');

        return $this->redirect($response, '/admin?tab=categorias');
    }
}
