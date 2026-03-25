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
        $currentSeo = $this->seoRepository->findByPage($pageName) ?? [
            'title' => '',
            'meta_description' => '',
            'og_image' => '',
        ];

        $payload = [
            'title' => (string) ($currentSeo['title'] ?? ''),
            'meta_description' => (string) ($currentSeo['meta_description'] ?? ''),
            'og_image' => (string) ($currentSeo['og_image'] ?? ''),
        ];

        foreach (['title', 'meta_description', 'og_image'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = trim((string) $data[$field]);
            }
        }

        $this->seoRepository->updatePage($pageName, $payload);

        $this->flash('success', 'La configuración SEO fue actualizada.');

        return $this->redirect($response, '/admin');
    }
}
