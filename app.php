<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function startSessionIfNeeded(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function redirectTo(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function currentUser(): ?array
{
    startSessionIfNeeded();

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int)$_SESSION['user_id'],
        'name' => (string)($_SESSION['user_name'] ?? ''),
        'role' => (string)($_SESSION['user_role'] ?? ''),
    ];
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdminUser(): bool
{
    $user = currentUser();

    return $user !== null && $user['role'] === 'admin';
}

function requireAdminUser(): void
{
    if (!isAdminUser()) {
        redirectTo('kyqu.php');
    }
}

function appDoctors(): array
{
    return [
        'Dr. Arben Krasniqi',
        'Dr. Sara Hoxha',
        'Dr. Blendina Gashi',
        'Dr. Liridon Berisha',
    ];
}

function appTimeSlots(): array
{
    return [
        '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00', '18:00', '19:00',
    ];
}

function ensureBlockedDoctorDatesTable(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS blocked_doctor_dates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            doctor_name VARCHAR(120) NOT NULL,
            blocked_date DATE NOT NULL,
            blocked_time TIME DEFAULT NULL,
            note VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_doctor_date_time (doctor_name, blocked_date, blocked_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $columns = $pdo->query("SHOW COLUMNS FROM blocked_doctor_dates LIKE 'blocked_time'")->fetchAll();
    if (count($columns) === 0) {
        $pdo->exec('ALTER TABLE blocked_doctor_dates ADD COLUMN blocked_time TIME DEFAULT NULL AFTER blocked_date');
    }
}

function normalizeTimeSlot(?string $time): ?string
{
    if ($time === null || $time === '') {
        return null;
    }

    return substr($time, 0, 5);
}

function postValue(string $key, string $default = ''): string
{
    return trim((string)($_POST[$key] ?? $default));
}
