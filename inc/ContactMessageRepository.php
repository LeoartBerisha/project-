<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

final class ContactMessageRepository
{
    public static function create(string $fullName, string $email, string $subject, string $message): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (full_name, email, subject, message, status)
            VALUES (:full_name, :email, :subject, :message, 'new')
        ");
        $stmt->execute([
            ':full_name' => $fullName,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message,
        ]);
    }

    public static function getAllForAdmin(): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT *
            FROM contact_messages
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = :status WHERE id = :id LIMIT 1");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public static function delete(int $id): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
    }
}

