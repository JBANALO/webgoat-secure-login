<?php

declare(strict_types=1);

function normalize_username(string $username): string
{
    return trim($username);
}

function validate_username(string $username): ?string
{
    if ($username === '') {
        return 'Username is required.';
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        return 'Username must be 3-50 characters.';
    }

    $has_at = strpos($username, '@') !== false;

    if ($has_at) {
        if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
            return 'Username must be a valid email address.';
        }
    } elseif (!preg_match('/^[A-Za-z0-9._-]+$/', $username)) {
        return 'Username contains invalid characters.';
    }

    return null;
}

function validate_password(string $password): ?string
{
    if ($password === '') {
        return 'Password is required.';
    }

    if (strlen($password) < 8 || strlen($password) > 128) {
        return 'Password must be 8-128 characters.';
    }

    return null;
}

function is_password_strong(string $password): bool
{
    $length_ok = strlen($password) >= 10;
    $upper_ok = preg_match('/[A-Z]/', $password) === 1;
    $lower_ok = preg_match('/[a-z]/', $password) === 1;
    $digit_ok = preg_match('/[0-9]/', $password) === 1;
    $symbol_ok = preg_match('/[^A-Za-z0-9]/', $password) === 1;

    return $length_ok && $upper_ok && $lower_ok && $digit_ok && $symbol_ok;
}
