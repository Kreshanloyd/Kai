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
  $title  = trim($_POST['title']);
  $author = trim($_POST['author']);
  $status = $_POST['status'];

  if ($title == "" || $author == "") {
    $error = "All fields are required.";
  } else {
    $stmt = $conn->prepare("INSERT INTO books (title, author, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $author, $status);
    if ($stmt->execute()) {
      header("Location: books.php");
      exit();
    } else {
      $error = "Failed to save book.";
    }
  }
}

$totalBooks = $conn->query("SELECT COUNT(*) as t FROM books")->fetch_assoc()['t'];
$recentBooks = $conn->query("SELECT title, author, status FROM books ORDER BY id DESC LIMIT 6");
?>

<style>
.form-shell {
  display: grid;
  grid-template-columns: 420px 1fr;
  gap: 24px;
  width: 100%;
  align-items: start;
}

.form-left-title {
  font-family: 'DM Serif Display', serif;
  font-size: 1.9rem; font-weight: 400; margin-bottom: 5px;
}

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
  background: radial-gradient(circle, rgba(79,142,247,0.07) 0%, transparent 70%);
  pointer-events: none;
}

.form-field { margin-bottom: 20px; }

.form-field label {
  display: block; font-size: 10.5px; font-weight: 500;
  color: rgba(255,255,255,0.4); text-transform: uppercase;
  letter-spacing: 0.12em; margin-bottom: 8px;
}

.form-field input, .form-field select {
  width: 100%; padding: 11px 14px;
  background: rgba(255,255,255,0.05);
  border: 0.5px solid rgba(255,255,255,0.1);
  border-radius: 10px; color: var(--text);
  font-family: 'DM Sans', sans-serif; font-size: 13.5px;
  outline: none; transition: border-color 0.15s, box-shadow 0.15s;
  -webkit-appearance: none;
}

.form-field input:focus, .form-field select:focus {
  border-color: rgba(79,142,247,0.5);
  box-shadow: 0 0 0 3px rgba(79,142,247,0.08);
}

.form-field input::placeholder { color: rgba(255,255,255,0.25); }
.form-field select option { background: #152032; }

/* STATUS TOGGLE */
.status-toggle { display: flex; gap: 8px; }

.status-opt {
  flex: 1; padding: 10px;
  border: 0.5px solid rgba(255,255,255,0.1);
  border-radius: 10px; text-align: center;
  font-size: 13px; font-weight: 500;
  cursor: pointer; transition: all 0.15s;
  color: rgba(255,255,255,0.4);
  background: rgba(255,255,255,0.03);
}

.status-opt.active-avail {
  background: rgba(52,211,153,0.12);
  border-color: rgba(52,211,153,0.3);
  color: #34d399;
}

.status-opt.active-borrow {
  background: rgba(248,113,113,0.12);
  border-color: rgba(248,113,113,0.3);
  color: #f87171;
}

.form-actions { display: flex; gap: 10px; margin-top: 26px; }

.btn-save {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 12px; background: linear-gradient(135deg, #4f8ef7, #34d399);
  color: white; border: none; border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: opacity 0.2s, transform 0.15s;
}
.btn-save:hover { opacity: 0.9; transform: translateY(-1px); }

.btn-cancel {
  padding: 12px 18px; background: transparent;
  color: rgba(255,255,255,0.4);
  border: 0.5px solid rgba(255,255,255,0.08);
  border-radius: 10px; font-family: 'DM Sans', sans-serif;
  font-size: 13px; cursor: pointer; text-decoration: none;
  display: flex; align-items: center; transition: all 0.15s;
}
.btn-cancel:hover { color: var(--text); border-color: rgba(255,255,255,0.2); }

.alert-error {
  padding: 12px 16px; border-radius: 10px;
  background: rgba(248,113,113,0.1); color: #f87171;
  border: 0.5px solid rgba(248,113,113,0.2);
  font-size: 13px; margin-bottom: 18px;
}

/* RIGHT PANEL */
.right-col { display: flex; flex-direction: column; gap: 16px; }

.panel {
  background: rgba(255,255,255,0.025);
  border: 0.5px solid rgba(255,255,255,0.07);
  border-radius: 14px; padding: 18px;
}

.panel-label {
  font-size: 10px; font-weight: 500;
  color: rgba(255,255,255,0.35);
  text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 14px;
}

/* TIPS */
.tip-list { display: flex; flex-direction: column; gap: 10px; }

.tip-row {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 10px 12px;
  background: rgba(255,255,255,0.03);
  border-radius: 8px; border: 0.5px solid rgba(255,255,255,0.05);
}

.tip-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.tip-text { font-size: 12px; color: rgba(255,255,255,0.55); line-height: 1.5; }
.tip-text strong { color: rgba(255,255,255,0.8); }

/* RECENT BOOKS */
.recent-list { display: flex; flex-direction: column; gap: 8px; }

.recent-row {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 10px;
  background: rgba(255,255,255,0.03);
  border-radius: 8px; border: 0.5px solid rgba(255,255,255,0.04);
  transition: background 0.12s;
}
.recent-row:hover { background: rgba(255,255,255,0.06); }

.recent-icon { width: 28px; height: 36px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
.recent-info { flex: 1; min-width: 0; }
.recent-title { font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-author { font-size: 11px; color: rgba(255,255,255,0.35); }

.badge-sm { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 10.5px; font-weight: 500; }
.badge-sm.available { background: rgba(52,211,153,0.1); color: #34d399; border: 0.5px solid rgba(52,211,153,0.2); }
.badge-sm.borrowed { background: rgba(248,113,113,0.1); color: #f87171; border: 0.5px solid rgba(248,113,113,0.2); }

/* COUNTER */
.counter-card {
  display: flex; align-items: center; gap: 16px;
  padding: 16px;
  background: linear-gradient(135deg, rgba(79,142,247,0.1), rgba(52,211,153,0.07));
  border: 0.5px solid rgba(79,142,247,0.2);
  border-radius: 12px;
}
.counter-num { font-family: 'DM Serif Display', serif; font-size: 2.5rem; color: #4f8ef7; line-height: 1; }
.counter-label { font-size: 13px; color: rgba(255,255,255,0.5); }
.counter-sub { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 2px; }
</style>

<div class="form-shell">

  <!-- LEFT: FORM -->
  <div>
    <div class="form-left-title">Add New Book</div>
    <div class="form-left-sub">Fill in the details to add a book to your collection</div>

    <div class="form-card">

      <?php if($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" id="bookForm">

        <div class="form-field">
          <label>Book Title</label>
          <input type="text" name="title" placeholder="e.g. The Great Gatsby" required>
        </div>

        <div class="form-field">
          <label>Author Name</label>
          <input type="text" name="author" placeholder="e.g. F. Scott Fitzgerald" required>
        </div>

        <div class="form-field">
          <label>Availability Status</label>
          <div class="status-toggle">
            <div class="status-opt active-avail" id="opt-avail" onclick="setStatus('Available')">✅ Available</div>
            <div class="status-opt" id="opt-borrow" onclick="setStatus('Borrowed')">📤 Borrowed</div>
          </div>
          <input type="hidden" name="status" id="statusInput" value="Available">
        </div>

        <div class="form-actions">
          <a href="books.php" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">📚 Save Book</button>
        </div>

      </form>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-col">

    <!-- COLLECTION COUNTER -->
    <div class="panel">
      <div class="panel-label">📊 Current Collection</div>
      <div class="counter-card">
        <div>
          <div class="counter-num"><?= $totalBooks ?></div>
        </div>
        <div>
          <div class="counter-label">Books in library</div>
          <div class="counter-sub">Adding 1 more after save</div>
        </div>
      </div>
    </div>

    <!-- TIPS -->
    <div class="panel">
      <div class="panel-label">💡 Tips for Adding Books</div>
      <div class="tip-list">
        <div class="tip-row">
          <div class="tip-icon">📝</div>
          <div class="tip-text"><strong>Use full titles</strong> — avoid abbreviations so members can easily search for books.</div>
        </div>
        <div class="tip-row">
          <div class="tip-icon">👤</div>
          <div class="tip-text"><strong>Full author name</strong> — include first and last name for better searchability.</div>
        </div>
        <div class="tip-row">
          <div class="tip-icon">✅</div>
          <div class="tip-text"><strong>Set correct status</strong> — mark as Borrowed only if the book is already checked out.</div>
        </div>
      </div>
    </div>

    <!-- RECENTLY ADDED -->
    <div class="panel">
      <div class="panel-label">🕒 Recently Added</div>
      <div class="recent-list">
        <?php
        $colors = ['#1a3a5c','#1a3d2b','#3d1a1a','#2d1a3d','#3d2e1a','#1a2d3d'];
        $i = 0;
        while($rb = $recentBooks->fetch_assoc()):
          $c = $colors[$i % count($colors)];
          $i++;
        ?>
        <div class="recent-row">
          <div class="recent-icon" style="background:<?= $c ?>">📖</div>
          <div class="recent-info">
            <div class="recent-title"><?= htmlspecialchars($rb['title']) ?></div>
            <div class="recent-author"><?= htmlspecialchars($rb['author']) ?></div>
          </div>
          <span class="badge-sm <?= $rb['status'] === 'Available' ? 'available' : 'borrowed' ?>">
            <?= $rb['status'] ?>
          </span>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

  </div>
</div>

<script>
function setStatus(val) {
  document.getElementById('statusInput').value = val;
  document.getElementById('opt-avail').className = 'status-opt' + (val === 'Available' ? ' active-avail' : '');
  document.getElementById('opt-borrow').className = 'status-opt' + (val === 'Borrowed' ? ' active-borrow' : '');
}
</script>

<?php include 'footer.php'; ?>