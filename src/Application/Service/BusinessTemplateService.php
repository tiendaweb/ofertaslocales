<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\BusinessTemplate\BusinessTemplateRepository;

class BusinessTemplateService
{
    private BusinessTemplateRepository $templateRepository;

    public function __construct(BusinessTemplateRepository $templateRepository)
    {
        $this->templateRepository = $templateRepository;
    }

    /**
     * Get all available templates
     *
     * @return array<array<string, mixed>>
     */
    public function getAllTemplates(): array
    {
        return $this->templateRepository->findAllActive();
    }

    /**
     * Get template by key
     *
     * @param string $key
     * @return array<string, mixed>|null
     */
    public function getTemplate(string $key): ?array
    {
        $template = $this->templateRepository->findByKey($key);

        if ($template && $template['fields_json']) {
            $template['fields'] = json_decode($template['fields_json'], true) ?? [];
        }

        return $template;
    }

    /**
     * Get template fields
     *
     * @param string $key
     * @return array<string, mixed>
     */
    public function getTemplateFields(string $key): array
    {
        $template = $this->getTemplate($key);

        if (!$template) {
            return [];
        }

        return $template['fields'] ?? [];
    }

    /**
     * Validate template data against template fields
     *
     * @param string $templateKey
     * @param array<string, mixed> $data
     * @return bool
     */
    public function validateTemplateData(string $templateKey, array $data): bool
    {
        $template = $this->getTemplate($templateKey);

        if (!$template) {
            return false;
        }

        // For now, accept any data. In the future, validate against fields
        return true;
    }

    /**
     * Create template
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createTemplate(array $data): array
    {
        return $this->templateRepository->create($data);
    }

    /**
     * Update template
     *
     * @param string $key
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function updateTemplate(string $key, array $data): ?array
    {
        return $this->templateRepository->update($key, $data);
    }

    /**
     * Delete template
     *
     * @param string $key
     * @return bool
     */
    public function deleteTemplate(string $key): bool
    {
        // Prevent deletion of 'default' template
        if ($key === 'default') {
            return false;
        }

        return $this->templateRepository->delete($key);
    }
}
