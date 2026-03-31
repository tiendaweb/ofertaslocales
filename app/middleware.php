<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\SiteMaintenanceMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(SiteMaintenanceMiddleware::class);
};
