<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
if(isset($_GET['delete'])){ $id=intval($_GET['delete']); $conn->query("DELETE FROM books WHERE id=$id"); header("Location: books.php"); exit(); }
include 'header.php';
$total     = $conn->query("SELECT COUNT(*) as t FROM books")->fetch_assoc()['t'];
$available = $conn->query("SELECT COUNT(*) as t FROM books WHERE status='Available'")->fetch_assoc()['t'];
$borrowed  = $conn->query("SELECT COUNT(*) as t FROM books WHERE status='Borrowed'")->fetch_assoc()['t'];
$pct       = $total > 0 ? round(($available/$total)*100) : 0;
$r = 38; $circ = 2*M_PI*$r;
$availDash = $total > 0 ? ($available/$total)*$circ : 0;
$borrowDash = $circ - $availDash;
?>
<style>
.books-shell{display:grid;grid-template-columns:1fr 280px;gap:22px;width:100%;align-items:stretch;}
.books-left{display:flex;flex-direction:column;}
.books-right{display:flex;flex-direction:column;gap:13px;}

/* stretch last panel to fill remaining */
.books-right .panel:last-child{flex:1;}

.page-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;}
.page-head h1{font-family:'DM Serif Display',serif;font-size:1.9rem;font-weight:400;line-height:1;margin-bottom:5px;}
.page-head p{font-size:13px;color:var(--text-muted);}
.mini-stats{display:flex;gap:10px;margin-bottom:16px;}
.mini-stat{flex:1;padding:13px 15px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.07);border-radius:12px;display:flex;align-items:center;gap:11px;}
.mini-stat-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.mini-stat-num{font-size:1.25rem;font-weight:600;line-height:1;}
.mini-stat-label{font-size:11px;color:var(--text-muted);margin-top:2px;}
.search-wrap{position:relative;margin-bottom:13px;}
.search-wrap input{width:100%;padding:10px 14px 10px 40px;background:rgba(255,255,255,0.04);border:0.5px solid rgba(255,255,255,0.08);border-radius:10px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:13px;outline:none;transition:border-color 0.15s;}
.search-wrap input:focus{border-color:rgba(79,142,247,0.4);}
.search-wrap input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;}
.books-table-wrap{background:rgba(255,255,255,0.02);border:0.5px solid rgba(255,255,255,0.07);border-radius:14px;overflow:hidden;flex:1;}
table{width:100%;border-collapse:collapse;}
thead tr{border-bottom:0.5px solid rgba(255,255,255,0.06);}
th{padding:11px 15px;text-align:left;font-size:10.5px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.1em;background:transparent;}
td{padding:12px 15px;font-size:13px;border-bottom:0.5px solid rgba(255,255,255,0.04);}
tbody tr:last-child td{border-bottom:none;}
tbody tr{transition:background 0.12s;}
tbody tr:hover{background:rgba(255,255,255,0.025);}
.book-cell{display:flex;align-items:center;gap:11px;}
.book-icon{width:30px;height:40px;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
.book-title{font-weight:500;font-size:13px;}
.book-id{font-size:11px;color:rgba(255,255,255,0.3);margin-top:1px;}
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;}
.badge::before{content:'';width:5px;height:5px;border-radius:50%;}
.badge.available{background:rgba(52,211,153,0.1);color:#34d399;border:0.5px solid rgba(52,211,153,0.2);}
.badge.available::before{background:#34d399;}
.badge.borrowed{background:rgba(248,113,113,0.1);color:#f87171;border:0.5px solid rgba(248,113,113,0.2);}
.badge.borrowed::before{background:#f87171;}
.row-actions{display:flex;gap:6px;}
.row-btn{padding:4px 10px;border-radius:7px;font-size:12px;font-weight:500;text-decoration:none;transition:all 0.15s;border:0.5px solid transparent;}
.row-btn.edit{color:#4f8ef7;background:rgba(79,142,247,0.08);border-color:rgba(79,142,247,0.15);}
.row-btn.edit:hover{background:rgba(79,142,247,0.18);}
.row-btn.del{color:#f87171;background:rgba(248,113,113,0.08);border-color:rgba(248,113,113,0.15);}
.row-btn.del:hover{background:rgba(248,113,113,0.18);}

/* PANELS */
.panel{background:rgba(255,255,255,0.025);border:0.5px solid rgba(255,255,255,0.07);border-radius:14px;padding:16px;}
.panel-label{font-size:10px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:12px;}

/* DONUT */
.avail-ring-wrap{display:flex;align-items:center;gap:14px;margin-bottom:10px;}
.ring-legend{display:flex;flex-direction:column;gap:7px;flex:1;}
.ring-legend-item{display:flex;align-items:center;justify-content:space-between;}
.ring-legend-left{display:flex;align-items:center;gap:7px;font-size:12px;color:rgba(255,255,255,0.6);}
.ring-dot{width:7px;height:7px;border-radius:50%;}
.ring-val{font-size:13px;font-weight:600;}
.prog-track{height:4px;background:rgba(255,255,255,0.06);border-radius:99px;overflow:hidden;margin-top:8px;}
.prog-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#4f8ef7,#34d399);}

/* BREAKDOWN */
.breakdown{display:flex;flex-direction:column;gap:8px;}
.breakdown-row{display:flex;align-items:center;gap:10px;}
.breakdown-label{font-size:12px;color:rgba(255,255,255,0.5);width:68px;flex-shrink:0;}
.breakdown-bar-wrap{flex:1;height:5px;background:rgba(255,255,255,0.06);border-radius:99px;overflow:hidden;}
.breakdown-bar{height:100%;border-radius:99px;}
.breakdown-val{font-size:11px;font-weight:600;width:22px;text-align:right;flex-shrink:0;}

/* MOST BORROWED */
.top-books{display:flex;flex-direction:column;gap:7px;}
.top-book-row{display:flex;align-items:center;gap:9px;padding:8px 10px;background:rgba(255,255,255,0.03);border-radius:8px;}
.top-book-rank{width:19px;height:19px;border-radius:5px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:rgba(255,255,255,0.4);flex-shrink:0;}
.top-book-info{flex:1;min-width:0;}
.top-book-title{font-size:12px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.top-book-author{font-size:11px;color:rgba(255,255,255,0.35);}
.top-book-count{font-size:11px;color:#4f8ef7;font-weight:600;}

/* RECENT BORROWS */
.rb-list{display:flex;flex-direction:column;gap:7px;}
.rb-row{display:flex;align-items:center;gap:9px;padding:8px 10px;background:rgba(255,255,255,0.03);border-radius:8px;}
.rb-icon{width:26px;height:34px;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;}
.rb-info{flex:1;min-width:0;}
.rb-title{font-size:12px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.rb-by{font-size:11px;color:rgba(255,255,255,0.35);}
.rb-date{font-size:10px;color:rgba(255,255,255,0.25);white-space:nowrap;}

/* QUICK ACTIONS */
.qa-list{display:flex;flex-direction:column;gap:7px;}
.qa-item{display:flex;align-items:center;gap:10px;padding:10px 12px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.06);border-radius:9px;text-decoration:none;color:rgba(255,255,255,0.6);font-size:13px;transition:all 0.15s;}
.qa-item:hover{background:rgba(255,255,255,0.07);color:white;}
.qa-item.primary{background:rgba(79,142,247,0.1);border-color:rgba(79,142,247,0.2);color:#4f8ef7;}
.qa-item.primary:hover{background:rgba(79,142,247,0.2);}
.qa-icon{width:26px;height:26px;border-radius:7px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
.qa-item.primary .qa-icon{background:rgba(79,142,247,0.15);}

/* FILLER PANEL — stretchy content */
.filler-panel{flex:1;display:flex;flex-direction:column;justify-content:space-between;}
.filler-tip{padding:12px;background:rgba(255,255,255,0.03);border-radius:8px;border:0.5px solid rgba(255,255,255,0.05);margin-bottom:8px;}
.filler-tip-icon{font-size:18px;margin-bottom:6px;}
.filler-tip-title{font-size:12px;font-weight:500;margin-bottom:3px;}
.filler-tip-text{font-size:11px;color:rgba(255,255,255,0.4);line-height:1.5;}
</style>

<div class="books-shell">
  <!-- LEFT -->
  <div class="books-left">
    <div class="page-head">
      <div><h1>Book Management</h1><p>Manage your library's entire book collection</p></div>
      <a href="add_book.php" class="btn-primary" style="padding:10px 20px;">+ Add Book</a>
    </div>
    <div class="mini-stats">
      <div class="mini-stat"><div class="mini-stat-dot" style="background:#4f8ef7;box-shadow:0 0 6px #4f8ef7"></div><div><div class="mini-stat-num" style="color:#4f8ef7"><?=$total?></div><div class="mini-stat-label">Total</div></div></div>
      <div class="mini-stat"><div class="mini-stat-dot" style="background:#34d399;box-shadow:0 0 6px #34d399"></div><div><div class="mini-stat-num" style="color:#34d399"><?=$available?></div><div class="mini-stat-label">Available</div></div></div>
      <div class="mini-stat"><div class="mini-stat-dot" style="background:#f87171;box-shadow:0 0 6px #f87171"></div><div><div class="mini-stat-num" style="color:#f87171"><?=$borrowed?></div><div class="mini-stat-label">Borrowed</div></div></div>
    </div>
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" id="bookSearch" placeholder="Search by title or author…">
    </div>
    <div class="books-table-wrap">
      <table id="bookTable">
        <thead><tr><th>#</th><th>Book</th><th>Author</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php
          $colors=['#1a3a5c','#1a3d2b','#3d1a1a','#2d1a3d','#3d2e1a','#1a2d3d','#2a1a3d','#1a2a2d'];
          $result=$conn->query("SELECT * FROM books");
          while($book=$result->fetch_assoc()):
            $c=$colors[($book['id']-1)%count($colors)];
          ?>
          <tr>
            <td style="color:rgba(255,255,255,0.2);width:34px"><?=$book['id']?></td>
            <td><div class="book-cell"><div class="book-icon" style="background:<?=$c?>">📖</div><div><div class="book-title"><?=htmlspecialchars($book['title'])?></div><div class="book-id">ID #<?=$book['id']?></div></div></div></td>
            <td style="color:rgba(255,255,255,0.5)"><?=htmlspecialchars($book['author'])?></td>
            <td><?php if($book['status']=='Available'): ?><span class="badge available">Available</span><?php else: ?><span class="badge borrowed">Borrowed</span><?php endif; ?></td>
            <td><div class="row-actions"><a href="edit_book.php?id=<?=$book['id']?>" class="row-btn edit">Edit</a><a href="books.php?delete=<?=$book['id']?>" class="row-btn del" onclick="return confirm('Delete this book?')">Delete</a></div></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="books-right">

    <div class="panel">
      <div class="panel-label">📊 Availability</div>
      <div class="avail-ring-wrap">
        <svg width="88" height="88" viewBox="0 0 88 88" style="flex-shrink:0">
          <circle cx="44" cy="44" r="<?=$r?>" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="9"/>
          <circle cx="44" cy="44" r="<?=$r?>" fill="none" stroke="#f87171" stroke-width="9" stroke-dasharray="<?=$circ?>" stroke-dashoffset="0" transform="rotate(-90 44 44)"/>
          <circle cx="44" cy="44" r="<?=$r?>" fill="none" stroke="#34d399" stroke-width="9" stroke-dasharray="<?=$availDash?> <?=$borrowDash?>" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 44 44)"/>
          <text x="44" y="40" text-anchor="middle" fill="white" font-size="13" font-weight="600" font-family="DM Sans"><?=$pct?>%</text>
          <text x="44" y="53" text-anchor="middle" fill="rgba(255,255,255,0.4)" font-size="7.5" font-family="DM Sans">available</text>
        </svg>
        <div class="ring-legend">
          <div class="ring-legend-item"><div class="ring-legend-left"><div class="ring-dot" style="background:#34d399"></div>Available</div><div class="ring-val" style="color:#34d399"><?=$available?></div></div>
          <div class="ring-legend-item"><div class="ring-legend-left"><div class="ring-dot" style="background:#f87171"></div>Borrowed</div><div class="ring-val" style="color:#f87171"><?=$borrowed?></div></div>
          <div class="ring-legend-item"><div class="ring-legend-left"><div class="ring-dot" style="background:#4f8ef7"></div>Total</div><div class="ring-val" style="color:#4f8ef7"><?=$total?></div></div>
        </div>
      </div>
      <div class="prog-track"><div class="prog-fill" style="width:<?=$pct?>%"></div></div>
    </div>

    <div class="panel">
      <div class="panel-label">📈 Status Breakdown</div>
      <div class="breakdown">
        <div class="breakdown-row"><div class="breakdown-label">Available</div><div class="breakdown-bar-wrap"><div class="breakdown-bar" style="width:<?=$total>0?round(($available/$total)*100):0?>%;background:#34d399"></div></div><div class="breakdown-val" style="color:#34d399"><?=$available?></div></div>
        <div class="breakdown-row"><div class="breakdown-label">Borrowed</div><div class="breakdown-bar-wrap"><div class="breakdown-bar" style="width:<?=$total>0?round(($borrowed/$total)*100):0?>%;background:#f87171"></div></div><div class="breakdown-val" style="color:#f87171"><?=$borrowed?></div></div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-label">🏆 Most Borrowed</div>
      <div class="top-books">
        <?php
        $mb=$conn->query("SELECT b.title,b.author,COUNT(br.id) as times FROM borrow_records br JOIN books b ON br.book_id=b.id GROUP BY br.book_id ORDER BY times DESC LIMIT 5");
        if($mb&&$mb->num_rows>0){$rank=1;while($row=$mb->fetch_assoc()):?>
        <div class="top-book-row"><div class="top-book-rank"><?=$rank?></div><div class="top-book-info"><div class="top-book-title"><?=htmlspecialchars($row['title'])?></div><div class="top-book-author"><?=htmlspecialchars($row['author'])?></div></div><div class="top-book-count"><?=$row['times']?>×</div></div>
        <?php $rank++;endwhile;}else{echo '<div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;padding:10px 0">No history yet</div>';}?>
      </div>
    </div>

    <div class="panel">
      <div class="panel-label">🕒 Recent Borrows</div>
      <div class="rb-list">
        <?php
        $rb=$conn->query("SELECT br.user_name,b.title,br.borrow_date FROM borrow_records br JOIN books b ON br.book_id=b.id ORDER BY br.id DESC LIMIT 5");
        if($rb&&$rb->num_rows>0){while($r=$rb->fetch_assoc()):?>
        <div class="rb-row"><div class="rb-icon" style="background:#1a3a5c">📖</div><div class="rb-info"><div class="rb-title"><?=htmlspecialchars($r['title'])?></div><div class="rb-by">by <?=htmlspecialchars($r['user_name'])?></div></div><div class="rb-date"><?=date('M j',(int)strtotime($r['borrow_date']))?></div></div>
        <?php endwhile;}else{echo '<div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;padding:10px 0">No records yet</div>';}?>
      </div>
    </div>

    <div class="panel filler-panel">
      <div class="panel-label">⚡ Quick Actions</div>
      <div class="qa-list">
        <a href="add_book.php" class="qa-item primary"><div class="qa-icon">📚</div>Add New Book</a>
        <a href="borrow.php" class="qa-item"><div class="qa-icon">↩</div>Borrow a Book</a>
        <a href="return.php" class="qa-item"><div class="qa-icon">↪</div>Return a Book</a>
        <a href="users.php" class="qa-item"><div class="qa-icon">👥</div>Manage Users</a>
        <a href="dashboard.php" class="qa-item"><div class="qa-icon">⊞</div>Back to Dashboard</a>
      </div>
      <div style="margin-top:auto;padding-top:14px;border-top:0.5px solid rgba(255,255,255,0.06);">
        <div style="font-size:11px;color:rgba(255,255,255,0.25);line-height:1.6;">
          💡 <strong style="color:rgba(255,255,255,0.4)">Tip:</strong> Use the search bar to quickly find books by title or author name.
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const searchInput=document.getElementById('bookSearch');
const rows=document.querySelectorAll('#bookTable tbody tr');
searchInput.addEventListener('keyup',function(){const value=this.value.toLowerCase();rows.forEach(row=>{row.style.display=row.innerText.toLowerCase().includes(value)?'':'none';});});
</script>
<?php include 'footer.php'; ?>