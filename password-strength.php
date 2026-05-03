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
  <title>WebGoat - Password Strength</title>
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
      background: linear-gradient(135deg, #2e0d0d 0%, #7a1c1c 100%);
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

    .code-block {
      background: #1e1e1e; color: #d4d4d4;
      border-radius: 8px; padding: 16px 18px;
      font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; line-height: 1.7;
      margin: 12px 0; overflow-x: auto;
    }
    .comment { color: #6a9955; } .keyword { color: #569cd6; }
    .string { color: #ce9178; } .danger { color: #f44747; } .safe { color: #4ec9b0; }

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
  <h1>Password Strength</h1>
  <p>Learn how weak passwords lead to account takeovers and how to build strong, resilient password policies.</p>
  <span class="badge">OWASP A07</span>
  <span class="badge">Auth Failure</span>
  <span class="badge">Intermediate</span>
</div>

<div class="main">
  <a href="dashboard.php" class="back-link"><- Back to Dashboard</a>

  <div class="card">
    <h2>Why Password Strength Matters</h2>
    <p>Attackers rely on automated tools to guess passwords quickly. A weak password can be cracked in seconds, while a strong one can resist brute-force and credential stuffing attacks.</p>
    <br/>
    <ul>
      <li><strong>Credential stuffing:</strong> attackers reuse leaked credentials from other sites.</li>
      <li><strong>Brute force:</strong> automated guessing of common words and patterns.</li>
      <li><strong>Password spraying:</strong> trying a few common passwords on many accounts.</li>
    </ul>
  </div>

  <div class="card">
    <h2>Common Weak Patterns</h2>
    <ul>
      <li><strong>Short length:</strong> passwords under 8 characters crack quickly.</li>
      <li><strong>Predictable words:</strong> names, birthdays, or simple phrases.</li>
      <li><strong>Reused passwords:</strong> one breach compromises multiple accounts.</li>
    </ul>
    <div class="code-block">
<span class="comment">// Examples of weak passwords</span>
<span class="danger">password123</span>
<span class="danger">qwerty2024</span>
<span class="danger">john1985</span>

<span class="comment">// Example of a strong password</span>
<span class="safe">C0mplex!River#96</span>
    </div>
  </div>

  <div class="card">
    <h2>Strong Password Checklist</h2>
    <ul>
      <li><strong>Length:</strong> 10+ characters.</li>
      <li><strong>Complexity:</strong> mix of upper, lower, digits, and symbols.</li>
      <li><strong>Uniqueness:</strong> never reuse across sites.</li>
      <li><strong>Rotation:</strong> change if compromise is suspected.</li>
      <li><strong>MFA:</strong> add a second factor for high-value accounts.</li>
    </ul>
  </div>

  <div class="card">
    <h2>Secure Code Example</h2>
    <p>Use PHP's built-in hashing API. Never store raw passwords.</p>
    <div class="code-block">
<span class="comment">// SECURE: Hash on registration</span>
$hash = <span class="safe">password_hash</span>($password, <span class="safe">PASSWORD_DEFAULT</span>);

<span class="comment">// SECURE: Verify on login</span>
if (<span class="safe">password_verify</span>($password, $hash)) {
  <span class="comment">// Password is valid</span>
}
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
      <p>1. Which password is strongest?</p>
      <label><input type="radio" name="q1" value="a"/> password123</label>
      <label><input type="radio" name="q1" value="b"/> John1998</label>
      <label><input type="radio" name="q1" value="c"/> C0mplex!River#96</label>
      <label><input type="radio" name="q1" value="d"/> abc123</label>
    </div>

    <div class="quiz-q">
      <p>2. What is credential stuffing?</p>
      <label><input type="radio" name="q2" value="a"/> Guessing random passwords for a single user</label>
      <label><input type="radio" name="q2" value="b"/> Reusing leaked usernames and passwords on other sites</label>
      <label><input type="radio" name="q2" value="c"/> Encrypting user passwords with AES</label>
      <label><input type="radio" name="q2" value="d"/> Forcing users to reset passwords daily</label>
    </div>

    <div class="quiz-q">
      <p>3. Which practice reduces password-related risk the most?</p>
      <label><input type="radio" name="q3" value="a"/> Allowing any password length</label>
      <label><input type="radio" name="q3" value="b"/> Storing passwords in plaintext</label>
      <label><input type="radio" name="q3" value="c"/> Enforcing strong passwords and hashing them</label>
      <label><input type="radio" name="q3" value="d"/> Sending passwords via email</label>
    </div>

    <button class="btn-submit" onclick="checkQuiz()">Submit Answers</button>
    <div id="quiz-result"></div>
  </div>
</div>

<div class="footer">WebGoat | For Educational Purposes Only | OWASP Foundation</div>

<script>
function checkQuiz() {
  const answers = { q1: 'c', q2: 'b', q3: 'c' };
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
    result.innerHTML = `You got ${score}/3 correct. Review the lesson and try again. Correct answers: (1) C, (2) B, (3) C`;
  } else {
    result.className = 'incorrect';
    result.innerHTML = 'No correct answers. Please re-read the lesson carefully. Correct answers: (1) C, (2) B, (3) C';
  }
}
</script>
</body>
</html>
