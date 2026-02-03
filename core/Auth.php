<?php

require_once __DIR__ . '/../config/database.php';

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(string $email, string $password): bool
    {
        self::startSession();
        $pdo = Database::getInstance();

        $stmt = $pdo->prepare('SELECT u.id, u.name, u.email, u.password, u.is_active, r.name AS role_name, r.id AS role_id
                               FROM users u
                               JOIN roles r ON u.role_id = r.id
                               WHERE u.email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Only admins can log in regardless of activation; normal users require approval
            if ($user['role_name'] !== 'admin' && (int)$user['is_active'] !== 1) {
                return false;
            }
            unset($user['password']);
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function hasRole(array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        return in_array($user['role_name'], $roles, true);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: login.php');
            exit;
        }
    }

    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            echo 'Access denied';
            exit;
        }
    }

    public static function redirectAfterLogin(): void
    {
        $user = self::user();
        if (!$user) {
            header('Location: login.php');
            exit;
        }

        switch ($user['role_name']) {
            case 'admin':
            case 'manager':
                header('Location: index.php?route=admin/dashboard');
                break;
            case 'tenant':
                header('Location: index.php?route=tenant/dashboard');
                break;
            default:
                header('Location: index.php');
                break;
        }
        exit;
    }
}
