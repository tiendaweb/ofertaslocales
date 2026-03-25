<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAdminCategoryAction extends PageAction
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
        $name = trim((string) (((array) $request->getParsedBody())['name'] ?? ''));
        $adminId = (int) ($this->currentUser()['id'] ?? 0);

        if ($categoryId <= 0 || $name === '') {
            $this->flash('error', 'Debes indicar una categoría válida para editar.');

            return $this->redirect($response, '/admin?tab=categorias');
        }

        $updated = $this->categoryRepository->updateName($categoryId, $name, $adminId);
        $this->flash($updated ? 'success' : 'error', $updated
            ? 'La categoría fue actualizada correctamente.'
            : 'No se pudo actualizar la categoría. Verifica que el nombre no esté duplicado.');

        return $this->redirect($response, '/admin?tab=categorias');
    }
}
