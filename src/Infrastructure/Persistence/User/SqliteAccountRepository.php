<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\AccountRepository;
use PDO;

class SqliteAccountRepository implements AccountRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findBusinessAccounts(): array
    {
        $statement = $this->pdo->query(
            "SELECT id, email, role, business_name, whatsapp, created_at
             FROM users
             WHERE role IN ('business', 'admin')
             ORDER BY business_name ASC"
        );

        return $statement->fetchAll();
    }

    public function countByRole(string $role): int
    {
        $statement = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
        $statement->execute(['role' => $role]);

        return (int) $statement->fetchColumn();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, email, password, role, business_name, whatsapp, created_at
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $account = $statement->fetch();

        return $account === false ? null : $account;
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, email, password, role, business_name, whatsapp, created_at FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $account = $statement->fetch();

        return $account === false ? null : $account;
    }

    public function createBusinessAccount(array $data): array
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password, role, business_name, whatsapp, created_at)
             VALUES (:email, :password, :role, :business_name, :whatsapp, :created_at)'
        );

        $createdAt = gmdate('Y-m-d H:i:s');
        $statement->execute([
            'email' => strtolower(trim((string) $data['email'])),
            'password' => (string) $data['password'],
            'role' => $data['role'] ?? 'business',
            'business_name' => trim((string) $data['business_name']),
            'whatsapp' => trim((string) $data['whatsapp']),
            'created_at' => $createdAt,
        ]);

        return $this->findById((int) $this->pdo->lastInsertId()) ?? [];
    }
}
