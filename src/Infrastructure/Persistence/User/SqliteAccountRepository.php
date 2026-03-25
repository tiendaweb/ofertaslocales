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
            "SELECT id, email, role, business_name, whatsapp, bio, instagram_url, facebook_url, tiktok_url, website_url, logo_url, status, created_at
             FROM users
             WHERE role IN ('business', 'admin')
             ORDER BY business_name ASC"
        );

        return $statement->fetchAll();
    }

    public function countByRole(string $role): int
    {
        $statement = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE role = :role AND status = 'active'");
        $statement->execute(['role' => $role]);

        return (int) $statement->fetchColumn();
    }

    public function findAllPaginated(int $page = 1, int $perPage = 10): array
    {
        $safePage = max(1, $page);
        $safePerPage = max(1, min(100, $perPage));
        $offset = ($safePage - 1) * $safePerPage;

        $total = (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

        $statement = $this->pdo->prepare(
            'SELECT id, email, role, business_name, whatsapp, bio, instagram_url, facebook_url, tiktok_url, website_url, logo_url, status, suspended_at, suspended_reason, created_at, updated_at
             FROM users
             ORDER BY created_at DESC, id DESC
             LIMIT :limit OFFSET :offset'
        );
        $statement->bindValue(':limit', $safePerPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'page' => $safePage,
            'per_page' => $safePerPage,
            'total_pages' => max(1, (int) ceil($total / $safePerPage)),
        ];
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, email, password, role, business_name, whatsapp, bio, instagram_url, facebook_url, tiktok_url, website_url, logo_url, street, street_number, postal_code, city, municipality, province, address_lat, address_lon, status, is_suspended, created_at
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
            'SELECT id, email, password, role, business_name, whatsapp, bio, instagram_url, facebook_url, tiktok_url, website_url, logo_url, street, street_number, postal_code, city, municipality, province, address_lat, address_lon, status, is_suspended, suspended_at, suspended_reason, suspended_by, created_at, updated_at
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $account = $statement->fetch();

        return $account === false ? null : $account;
    }

    public function create(array $data): array
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password, role, business_name, whatsapp, bio, instagram_url, facebook_url, tiktok_url, website_url, logo_url, street, street_number, postal_code, city, municipality, province, address_lat, address_lon, status, is_suspended, created_at, updated_at)
             VALUES (:email, :password, :role, :business_name, :whatsapp, :bio, :instagram_url, :facebook_url, :tiktok_url, :website_url, :logo_url, :street, :street_number, :postal_code, :city, :municipality, :province, :address_lat, :address_lon, :status, :is_suspended, :created_at, :updated_at)'
        );

        $createdAt = gmdate('Y-m-d H:i:s');
        $statement->execute([
            'email' => strtolower(trim((string) $data['email'])),
            'password' => (string) $data['password'],
            'role' => $data['role'] ?? 'business',
            'business_name' => isset($data['business_name']) ? trim((string) $data['business_name']) : null,
            'whatsapp' => isset($data['whatsapp']) ? trim((string) $data['whatsapp']) : null,
            'bio' => isset($data['bio']) ? trim((string) $data['bio']) : null,
            'instagram_url' => isset($data['instagram_url']) ? trim((string) $data['instagram_url']) : null,
            'facebook_url' => isset($data['facebook_url']) ? trim((string) $data['facebook_url']) : null,
            'tiktok_url' => isset($data['tiktok_url']) ? trim((string) $data['tiktok_url']) : null,
            'website_url' => isset($data['website_url']) ? trim((string) $data['website_url']) : null,
            'logo_url' => isset($data['logo_url']) ? trim((string) $data['logo_url']) : null,
            'street' => isset($data['street']) ? trim((string) $data['street']) : null,
            'street_number' => isset($data['street_number']) ? trim((string) $data['street_number']) : null,
            'postal_code' => isset($data['postal_code']) ? trim((string) $data['postal_code']) : null,
            'city' => isset($data['city']) ? trim((string) $data['city']) : null,
            'municipality' => isset($data['municipality']) ? trim((string) $data['municipality']) : null,
            'province' => isset($data['province']) ? trim((string) $data['province']) : null,
            'address_lat' => isset($data['address_lat']) ? (float) $data['address_lat'] : null,
            'address_lon' => isset($data['address_lon']) ? (float) $data['address_lon'] : null,
            'status' => $data['status'] ?? 'active',
            'is_suspended' => ($data['status'] ?? 'active') === 'suspended' ? 1 : 0,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return $this->findById((int) $this->pdo->lastInsertId()) ?? [];
    }

    public function update(int $id, array $data): ?array
    {
        $existing = $this->findById($id);
        if ($existing === null) {
            return null;
        }

        $statement = $this->pdo->prepare(
            'UPDATE users
             SET email = :email,
                 role = :role,
                 business_name = :business_name,
                 whatsapp = :whatsapp,
                 bio = :bio,
                 instagram_url = :instagram_url,
                 facebook_url = :facebook_url,
                 tiktok_url = :tiktok_url,
                 website_url = :website_url,
                 logo_url = :logo_url,
                 street = :street,
                 street_number = :street_number,
                 postal_code = :postal_code,
                 city = :city,
                 municipality = :municipality,
                 province = :province,
                 address_lat = :address_lat,
                 address_lon = :address_lon,
                 updated_at = :updated_at
             WHERE id = :id'
        );

        $statement->execute([
            'id' => $id,
            'email' => strtolower(trim((string) ($data['email'] ?? $existing['email']))),
            'role' => $data['role'] ?? $existing['role'],
            'business_name' => $data['business_name'] ?? $existing['business_name'],
            'whatsapp' => $data['whatsapp'] ?? $existing['whatsapp'],
            'bio' => $data['bio'] ?? $existing['bio'],
            'instagram_url' => $data['instagram_url'] ?? $existing['instagram_url'],
            'facebook_url' => $data['facebook_url'] ?? $existing['facebook_url'],
            'tiktok_url' => $data['tiktok_url'] ?? $existing['tiktok_url'],
            'website_url' => $data['website_url'] ?? $existing['website_url'],
            'logo_url' => $data['logo_url'] ?? $existing['logo_url'],
            'street' => $data['street'] ?? $existing['street'],
            'street_number' => $data['street_number'] ?? $existing['street_number'],
            'postal_code' => $data['postal_code'] ?? $existing['postal_code'],
            'city' => $data['city'] ?? $existing['city'],
            'municipality' => $data['municipality'] ?? $existing['municipality'],
            'province' => $data['province'] ?? $existing['province'],
            'address_lat' => array_key_exists('address_lat', $data) ? $data['address_lat'] : $existing['address_lat'],
            'address_lon' => array_key_exists('address_lon', $data) ? $data['address_lon'] : $existing['address_lon'],
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);

        return $this->findById($id);
    }

    public function suspend(int $id, int $suspendedBy, ?string $reason = null): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE users
             SET status = 'suspended',
                 is_suspended = 1,
                 suspended_at = :suspended_at,
                 suspended_reason = :suspended_reason,
                 suspended_by = :suspended_by,
                 updated_at = :updated_at
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'suspended_at' => gmdate('Y-m-d H:i:s'),
            'suspended_reason' => $reason !== null && trim($reason) !== '' ? trim($reason) : null,
            'suspended_by' => $suspendedBy,
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function unsuspend(int $id): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE users
             SET status = 'active',
                 is_suspended = 0,
                 suspended_at = NULL,
                 suspended_reason = NULL,
                 suspended_by = NULL,
                 updated_at = :updated_at
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public function createBusinessAccount(array $data): array
    {
        return $this->create($data);
    }
}
