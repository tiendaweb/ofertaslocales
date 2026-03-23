<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use Psr\Http\Message\ResponseInterface as Response;

interface TemplateRendererInterface
{
    public function render(Response $response, string $template, array $data = []): Response;
}
