<?php

declare(strict_types=1);

namespace App\Application\Service;

class OfferPublishPolicy
{
    public function resolve(array $user, array $settings): array
    {
        $role = (string) ($user['role'] ?? 'user');
        $approvalMode = $this->normalizeApprovalMode((string) ($settings['approval_mode'] ?? 'manual'));
        $defaultUserPublishMode = $this->normalizeUserPublishMode(
            (string) ($settings['default_user_publish_mode'] ?? 'review')
        );

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

        if ($defaultUserPublishMode === 'direct') {
            return [
                'can_publish' => true,
                'status' => 'active',
                'policy_mode' => 'user_direct',
                'headline' => 'Tu cuenta publica de forma inmediata.',
                'description' => 'Las publicaciones de usuarios generales salen activas sin revisión previa.',
                'blocked_reason' => null,
            ];
        }

        if ($defaultUserPublishMode === 'review') {
            return [
                'can_publish' => true,
                'status' => 'pending',
                'policy_mode' => 'user_review',
                'headline' => 'Tu cuenta publica con revisión manual.',
                'description' => 'Cada publicación de usuarios generales queda pendiente hasta aprobación administrativa.',
                'blocked_reason' => null,
            ];
        }

        $isProfileComplete = $this->isUserProfileComplete($user);
        if ($isProfileComplete) {
            return [
                'can_publish' => true,
                'status' => 'pending',
                'policy_mode' => 'user_profile_required',
                'headline' => 'Perfil validado: ya podés publicar.',
                'description' => 'Como usuario general, tus publicaciones quedan bajo revisión manual.',
                'blocked_reason' => null,
            ];
        }

        return [
            'can_publish' => false,
            'status' => null,
            'policy_mode' => 'user_profile_required',
            'headline' => 'Completá tu perfil para habilitar publicaciones.',
            'description' => 'Necesitás cargar nombre comercial y WhatsApp en tu cuenta para poder publicar.',
            'blocked_reason' => 'Para publicar primero debes completar tu perfil con nombre comercial y WhatsApp.',
        ];
    }

    private function normalizeApprovalMode(string $value): string
    {
        return in_array($value, ['manual', 'auto'], true) ? $value : 'manual';
    }

    private function normalizeUserPublishMode(string $value): string
    {
        return in_array($value, ['direct', 'review', 'profile_required'], true) ? $value : 'review';
    }

    private function isUserProfileComplete(array $user): bool
    {
        $businessName = trim((string) ($user['business_name'] ?? ''));
        $whatsapp = trim((string) ($user['whatsapp'] ?? ''));

        return $businessName !== '' && $whatsapp !== '';
    }
}
