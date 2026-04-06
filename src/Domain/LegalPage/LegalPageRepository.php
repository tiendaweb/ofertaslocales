<?php

declare(strict_types=1);

namespace App\Domain\LegalPage;

interface LegalPageRepository
{
    /**
     * Find all legal pages
     *
     * @return array<array<string, mixed>>
     */
    public function findAll(): array;

    /**
     * Find legal page by key
     *
     * @param string $key
     * @return array<string, mixed>|null
     */
    public function findByKey(string $key): ?array;

    /**
     * Update legal page content
     *
     * @param string $key
     * @param string $title
     * @param string $contentHtml
     * @param int|null $userId
     * @return array<string, mixed>|null
     */
    public function update(string $key, string $title, string $contentHtml, ?int $userId = null): ?array;

    /**
     * Record policy acceptance
     *
     * @param int $userId
     * @param string $policyType
     * @param string|null $ipAddress
     * @return bool
     */
    public function recordAcceptance(int $userId, string $policyType, ?string $ipAddress = null): bool;

    /**
     * Check if user accepted policy
     *
     * @param int $userId
     * @param string $policyType
     * @return bool
     */
    public function hasAccepted(int $userId, string $policyType): bool;
}
