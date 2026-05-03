<?php

declare(strict_types=1);

function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function users_file_path(): string
{
    return __DIR__ . '/../data/users.json';
}

function read_users(): array
{
    $path = users_file_path();
    if (!file_exists($path)) {
        return [];
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function write_users(array $users): bool
{
    $path = users_file_path();
    $json = json_encode($users, JSON_PRETTY_PRINT);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json, LOCK_EX) !== false;
}

function users_exist(): bool
{
    return count(read_users()) > 0;
}

function find_user(string $username): ?array
{
    foreach (read_users() as $user) {
        if (strcasecmp($user['username'] ?? '', $username) === 0) {
            return $user;
        }
    }

    return null;
}

function login_user(string $username): void
{
    ensure_session();
    session_regenerate_id(true);
    $_SESSION['user'] = $username;
}

function logout_user(): void
{
    ensure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function is_logged_in(): bool
{
    ensure_session();
    return isset($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}
