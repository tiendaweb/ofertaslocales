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
            "SELECT id, email, role, business_name, created_at
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
}
