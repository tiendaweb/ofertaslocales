<?php

declare(strict_types=1);

namespace App\Application\Service;

class OfferPublishPolicy
{
    public function resolve(array $user, array $settings): array
    {
        $role = (string) ($user['role'] ?? 'user');
        $approvalMode = $this->normalizeApprovalMode((string) ($settings['approval_mode'] ?? 'manual'));

        if (in_array($role, ['business', 'admin'], true)) {
            $status = $approvalMode === 'auto' ? 'active' : 'pending';

            return [
                'can_publish' => true,
                'status' => $status,
                'policy_mode' => $approvalMode === 'auto' ? 'business_auto' : 'business_manual',
                'headline' => $status === 'active'
                    ? 'Tus ofertas se publican de forma inmediata.'
                    : 'Tus ofertas ingresan en revisión manual.',
                'description' => $status === 'active'
                    ? 'La configuración actual permite aprobación automática para cuentas comerciales.'
                    : 'La configuración actual requiere revisión del administrador para cuentas comerciales.',
                'blocked_reason' => null,
            ];
        }

        return [
            'can_publish' => false,
            'status' => null,
            'policy_mode' => 'business_only',
            'headline' => 'Solo las cuentas de negocio pueden publicar ofertas.',
            'description' => 'Registrá un negocio para crear y gestionar publicaciones comerciales.',
            'blocked_reason' => 'Para publicar ofertas necesitas una cuenta de negocio.',
        ];
    }

    private function normalizeApprovalMode(string $value): string
    {
        return in_array($value, ['manual', 'auto'], true) ? $value : 'manual';
    }

}
