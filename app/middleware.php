<?php

declare(strict_types=1);

use App\Application\Middleware\MaintenanceModeMiddleware;
use App\Application\Middleware\RuntimeViewSettingsMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(MaintenanceModeMiddleware::class);
    $app->add(RuntimeViewSettingsMiddleware::class);
};
