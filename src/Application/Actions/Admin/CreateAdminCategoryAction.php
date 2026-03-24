<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Category\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CreateAdminCategoryAction extends PageAction
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
        $data = (array) $request->getParsedBody();
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            $this->flash('error', 'El nombre de la categoría es obligatorio.');

            return $this->redirect($response, '/admin?tab=categorias');
        }

        $adminId = (int) ($this->currentUser()['id'] ?? 0);
        $created = $this->categoryRepository->createApproved($name, $adminId);

        $this->flash($created ? 'success' : 'error', $created
            ? 'La categoría fue guardada correctamente.'
            : 'No se pudo guardar la categoría.');

        return $this->redirect($response, '/admin?tab=categorias');
    }
}
