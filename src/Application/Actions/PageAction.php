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
        return $this->renderer->render($response, $template, $data + [
            'currentUser' => $this->currentUser(),
            'flash' => $this->consumeFlash(),
        ]);
    }

    protected function redirect(Response $response, string $path, int $status = 302): Response
    {
        return $response->withHeader('Location', $path)->withStatus($status);
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function flashFormErrors(array $errors, array $old = []): void
    {
        $_SESSION['flash']['form_errors'] = $errors;
        $_SESSION['flash']['old'] = $old;
    }

    protected function consumeFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : [];
    }

    protected function currentUser(): ?array
    {
        $user = $_SESSION['auth']['user'] ?? null;

        return is_array($user) ? $user : null;
    }
}
