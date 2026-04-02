<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use App\Application\Support\RuntimeViewSettingsStore;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;

class PhpTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(private readonly string $templatePath)
    {
    }

    public function render(Response $response, string $template, array $data = []): Response
    {
        $html = $this->renderTemplate($template, $data);
        $response->getBody()->write($html);

        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    private function renderTemplate(string $template, array $data): string
    {
        $contentTemplate = $this->templatePath . '/' . ltrim($template, '/');

        if (!is_file($contentTemplate)) {
            throw new InvalidArgumentException(sprintf('La plantilla "%s" no existe.', $template));
        }

        $layoutTemplate = $this->templatePath . '/layout.php';
        if (!is_file($layoutTemplate)) {
            throw new InvalidArgumentException('No se encontró la plantilla base layout.php.');
        }

        extract($data + [
            'contentTemplate' => $contentTemplate,
            'currentYear' => (int) date('Y'),
            'runtimeSettings' => RuntimeViewSettingsStore::all(),
        ], EXTR_SKIP);

        ob_start();
        include $layoutTemplate;

        return (string) ob_get_clean();
    }
}
