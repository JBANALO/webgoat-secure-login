# Secure Login Web App with WebGoat Lesson

This is a simple PHP web app that demonstrates secure login with password hashing and a post-login link to the WebGoat Password Strength lesson.

## Setup

1. Ensure you have PHP installed.
2. Start a local server in this folder:

```bash
php -S localhost:8000 -t .
```

3. Open the app:

- Setup account: http://localhost:8000/setup.php
- Login page: http://localhost:8000/index.php
- Register page: http://localhost:8000/register.php

## WebGoat Lesson

The dashboard includes local lesson pages for Password Strength, SQL Injection, and Insecure Login. You can also point lessons to an external WebGoat instance by updating `dashboard.php`.

## Security Measures Implemented

- Passwords are hashed using `password_hash` and verified with `password_verify`.
- Basic input validation is enforced for usernames and passwords.
- Login errors are generic to avoid leaking system details.
- Session ID is regenerated on login to reduce session fixation risk.

## Vulnerability Awareness (Example: Insecure Login)

- If passwords were stored in plain text, a data leak would expose every account.
- Weak validation and detailed error messages can help attackers guess valid usernames.
- Hashing and generic errors reduce the impact of credential theft and enumeration.
- Strong password rules make brute-force attacks harder.

## Notes

- The first user must be created in `setup.php`.
- The user database is stored in `data/users.json`.
- This project is intended for classroom use only.

## Email verification (Registration)

Registration uses a 6-digit email verification code. Configure these environment variables (e.g., Railway Variables):

- `SMTP_HOST`
- `SMTP_PORT` (default: 587)
- `SMTP_USER`
- `SMTP_PASS`
- `SMTP_FROM` (from email address)
- `SMTP_FROM_NAME` (optional)
- `SMTP_ENCRYPTION` (`tls` or `ssl`, default: `tls`)
- `OTP_EXPIRY_MINUTES` (default: 5)
