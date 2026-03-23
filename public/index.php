<?php

declare(strict_types=1);

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();
$request = ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$appSettings = $container->get(SettingsInterface::class);
$displayErrorDetails = $appSettings->get('displayErrorDetails');
$logErrors = $appSettings->get('logError');
$logErrorDetails = $appSettings->get('logErrorDetails');

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);

$response = $app->handle($request);
(new ResponseEmitter())->emit($response);
