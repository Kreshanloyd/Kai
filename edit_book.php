<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
if(!isset($_GET['id'])){ header("Location: books.php"); exit(); }

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if(!$current){ header("Location: books.php"); exit(); }

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $title  = trim($_POST['title']);
  $author = trim($_POST['author']);
  $status = $_POST['status'];
  $stmt   = $conn->prepare("UPDATE books SET title=?, author=?, status=? WHERE id=?");
  $stmt->bind_param("sssi", $title, $author, $status, $id);
  $stmt->execute();
  header("Location: books.php");
  exit();
}

include 'header.php';

$totalBooks  = $conn->query("SELECT COUNT(*) as t FROM books")->fetch_assoc()['t'];
$borrowTimes = $conn->query("SELECT COUNT(*) as t FROM borrow_records WHERE book_id=$id")->fetch_assoc()['t'];
$otherBooks  = $conn->query("SELECT title, author, status FROM books WHERE id != $id ORDER BY id DESC LIMIT 5");
?>

<style>
.form-shell { display: grid; grid-template-columns: 420px 1fr; gap: 24px; width: 100%; align-items: start; }
.form-left-title { font-family: 'DM Serif Display', serif; font-size: 1.9rem; font-weight: 400; margin-bottom: 5px; }
.form-left-sub { font-size: 13px; color: var(--text-muted); margin-bottom: 22px; }

.form-card { background: rgba(255,255,255,0.025); border: 0.5px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 28px; position: relative; overflow: hidden; }
.form-card::before { content: ''; position: absolute; top: -50px; right: -50px; width: 160px; height: 160px; border-radius: 50%; background: radial-gradient(circle, rgba(251,191,36,0.07) 0%, transparent 70%); pointer-events: none; }

/* BOOK PREVIEW */
.book-preview {
  display: flex; align-items: center; gap: 14px;
  padding: 14px; margin-bottom: 22px;
  background: rgba(255,255,255,0.03);
  border: 0.5px solid rgba(255,255,255,0.06);
  border-radius: 12px;
}
.book-preview-cover { width: 42px; height: 56px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: #1a3a5c; flex-shrink: 0; }
.book-preview-id { font-size: 11px; color: rgba(255,255,255,0.3); margin-bottom: 2px; }
.book-preview-title { font-size: 14px; font-weight: 500; }
.book-preview-author { font-size: 12px; color: rgba(255,255,255,0.4); }

.form-field { margin-bottom: 20px; }
.form-field label { display: block; font-size: 10.5px; font-weight: 500; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 8px; }
.form-field input, .form-field select { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.05); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 13.5px; outline: none; transition: border-color 0.15s, box-shadow 0.15s; -webkit-appearance: none; }
.form-field input:focus, .form-field select:focus { border-color: rgba(251,191,36,0.5); box-shadow: 0 0 0 3px rgba(251,191,36,0.08); }
.form-field input::placeholder { color: rgba(255,255,255,0.25); }
.form-field select option { background: #152032; }

.status-toggle { display: flex; gap: 8px; }
.status-opt { flex: 1; padding: 10px; border: 0.5px solid rgba(255,255,255,0.1); border-radius: 10px; text-align: center; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.03); }
.status-opt.active-avail { background: rgba(52,211,153,0.12); border-color: rgba(52,211,153,0.3); color: #34d399; }
.status-opt.active-borrow { background: rgba(248,113,113,0.12); border-color: rgba(248,113,113,0.3); color: #f87171; }

.form-actions { display: flex; gap: 10px; margin-top: 26px; }
.btn-save { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: linear-gradient(135deg, #fbbf24, #f87171); color: white; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; cursor: pointer; transition: opacity 0.2s, transform 0.15s; }
.btn-save:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-cancel { padding: 12px 18px; background: transparent; color: rgba(255,255,255,0.4); border: 0.5px solid rgba(255,255,255,0.08); border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; text-decoration: none; display: flex; align-items: center; transition: all 0.15s; }
.btn-cancel:hover { color: var(--text); border-color: rgba(255,255,255,0.2); }

/* RIGHT */
.right-col { display: flex; flex-direction: column; gap: 16px; }
.panel { background: rgba(255,255,255,0.025); border: 0.5px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 18px; }
.panel-label { font-size: 10px; font-weight: 500; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 0.14em; margin-bottom: 14px; }

.book-stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.bstat { padding: 14px; background: rgba(255,255,255,0.03); border: 0.5px solid rgba(255,255,255,0.06); border-radius: 10px; }
.bstat-num { font-family: 'DM Serif Display', serif; font-size: 1.8rem; line-height: 1; margin-bottom: 3px; }
.bstat-label { font-size: 11px; color: rgba(255,255,255,0.4); }

.recent-list { display: flex; flex-direction: column; gap: 8px; }
.recent-row { display: flex; align-items: center; gap: 10px; padding: 9px 10px; background: rgba(255,255,255,0.03); border-radius: 8px; border: 0.5px solid rgba(255,255,255,0.04); }
.recent-icon { width: 28px; height: 36px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
.recent-info { flex: 1; min-width: 0; }
.recent-title { font-size: 12.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.recent-author { font-size: 11px; color: rgba(255,255,255,0.35); }
.badge-sm { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 20px; font-size: 10.5px; font-weight: 500; }
.badge-sm.available { background: rgba(52,211,153,0.1); color: #34d399; border: 0.5px solid rgba(52,211,153,0.2); }
.badge-sm.borrowed { background: rgba(248,113,113,0.1); color: #f87171; border: 0.5px solid rgba(248,113,113,0.2); }
</style>

<div class="form-shell">

  <!-- LEFT: FORM -->
  <div>
    <div class="form-left-title">Edit Book</div>
    <div class="form-left-sub">Update the details for this book</div>

    <div class="form-card">

      <!-- CURRENT BOOK PREVIEW -->
      <div class="book-preview">
        <div class="book-preview-cover">📖</div>
        <div>
          <div class="book-preview-id">ID #<?= $current['id'] ?></div>
          <div class="book-preview-title"><?= htmlspecialchars($current['title']) ?></div>
          <div class="book-preview-author"><?= htmlspecialchars($current['author']) ?></div>
        </div>
      </div>

      <form method="POST">

        <div class="form-field">
          <label>Book Title</label>
          <input type="text" name="title" value="<?= htmlspecialchars($current['title']) ?>" required>
        </div>

        <div class="form-field">
          <label>Author Name</label>
          <input type="text" name="author" value="<?= htmlspecialchars($current['author']) ?>" required>
        </div>

        <div class="form-field">
          <label>Availability Status</label>
          <div class="status-toggle">
            <div class="status-opt <?= $current['status']==='Available' ? 'active-avail' : '' ?>" id="opt-avail" onclick="setStatus('Available')">✅ Available</div>
            <div class="status-opt <?= $current['status']==='Borrowed' ? 'active-borrow' : '' ?>" id="opt-borrow" onclick="setStatus('Borrowed')">📤 Borrowed</div>
          </div>
          <input type="hidden" name="status" id="statusInput" value="<?= $current['status'] ?>">
        </div>

        <div class="form-actions">
          <a href="books.php" class="btn-cancel">Cancel</a>
          <button type="submit" class="btn-save">✏️ Update Book</button>
        </div>

      </form>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right-col">

    <!-- BOOK STATS -->
    <div class="panel">
      <div class="panel-label">📊 Book Stats</div>
      <div class="book-stats-grid">
        <div class="bstat">
          <div class="bstat-num" style="color:#fbbf24"><?= $borrowTimes ?></div>
          <div class="bstat-label">Times borrowed</div>
        </div>
        <div class="bstat">
          <div class="bstat-num" style="color:<?= $current['status']==='Available' ? '#34d399' : '#f87171' ?>"><?= $current['status'] === 'Available' ? '✓' : '✗' ?></div>
          <div class="bstat-label">Currently <?= strtolower($current['status']) ?></div>
        </div>
        <div class="bstat" style="grid-column: 1/-1;">
          <div class="bstat-num" style="color:#4f8ef7"><?= $totalBooks ?></div>
          <div class="bstat-label">Total books in library</div>
        </div>
      </div>
    </div>

    <!-- OTHER BOOKS -->
    <div class="panel">
      <div class="panel-label">📚 Other Books</div>
      <div class="recent-list">
        <?php
        $colors = ['#1a3a5c','#1a3d2b','#3d1a1a','#2d1a3d','#3d2e1a'];
        $i = 0;
        while($ob = $otherBooks->fetch_assoc()):
          $c = $colors[$i % count($colors)];
          $i++;
        ?>
        <div class="recent-row">
          <div class="recent-icon" style="background:<?= $c ?>">📖</div>
          <div class="recent-info">
            <div class="recent-title"><?= htmlspecialchars($ob['title']) ?></div>
            <div class="recent-author"><?= htmlspecialchars($ob['author']) ?></div>
          </div>
          <span class="badge-sm <?= $ob['status']==='Available' ? 'available' : 'borrowed' ?>"><?= $ob['status'] ?></span>
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