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
  <title>WebGoat - SQL Injection</title>
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
      background: linear-gradient(135deg, #3d0e0e 0%, #7a1c1c 100%);
      color: #fff; padding: 48px 40px 40px; text-align: center;
    }
    .hero h1 { font-family: 'JetBrains Mono', monospace; font-size: 1.8rem; font-weight: 700; margin-bottom: 8px; }
    .hero p { font-size: 0.95rem; opacity: 0.8; max-width: 520px; margin: 0 auto; line-height: 1.6; }
    .hero .badge { display: inline-block; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 20px; padding: 4px 14px; font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; margin: 12px 4px 0; }

    .main { max-width: 800px; margin: 0 auto; padding: 40px 24px 60px; }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 28px 30px; box-shadow: var(--shadow); margin-bottom: 24px; }
    .card h2 { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
    .card p, .card li { font-size: 0.9rem; line-height: 1.7; color: #3a3530; }
    .card ul { padding-left: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .card ul li { padding-left: 20px; position: relative; }
    .card ul li::before { content: '>'; position: absolute; left: 0; color: var(--accent); }

    .step-num {
      display: inline-flex; align-items: center; justify-content: center;
      width: 28px; height: 28px; border-radius: 50%;
      background: var(--accent); color: #fff;
      font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700;
      margin-right: 10px; flex-shrink: 0;
    }
    .step { display: flex; align-items: flex-start; gap: 4px; margin-bottom: 14px; }
    .step-body { flex: 1; }
    .step-body strong { font-size: 0.9rem; display: block; margin-bottom: 4px; }
    .step-body p { font-size: 0.87rem; color: var(--muted); }

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
    .btn-submit {
      background: var(--accent); color: #fff; border: none; border-radius: 8px;
      padding: 11px 26px; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem;
      font-weight: 600; cursor: pointer; transition: background 0.2s;
    }
    .btn-submit:hover { background: #a52828; }
    #quiz-result { margin-top: 16px; padding: 14px 16px; border-radius: 8px; font-size: 0.88rem; display: none; }
    #quiz-result.correct { background: #d4edda; color: #155724; border: 1px solid #b7dfb8; }
    #quiz-result.incorrect { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .tag { display: inline-block; border-radius: 6px; padding: 3px 10px; font-size: 0.73rem; font-family: 'JetBrains Mono', monospace; font-weight: 600; margin-right: 6px; }
    .tag-sql { background: #fff3cd; color: #856404; }
    .tag-owasp { background: #ede7f6; color: #4527a0; }

    .back-link { display: inline-block; margin-bottom: 24px; font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; color: var(--accent); text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .footer { text-align: center; padding: 24px; font-size: 0.75rem; color: var(--muted); font-family: 'JetBrains Mono', monospace; border-top: 1px solid var(--border); }
  </style>
</head>
<body>

<nav class="navbar">
  <div style="font-family:'JetBrains Mono',monospace; font-weight:700; letter-spacing:0.05em;">WEBGOAT</div>
  <a href="dashboard.php"><- Back to Dashboard</a>
</nav>

<div class="hero">
  <h1>SQL Injection</h1>
  <p>Learn how attackers manipulate SQL queries through user input to bypass authentication and steal data.</p>
  <span class="badge">OWASP A03</span>
  <span class="badge">Injection</span>
  <span class="badge">Intermediate</span>
</div>

<div class="main">
  <a href="dashboard.php" class="back-link"><- Back to Dashboard</a>

  <div class="card">
    <h2>What is SQL Injection?</h2>
    <p>SQL Injection (SQLi) occurs when an attacker inserts malicious SQL code into an input field that is then executed by the database. This happens when user input is concatenated directly into a SQL query without sanitization.</p>
    <br/>
    <ul>
      <li><strong>In-band SQLi:</strong> the attacker uses the same channel to launch the attack and gather results (most common).</li>
      <li><strong>Blind SQLi:</strong> the application does not return query results, but the attacker can infer data through true/false responses.</li>
      <li><strong>Out-of-band SQLi:</strong> results are delivered via a different channel (for example, DNS or HTTP requests to an attacker-controlled server).</li>
    </ul>
  </div>

  <div class="card">
    <h2>How It Works - Step by Step</h2>
    <div class="step"><span class="step-num">1</span><div class="step-body"><strong>Application receives user input</strong><p>A login form takes a username and password from the user.</p></div></div>
    <div class="step"><span class="step-num">2</span><div class="step-body"><strong>Input is directly concatenated into SQL</strong><p>The developer builds the query by joining strings - a dangerous practice.</p></div></div>
    <div class="step"><span class="step-num">3</span><div class="step-body"><strong>Attacker crafts malicious input</strong><p>Instead of a username, the attacker enters SQL syntax to alter the query logic.</p></div></div>
    <div class="step"><span class="step-num">4</span><div class="step-body"><strong>Database executes the modified query</strong><p>The database treats the injected code as legitimate SQL, returning unintended results.</p></div></div>

    <div class="code-block">
<span class="comment">-- Vulnerable PHP code (do not use in production):</span>
$query = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'
          AND password = '" . $_POST['password'] . "'";

<span class="comment">-- Attacker enters: username = ' OR '1'='1' --</span>
<span class="comment">-- Resulting query becomes:</span>
<span class="keyword">SELECT</span> * <span class="keyword">FROM</span> users
<span class="keyword">WHERE</span> username = '<span class="danger">' OR '1'='1' --</span>'
<span class="keyword">AND</span> password = '...';

<span class="comment">-- '1'='1' is always TRUE, login bypassed.</span>
    </div>
  </div>

  <div class="card">
    <h2>Secure Code Example</h2>
    <p>Use <strong>prepared statements</strong> (parameterized queries) to prevent SQL injection. User input is never interpreted as SQL code.</p>
    <div class="code-block">
<span class="comment">// SECURE: Using PDO prepared statements in PHP</span>
$stmt = $pdo-><span class="safe">prepare</span>(<span class="string">"SELECT * FROM users WHERE username = ? AND password = ?"</span>);
$stmt-><span class="safe">execute</span>([$username, $password]);
$user = $stmt->fetch();

<span class="comment">// User input is treated as data, not SQL code.</span>
<span class="comment">// Even if attacker inputs: ' OR '1'='1</span>
<span class="comment">// It becomes a literal string - not SQL logic.</span>
    </div>
  </div>

  <div class="quiz-section">
    <h2>Knowledge Check</h2>

    <div class="quiz-q">
      <p>1. What is the main cause of SQL Injection vulnerabilities?</p>
      <label><input type="radio" name="q1" value="a"/> Using HTTPS instead of HTTP</label>
      <label><input type="radio" name="q1" value="b"/> Directly concatenating user input into SQL queries without sanitization</label>
      <label><input type="radio" name="q1" value="c"/> Having a weak password policy</label>
      <label><input type="radio" name="q1" value="d"/> Using an outdated operating system</label>
    </div>

    <div class="quiz-q">
      <p>2. Which of the following is the best defense against SQL Injection?</p>
      <label><input type="radio" name="q2" value="a"/> Disabling JavaScript on the browser</label>
      <label><input type="radio" name="q2" value="b"/> Using a firewall to block all traffic</label>
      <label><input type="radio" name="q2" value="c"/> Using prepared statements / parameterized queries</label>
      <label><input type="radio" name="q2" value="d"/> Hiding the login form from users</label>
    </div>

    <div class="quiz-q">
      <p>3. What does the payload <code>' OR '1'='1</code> do in a vulnerable login form?</p>
      <label><input type="radio" name="q3" value="a"/> It deletes all records from the database</label>
      <label><input type="radio" name="q3" value="b"/> It encrypts the database</label>
      <label><input type="radio" name="q3" value="c"/> It always evaluates to TRUE, bypassing authentication</label>
      <label><input type="radio" name="q3" value="d"/> It crashes the web server</label>
    </div>

    <button class="btn-submit" onclick="checkQuiz()">Submit Answers</button>
    <div id="quiz-result"></div>
  </div>
</div>

<div class="footer">WebGoat | For Educational Purposes Only | OWASP Foundation</div>

<script>
function checkQuiz() {
  const answers = { q1: 'b', q2: 'c', q3: 'c' };
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
    result.innerHTML = `You got ${score}/3 correct. Review the lesson and try again. Correct answers: (1) B, (2) C, (3) C`;
  } else {
    result.className = 'incorrect';
    result.innerHTML = 'No correct answers. Please re-read the lesson carefully. Correct answers: (1) B, (2) C, (3) C';
  }
}
</script>
</body>
</html>
