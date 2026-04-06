<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\LegalPage\LegalPageRepository;

class LegalPageService
{
    private LegalPageRepository $legalPageRepository;

    public function __construct(LegalPageRepository $legalPageRepository)
    {
        $this->legalPageRepository = $legalPageRepository;
    }

    /**
     * Get all legal pages
     *
     * @return array<array<string, mixed>>
     */
    public function getAllPages(): array
    {
        return $this->legalPageRepository->findAll();
    }

    /**
     * Get legal page by key
     *
     * @param string $key
     * @return array<string, mixed>|null
     */
    public function getPage(string $key): ?array
    {
        return $this->legalPageRepository->findByKey($key);
    }

    /**
     * Update legal page
     *
     * @param string $key
     * @param string $title
     * @param string $contentHtml
     * @param int|null $userId
     * @return array<string, mixed>|null
     */
    public function updatePage(string $key, string $title, string $contentHtml, ?int $userId = null): ?array
    {
        // Sanitize HTML to prevent XSS (basic sanitization)
        // In production, use a library like HTML Purifier or sanitize-html
        $contentHtml = $this->sanitizeHtml($contentHtml);

        return $this->legalPageRepository->update($key, $title, $contentHtml, $userId);
    }

    /**
     * Record policy acceptance
     *
     * @param int $userId
     * @param string $policyType
     * @param string|null $ipAddress
     * @return bool
     */
    public function recordAcceptance(int $userId, string $policyType, ?string $ipAddress = null): bool
    {
        $validTypes = ['terms', 'privacy', 'cookies'];

        if (!in_array($policyType, $validTypes, true)) {
            return false;
        }

        return $this->legalPageRepository->recordAcceptance($userId, $policyType, $ipAddress);
    }

    /**
     * Check if user accepted policy
     *
     * @param int $userId
     * @param string $policyType
     * @return bool
     */
    public function hasAccepted(int $userId, string $policyType): bool
    {
        return $this->legalPageRepository->hasAccepted($userId, $policyType);
    }

    /**
     * Basic HTML sanitization
     * Allows safe tags: p, h1-h6, strong, em, ul, ol, li, a, br
     *
     * @param string $html
     * @return string
     */
    private function sanitizeHtml(string $html): string
    {
        $allowedTags = '<p><h1><h2><h3><h4><h5><h6><strong><em><u><ul><ol><li><a><br>';

        return strip_tags($html, $allowedTags);
    }
}
