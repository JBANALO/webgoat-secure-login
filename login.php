<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = normalize_username($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (validate_username($username) || validate_password($password)) {
    header('Location: index.php?error=1');
    exit;
}

$user = find_user($username);
if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
    header('Location: index.php?error=1');
    exit;
}

login_user($user['username']);
header('Location: dashboard.php');
exit;
