<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

require_login();

$lesson_url = 'http://localhost:8080/WebGoat/lessons/PasswordStrength';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>WebGoat Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap');

        :root {
            --bg: #f4f1ec;
            --card: #ffffff;
            --border: #e2ddd6;
            --accent: #7a1c1c;
            --accent-light: #a52828;
            --text: #1a1a1a;
            --muted: #6b6560;
            --tag-bg: #f0ebe4;
            --tag-sql: #fff3cd;
            --tag-sql-text: #856404;
            --tag-auth: #d1ecf1;
            --tag-auth-text: #0c5460;
            --tag-pass: #d4edda;
            --tag-pass-text: #155724;
            --shadow: 0 2px 12px rgba(0,0,0,0.07);
            --shadow-hover: 0 8px 28px rgba(0,0,0,0.13);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 0;
        }

        .navbar {
            background: var(--accent);
            color: #fff;
            padding: 14px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.95rem;
            letter-spacing: 0.03em;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(122,28,28,0.3);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.06em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand span.icon { font-size: 1rem; }
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            font-weight: 400;
            opacity: 0.85;
            font-size: 0.82rem;
        }
        .nav-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.82rem;
            opacity: 0.9;
        }
        .nav-link:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .hero {
            background: linear-gradient(135deg, #7a1c1c 0%, #3d0e0e 100%);
            color: #fff;
            padding: 56px 40px 48px;
            text-align: center;
        }
        .hero h1 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1rem;
            opacity: 0.8;
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .hero .badge-row {
            margin-top: 22px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero .badge {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 0.78rem;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.04em;
        }

        .main {
            max-width: 860px;
            margin: 0 auto;
            padding: 44px 24px 60px;
        }

        .section-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .lessons-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 48px;
        }

        .lesson-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px 32px;
            box-shadow: var(--shadow);
            transition: box-shadow 0.25s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        .lesson-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--accent);
            border-radius: 4px 0 0 4px;
        }
        .lesson-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .card-title-group { flex: 1; }
        .card-icon {
            font-size: 1.1rem;
            margin-bottom: 4px;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.08em;
        }
        .card-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .card-subtitle {
            font-size: 0.82rem;
            color: var(--muted);
        }

        .tags {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }
        .tag {
            border-radius: 6px;
            padding: 3px 10px;
            font-size: 0.73rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            letter-spacing: 0.04em;
        }
        .tag-sql { background: var(--tag-sql); color: var(--tag-sql-text); }
        .tag-auth { background: var(--tag-auth); color: var(--tag-auth-text); }
        .tag-pass { background: var(--tag-pass); color: var(--tag-pass-text); }
        .tag-owasp { background: #ede7f6; color: #4527a0; }
        .tag-intermediate { background: #fce4ec; color: #880e4f; }

        .card-desc {
            font-size: 0.9rem;
            color: #3a3530;
            line-height: 1.65;
            margin-bottom: 20px;
        }

        .lesson-notes {
            background: #faf8f5;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
        .lesson-notes h4 {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 12px;
        }
        .lesson-notes ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .lesson-notes ul li {
            font-size: 0.88rem;
            color: #3a3530;
            line-height: 1.55;
            padding-left: 18px;
            position: relative;
        }
        .lesson-notes ul li::before {
            content: '>';
            position: absolute;
            left: 0;
            color: var(--accent);
            font-size: 0.75rem;
            top: 2px;
        }
        .lesson-notes ul li strong { color: var(--text); }

        .vuln-box {
            background: #fff5f5;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .vuln-box h4 {
            font-size: 0.78rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #c0392b;
            margin-bottom: 8px;
        }
        .vuln-box ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .vuln-box ul li {
            font-size: 0.87rem;
            color: #5a2020;
            padding-left: 16px;
            position: relative;
            line-height: 1.5;
        }
        .vuln-box ul li::before {
            content: '!';
            position: absolute;
            left: 0;
            color: #c0392b;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            border-radius: 8px;
            padding: 14px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            line-height: 1.6;
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .code-block .comment { color: #6a9955; }
        .code-block .keyword { color: #569cd6; }
        .code-block .string { color: #ce9178; }
        .code-block .danger { color: #f44747; }

        .mitigation-box {
            background: #f0fff4;
            border: 1px solid #b7dfb8;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .mitigation-box h4 {
            font-size: 0.78rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #1a7f37;
            margin-bottom: 8px;
        }
        .mitigation-box ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .mitigation-box ul li {
            font-size: 0.87rem;
            color: #1a4a2a;
            padding-left: 18px;
            position: relative;
            line-height: 1.5;
        }
        .mitigation-box ul li::before {
            content: '+';
            position: absolute;
            left: 0;
            color: #1a7f37;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .btn-open {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 11px 24px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-open:hover {
            background: var(--accent-light);
            transform: translateY(-1px);
        }

        .footer {
            text-align: center;
            padding: 28px;
            font-size: 0.78rem;
            color: var(--muted);
            font-family: 'JetBrains Mono', monospace;
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <span class="icon">WG</span> WEBGOAT
    </div>
    <div class="navbar-right">
        <span>Intermediate Level | Educational Use Only</span>
        <a class="nav-link" href="logout.php">Log out</a>
    </div>
</nav>

<div class="hero">
    <h1>Welcome</h1>
    <p>You are logged in. Access the selected WebGoat lesson below. Each lesson includes explanation, vulnerability examples, and mitigations.</p>
    <div class="badge-row">
        <span class="badge">OWASP Top 10</span>
        <span class="badge">Intermediate</span>
        <span class="badge">3 Lessons</span>
    </div>
</div>

<div class="main">

    <div class="section-title">Available Lessons</div>

    <div class="lessons-grid">

        <div class="lesson-card">
            <div class="card-header">
                <div class="card-title-group">
                    <div class="card-icon">PW</div>
                    <div class="card-title">WebGoat: Password Strength</div>
                    <div class="card-subtitle">OWASP A07 - Identification &amp; Authentication Failures</div>
                </div>
            </div>
            <div class="tags">
                <span class="tag tag-pass">Password Security</span>
                <span class="tag tag-owasp">OWASP A07</span>
                <span class="tag tag-intermediate">Intermediate</span>
            </div>
            <p class="card-desc">
                This lesson demonstrates why weak or predictable passwords are dangerous and how attackers exploit them through brute force or credential stuffing attacks.
            </p>
            <div class="lesson-notes">
                <h4>Lesson Notes</h4>
                <ul>
                    <li><strong>Strong password:</strong> 10+ characters, with upper/lowercase letters, numbers, and symbols.</li>
                    <li><strong>Weak password:</strong> easy to guess or brute-force (for example, "password123", "abc123").</li>
                    <li><strong>Broken authentication:</strong> when login rules are weak, accounts can be taken over.</li>
                    <li><strong>Example impact:</strong> an attacker can access the system using commonly used passwords.</li>
                </ul>
            </div>
            <div class="vuln-box">
                <h4>Vulnerability Awareness</h4>
                <ul>
                    <li>No account lockout after multiple failed login attempts enables brute-force attacks.</li>
                    <li>Storing passwords in plaintext or using weak hashing (for example, MD5) exposes credentials on breach.</li>
                </ul>
            </div>
            <div class="mitigation-box">
                <h4>Mitigations</h4>
                <ul>
                    <li>Enforce strong password policies (minimum length and complexity requirements).</li>
                    <li>Implement account lockout or CAPTCHA after failed attempts.</li>
                    <li>Store passwords using bcrypt or Argon2 hashing.</li>
                </ul>
            </div>
            <a href="<?php echo htmlspecialchars($lesson_url); ?>" class="btn-open" target="_blank" rel="noopener noreferrer">Open Lesson</a>
        </div>

        <div class="lesson-card">
            <div class="card-header">
                <div class="card-title-group">
                    <div class="card-icon">SQL</div>
                    <div class="card-title">WebGoat: SQL Injection</div>
                    <div class="card-subtitle">OWASP A03 - Injection</div>
                </div>
            </div>
            <div class="tags">
                <span class="tag tag-sql">SQL Injection</span>
                <span class="tag tag-owasp">OWASP A03</span>
                <span class="tag tag-intermediate">Intermediate</span>
            </div>
            <p class="card-desc">
                SQL Injection is one of the most critical web vulnerabilities. It occurs when untrusted user input is inserted directly into a SQL query, allowing attackers to manipulate the database - bypassing authentication, reading sensitive data, or even deleting records.
            </p>
            <div class="lesson-notes">
                <h4>Lesson Notes</h4>
                <ul>
                    <li><strong>What is SQLi?</strong> inserting malicious SQL code into an input field to manipulate database queries.</li>
                    <li><strong>Classic example:</strong> entering <code>' OR '1'='1</code> in a login form to bypass authentication.</li>
                    <li><strong>Types:</strong> in-band (classic), blind, and out-of-band SQL Injection.</li>
                    <li><strong>Impact:</strong> unauthorized data access, authentication bypass, data deletion, or full database takeover.</li>
                </ul>
            </div>

            <div class="code-block">
<span class="comment">-- Vulnerable query (do not use in production)</span>
<span class="keyword">SELECT</span> * <span class="keyword">FROM</span> users
<span class="keyword">WHERE</span> username = '<span class="danger">' OR '1'='1</span>'
<span class="keyword">AND</span> password = '<span class="danger">anything</span>';

<span class="comment">-- This always returns TRUE, bypassing login.</span>
            </div>

            <div class="vuln-box">
                <h4>Vulnerability Awareness</h4>
                <ul>
                    <li>Directly concatenating user input into SQL queries without sanitization.</li>
                    <li>Exposing detailed database error messages to the user.</li>
                    <li>Running the web app database as a high-privilege user (for example, root/sa).</li>
                </ul>
            </div>
            <div class="mitigation-box">
                <h4>Mitigations</h4>
                <ul>
                    <li>Use <strong>prepared statements</strong> (parameterized queries) - never concatenate user input.</li>
                    <li>Use an ORM (Object-Relational Mapper) to abstract database queries.</li>
                    <li>Apply the <strong>principle of least privilege</strong> for database accounts.</li>
                    <li>Disable verbose error messages in production.</li>
                    <li>Validate and sanitize all user inputs on the server side.</li>
                </ul>
            </div>
            <a href="sql-injection.php" class="btn-open">Open Lesson</a>
        </div>

        <div class="lesson-card">
            <div class="card-header">
                <div class="card-title-group">
                    <div class="card-icon">AUTH</div>
                    <div class="card-title">WebGoat: Insecure Login</div>
                    <div class="card-subtitle">OWASP A07 - Identification &amp; Authentication Failures</div>
                </div>
            </div>
            <div class="tags">
                <span class="tag tag-auth">Auth Failure</span>
                <span class="tag tag-owasp">OWASP A07</span>
                <span class="tag tag-intermediate">Intermediate</span>
            </div>
            <p class="card-desc">
                Insecure Login vulnerabilities occur when authentication mechanisms are poorly implemented - transmitting credentials in plaintext, using predictable session tokens, or failing to properly validate user identity. Attackers can intercept or forge credentials to gain unauthorized access.
            </p>
            <div class="lesson-notes">
                <h4>Lesson Notes</h4>
                <ul>
                    <li><strong>Plaintext credentials:</strong> sending username/password over HTTP (not HTTPS) allows interception.</li>
                    <li><strong>Weak session tokens:</strong> predictable or short tokens can be guessed or brute-forced.</li>
                    <li><strong>Missing MFA:</strong> single-factor authentication is easier to compromise.</li>
                    <li><strong>Credential stuffing:</strong> attackers use leaked username/password pairs from other breaches.</li>
                    <li><strong>Impact:</strong> account takeover, unauthorized access to sensitive data or admin panels.</li>
                </ul>
            </div>

            <div class="code-block">
<span class="comment">// INSECURE: credentials sent over plain HTTP</span>
<span class="keyword">POST</span> http://example.com/login
username=<span class="danger">admin</span>&amp;password=<span class="danger">password123</span>

<span class="comment">// INSECURE: weak session token</span>
Set-Cookie: sessionId=<span class="danger">12345</span>; HttpOnly=false
            </div>

            <div class="vuln-box">
                <h4>Vulnerability Awareness</h4>
                <ul>
                    <li>Transmitting login credentials over unencrypted HTTP connections.</li>
                    <li>Using sequential or easily guessable session identifiers.</li>
                    <li>Not expiring sessions after logout or a period of inactivity.</li>
                    <li>Storing session tokens in localStorage (vulnerable to XSS).</li>
                </ul>
            </div>
            <div class="mitigation-box">
                <h4>Mitigations</h4>
                <ul>
                    <li>Always use <strong>HTTPS/TLS</strong> to encrypt data in transit.</li>
                    <li>Generate cryptographically random, long session tokens (use <code>random_bytes()</code> in PHP).</li>
                    <li>Set cookies with <strong>HttpOnly</strong>, <strong>Secure</strong>, and <strong>SameSite</strong> flags.</li>
                    <li>Implement session expiration and invalidate tokens on logout.</li>
                    <li>Enable <strong>Multi-Factor Authentication (MFA)</strong> for sensitive accounts.</li>
                </ul>
            </div>
            <a href="insecure-login.php" class="btn-open">Open Lesson</a>
        </div>

    </div>
</div>

<div class="footer">
    WebGoat | For Educational Purposes Only | OWASP Foundation
</div>

</body>
</html>
