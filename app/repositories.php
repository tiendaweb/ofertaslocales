<?php

declare(strict_types=1);

use App\Domain\Business\BusinessDirectoryRepository;
use App\Domain\Category\CategoryRepository;
use App\Domain\Offer\OfferRepository;
use App\Domain\Offer\PublicOfferRepository;
use App\Domain\Site\SeoRepository;
use App\Domain\Site\SettingsRepository;
use App\Domain\User\AccountRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Business\SqliteBusinessDirectoryRepository;
use App\Infrastructure\Persistence\Category\SqliteCategoryRepository;
use App\Infrastructure\Persistence\Offer\SqliteOfferRepository;
use App\Infrastructure\Persistence\Offer\SqlitePublicOfferRepository;
use App\Infrastructure\Persistence\Site\SqliteSeoRepository;
use App\Infrastructure\Persistence\Site\SqliteSettingsRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use App\Infrastructure\Persistence\User\SqliteAccountRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        OfferRepository::class => \DI\autowire(SqliteOfferRepository::class),
        PublicOfferRepository::class => \DI\autowire(SqlitePublicOfferRepository::class),
        BusinessDirectoryRepository::class => \DI\autowire(SqliteBusinessDirectoryRepository::class),
        CategoryRepository::class => \DI\autowire(SqliteCategoryRepository::class),
        SettingsRepository::class => \DI\autowire(SqliteSettingsRepository::class),
        SeoRepository::class => \DI\autowire(SqliteSeoRepository::class),
        AccountRepository::class => \DI\autowire(SqliteAccountRepository::class),
        UserRepository::class => \DI\autowire(InMemoryUserRepository::class),
    ]);
};
