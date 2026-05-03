<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WebGoat - Insecure Login</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap');
    :root {
      --bg: #f4f1ec; --card: #fff; --border: #e2ddd6;
      --accent: #7a1c1c; --text: #1a1a1a; --muted: #6b6560;
      --shadow: 0 2px 12px rgba(0,0,0,0.07);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }
    .navbar {
      background: var(--accent); color: #fff; padding: 14px 40px;
      display: flex; align-items: center; justify-content: space-between;
      font-family: 'JetBrains Mono', monospace; font-size: 0.9rem;
      position: sticky; top: 0; z-index: 100;
    }
    .navbar a { color: #fff; text-decoration: none; opacity: 0.75; font-size: 0.82rem; }
    .navbar a:hover { opacity: 1; }
    .hero {
      background: linear-gradient(135deg, #1a3a5c 0%, #2c5f8a 100%);
      color: #fff; padding: 48px 40px 40px; text-align: center;
    }
    .hero h1 { font-family: 'JetBrains Mono', monospace; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px; }
    .hero p { font-size: 0.95rem; opacity: 0.8; max-width: 520px; margin: 0 auto; line-height: 1.6; }
    .hero .badge { display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 20px; padding: 4px 14px; font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; margin: 12px 4px 0; }

    .main { max-width: 800px; margin: 0 auto; padding: 40px 24px 60px; }
    .back-link { display: inline-block; margin-bottom: 24px; font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; color: var(--accent); text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 28px 30px; box-shadow: var(--shadow); margin-bottom: 24px; }
    .card h2 { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
    .card p, .card li { font-size: 0.9rem; line-height: 1.7; color: #3a3530; }
    .card ul { padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .card ul li { padding-left: 20px; position: relative; }
    .card ul li::before { content: '>'; position: absolute; left: 0; color: var(--accent); }

    .compare-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px; }
    @media (max-width: 600px) { .compare-grid { grid-template-columns: 1fr; } }
    .compare-bad { background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 10px; padding: 16px 18px; }
    .compare-good { background: #f0fff4; border: 1px solid #b7dfb8; border-radius: 10px; padding: 16px 18px; }
    .compare-bad h4 { color: #c0392b; font-size: 0.78rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 10px; }
    .compare-good h4 { color: #1a7f37; font-size: 0.78rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 10px; }
    .compare-bad ul li::before { content: 'x'; color: #c0392b; }
    .compare-good ul li::before { content: '+'; color: #1a7f37; }

    .code-block { background: #1e1e1e; color: #d4d4d4; border-radius: 8px; padding: 16px 18px; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; line-height: 1.7; margin: 12px 0; overflow-x: auto; }
    .comment { color: #6a9955; } .keyword { color: #569cd6; }
    .string { color: #ce9178; } .danger { color: #f44747; } .safe { color: #4ec9b0; }

    .sim-box { background: #f9f9f9; border: 2px dashed #d0cac3; border-radius: 12px; padding: 24px; margin-top: 12px; }
    .sim-box h3 { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 700; margin-bottom: 16px; color: var(--text); }
    .sim-form { display: flex; flex-direction: column; gap: 10px; max-width: 360px; }
    .sim-form input { border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 0.87rem; font-family: 'Inter', sans-serif; background: #fff; outline: none; transition: border-color 0.2s; }
    .sim-form input:focus { border-color: var(--accent); }
    .sim-form button { background: var(--accent); color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; align-self: flex-start; }
    .sim-form button:hover { background: #a52828; }
    #sim-result { margin-top: 14px; padding: 12px 16px; border-radius: 8px; font-size: 0.87rem; display: none; }
    #sim-result.ok { background: #d4edda; color: #155724; border: 1px solid #b7dfb8; }
    #sim-result.warn { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    #packet-display { background: #1e1e1e; color: #d4d4d4; border-radius: 8px; padding: 12px 16px; font-family: 'JetBrains Mono', monospace; font-size: 0.77rem; line-height: 1.6; margin-top: 10px; display: none; }

    .quiz-section { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 28px 30px; box-shadow: var(--shadow); }
    .quiz-section h2 { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent); margin-bottom: 20px; }
    .quiz-q { margin-bottom: 22px; }
    .quiz-q p { font-size: 0.92rem; font-weight: 600; margin-bottom: 10px; }
    .quiz-q label { display: flex; align-items: center; gap: 10px; font-size: 0.87rem; padding: 8px 12px; border-radius: 8px; cursor: pointer; border: 1px solid var(--border); margin-bottom: 6px; transition: background 0.15s; }
    .quiz-q label:hover { background: #faf8f5; }
    .quiz-q input[type="radio"] { accent-color: var(--accent); }
    .btn-submit { background: var(--accent); color: #fff; border: none; border-radius: 8px; padding: 11px 26px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .btn-submit:hover { background: #a52828; }
    #quiz-result { margin-top: 16px; padding: 14px 16px; border-radius: 8px; font-size: 0.88rem; display: none; }
    #quiz-result.correct { background: #d4edda; color: #155724; border: 1px solid #b7dfb8; }
    #quiz-result.incorrect { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .footer { text-align: center; padding: 24px; font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace; border-top: 1px solid var(--border); }
  </style>
</head>
<body>

<nav class="navbar">
  <div style="font-family:'JetBrains Mono',monospace;font-weight:700;letter-spacing:0.05em;">WEBGOAT</div>
  <a href="dashboard.php"><- Back to Dashboard</a>
</nav>

<div class="hero">
  <h1>Insecure Login</h1>
  <p>Understand how poorly implemented authentication allows attackers to intercept or forge credentials and take over accounts.</p>
  <span class="badge">OWASP A07</span>
  <span class="badge">Auth Failure</span>
  <span class="badge">Intermediate</span>
</div>

<div class="main">
  <a href="dashboard.php" class="back-link"><- Back to Dashboard</a>

  <div class="card">
    <h2>What is Insecure Login?</h2>
    <p>Insecure Login vulnerabilities arise when authentication mechanisms are improperly implemented. Common issues include transmitting credentials over unencrypted connections, using weak or predictable session tokens, or lacking account lockout mechanisms.</p>
    <br/>
    <ul>
      <li><strong>Credential interception:</strong> sending login data over HTTP allows attackers on the same network to read it using tools like Wireshark.</li>
      <li><strong>Session hijacking:</strong> weak or predictable session tokens can be guessed, allowing attackers to impersonate logged-in users.</li>
      <li><strong>Credential stuffing:</strong> attackers use automated tools with leaked username/password lists from other breaches.</li>
      <li><strong>Missing MFA:</strong> without multi-factor authentication, a stolen password alone grants full access.</li>
    </ul>
  </div>

  <div class="card">
    <h2>Secure vs. Insecure Practices</h2>
    <div class="compare-grid">
      <div class="compare-bad">
        <h4>Insecure</h4>
        <ul>
          <li style="padding-left:20px;position:relative;">Credentials sent over HTTP</li>
          <li style="padding-left:20px;position:relative;">Session token: <code>sessionId=1234</code></li>
          <li style="padding-left:20px;position:relative;">No account lockout</li>
          <li style="padding-left:20px;position:relative;">Cookie without HttpOnly/Secure</li>
          <li style="padding-left:20px;position:relative;">Session never expires</li>
        </ul>
      </div>
      <div class="compare-good">
        <h4>Secure</h4>
        <ul>
          <li style="padding-left:20px;position:relative;">Credentials sent over HTTPS/TLS</li>
          <li style="padding-left:20px;position:relative;">Session token: 64+ random bytes</li>
          <li style="padding-left:20px;position:relative;">Lockout after 5 failed attempts</li>
          <li style="padding-left:20px;position:relative;">Cookie: HttpOnly; Secure; SameSite=Strict</li>
          <li style="padding-left:20px;position:relative;">Session expires after 30 min idle</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Code Example</h2>
    <div class="code-block">
<span class="comment">// INSECURE: predictable session token</span>
$_SESSION['user_id'] = <span class="danger">$user_id</span>;
setcookie(<span class="string">'session'</span>, <span class="danger">$user_id</span>);   <span class="comment">// Just the user ID</span>

<span class="comment">// SECURE: cryptographically random session token</span>
$token = bin2hex(random_bytes(<span class="safe">32</span>));  <span class="comment">// 64-char random hex</span>
setcookie(<span class="string">'session'</span>, $token, [
  <span class="string">'httponly'</span> => <span class="safe">true</span>,    <span class="comment">// Prevent JS access</span>
  <span class="string">'secure'</span>   => <span class="safe">true</span>,    <span class="comment">// HTTPS only</span>
  <span class="string">'samesite'</span> => <span class="string">'Strict'</span>, <span class="comment">// CSRF protection</span>
  <span class="string">'expires'</span>  => time() + 1800  <span class="comment">// 30-min expiry</span>
]);
    </div>
  </div>

  <div class="card">
    <h2>Interactive Simulation</h2>
    <p style="margin-bottom:14px;">See how credentials would appear to a network attacker depending on whether HTTP or HTTPS is used.</p>
    <div class="sim-box">
      <h3>Simulate a Login Request</h3>
      <div class="sim-form">
        <input type="text" id="sim-user" placeholder="Enter username (e.g. john_doe)" />
        <input type="password" id="sim-pass" placeholder="Enter password (e.g. mypassword)" />
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
          <button onclick="simulateLogin('http')">Send via HTTP (Insecure)</button>
          <button onclick="simulateLogin('https')" style="background:#1a7f37;">Send via HTTPS (Secure)</button>
        </div>
      </div>
      <div id="sim-result"></div>
      <div id="packet-display"></div>
    </div>
  </div>

  <div class="card">
    <h2>Sources</h2>
    <p>Inspired by the OWASP WebGoat project and OWASP Top 10 guidance.</p>
    <ul>
      <li><a href="https://owasp.org/www-project-webgoat/" target="_blank" rel="noopener noreferrer">OWASP WebGoat</a></li>
      <li><a href="https://owasp.org/Top10/" target="_blank" rel="noopener noreferrer">OWASP Top 10</a></li>
    </ul>
  </div>

  <div class="quiz-section">
    <h2>Knowledge Check</h2>
    <div class="quiz-q">
      <p>1. What is the primary risk of sending login credentials over plain HTTP?</p>
      <label><input type="radio" name="q1" value="a"/> The page loads slower</label>
      <label><input type="radio" name="q1" value="b"/> Credentials can be intercepted by attackers on the same network</label>
      <label><input type="radio" name="q1" value="c"/> The login form breaks on mobile devices</label>
      <label><input type="radio" name="q1" value="d"/> The server cannot process the request</label>
    </div>
    <div class="quiz-q">
      <p>2. Which cookie flag prevents JavaScript from accessing the session cookie?</p>
      <label><input type="radio" name="q2" value="a"/> Secure</label>
      <label><input type="radio" name="q2" value="b"/> SameSite</label>
      <label><input type="radio" name="q2" value="c"/> HttpOnly</label>
      <label><input type="radio" name="q2" value="d"/> Expires</label>
    </div>
    <div class="quiz-q">
      <p>3. What is "credential stuffing"?</p>
      <label><input type="radio" name="q3" value="a"/> Filling all form fields with random data</label>
      <label><input type="radio" name="q3" value="b"/> Using stolen username/password pairs from other breaches to log into different sites</label>
      <label><input type="radio" name="q3" value="c"/> Encrypting credentials before storing them</label>
      <label><input type="radio" name="q3" value="d"/> A method to strengthen passwords automatically</label>
    </div>
    <button class="btn-submit" onclick="checkQuiz()">Submit Answers</button>
    <div id="quiz-result"></div>
  </div>
</div>

<div class="footer">WebGoat | For Educational Purposes Only | OWASP Foundation</div>

<script>
function simulateLogin(protocol) {
  const user = document.getElementById('sim-user').value || 'john_doe';
  const pass = document.getElementById('sim-pass').value || 'mypassword';
  const result = document.getElementById('sim-result');
  const packet = document.getElementById('packet-display');
  result.style.display = 'block';
  packet.style.display = 'block';

  if (protocol === 'http') {
    result.className = 'warn';
    result.innerHTML = '<strong>WARNING:</strong> HTTP used. An attacker on the same network can see your credentials using a packet sniffer like Wireshark.';
    packet.innerHTML =
      `<span style="color:#f44747">// Network packet captured by attacker (Wireshark)</span>\n` +
      `POST http://example.com/login HTTP/1.1\n` +
      `Host: example.com\n` +
      `Content-Type: application/x-www-form-urlencoded\n\n` +
      `username=<span style="color:#f44747">${user}</span>&password=<span style="color:#f44747">${pass}</span>\n\n` +
      `<span style="color:#f44747">// ALERT: credentials visible in plaintext</span>`;
  } else {
    result.className = 'ok';
    result.innerHTML = '<strong>OK:</strong> HTTPS used. The connection is encrypted with TLS, so intercepted packets are unreadable.';
    packet.innerHTML =
      `<span style="color:#4ec9b0">// Network packet captured by attacker (Wireshark)</span>\n` +
      `TLSv1.3 Record Layer: Application Data\n` +
      `  Encrypted Data: <span style="color:#4ec9b0">a3f82c91d7b4e56...f1029ac83e7d214b\n` +
      `                   9c4a1f0b83e2d571...2e8f4c90a1b73d56</span>\n\n` +
      `<span style="color:#4ec9b0">// OK: credentials are encrypted</span>`;
  }
}

function checkQuiz() {
  const answers = { q1: 'b', q2: 'c', q3: 'b' };
  let score = 0;
  for (const [q, correct] of Object.entries(answers)) {
    const selected = document.querySelector(`input[name="${q}"]:checked`);
    if (selected && selected.value === correct) score++;
  }
  const result = document.getElementById('quiz-result');
  result.style.display = 'block';
  if (score === 3) {
    result.className = 'correct';
    result.innerHTML = 'Perfect score. You answered all 3 questions correctly.';
  } else if (score >= 1) {
    result.className = 'incorrect';
    result.innerHTML = `You got ${score}/3 correct. Review the lesson and try again. Correct answers: (1) B, (2) C, (3) B`;
  } else {
    result.className = 'incorrect';
    result.innerHTML = 'No correct answers. Please re-read the lesson carefully. Correct answers: (1) B, (2) C, (3) B';
  }
}
</script>
</body>
</html>
