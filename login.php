<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (validate_email($email) || validate_password($password)) {
    header('Location: index.php?error=1');
    exit;
}

$user = find_user_by_email($email);
if (!$user) {
    $user = find_user($email);
}
if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
    header('Location: index.php?error=1');
    exit;
}

login_user($user['username']);
header('Location: dashboard.php');
exit;
