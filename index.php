<?php
session_start();
include 'db.php';

if(isset($_SESSION['user'])){ header("Location: dashboard.php"); exit(); }

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $email    = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if($user && password_verify($password, $user['password'])){
    $_SESSION['user']    = $user['name'];
    $_SESSION['user_id'] = $user['id'];
    header("Location: dashboard.php");
    exit();
  } else {
    $error = "Invalid email or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — BookNest</title>
  <link rel="stylesheet" href="style.css">
  <style>
  .auth-page {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--navy);
  }

  /* LEFT PANEL */
  .auth-left {
    display: flex; flex-direction: column; justify-content: center;
    padding: 60px; position: relative; overflow: hidden;
    background: linear-gradient(160deg, #0d1f3c 0%, #0d2a1f 100%);
    border-right: 0.5px solid rgba(255,255,255,0.07);
  }

  .auth-left-glow1 {
    position: absolute; top: -80px; left: -80px;
    width: 300px; height: 300px; border-radius: 50%;
    background: radial-gradient(circle, rgba(79,142,247,0.15) 0%, transparent 70%);
    pointer-events: none;
  }

  .auth-left-glow2 {
    position: absolute; bottom: -60px; right: -60px;
    width: 250px; height: 250px; border-radius: 50%;
    background: radial-gradient(circle, rgba(52,211,153,0.1) 0%, transparent 70%);
    pointer-events: none;
  }

  .auth-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 60px; }
  .auth-brand-icon { width: 44px; height: 44px; background: linear-gradient(135deg,#4f8ef7,#34d399); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
  .auth-brand-name { font-family: 'DM Serif Display', serif; font-size: 1.4rem; }
  .auth-brand-name em { font-style: italic; color: #4f8ef7; }

  .auth-headline { font-family: 'DM Serif Display', serif; font-size: 2.8rem; font-weight: 400; line-height: 1.15; margin-bottom: 16px; }
  .auth-headline em { font-style: italic; color: #34d399; }
  .auth-tagline { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.7; max-width: 340px; margin-bottom: 48px; }

  .auth-features { display: flex; flex-direction: column; gap: 14px; }
  .auth-feat { display: flex; align-items: center; gap: 12px; }
  .auth-feat-icon { width: 36px; height: 36px; border-radius: 9px; background: rgba(255,255,255,0.06); border: 0.5px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
  .auth-feat-text { font-size: 13px; color: rgba(255,255,255,0.6); }
  .auth-feat-text strong { color: rgba(255,255,255,0.9); }

  /* RIGHT PANEL */
  .auth-right {
    display: flex; align-items: center; justify-content: center;
    padding: 40px;
    background: var(--navy);
  }

  .auth-form-wrap { width: 100%; max-width: 380px; }

  .auth-form-title { font-family: 'DM Serif Display', serif; font-size: 1.8rem; font-weight: 400; margin-bottom: 4px; }
  .auth-form-sub { font-size: 13px; color: rgba(255,255,255,0.4); margin-bottom: 32px; }

  .auth-field { margin-bottom: 18px; }
  .auth-field label { display: block; font-size: 10.5px; font-weight: 500; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
  .auth-field input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: all 0.15s; }
  .auth-field input:focus { border-color: rgba(79,142,247,0.5); box-shadow: 0 0 0 3px rgba(79,142,247,0.1); }
  .auth-field input::placeholder { color: rgba(255,255,255,0.2); }

  .auth-btn { width: 100%; padding: 13px; background: linear-gradient(135deg,#4f8ef7,#34d399); color: white; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; cursor: pointer; transition: opacity 0.2s, transform 0.15s; margin-top: 8px; }
  .auth-btn:hover { opacity: 0.9; transform: translateY(-1px); }

  .auth-error { padding: 11px 14px; background: rgba(248,113,113,0.1); color: #f87171; border: 0.5px solid rgba(248,113,113,0.2); border-radius: 10px; font-size: 13px; margin-bottom: 18px; }

  .auth-link { text-align: center; font-size: 13px; color: rgba(255,255,255,0.35); margin-top: 20px; }
  .auth-link a { color: #4f8ef7; text-decoration: none; }

  .auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
  .auth-divider-line { flex: 1; height: 0.5px; background: rgba(255,255,255,0.08); }
  .auth-divider-text { font-size: 11px; color: rgba(255,255,255,0.25); white-space: nowrap; }
  </style>
</head>
<body>

<div class="auth-page">

  <!-- LEFT: HERO -->
  <div class="auth-left">
    <div class="auth-left-glow1"></div>
    <div class="auth-left-glow2"></div>

    <div class="auth-brand">
      <div class="auth-brand-icon">📚</div>
      <div class="auth-brand-name">Book<em>Nest</em></div>
    </div>

    <h1 class="auth-headline">Your library,<br><em>beautifully</em><br>managed.</h1>
    <p class="auth-tagline">A modern library management system built to keep your book collection organized, accessible, and beautiful.</p>

    <div class="auth-features">
      <div class="auth-feat">
        <div class="auth-feat-icon">📚</div>
        <div class="auth-feat-text"><strong>Book Management</strong> — Add, edit, and track your entire collection</div>
      </div>
      <div class="auth-feat">
        <div class="auth-feat-icon">👥</div>
        <div class="auth-feat-text"><strong>Member Tracking</strong> — Manage library members with ease</div>
      </div>
      <div class="auth-feat">
        <div class="auth-feat-icon">↩</div>
        <div class="auth-feat-text"><strong>Borrow & Return</strong> — Track who has what and when it's due</div>
      </div>
      <div class="auth-feat">
        <div class="auth-feat-icon">📊</div>
        <div class="auth-feat-text"><strong>Live Dashboard</strong> — Real-time stats and availability overview</div>
      </div>
    </div>
  </div>

  <!-- RIGHT: FORM -->
  <div class="auth-right">
    <div class="auth-form-wrap">

      <div class="auth-form-title">Welcome back</div>
      <div class="auth-form-sub">Sign in to your BookNest account</div>

      <?php if($error): ?>
        <div class="auth-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="auth-field">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="auth-field">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="auth-btn">Sign in →</button>
      </form>

      <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <div class="auth-divider-text">New to BookNest?</div>
        <div class="auth-divider-line"></div>
      </div>

      <div class="auth-link"><a href="register.php">Create an account</a></div>

    </div>
  </div>

</div>

</body>
</html>