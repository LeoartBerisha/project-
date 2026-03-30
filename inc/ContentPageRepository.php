<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

final class ContentPageRepository
{
    public static function getPublishedBySlug(string $slug): ?array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            SELECT slug, title, body, is_published
            FROM content_pages
            WHERE slug = :slug AND is_published = 1
            LIMIT 1
        ");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

