<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Domain\User\AccountRepository;

class AuthService
{
    public function __construct(private readonly AccountRepository $accountRepository)
    {
    }

    public function currentUser(): ?array
    {
        $user = $_SESSION['auth']['user'] ?? null;

        return is_array($user) ? $user : null;
    }

    public function isAuthenticated(): bool
    {
        return $this->currentUser() !== null;
    }

    public function hasRole(string ...$roles): bool
    {
        $user = $this->currentUser();
        if ($user === null) {
            return false;
        }

        return in_array((string) $user['role'], $roles, true);
    }

    public function attempt(string $email, string $password): ?array
    {
        $account = $this->accountRepository->findByEmail($email);
        if ($account === null) {
            return null;
        }

        if (!password_verify($password, (string) $account['password'])) {
            return null;
        }

        if (($account['status'] ?? 'active') !== 'active' || (int) ($account['is_suspended'] ?? 0) === 1) {
            return null;
        }

        return $account;
    }

    public function login(array $account): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['auth'] = $this->buildSessionPayload($account);
    }

    public function impersonate(array $account, int $impersonatorId): void
    {
        $_SESSION['auth'] = $this->buildSessionPayload($account) + [
            'impersonator_id' => $impersonatorId,
        ];
    }

    public function isImpersonating(): bool
    {
        return isset($_SESSION['auth']['impersonator_id']);
    }

    public function stopImpersonation(): void
    {
        $impersonatorId = (int) ($_SESSION['auth']['impersonator_id'] ?? 0);
        if ($impersonatorId <= 0) {
            return;
        }

        $admin = $this->accountRepository->findById($impersonatorId);
        if ($admin === null) {
            $this->logout();

            return;
        }

        $_SESSION['auth'] = $this->buildSessionPayload($admin);
    }

    public function logout(): void
    {
        unset($_SESSION['auth']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    private function buildSessionPayload(array $account): array
    {
        return [
            'user' => [
                'id' => (int) $account['id'],
                'email' => (string) $account['email'],
                'role' => (string) $account['role'],
                'business_name' => $account['business_name'] !== null ? (string) $account['business_name'] : null,
                'whatsapp' => $account['whatsapp'] !== null ? (string) $account['whatsapp'] : null,
            ],
            'logged_in_at' => gmdate('Y-m-d H:i:s'),
        ];
    }
}
