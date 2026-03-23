<?php

declare(strict_types=1);

namespace App\Domain\Business;

interface BusinessDirectoryRepository
{
    public function findBusinessesWithActiveOffers(): array;

    public function countBusinessesWithActiveOffers(): int;
}
