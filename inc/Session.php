<?php
declare(strict_types=1);

final class Session
{
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function setUser(array $user): void
    {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'role' => (string)$user['role'],
            'email' => (string)$user['email'],
        ];
    }

    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}

