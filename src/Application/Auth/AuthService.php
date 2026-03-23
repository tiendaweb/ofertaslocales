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

        return $account;
    }

    public function login(array $account): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['auth'] = [
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

    public function logout(): void
    {
        unset($_SESSION['auth']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
