<?php
include 'db.php';

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);

  if($stmt->execute()){
    header("Location: index.php");
    exit();
  } else {
    $error = "Registration failed. Email may already be in use.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register — BookNest</title>
  <link rel="stylesheet" href="style.css">
  <style>
  .auth-page { min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; background: var(--navy); }

  .auth-left {
    display: flex; flex-direction: column; justify-content: center;
    padding: 60px; position: relative; overflow: hidden;
    background: linear-gradient(160deg, #1a0d3c 0%, #0d1a3c 100%);
    border-right: 0.5px solid rgba(255,255,255,0.07);
  }

  .auth-left-glow1 { position: absolute; top: -80px; left: -80px; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(167,139,250,0.15) 0%, transparent 70%); pointer-events: none; }
  .auth-left-glow2 { position: absolute; bottom: -60px; right: -60px; width: 250px; height: 250px; border-radius: 50%; background: radial-gradient(circle, rgba(79,142,247,0.1) 0%, transparent 70%); pointer-events: none; }

  .auth-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 60px; }
  .auth-brand-icon { width: 44px; height: 44px; background: linear-gradient(135deg,#a78bfa,#4f8ef7); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
  .auth-brand-name { font-family: 'DM Serif Display', serif; font-size: 1.4rem; }
  .auth-brand-name em { font-style: italic; color: #a78bfa; }

  .auth-headline { font-family: 'DM Serif Display', serif; font-size: 2.8rem; font-weight: 400; line-height: 1.15; margin-bottom: 16px; }
  .auth-headline em { font-style: italic; color: #a78bfa; }
  .auth-tagline { font-size: 14px; color: rgba(255,255,255,0.45); line-height: 1.7; max-width: 340px; margin-bottom: 48px; }

  .auth-steps { display: flex; flex-direction: column; gap: 0; }
  .auth-step { display: flex; gap: 16px; padding-bottom: 20px; position: relative; }
  .auth-step:last-child { padding-bottom: 0; }
  .auth-step-left { display: flex; flex-direction: column; align-items: center; }
  .auth-step-num { width: 30px; height: 30px; border-radius: 50%; background: rgba(167,139,250,0.15); border: 0.5px solid rgba(167,139,250,0.3); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #a78bfa; flex-shrink: 0; }
  .auth-step-line { width: 1px; flex: 1; background: rgba(255,255,255,0.07); margin-top: 6px; }
  .auth-step:last-child .auth-step-line { display: none; }
  .auth-step-content { padding-top: 4px; }
  .auth-step-title { font-size: 13px; font-weight: 500; margin-bottom: 3px; }
  .auth-step-desc { font-size: 12px; color: rgba(255,255,255,0.4); line-height: 1.5; }

  .auth-right { display: flex; align-items: center; justify-content: center; padding: 40px; background: var(--navy); }
  .auth-form-wrap { width: 100%; max-width: 380px; }

  /* LIVE AVATAR */
  .avatar-preview-wrap { display: flex; justify-content: center; margin-bottom: 24px; }
  .avatar-preview { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #1a0d3c, #a78bfa); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; color: white; border: 2px solid rgba(167,139,250,0.3); font-family: 'DM Sans', sans-serif; transition: all 0.2s; }

  .auth-form-title { font-family: 'DM Serif Display', serif; font-size: 1.8rem; font-weight: 400; margin-bottom: 4px; }
  .auth-form-sub { font-size: 13px; color: rgba(255,255,255,0.4); margin-bottom: 28px; }

  .auth-field { margin-bottom: 16px; }
  .auth-field label { display: block; font-size: 10.5px; font-weight: 500; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
  .auth-field input { width: 100%; padding: 12px 16px; background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; color: white; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; transition: all 0.15s; }
  .auth-field input:focus { border-color: rgba(167,139,250,0.5); box-shadow: 0 0 0 3px rgba(167,139,250,0.1); }
  .auth-field input::placeholder { color: rgba(255,255,255,0.2); }

  .auth-btn { width: 100%; padding: 13px; background: linear-gradient(135deg,#a78bfa,#4f8ef7); color: white; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500; cursor: pointer; transition: opacity 0.2s, transform 0.15s; margin-top: 8px; }
  .auth-btn:hover { opacity: 0.9; transform: translateY(-1px); }

  .auth-error { padding: 11px 14px; background: rgba(248,113,113,0.1); color: #f87171; border: 0.5px solid rgba(248,113,113,0.2); border-radius: 10px; font-size: 13px; margin-bottom: 18px; }

  .auth-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
  .auth-divider-line { flex: 1; height: 0.5px; background: rgba(255,255,255,0.08); }
  .auth-divider-text { font-size: 11px; color: rgba(255,255,255,0.25); }
  .auth-link { text-align: center; font-size: 13px; color: rgba(255,255,255,0.35); }
  .auth-link a { color: #a78bfa; text-decoration: none; }
  </style>
</head>
<body>

<div class="auth-page">

  <!-- LEFT -->
  <div class="auth-left">
    <div class="auth-left-glow1"></div>
    <div class="auth-left-glow2"></div>

    <div class="auth-brand">
      <div class="auth-brand-icon">📚</div>
      <div class="auth-brand-name">Book<em>Nest</em></div>
    </div>

    <h1 class="auth-headline">Join the<br><em>community</em><br>of readers.</h1>
    <p class="auth-tagline">Create your account and start exploring a smarter way to manage and discover books.</p>

    <div class="auth-steps">
      <div class="auth-step">
        <div class="auth-step-left"><div class="auth-step-num">1</div><div class="auth-step-line"></div></div>
        <div class="auth-step-content"><div class="auth-step-title">Create your account</div><div class="auth-step-desc">Fill in your name, email, and a secure password</div></div>
      </div>
      <div class="auth-step">
        <div class="auth-step-left"><div class="auth-step-num">2</div><div class="auth-step-line"></div></div>
        <div class="auth-step-content"><div class="auth-step-title">Sign in to the system</div><div class="auth-step-desc">Use your credentials to access the dashboard</div></div>
      </div>
      <div class="auth-step">
        <div class="auth-step-left"><div class="auth-step-num">3</div><div class="auth-step-line"></div></div>
        <div class="auth-step-content"><div class="auth-step-title">Start managing books</div><div class="auth-step-desc">Add books, manage members, track borrows and returns</div></div>
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="auth-right">
    <div class="auth-form-wrap">

      <div class="avatar-preview-wrap">
        <div class="avatar-preview" id="avatarPreview">?</div>
      </div>

      <div class="auth-form-title">Create account</div>
      <div class="auth-form-sub">Join BookNest — it's free</div>

      <?php if($error): ?>
        <div class="auth-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="auth-field">
          <label>Full Name</label>
          <input type="text" name="name" id="nameInput" placeholder="Your full name" required>
        </div>
        <div class="auth-field">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="auth-field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Min. 8 characters" required>
        </div>
        <button type="submit" class="auth-btn">Create account →</button>
      </form>

      <div class="auth-divider">
        <div class="auth-divider-line"></div>
        <div class="auth-divider-text">Already have an account?</div>
        <div class="auth-divider-line"></div>
      </div>

      <div class="auth-link"><a href="index.php">Sign in instead</a></div>

    </div>
  </div>

</div>

<script>
document.getElementById('nameInput').addEventListener('input', function() {
  const val = this.value.trim();
  document.getElementById('avatarPreview').textContent = val ? val.charAt(0).toUpperCase() : '?';
});
</script>

</body>
</html>