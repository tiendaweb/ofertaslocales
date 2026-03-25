<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeleteAdminCategoryAction extends PageAction
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

        if ($categoryId <= 0) {
            $this->flash('error', 'La categoría seleccionada no es válida.');

            return $this->redirect($response, '/admin?tab=categorias');
        }

        $deleted = $this->categoryRepository->delete($categoryId);
        $this->flash($deleted ? 'success' : 'error', $deleted
            ? 'La categoría fue eliminada.'
            : 'No pudimos eliminar la categoría seleccionada.');

        return $this->redirect($response, '/admin?tab=categorias');
    }
}
