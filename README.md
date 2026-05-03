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

## WebGoat Lesson

The dashboard links to the WebGoat Password Strength lesson. Update the URL in `dashboard.php` if your WebGoat host or path is different.

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
