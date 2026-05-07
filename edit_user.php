<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
if(!isset($_GET['id'])){ header("Location: users.php"); exit(); }

$id   = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){ header("Location: users.php"); exit(); }

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $name  = trim($_POST['name']);
  $email = trim($_POST['email']);
  $stmt  = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
  $stmt->bind_param("ssi", $name, $email, $id);
  $stmt->execute();
  header("Location: users.php");
  exit();
}

include 'header.php';

$borrowCount = $conn->query("SELECT COUNT(*) as t FROM borrow_records WHERE user_name='" . $conn->real_escape_string($user['name']) . "'")->fetch_assoc()['t'];
$userBorrows = $conn->query("SELECT b.title, b.author, br.borrow_date FROM borrow_records br JOIN books b ON br.book_id=b.id WHERE br.user_name='" . $conn->real_escape_string($user['name']) . "' ORDER BY br.id DESC LIMIT 5");
$totalUsers  = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
?>

<style>
.form-shell { display: grid; grid-template-columns: 420px 1fr; gap: 24px; width: 100%; align-items: start; }
.form-left-title { font-family: 'DM Serif Display', serif; font-size: 1.9rem; font-weight: 400; margin-bottom: 5px; }
.form-left-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 22px; }

.form-card { background: rgba(255,255,255,0.025); border: 0.5px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 28px; position: relative; overflow: hidden; }
.form-card::before { content: ''; position: absolute; top: -50px; right: -50px; width: 160px; height: 160px; border-radius: 50%; background: radial-gradient(circle, rgba(52,211,153,0.07) 0%, transparent 70%); pointer-events: none; }

/* USER HERO */
.user-hero { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 20px; margin-bottom: 22px; background: rgba(255,255,255,0.03); border: 0.5px solid rgba(255,255,255,0.06); border-radius: 12px; }
.user-hero-av { width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #1a3d2b, #34d399); display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: white; border: 2px solid rgba(52,211,153,0.3); font-family: 'DM Sans', sans-serif; }
.user-hero-name { font-size: 16px; font-weight: 600; }
.user-hero-id { font-size: 11px; color: rgba(255,255,255,0.35); }

.form-field { margin-bottom: 20px; }
.form-field label { display: block; font-size: 10.5px; font-weight: 500; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
.form-field input { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 13.5px; outline: none; transition: border-color 0.15s, box-shadow 0.15s; }
.form-field input:focus { border-color: rgba(52,211,153,0.5); box-shadow: 0 0 0 3px rgba(52,211,153,0.08); }

.form-actions { display: flex; gap: 10px; margin-top: 26px; }
.btn-save { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: linear-gradient(135deg, #34d399, #4f8ef7); color: white; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity 0.2s, transform 0.15s; }
.btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-cancel { padding: 12px 18px; background: transparent; color: rgba(255,255,255,0.4); border: 0.5px solid rgba(255,255,255,0.08); border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; text-decoration: none; display: flex; align-items: center; transition: all 0.15s; }
.btn-cancel:hover { color: var(--text); border-color: rgba(255,255,255,0.2); }

/* RIGHT */
.right-col { display: flex; flex-direction: column; gap: 16px; }
.panel { background: rgba(255,255,255,0.025); border: 0.5px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 18px; }
.panel-label { font-size: 10px; font-weight: 500; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 14px; }

.ustat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 0; }
.ustat { padding: 14px; background: rgba(255,255,255,0.03); border: 0.5px solid rgba(255,255,255,0.06); border-radius: 10px; }
.ustat-num { font-family: 'DM Serif Display', serif; font-size: 1.8rem; line-height: 1; margin-bottom: 3px; }
.ustat-label { font-size: 11px; color: rgba(255,255,255,0.4); }

.borrow-hist { display: flex; flex-direction: column; gap: 8px; }
.bh-row { display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 0.5px solid rgba(255,255,255,0.04); }
.bh-icon { width: 32px; height: 40px; border-radius: 5px; background: #1a3a5c; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
.bh-info { flex: 1; min-width: 0; }
.bh-title { font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bh-author { font-size: 11px; color: rgba(255,255,255,0.35); }
.bh-date { font-size: 11px; color: rgba(255,255,255,0.25); white-space: nowrap; }

.empty-hist { text-align: center; padding: 20px; font-size: 12px; color: rgba(255,255,255,0.3); }
</style>

<div class="form-shell">

  <!-- LEFT: FORM -->
  <div>
    <div class="form-left-title">Edit User</div>
    <div class="form-left-sub">Update member details</div>

    <div class="form-card">

      <!-- USER HERO -->
      <div class="user-hero">
        <div class="user-hero-av"><?= strtoupper(substr($user['name'],0,1)) ?></div>
        <div class="user-hero-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="user-hero-id">Member #<?= $user['id'] ?></div>
      </div>

      <form method="POST">

        <div class="form-field">
          <label>Full Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-field">
          <label>Email Address</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-actions">
          <a href="users.php" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">✏️ Update User</button>
        </div>

      </form>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right-col">

    <!-- USER STATS -->
    <div class="panel">
      <div class="panel-label">📊 Member Stats</div>
      <div class="ustat-grid">
        <div class="ustat">
          <div class="ustat-num" style="color:#34d399"><?= $borrowCount ?></div>
          <div class="ustat-label">Total borrows</div>
        </div>
        <div class="ustat">
          <div class="ustat-num" style="color:#4f8ef7">#<?= $user['id'] ?></div>
          <div class="ustat-label">Member ID</div>
        </div>
      </div>
    </div>

    <!-- BORROW HISTORY -->
    <div class="panel">
      <div class="panel-label">📖 Borrow History</div>
      <div class="borrow-hist">
        <?php if($userBorrows && $userBorrows->num_rows > 0):
          while($bh = $userBorrows->fetch_assoc()): ?>
        <div class="bh-row">
          <div class="bh-icon">📖</div>
          <div class="bh-info">
            <div class="bh-title"><?= htmlspecialchars($bh['title']) ?></div>
            <div class="bh-author"><?= htmlspecialchars($bh['author']) ?></div>
          </div>
          <div class="bh-date"><?= date('M j, Y', strtotime($bh['borrow_date'])) ?></div>
        </div>
        <?php endwhile; else: ?>
        <div class="empty-hist">No borrow history for this member</div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>