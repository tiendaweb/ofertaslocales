<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Support\RuntimeViewSettingsStore;
use App\Domain\Site\SettingsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RuntimeViewSettingsMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SettingsRepository $settingsRepository)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        RuntimeViewSettingsStore::set($this->settingsRepository->findByKeys([
            'custom_css_frontend',
            'custom_js_frontend',
            'custom_css_panel',
            'custom_js_panel',
            'safe_mode',
        ]));

        return $handler->handle($request);
    }
}
