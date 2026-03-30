<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

final class PortfolioRepository
{
    public static function getPublished(): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT id, dentist_name, description, image_path, pdf_path
            FROM portfolio
            WHERE is_published = 1
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function getDistinctDentists(): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT DISTINCT dentist_name
            FROM portfolio
            WHERE is_published = 1 AND dentist_name <> ''
            ORDER BY dentist_name
        ");
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => $r['dentist_name'], $rows);
    }

    public static function getAllForAdmin(): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT p.*, u.full_name AS created_by_name, u2.full_name AS updated_by_name
            FROM portfolio p
            LEFT JOIN users u ON u.id = p.created_by
            LEFT JOIN users u2 ON u2.id = p.updated_by
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT *
            FROM portfolio
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data, int $adminId): int
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO portfolio (dentist_name, description, image_path, pdf_path, created_by, updated_by, is_published)
            VALUES (:dentist_name, :description, :image_path, :pdf_path, :created_by, :updated_by, :is_published)
        ");
        $stmt->execute([
            ':dentist_name' => $data['dentist_name'],
            ':description' => $data['description'],
            ':image_path' => $data['image_path'],
            ':pdf_path' => $data['pdf_path'],
            ':created_by' => $adminId,
            ':updated_by' => $adminId,
            ':is_published' => $data['is_published'] ? 1 : 0,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data, int $adminId): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            UPDATE portfolio
            SET dentist_name = :dentist_name,
                description = :description,
                image_path = :image_path,
                pdf_path = :pdf_path,
                updated_by = :updated_by,
                is_published = :is_published
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute([
            ':dentist_name' => $data['dentist_name'],
            ':description' => $data['description'],
            ':image_path' => $data['image_path'],
            ':pdf_path' => $data['pdf_path'],
            ':updated_by' => $adminId,
            ':is_published' => $data['is_published'] ? 1 : 0,
            ':id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
    }
}

