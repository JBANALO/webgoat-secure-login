<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = isset($_GET['error']);
$setup_needed = !users_exist();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Login</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main class="card">
        <h1>Secure Login</h1>

        <?php if ($setup_needed): ?>
            <div class="notice">
                No users exist yet. You can run setup or register.
                <a href="setup.php">Go to setup</a>
                <span>or</span>
                <a href="register.php">Create account</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error">Login failed. Please try again.</div>
        <?php endif; ?>

        <form method="post" action="login.php" class="form">
            <label>
                Username
                <input type="text" name="username" autocomplete="username" required>
            </label>
            <label>
                Password
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit">Log in</button>
        </form>

        <p class="hint">Passwords are hashed and never stored in plain text.</p>
        <a class="link" href="register.php">Create an account</a>
    </main>
</body>
</html>
