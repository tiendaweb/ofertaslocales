<?php

declare(strict_types=1);

namespace App\Domain\BusinessTemplate;

interface BusinessTemplateRepository
{
    /**
     * Find all active templates
     *
     * @return array<array<string, mixed>>
     */
    public function findAllActive(): array;

    /**
     * Find template by key
     *
     * @param string $key
     * @return array<string, mixed>|null
     */
    public function findByKey(string $key): ?array;

    /**
     * Create a new template
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array;

    /**
     * Update existing template
     *
     * @param string $key
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function update(string $key, array $data): ?array;

    /**
     * Delete template
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;
}
