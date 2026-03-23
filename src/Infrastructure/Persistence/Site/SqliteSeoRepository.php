<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Site;

use App\Domain\Site\SeoRepository;
use PDO;

class SqliteSeoRepository implements SeoRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $statement = $this->pdo->query(
            'SELECT page_name, title, meta_description, og_image FROM seo ORDER BY page_name ASC'
        );

        return $statement->fetchAll();
    }

    public function findByPage(string $pageName): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT page_name, title, meta_description, og_image FROM seo WHERE page_name = :page_name LIMIT 1'
        );
        $statement->execute(['page_name' => $pageName]);

        $seo = $statement->fetch();

        return $seo === false ? null : $seo;
    }
}
