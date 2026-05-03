<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/mailer.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

ensure_session();

$error = '';
$info = '';
$pending = $_SESSION['pending_registration'] ?? null;

if (isset($_GET['reset'])) {
    unset($_SESSION['pending_registration']);
    $pending = null;
}

if ($pending && time() > ($pending['expires_at'] ?? 0)) {
    unset($_SESSION['pending_registration']);
    $pending = null;
    $error = 'Verification code expired. Please register again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 'request_code';

    if ($step === 'request_code') {
        $username = normalize_username($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        $username_error = validate_username($username);
        $email_error = validate_email($email);
        $password_error = validate_password($password);

        if ($username_error) {
            $error = $username_error;
        } elseif ($email_error) {
            $error = $email_error;
        } elseif ($password_error) {
            $error = $password_error;
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (!is_password_strong($password)) {
            $error = 'Password must be strong (10+ chars, upper, lower, number, symbol).';
        } elseif (find_user($username)) {
            $error = 'Username already exists.';
        } elseif (find_user_by_email($email)) {
            $error = 'Email already exists.';
        } else {
            $code = (string) random_int(100000, 999999);
            $expiry_minutes = (int) env_value('OTP_EXPIRY_MINUTES', '5');
            if ($expiry_minutes <= 0) {
                $expiry_minutes = 5;
            }

            $pending = [
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'code_hash' => password_hash($code, PASSWORD_DEFAULT),
                'expires_at' => time() + ($expiry_minutes * 60),
            ];

            $_SESSION['pending_registration'] = $pending;

            $subject = 'Your verification code';
            $body = "Your verification code is: {$code}\n\n" .
                "This code expires in {$expiry_minutes} minutes.";

            if (!send_email_smtp($email, $subject, $body)) {
                unset($_SESSION['pending_registration']);
                $pending = null;
                $error = 'Could not send verification email. Check SMTP settings.';
            } else {
                $info = 'Verification code sent. Check your email.';
            }
        }
    } elseif ($step === 'verify_code') {
        $code_input = trim($_POST['code'] ?? '');

        if (!$pending) {
            $error = 'No pending registration found. Please register again.';
        } elseif (!preg_match('/^\d{6}$/', $code_input)) {
            $error = 'Verification code must be 6 digits.';
        } elseif (time() > ($pending['expires_at'] ?? 0)) {
            unset($_SESSION['pending_registration']);
            $pending = null;
            $error = 'Verification code expired. Please register again.';
        } elseif (!password_verify($code_input, $pending['code_hash'] ?? '')) {
            $error = 'Invalid verification code.';
        } else {
            if (find_user($pending['username'])) {
                $error = 'Username already exists.';
            } elseif (find_user_by_email($pending['email'])) {
                $error = 'Email already exists.';
            } else {
                $users = read_users();
                $users[] = [
                    'username' => $pending['username'],
                    'email' => $pending['email'],
                    'password_hash' => $pending['password_hash'],
                    'created_at' => date('c'),
                ];

                if (!write_users($users)) {
                    $error = 'Could not save user file. Check permissions.';
                } else {
                    unset($_SESSION['pending_registration']);
                    login_user($pending['username']);
                    header('Location: dashboard.php');
                    exit;
                }
            }
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

        <?php if ($info): ?>
            <div class="notice"><?php echo htmlspecialchars($info); ?></div>
        <?php endif; ?>

        <?php if ($pending): ?>
            <div class="notice">Code sent to <?php echo htmlspecialchars($pending['email']); ?>.</div>
            <form method="post" class="form">
                <input type="hidden" name="step" value="verify_code">
                <label>
                    Verification Code
                    <input type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" required>
                </label>
                <button type="submit">Verify</button>
            </form>
            <a class="link" href="register.php?reset=1">Start over</a>
        <?php else: ?>
            <form method="post" class="form">
                <input type="hidden" name="step" value="request_code">
                <label>
                    Username
                    <input type="text" name="username" autocomplete="username" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" autocomplete="email" required>
                </label>
                <label>
                    Password
                    <input type="password" name="password" autocomplete="new-password" required>
                </label>
                <label>
                    Confirm Password
                    <input type="password" name="confirm" autocomplete="new-password" required>
                </label>
                <button type="submit">Send verification code</button>
            </form>

            <p class="hint">Use a strong password for the Password Strength lesson.</p>
            <a class="link" href="index.php">Back to login</a>
        <?php endif; ?>
    </main>
</body>
</html>
