#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Domain\Offer\OfferRepository;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

/** @var OfferRepository $offerRepository */
$offerRepository = $container->get(OfferRepository::class);
$expiredOffers = $offerRepository->expireOffers();

echo sprintf("Ofertas expiradas actualizadas: %d\n", $expiredOffers);
