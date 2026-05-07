<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);

  if ($stmt->execute()) {
    header("Location: users.php");
    exit();
  } else {
    $error = "Failed to add user. Email may already be in use.";
  }
}

$totalUsers  = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$allUsers    = $conn->query("SELECT id, name FROM users ORDER BY id DESC LIMIT 8");
?>

<style>
.form-shell {
  display: grid;
  grid-template-columns: 420px 1fr;
  gap: 24px; width: 100%; align-items: start;
}

.form-left-title { font-family: 'DM Serif Display', serif; font-size: 1.9rem; font-weight: 400; margin-bottom: 5px; }
.form-left-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 22px; }

.form-card {
  background: rgba(255,255,255,0.025);
  border: 0.5px solid rgba(255,255,255,0.08);
  border-radius: 16px; padding: 28px;
  position: relative; overflow: hidden;
}

.form-card::before {
  content: '';
  position: absolute; top: -50px; right: -50px;
  width: 160px; height: 160px; border-radius: 50%;
  background: radial-gradient(circle, rgba(167,139,250,0.07) 0%, transparent 70%);
  pointer-events: none;
}

/* AVATAR PREVIEW */
.avatar-preview-wrap { display: flex; justify-content: center; margin-bottom: 24px; }
.avatar-preview {
  width: 72px; height: 72px; border-radius: 50%;
  background: linear-gradient(135deg, #1a3a5c, #4f8ef7);
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 700; color: white;
  border: 2px solid rgba(79,142,247,0.3);
  transition: all 0.2s;
  font-family: 'DM Sans', sans-serif;
}

.form-field { margin-bottom: 20px; }
.form-field label { display: block; font-size: 10.5px; font-weight: 500; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
.form-field input { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 13.5px; outline: none; transition: border-color 0.15s, box-shadow 0.15s; }
.form-field input:focus { border-color: rgba(167,139,250,0.5); box-shadow: 0 0 0 3px rgba(167,139,250,0.08); }
.form-field input::placeholder { color: rgba(255,255,255,0.25); }

.form-actions { display: flex; gap: 10px; margin-top: 26px; }

.btn-save {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 12px; background: linear-gradient(135deg, #a78bfa, #4f8ef7);
  color: white; border: none; border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: opacity 0.2s, transform 0.15s;
}
.btn-save:hover { opacity: 0.9; transform: translateY(-1px); }

.btn-cancel { padding: 12px 18px; background: transparent; color: rgba(255,255,255,0.4); border: 0.5px solid rgba(255,255,255,0.08); border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; text-decoration: none; display: flex; align-items: center; transition: all 0.15s; }
.btn-cancel:hover { color: var(--text); border-color: rgba(255,255,255,0.2); }

.alert-error { padding: 12px 16px; border-radius: 10px; background: rgba(248,113,113,0.1); color: #f87171; border: 0.5px solid rgba(248,113,113,0.2); font-size: 13px; margin-bottom: 18px; }

/* RIGHT */
.right-col { display: flex; flex-direction: column; gap: 16px; }
.panel { background: rgba(255,255,255,0.025); border: 0.5px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 18px; }
.panel-label { font-size: 10px; font-weight: 500; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 14px; }

/* MEMBER COUNTER */
.member-counter {
  display: flex; align-items: center; gap: 16px; padding: 16px;
  background: linear-gradient(135deg, rgba(167,139,250,0.1), rgba(79,142,247,0.07));
  border: 0.5px solid rgba(167,139,250,0.2); border-radius: 12px;
}
.counter-num { font-family: 'DM Serif Display', serif; font-size: 2.5rem; color: #a78bfa; line-height: 1; }
.counter-label { font-size: 13px; color: rgba(255,255,255,0.5); }
.counter-sub { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 2px; }

/* MEMBER GRID */
.member-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }

.member-chip {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px;
  background: rgba(255,255,255,0.03);
  border: 0.5px solid rgba(255,255,255,0.06);
  border-radius: 10px; transition: all 0.15s;
}
.member-chip:hover { background: rgba(255,255,255,0.07); }

.mc-av { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: white; flex-shrink: 0; }
.mc-name { font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mc-id { font-size: 10px; color: rgba(255,255,255,0.3); }

/* TIPS */
.tip-list { display: flex; flex-direction: column; gap: 10px; }
.tip-row { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 0.5px solid rgba(255,255,255,0.05); }
.tip-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.tip-text { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.5; }
.tip-text strong { color: rgba(255,255,255,0.8); }
</style>

<div class="form-shell">

  <!-- LEFT: FORM -->
  <div>
    <div class="form-left-title">Add New User</div>
    <div class="form-left-sub">Register a new member to the library system</div>

    <div class="form-card">

      <?php if($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- LIVE AVATAR PREVIEW -->
      <div class="avatar-preview-wrap">
        <div class="avatar-preview" id="avatarPreview">?</div>
      </div>

      <form method="POST">

        <div class="form-field">
          <label>Full Name</label>
          <input type="text" name="name" id="nameInput" placeholder="e.g. John Doe" required>
        </div>

        <div class="form-field">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="e.g. john@example.com" required>
        </div>

        <div class="form-field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Min. 8 characters" required>
        </div>

        <div class="form-actions">
          <a href="users.php" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">👤 Save User</button>
        </div>

      </form>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-col">

    <!-- MEMBER COUNTER -->
    <div class="panel">
      <div class="panel-label">👥 Current Members</div>
      <div class="member-counter">
        <div><div class="counter-num"><?= $totalUsers ?></div></div>
        <div>
          <div class="counter-label">Registered members</div>
          <div class="counter-sub">Adding 1 more after save</div>
        </div>
      </div>
    </div>

    <!-- EXISTING MEMBERS -->
    <div class="panel">
      <div class="panel-label">👤 Existing Members</div>
      <div class="member-grid">
        <?php
        $bgColors = ['#1a3a5c','#1a3d2b','#3d1a1a','#2d1a3d','#3d2e1a','#1a2d3d','#2a1a3d','#1a2a2d'];
        $acColors = ['#4f8ef7','#34d399','#f87171','#a78bfa','#fbbf24','#38bdf8','#f472b6','#4ade80'];
        $i = 0;
        while($u = $allUsers->fetch_assoc()):
          $bg = $bgColors[$i % count($bgColors)];
          $ac = $acColors[$i % count($acColors)];
          $i++;
        ?>
        <div class="member-chip">
          <div class="mc-av" style="background:<?= $bg ?>;border:1.5px solid <?= $ac ?>44">
            <?= strtoupper(substr($u['name'],0,1)) ?>
          </div>
          <div>
            <div class="mc-name"><?= htmlspecialchars($u['name']) ?></div>
            <div class="mc-id">#<?= $u['id'] ?></div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <!-- TIPS -->
    <div class="panel">
      <div class="panel-label">💡 Registration Tips</div>
      <div class="tip-list">
        <div class="tip-row">
          <div class="tip-icon">📧</div>
          <div class="tip-text"><strong>Unique email required</strong> — each member must have a different email address.</div>
        </div>
        <div class="tip-row">
          <div class="tip-icon">🔒</div>
          <div class="tip-text"><strong>Passwords are hashed</strong> — stored securely, never in plain text.</div>
        </div>
        <div class="tip-row">
          <div class="tip-icon">✏️</div>
          <div class="tip-text"><strong>Editable later</strong> — name and email can be changed from the Users page.</div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
// Live avatar preview
document.getElementById('nameInput').addEventListener('input', function() {
  const val = this.value.trim();
  const av = document.getElementById('avatarPreview');
  av.textContent = val ? val.charAt(0).toUpperCase() : '?';
});
</script>

<?php include 'footer.php'; ?>