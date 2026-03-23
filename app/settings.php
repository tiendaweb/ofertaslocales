<?php

declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true,
                'logError' => false,
                'logErrorDetails' => false,
                'paths' => [
                    'templates' => __DIR__ . '/../templates',
                    'database' => __DIR__ . '/../database/app.sqlite',
                    'databaseSchema' => __DIR__ . '/../database/schema.sql',
                ],
                'logger' => [
                    'name' => 'ofertas-cerca',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ]);
        },
    ]);
};
