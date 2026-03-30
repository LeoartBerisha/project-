<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Setup.php';

final class Auth
{
    public function __construct()
    {
        Session::start();
        $this->ensureDefaultAdmin();
        Setup::ensureDefaultContentPages();
    }

    private function ensureDefaultAdmin(): void
    {
        $pdo = DB::pdo();
        $stmt = $pdo->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'");
        $row = $stmt->fetch();
        $cnt = (int)($row['cnt'] ?? 0);
        if ($cnt > 0) {
            return;
        }

        $hash = password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, phone, password_hash, role, is_active)
            VALUES (:full_name, :email, :phone, :password_hash, 'admin', 1)
        ");
        $stmt->execute([
            ':full_name' => DEFAULT_ADMIN_FULL_NAME,
            ':email' => DEFAULT_ADMIN_EMAIL,
            ':phone' => null,
            ':password_hash' => $hash,
        ]);
    }

    public function requireLogin(): array
    {
        $user = Session::getUser();
        if (!$user) {
            redirect_to('login.php');
        }
        return $user;
    }

    public function requireAdmin(): array
    {
        $user = Session::getUser();
        if (!$user) {
            redirect_to('login.php');
        }
        if (($user['role'] ?? '') !== 'admin') {
            redirect_to('index.php');
        }
        return $user;
    }

    public function register(string $fullName, string $email, ?string $phone, string $password): void
    {
        $pdo = DB::pdo();

        $fullName = trim($fullName);
        $email = strtolower(trim($email));
        $phone = $phone !== null ? trim($phone) : null;

        if ($fullName === '' || mb_strlen($fullName) < 2) {
            throw new InvalidArgumentException('Emri eshte i pavlefshem.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email i pavlefshem.');
        }
        if ($password === '' || mb_strlen($password) < 6) {
            throw new InvalidArgumentException('Fjalekalimi eshte shume i shkurter.');
        }

        $role = 'user';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, phone, password_hash, role, is_active)
            VALUES (:full_name, :email, :phone, :password_hash, :role, 1)
        ");
        $stmt->execute([
            ':full_name' => $fullName,
            ':email' => $email,
            ':phone' => $phone,
            ':password_hash' => $hash,
            ':role' => $role,
        ]);
    }

    public function login(string $email, string $password): void
    {
        $pdo = DB::pdo();
        $email = strtolower(trim($email));

        $stmt = $pdo->prepare("
            SELECT id, full_name, email, role, password_hash
            FROM users
            WHERE email = :email
              AND role IN ('admin', 'user')
              AND is_active = 1
            LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new RuntimeException('Email ose fjalekalim i gabuar.');
        }

        if (!password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('Email ose fjalekalim i gabuar.');
        }

        Session::setUser([
            'id' => (int)$user['id'],
            'role' => (string)$user['role'],
            'email' => (string)$user['email'],
        ]);
    }
}

