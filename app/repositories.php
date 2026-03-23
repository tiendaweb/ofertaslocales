<?php

declare(strict_types=1);

use App\Domain\Offer\OfferRepository;
use App\Domain\User\AccountRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Offer\SqliteOfferRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use App\Infrastructure\Persistence\User\SqliteAccountRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        OfferRepository::class => \DI\autowire(SqliteOfferRepository::class),
        AccountRepository::class => \DI\autowire(SqliteAccountRepository::class),
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};
