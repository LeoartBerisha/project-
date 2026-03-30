<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

final class AppointmentRepository
{
    private static function allowedStatuses(): array
    {
        return ['pending', 'confirmed', 'cancelled'];
    }

    public static function create(array $data, int $userId): int
    {
        $pdo = DB::pdo();

        $appointmentDate = (string)($data['appointment_date'] ?? '');
        $appointmentTime = (string)($data['appointment_time'] ?? '');
        $dentistName = (string)($data['dentist_name'] ?? '');

        if ($appointmentDate === '' || $appointmentTime === '') {
            throw new InvalidArgumentException('Datë ose orë e pavlefshme.');
        }

        // Blloko slotin nese ekziston nje rezervim per kete date/orë (pending/confirmed).
        $check = $pdo->prepare("
            SELECT id
            FROM appointments
            WHERE appointment_date = :date
              AND appointment_time = :time
              AND status IN ('pending', 'confirmed')
            LIMIT 1
        ");
        $check->execute([
            ':date' => $appointmentDate,
            ':time' => $appointmentTime,
        ]);
        if ($check->fetch()) {
            throw new RuntimeException('Kjo orë është e zënë për këtë datë.');
        }

        $stmt = $pdo->prepare("
            INSERT INTO appointments (user_id, service_id, dentist_name, appointment_date, appointment_time, reason, status)
            VALUES (:user_id, :service_id, :dentist_name, :appointment_date, :appointment_time, :reason, 'pending')
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':service_id' => $data['service_id'] ?? null,
            ':dentist_name' => $dentistName,
            ':appointment_date' => $appointmentDate,
            ':appointment_time' => $appointmentTime,
            ':reason' => $data['reason'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function getUnavailableTimesByDate(string $date, array $statuses = ['pending', 'confirmed']): array
    {
        $pdo = DB::pdo();
        if ($date === '') {
            return [];
        }

        // Whitelist statuses
        $allowed = array_intersect(self::allowedStatuses(), $statuses);
        if (empty($allowed)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($allowed), '?'));
        $stmt = $pdo->prepare("
            SELECT appointment_time
            FROM appointments
            WHERE appointment_date = ?
              AND status IN ($placeholders)
            GROUP BY appointment_time
        ");

        $params = array_merge([$date], array_values($allowed));
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => (string)$r['appointment_time'], $rows);
    }

    public static function getAllForAdmin(): array
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("
            SELECT a.*, u.full_name AS user_name
            FROM appointments a
            LEFT JOIN users u ON u.id = a.user_id
            ORDER BY a.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $id, string $status): void
    {
        $pdo = DB::pdo();

        if (!in_array($status, self::allowedStatuses(), true)) {
            throw new InvalidArgumentException('Status i pavlefshëm.');
        }

        $stmt = $pdo->prepare("UPDATE appointments SET status = :status WHERE id = :id LIMIT 1");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public static function delete(int $id): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
    }
}

