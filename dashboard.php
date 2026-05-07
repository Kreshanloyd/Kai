<?php
session_start();
if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'db.php';
include 'header.php';

$totalBooks     = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$borrowedBooks  = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='Borrowed'")->fetch_assoc()['total'];
$availableBooks = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='Available'")->fetch_assoc()['total'];
$totalUsers     = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalBorrows   = $conn->query("SELECT COUNT(*) as total FROM borrow_records")->fetch_assoc()['total'];
$recentBooks    = $conn->query("SELECT title, author, status FROM books ORDER BY id DESC LIMIT 5");
$coverBooks     = $conn->query("SELECT title, author, status FROM books LIMIT 6");
$pct            = $totalBooks > 0 ? round(($availableBooks / $totalBooks) * 100) : 0;
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

/* ── GRID SHELL ── */
.db {
  display: grid;
  grid-template-columns: 1fr 280px;
  grid-template-rows: auto auto 1fr;
  gap: 18px;
  width: 100%;
  padding-bottom: 30px;
  box-sizing: border-box;
}

/* Make main-content fill full width */
.main-content {
  box-sizing: border-box;
  width: 100%;
  min-width: 0;
}

/* ── WELCOME BANNER ── */
.db-welcome {
  grid-column: 1 / 2;
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  padding: 28px 32px;
  background: linear-gradient(120deg, #0d1f3c 0%, #0d2a1f 100%);
  border: 0.5px solid rgba(79,142,247,0.2);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.db-welcome::before {
  content: '';
  position: absolute;
  top: -60px; right: -60px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(79,142,247,0.18) 0%, transparent 70%);
  pointer-events: none;
}

.db-welcome::after {
  content: '';
  position: absolute;
  bottom: -40px; left: 30%;
  width: 160px; height: 160px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(52,211,153,0.12) 0%, transparent 70%);
  pointer-events: none;
}

.db-welcome h2 {
  font-family: 'DM Serif Display', serif;
  font-size: 1.9rem;
  font-weight: 400;
  line-height: 1.2;
  margin-bottom: 6px;
}

.db-welcome h2 em { font-style: italic; color: #4f8ef7; }
.db-welcome p { font-size: 13px; color: rgba(255,255,255,0.5); }

.db-welcome-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }

.online-pill {
  display: flex; align-items: center; gap: 6px;
  background: rgba(52,211,153,0.12);
  border: 0.5px solid rgba(52,211,153,0.3);
  border-radius: 20px; padding: 5px 13px;
  font-size: 12px; color: #34d399;
}

.online-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: #34d399;
  box-shadow: 0 0 6px #34d399;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,100% { opacity: 1; }
  50% { opacity: 0.4; }
}

.db-welcome-date {
  font-size: 11px;
  color: rgba(255,255,255,0.3);
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

/* ── STATS ROW ── */
.db-stats {
  grid-column: 1 / 2;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
}

.db-stat {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
  padding: 18px 16px 16px;
  background: rgba(255,255,255,0.03);
  border: 0.5px solid rgba(255,255,255,0.07);
  transition: transform 0.2s, border-color 0.2s;
  cursor: default;
}

.db-stat:hover {
  transform: translateY(-3px);
  border-color: rgba(255,255,255,0.14);
}

.db-stat::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 14px 14px 0 0;
}

.db-stat.s1::before { background: linear-gradient(90deg,#4f8ef7,transparent); }
.db-stat.s2::before { background: linear-gradient(90deg,#34d399,transparent); }
.db-stat.s3::before { background: linear-gradient(90deg,#f87171,transparent); }
.db-stat.s4::before { background: linear-gradient(90deg,#a78bfa,transparent); }
.db-stat.s5::before { background: linear-gradient(90deg,#fbbf24,transparent); }

.db-stat-glow {
  position: absolute;
  top: -20px; right: -20px;
  width: 80px; height: 80px;
  border-radius: 50%;
  opacity: 0.08;
  pointer-events: none;
}

.s1 .db-stat-glow { background: #4f8ef7; }
.s2 .db-stat-glow { background: #34d399; }
.s3 .db-stat-glow { background: #f87171; }
.s4 .db-stat-glow { background: #a78bfa; }
.s5 .db-stat-glow { background: #fbbf24; }

.db-stat-icon { font-size: 20px; margin-bottom: 12px; display: block; }

.db-stat-label {
  font-size: 10px; font-weight: 500;
  color: rgba(255,255,255,0.4);
  text-transform: uppercase; letter-spacing: 0.12em;
  margin-bottom: 6px;
}

.db-stat-num {
  font-family: 'DM Serif Display', serif;
  font-size: 2.2rem; font-weight: 400; line-height: 1;
  margin-bottom: 4px;
}

.s1 .db-stat-num { color: #4f8ef7; }
.s2 .db-stat-num { color: #34d399; }
.s3 .db-stat-num { color: #f87171; }
.s4 .db-stat-num { color: #a78bfa; }
.s5 .db-stat-num { color: #fbbf24; }

.db-stat-sub { font-size: 11px; color: rgba(255,255,255,0.35); }

/* ── RECENT BOOKS ── */
.db-recent { grid-column: 1 / 2; }

.db-section-head {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 14px;
}

.db-section-title {
  font-family: 'DM Serif Display', serif;
  font-size: 1.15rem; font-weight: 400;
}

.db-table-wrap {
  background: rgba(255,255,255,0.025);
  border: 0.5px solid rgba(255,255,255,0.07);
  border-radius: 14px;
  overflow: hidden;
}

/* ── RIGHT COLUMN ── */
.db-right {
  grid-column: 2 / 3;
  grid-row: 1 / 4;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.db-panel {
  background: rgba(255,255,255,0.025);
  border: 0.5px solid rgba(255,255,255,0.07);
  border-radius: 14px;
  padding: 18px;
}

.db-panel-title {
  font-size: 10px; font-weight: 500;
  color: rgba(255,255,255,0.4);
  text-transform: uppercase; letter-spacing: 0.14em;
  margin-bottom: 14px;
  display: flex; align-items: center; gap: 6px;
}

/* ── BOOK SHELF ── */
.book-shelf {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 8px;
}

.book-spine {
  border-radius: 8px;
  aspect-ratio: 2/3;
  position: relative;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.25s, box-shadow 0.25s;
}

.book-spine:hover {
  transform: translateY(-4px) scale(1.03);
  box-shadow: 0 12px 28px rgba(0,0,0,0.5);
  z-index: 2;
}

.book-spine-inner {
  width: 100%; height: 100%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 8px 6px; text-align: center;
  gap: 5px;
}

.book-spine-title {
  font-size: 9px; font-weight: 600;
  color: rgba(255,255,255,0.9);
  line-height: 1.3;
  word-break: break-word;
}

.book-spine-author { font-size: 8px; color: rgba(255,255,255,0.5); }

.book-spine-status {
  position: absolute;
  top: 5px; right: 5px;
  width: 6px; height: 6px;
  border-radius: 50%;
}

.spine-avail { background: #34d399; box-shadow: 0 0 5px #34d399; }
.spine-borrow { background: #f87171; box-shadow: 0 0 5px #f87171; }

/* ── HEALTH ── */
.health-rows { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }

.health-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 12px;
  background: rgba(255,255,255,0.03);
  border-radius: 8px;
  border: 0.5px solid rgba(255,255,255,0.05);
}

.health-label { font-size: 12px; color: rgba(255,255,255,0.5); }
.health-val { font-size: 13px; font-weight: 500; }

.prog-wrap { margin-top: 4px; }
.prog-head { display: flex; justify-content: space-between; font-size: 11px; color: rgba(255,255,255,0.35); margin-bottom: 6px; }
.prog-track { height: 4px; background: rgba(255,255,255,0.06); border-radius: 99px; overflow: hidden; }
.prog-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg,#4f8ef7,#34d399); transition: width 1s ease; }


</style>

<div class="db">

  <!-- WELCOME -->
  <div class="db-welcome">
    <div>
      <h2>Welcome back, <em><?= htmlspecialchars($_SESSION['user']) ?></em> 👋</h2>
      <p>Your library is running smoothly. Here's what's happening today.</p>
    </div>
    <div class="db-welcome-right">
      <div class="online-pill">
        <span class="online-dot"></span>
        System online
      </div>
      <div class="db-welcome-date"><?= date('l, F j Y') ?></div>
    </div>
  </div>

  <!-- STATS -->
  <div class="db-stats">
    <div class="db-stat s1">
      <div class="db-stat-glow"></div>
      <span class="db-stat-icon">📚</span>
      <div class="db-stat-label">Total Books</div>
      <div class="db-stat-num"><?= $totalBooks ?></div>
      <div class="db-stat-sub">in collection</div>
    </div>
    <div class="db-stat s2">
      <div class="db-stat-glow"></div>
      <span class="db-stat-icon">✅</span>
      <div class="db-stat-label">Available</div>
      <div class="db-stat-num"><?= $availableBooks ?></div>
      <div class="db-stat-sub">ready to borrow</div>
    </div>
    <div class="db-stat s3">
      <div class="db-stat-glow"></div>
      <span class="db-stat-icon">📤</span>
      <div class="db-stat-label">Borrowed</div>
      <div class="db-stat-num"><?= $borrowedBooks ?></div>
      <div class="db-stat-sub">currently out</div>
    </div>
    <div class="db-stat s4">
      <div class="db-stat-glow"></div>
      <span class="db-stat-icon">👥</span>
      <div class="db-stat-label">Members</div>
      <div class="db-stat-num"><?= $totalUsers ?></div>
      <div class="db-stat-sub">registered</div>
    </div>
    <div class="db-stat s5">
      <div class="db-stat-glow"></div>
      <span class="db-stat-icon">🔄</span>
      <div class="db-stat-label">Borrows</div>
      <div class="db-stat-num"><?= $totalBorrows ?></div>
      <div class="db-stat-sub">all time</div>
    </div>
  </div>

  <!-- RECENT BOOKS -->
  <div class="db-recent">
    <div class="db-section-head">
      <div class="db-section-title">Recent Books</div>
      <a href="books.php" class="btn-secondary" style="font-size:12px;padding:6px 14px;">View all →</a>
    </div>
    <div class="db-table-wrap">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $recentBooks->fetch_assoc()): ?>
          <tr>
            <td style="font-weight:500"><?= htmlspecialchars($row['title']) ?></td>
            <td style="color:rgba(255,255,255,0.4)"><?= htmlspecialchars($row['author']) ?></td>
            <td>
              <?php if($row['status'] === 'Available'): ?>
                <span class="badge available">Available</span>
              <?php else: ?>
                <span class="badge borrowed">Borrowed</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RIGHT COLUMN -->
  <div class="db-right">

    <!-- BOOK SHELF -->
    <div class="db-panel">
      <div class="db-panel-title">📖 Book Shelf</div>
      <div class="book-shelf">
        <?php
        $gradients = [
          ['135deg,#1a3a5c,#0d2035','#4f8ef7'],
          ['135deg,#1a3d2b,#0d2018','#34d399'],
          ['135deg,#3d1a1a,#200d0d','#f87171'],
          ['135deg,#2d1a3d,#190d20','#a78bfa'],
          ['135deg,#3d2e1a,#20180d','#fbbf24'],
          ['135deg,#1a2d3d,#0d1820','#38bdf8'],
        ];
        $i = 0;
        while($cb = $coverBooks->fetch_assoc()):
          $g = $gradients[$i % count($gradients)];
          $statusClass = $cb['status'] === 'Available' ? 'spine-avail' : 'spine-borrow';
        ?>
        <div class="book-spine" title="<?= htmlspecialchars($cb['title']) ?> — <?= htmlspecialchars($cb['author']) ?>">
          <div class="book-spine-inner" style="background: linear-gradient(<?= $g[0] ?>); border: 0.5px solid <?= $g[1] ?>22;">
            <div style="font-size:18px; margin-bottom:4px;">📖</div>
            <div class="book-spine-title"><?= htmlspecialchars($cb['title']) ?></div>
            <div class="book-spine-author"><?= htmlspecialchars($cb['author']) ?></div>
          </div>
          <div class="book-spine-status <?= $statusClass ?>"></div>
        </div>
        <?php $i++; endwhile; ?>
      </div>
      <div style="margin-top:12px; font-size:11px; color:rgba(255,255,255,0.3); display:flex; align-items:center; gap:12px;">
        <span style="display:flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;"></span> Available</span>
        <span style="display:flex;align-items:center;gap:4px;"><span style="width:6px;height:6px;border-radius:50%;background:#f87171;display:inline-block;"></span> Borrowed</span>
      </div>
    </div>

    <!-- LIBRARY HEALTH -->
    <div class="db-panel">
      <div class="db-panel-title">📊 Library Health</div>
      <div class="health-rows">
        <div class="health-row">
          <span class="health-label">Available</span>
          <span class="health-val" style="color:#34d399"><?= $availableBooks ?> books</span>
        </div>
        <div class="health-row">
          <span class="health-label">Borrowed</span>
          <span class="health-val" style="color:#f87171"><?= $borrowedBooks ?> books</span>
        </div>
        <div class="health-row">
          <span class="health-label">Members</span>
          <span class="health-val" style="color:#a78bfa"><?= $totalUsers ?> users</span>
        </div>
      </div>
      <div class="prog-wrap">
        <div class="prog-head">
          <span>Availability rate</span>
          <span style="color:white;font-weight:500"><?= $pct ?>%</span>
        </div>
        <div class="prog-track">
          <div class="prog-fill" style="width:<?= $pct ?>%"></div>
        </div>
      </div>
    </div>



  </div>

</div>

<?php include 'footer.php'; ?>