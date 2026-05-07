<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
include 'header.php';

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $user    = trim($_POST['user']);
  $book_id = intval($_POST['book_id']);
  $date    = $_POST['date'];
  $stmt = $conn->prepare("SELECT status,title,author FROM books WHERE id=?");
  $stmt->bind_param("i",$book_id); $stmt->execute();
  $book = $stmt->get_result()->fetch_assoc();
  if($book['status']=="Borrowed"){
    $error = "This book is already borrowed.";
  } else {
    $stmt = $conn->prepare("INSERT INTO borrow_records (user_name,book_id,borrow_date) VALUES (?,?,?)");
    $stmt->bind_param("sis",$user,$book_id,$date); $stmt->execute();
    $conn->query("UPDATE books SET status='Borrowed' WHERE id=$book_id");
    $success = "\"".htmlspecialchars($book['title'])."\" successfully borrowed!";
  }
}

$borrowedCount = $conn->query("SELECT COUNT(*) as t FROM books WHERE status='Borrowed'")->fetch_assoc()['t'];
$availableCount= $conn->query("SELECT COUNT(*) as t FROM books WHERE status='Available'")->fetch_assoc()['t'];
$totalBorrows  = $conn->query("SELECT COUNT(*) as t FROM borrow_records")->fetch_assoc()['t'];
$totalMembers  = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$availableBooks= $conn->query("SELECT * FROM books WHERE status='Available'");
$recentBorrows = $conn->query("SELECT br.user_name,b.title,b.author,br.borrow_date FROM borrow_records br JOIN books b ON br.book_id=b.id ORDER BY br.id DESC LIMIT 5");
$topBorrower   = $conn->query("SELECT user_name,COUNT(*) as cnt FROM borrow_records GROUP BY user_name ORDER BY cnt DESC LIMIT 1")->fetch_assoc();
$mostBorrowedBook = $conn->query("SELECT b.title,COUNT(*) as cnt FROM borrow_records br JOIN books b ON br.book_id=b.id GROUP BY br.book_id ORDER BY cnt DESC LIMIT 1")->fetch_assoc();
?>
<style>
.borrow-shell{display:grid;grid-template-columns:1fr 1fr;gap:24px;width:100%;align-items:stretch;}
.borrow-left{display:flex;flex-direction:column;}
.borrow-right{display:flex;flex-direction:column;gap:13px;}

.borrow-title{font-family:'DM Serif Display',serif;font-size:1.9rem;font-weight:400;margin-bottom:5px;}
.borrow-sub{font-size:13px;color:var(--text-muted);margin-bottom:20px;}

.borrow-card{background:rgba(255,255,255,0.025);border:0.5px solid rgba(255,255,255,0.08);border-radius:16px;padding:26px;position:relative;overflow:hidden;flex:1;}
.borrow-card::before{content:'';position:absolute;top:-50px;right:-50px;width:160px;height:160px;border-radius:50%;background:radial-gradient(circle,rgba(79,142,247,0.07) 0%,transparent 70%);pointer-events:none;}

.borrow-alert{padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:10px;border:0.5px solid transparent;}
.borrow-alert.error{background:rgba(248,113,113,0.1);color:#f87171;border-color:rgba(248,113,113,0.2);}
.borrow-alert.success{background:rgba(52,211,153,0.1);color:#34d399;border-color:rgba(52,211,153,0.2);}

.form-field{margin-bottom:18px;}
.form-field label{display:block;font-size:10.5px;font-weight:500;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px;}
.form-field input,.form-field select{width:100%;padding:11px 14px;background:rgba(255,255,255,0.05);border:0.5px solid rgba(255,255,255,0.1);border-radius:10px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:13.5px;outline:none;transition:border-color 0.15s,box-shadow 0.15s;-webkit-appearance:none;}
.form-field input:focus,.form-field select:focus{border-color:rgba(79,142,247,0.5);box-shadow:0 0 0 3px rgba(79,142,247,0.08);}
.form-field input::placeholder{color:rgba(255,255,255,0.25);}
.form-field select option{background:#152032;}

.btn-confirm{width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:linear-gradient(135deg,#4f8ef7,#34d399);color:white;border:none;border-radius:10px;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:500;cursor:pointer;transition:opacity 0.2s,transform 0.15s;margin-top:20px;}
.btn-confirm:hover{opacity:0.9;transform:translateY(-1px);}
.btn-cancel-wrap{text-align:center;margin-top:10px;}
.btn-cancel-link{font-size:13px;color:rgba(255,255,255,0.35);text-decoration:none;transition:color 0.15s;}
.btn-cancel-link:hover{color:rgba(255,255,255,0.6);}

/* RIGHT PANELS */
.panel{background:rgba(255,255,255,0.025);border:0.5px solid rgba(255,255,255,0.07);border-radius:14px;padding:16px;}
.panel-label{font-size:10px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:12px;}

.borrow-stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.bstat{padding:13px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.06);border-radius:10px;}
.bstat-num{font-family:'DM Serif Display',serif;font-size:1.7rem;line-height:1;margin-bottom:3px;}
.bstat-label{font-size:11px;color:rgba(255,255,255,0.4);}

/* HIGHLIGHTS */
.highlight-card{padding:12px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.06);border-radius:10px;display:flex;align-items:center;gap:11px;margin-bottom:8px;}
.highlight-card:last-child{margin-bottom:0;}
.hl-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.hl-label{font-size:11px;color:rgba(255,255,255,0.4);margin-bottom:2px;}
.hl-value{font-size:13px;font-weight:500;}

/* RECENT */
.activity-list{display:flex;flex-direction:column;}
.act-row{display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:0.5px solid rgba(255,255,255,0.04);}
.act-row:last-child{border-bottom:none;padding-bottom:0;}
.act-dot-wrap{display:flex;flex-direction:column;align-items:center;padding-top:4px;}
.act-dot{width:6px;height:6px;border-radius:50%;background:#4f8ef7;flex-shrink:0;}
.act-line{width:1px;flex:1;background:rgba(255,255,255,0.06);margin-top:3px;min-height:18px;}
.act-row:last-child .act-line{display:none;}
.act-info{flex:1;min-width:0;}
.act-title{font-size:12.5px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.act-meta{font-size:11px;color:rgba(255,255,255,0.4);margin-top:1px;}
.act-date{font-size:11px;color:rgba(255,255,255,0.25);white-space:nowrap;}

/* AVAIL BOOKS */
.avail-list{display:flex;flex-direction:column;gap:6px;}
.avail-row{display:flex;align-items:center;gap:9px;padding:8px 10px;background:rgba(52,211,153,0.05);border:0.5px solid rgba(52,211,153,0.1);border-radius:8px;}
.avail-dot{width:6px;height:6px;border-radius:50%;background:#34d399;flex-shrink:0;}
.avail-title{font-size:12px;font-weight:500;flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.avail-author{font-size:11px;color:rgba(255,255,255,0.35);}

.filler-panel{flex:1;display:flex;flex-direction:column;}
</style>

<div class="borrow-shell">

  <!-- LEFT: FORM -->
  <div class="borrow-left">
    <div class="borrow-title">Borrow a Book</div>
    <div class="borrow-sub">Fill in the details to record a new borrow</div>

    <div class="borrow-card">
      <?php if($error): ?><div class="borrow-alert error">⚠️ <?=htmlspecialchars($error)?></div><?php endif; ?>
      <?php if($success): ?><div class="borrow-alert success">✅ <?=$success?></div><?php endif; ?>

      <form method="POST">
        <div class="form-field">
          <label>Borrower Name</label>
          <input type="text" name="user" placeholder="Enter the member's name" required>
        </div>
        <div class="form-field">
          <label>Select Book</label>
          <select name="book_id" required>
            <option value="" disabled selected>Choose an available book…</option>
            <?php
            $availableBooks=$conn->query("SELECT * FROM books WHERE status='Available'");
            while($b=$availableBooks->fetch_assoc()):
            ?>
            <option value="<?=$b['id']?>"><?=htmlspecialchars($b['title'])?> — <?=htmlspecialchars($b['author'])?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-field">
          <label>Borrow Date</label>
          <input type="date" name="date" required value="<?=date('Y-m-d')?>">
        </div>
        <button type="submit" class="btn-confirm">↩ Confirm Borrow</button>
      </form>
      <div class="btn-cancel-wrap"><a href="dashboard.php" class="btn-cancel-link">← Back to Dashboard</a></div>

      <!-- AVAILABLE BOOKS LIST inside card -->
      <div style="margin-top:24px;padding-top:18px;border-top:0.5px solid rgba(255,255,255,0.06);">
        <div style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:10px;">✅ Available Now</div>
        <div class="avail-list">
          <?php
          $avBooks=$conn->query("SELECT title,author FROM books WHERE status='Available' LIMIT 6");
          if($avBooks&&$avBooks->num_rows>0){
            while($av=$avBooks->fetch_assoc()):?>
          <div class="avail-row">
            <div class="avail-dot"></div>
            <div class="avail-title"><?=htmlspecialchars($av['title'])?></div>
            <div class="avail-author"><?=htmlspecialchars($av['author'])?></div>
          </div>
          <?php endwhile;}else{echo '<div style="font-size:12px;color:rgba(255,255,255,0.3);padding:8px 0">No books available</div>';}?>
        </div>
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="borrow-right">

    <!-- STATS GRID -->
    <div class="panel">
      <div class="panel-label">📊 Borrow Overview</div>
      <div class="borrow-stats-grid">
        <div class="bstat"><div class="bstat-num" style="color:#f87171"><?=$borrowedCount?></div><div class="bstat-label">Currently out</div></div>
        <div class="bstat"><div class="bstat-num" style="color:#34d399"><?=$availableCount?></div><div class="bstat-label">Available</div></div>
        <div class="bstat"><div class="bstat-num" style="color:#4f8ef7"><?=$totalBorrows?></div><div class="bstat-label">All time borrows</div></div>
        <div class="bstat"><div class="bstat-num" style="color:#a78bfa"><?=$totalMembers?></div><div class="bstat-label">Members</div></div>
      </div>
    </div>

    <!-- HIGHLIGHTS -->
    <div class="panel">
      <div class="panel-label">🌟 Highlights</div>
      <?php if($topBorrower): ?>
      <div class="highlight-card">
        <div class="hl-icon" style="background:rgba(79,142,247,0.12);border:0.5px solid rgba(79,142,247,0.2)">🏆</div>
        <div><div class="hl-label">Top Borrower</div><div class="hl-value"><?=htmlspecialchars($topBorrower['user_name'])?> <span style="color:#4f8ef7;font-size:12px">(<?=$topBorrower['cnt']?>×)</span></div></div>
      </div>
      <?php endif; ?>
      <?php if($mostBorrowedBook): ?>
      <div class="highlight-card">
        <div class="hl-icon" style="background:rgba(251,191,36,0.12);border:0.5px solid rgba(251,191,36,0.2)">📖</div>
        <div><div class="hl-label">Most Popular Book</div><div class="hl-value"><?=htmlspecialchars($mostBorrowedBook['title'])?> <span style="color:#fbbf24;font-size:12px">(<?=$mostBorrowedBook['cnt']?>×)</span></div></div>
      </div>
      <?php endif; ?>
    </div>

    <!-- RECENT BORROWS TIMELINE -->
    <div class="panel filler-panel">
      <div class="panel-label">🕒 Recent Borrow Activity</div>
      <div class="activity-list">
        <?php
        $rb=$conn->query("SELECT br.user_name,b.title,b.author,br.borrow_date FROM borrow_records br JOIN books b ON br.book_id=b.id ORDER BY br.id DESC LIMIT 8");
        if($rb&&$rb->num_rows>0){while($r=$rb->fetch_assoc()):?>
        <div class="act-row">
          <div class="act-dot-wrap"><div class="act-dot"></div><div class="act-line"></div></div>
          <div class="act-info">
            <div class="act-title"><?=htmlspecialchars($r['title'])?></div>
            <div class="act-meta">by <?=htmlspecialchars($r['user_name'])?> · <?=htmlspecialchars($r['author'])?></div>
          </div>
          <div class="act-date"><?=date('M j',(int)strtotime($r['borrow_date']))?></div>
        </div>
        <?php endwhile;}else{echo '<div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;padding:20px 0">No borrow records yet</div>';}?>
      </div>
      <div style="margin-top:auto;padding-top:14px;border-top:0.5px solid rgba(255,255,255,0.06);">
        <div style="font-size:11px;color:rgba(255,255,255,0.25);line-height:1.6;">
          💡 <strong style="color:rgba(255,255,255,0.4)">Tip:</strong> Only available books can be borrowed. Returned books are marked available again.
        </div>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>