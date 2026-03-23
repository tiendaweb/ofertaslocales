<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Infrastructure\View\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

abstract class PageAction
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly TemplateRendererInterface $renderer
    ) {
    }

    abstract public function __invoke(Request $request, Response $response, array $args): Response;

    protected function renderPage(Response $response, string $template, array $data = []): Response
    {
        return $this->renderer->render($response, $template, $data);
    }
}
