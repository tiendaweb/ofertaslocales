<?php

declare(strict_types=1);

use App\Application\Auth\AuthService;
use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Persistence\Database\SqliteBootstrapper;
use App\Infrastructure\View\PhpTemplateRenderer;
use App\Infrastructure\View\TemplateRendererInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);
            $loggerSettings = $settings->get('logger');

            $logger = new Logger($loggerSettings['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($loggerSettings['path'], $loggerSettings['level']));

            return $logger;
        },
        SqliteBootstrapper::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);

            return new SqliteBootstrapper($settings->get('paths')['databaseMigrations']);
        },
        \PDO::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);
            $databasePath = $settings->get('paths')['database'];
            $databaseDirectory = dirname($databasePath);

            if (!is_dir($databaseDirectory)) {
                mkdir($databaseDirectory, 0775, true);
            }

            $pdo = new \PDO('sqlite:' . $databasePath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            $container->get(SqliteBootstrapper::class)->bootstrap($pdo);

            return $pdo;
        },
        TemplateRendererInterface::class => function (ContainerInterface $container) {
            $settings = $container->get(SettingsInterface::class);

            return new PhpTemplateRenderer($settings->get('paths')['templates']);
        },
        AuthService::class => \DI\autowire(),
    ]);
};
