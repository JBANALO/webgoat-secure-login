<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = normalize_username($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $username_error = validate_username($username);
    $password_error = validate_password($password);

    if ($username_error) {
        $error = $username_error;
    } elseif ($password_error) {
        $error = $password_error;
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!is_password_strong($password)) {
        $error = 'Password must be strong (10+ chars, upper, lower, number, symbol).';
    } elseif (find_user($username)) {
        $error = 'Username already exists.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $users = read_users();
        $users[] = [
            'username' => $username,
            'password_hash' => $hash,
            'created_at' => date('c'),
        ];

        if (!write_users($users)) {
            $error = 'Could not save user file. Check permissions.';
        } else {
            login_user($username);
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main class="card">
        <h1>Create Account</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="form">
            <label>
                Username
                <input type="text" name="username" autocomplete="username" required>
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="new-password" required>
            </label>
            <label>
                Confirm Password
                <input type="password" name="confirm" autocomplete="new-password" required>
            </label>
            <button type="submit">Create account</button>
        </form>

        <p class="hint">Use a strong password for the Password Strength lesson.</p>
        <a class="link" href="index.php">Back to login</a>
    </main>
</body>
</html>
