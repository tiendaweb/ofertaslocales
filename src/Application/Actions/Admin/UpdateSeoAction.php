<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\PageAction;
use App\Domain\Site\SeoRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateSeoAction extends PageAction
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Infrastructure\View\TemplateRendererInterface $renderer,
        private readonly SeoRepository $seoRepository
    ) {
        parent::__construct($logger, $renderer);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $pageName = (string) ($args['page_name'] ?? '');
        if ($pageName === '') {
            $this->flash('error', 'No se encontró la página SEO indicada.');

            return $this->redirect($response, '/admin');
        }

        $data = (array) $request->getParsedBody();
        $this->seoRepository->updatePage($pageName, [
            'title' => trim((string) ($data['title'] ?? '')),
            'meta_description' => trim((string) ($data['meta_description'] ?? '')),
            'og_image' => trim((string) ($data['og_image'] ?? '')),
        ]);

        $this->flash('success', 'La configuración SEO fue actualizada.');

        return $this->redirect($response, '/admin');
    }
}
