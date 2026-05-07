<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user'])){ header("Location: index.php"); exit(); }
if(isset($_GET['delete'])){ $id=intval($_GET['delete']); $conn->query("DELETE FROM users WHERE id=$id"); header("Location: users.php"); exit(); }
include 'header.php';
$totalUsers   = $conn->query("SELECT COUNT(*) as t FROM users")->fetch_assoc()['t'];
$totalBorrows = $conn->query("SELECT COUNT(*) as t FROM borrow_records")->fetch_assoc()['t'];
$topBorrower  = $conn->query("SELECT user_name, COUNT(*) as cnt FROM borrow_records GROUP BY user_name ORDER BY cnt DESC LIMIT 1")->fetch_assoc();
$avgBorrows   = $totalUsers > 0 ? round($totalBorrows/$totalUsers,1) : 0;
?>
<style>
.users-shell{display:grid;grid-template-columns:1fr 280px;gap:22px;width:100%;align-items:stretch;}
.users-left{display:flex;flex-direction:column;}
.users-right{display:flex;flex-direction:column;gap:13px;}

.page-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;}
.page-head h1{font-family:'DM Serif Display',serif;font-size:1.9rem;font-weight:400;line-height:1;margin-bottom:5px;}
.page-head p{font-size:13px;color:var(--text-muted);}
.member-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(167,139,250,0.1);color:#a78bfa;border:0.5px solid rgba(167,139,250,0.2);padding:5px 13px;border-radius:20px;font-size:12px;font-weight:500;}
.search-wrap{position:relative;margin-bottom:13px;}
.search-wrap input{width:100%;padding:10px 14px 10px 40px;background:rgba(255,255,255,0.04);border:0.5px solid rgba(255,255,255,0.08);border-radius:10px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:13px;outline:none;transition:border-color 0.15s;}
.search-wrap input:focus{border-color:rgba(79,142,247,0.4);}
.search-wrap input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:14px;pointer-events:none;}
.users-table-wrap{background:rgba(255,255,255,0.02);border:0.5px solid rgba(255,255,255,0.07);border-radius:14px;overflow:hidden;flex:1;}
table{width:100%;border-collapse:collapse;}
thead tr{border-bottom:0.5px solid rgba(255,255,255,0.06);}
th{padding:11px 15px;text-align:left;font-size:10.5px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.1em;background:transparent;}
td{padding:13px 15px;font-size:13px;border-bottom:0.5px solid rgba(255,255,255,0.04);}
tbody tr:last-child td{border-bottom:none;}
tbody tr{transition:background 0.12s;}
tbody tr:hover{background:rgba(255,255,255,0.025);}
.user-cell{display:flex;align-items:center;gap:11px;}
.user-av{width:35px;height:35px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;flex-shrink:0;color:white;}
.user-name-text{font-weight:500;font-size:13px;}
.user-id-text{font-size:11px;color:rgba(255,255,255,0.3);margin-top:1px;}
.row-actions{display:flex;gap:6px;}
.row-btn{padding:4px 10px;border-radius:7px;font-size:12px;font-weight:500;text-decoration:none;transition:all 0.15s;border:0.5px solid transparent;}
.row-btn.edit{color:#4f8ef7;background:rgba(79,142,247,0.08);border-color:rgba(79,142,247,0.15);}
.row-btn.edit:hover{background:rgba(79,142,247,0.18);}
.row-btn.del{color:#f87171;background:rgba(248,113,113,0.08);border-color:rgba(248,113,113,0.15);}
.row-btn.del:hover{background:rgba(248,113,113,0.18);}

.panel{background:rgba(255,255,255,0.025);border:0.5px solid rgba(255,255,255,0.07);border-radius:14px;padding:16px;}
.panel-label{font-size:10px;font-weight:500;color:rgba(255,255,255,0.35);text-transform:uppercase;letter-spacing:0.14em;margin-bottom:12px;}

.sum-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.sum-stat{padding:12px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.06);border-radius:10px;}
.sum-num{font-family:'DM Serif Display',serif;font-size:1.6rem;line-height:1;margin-bottom:3px;}
.sum-label{font-size:11px;color:rgba(255,255,255,0.4);}

.top-borrower{display:flex;align-items:center;gap:12px;padding:12px;background:linear-gradient(135deg,rgba(79,142,247,0.1),rgba(167,139,250,0.08));border:0.5px solid rgba(79,142,247,0.2);border-radius:12px;}
.top-bor-av{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#4f8ef7,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:white;flex-shrink:0;}
.top-bor-name{font-size:14px;font-weight:600;margin-bottom:2px;}
.top-bor-sub{font-size:11px;color:rgba(255,255,255,0.4);}
.top-bor-count{margin-left:auto;text-align:right;}
.top-bor-num{font-family:'DM Serif Display',serif;font-size:1.6rem;color:#4f8ef7;line-height:1;}
.top-bor-label{font-size:10px;color:rgba(255,255,255,0.3);}

.avatar-cloud{display:flex;flex-wrap:wrap;gap:7px;}
.av-chip{display:flex;align-items:center;gap:6px;padding:4px 9px 4px 4px;background:rgba(255,255,255,0.04);border:0.5px solid rgba(255,255,255,0.07);border-radius:20px;font-size:12px;transition:all 0.15s;}
.av-chip:hover{background:rgba(255,255,255,0.08);}
.av-chip-icon{width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:white;}

.activity-list{display:flex;flex-direction:column;}
.act-row{display:flex;align-items:flex-start;gap:10px;padding:8px 0;border-bottom:0.5px solid rgba(255,255,255,0.04);}
.act-row:last-child{border-bottom:none;padding-bottom:0;}
.act-dot-wrap{display:flex;flex-direction:column;align-items:center;padding-top:4px;}
.act-dot{width:6px;height:6px;border-radius:50%;background:#4f8ef7;flex-shrink:0;}
.act-line{width:1px;flex:1;background:rgba(255,255,255,0.06);margin-top:3px;min-height:16px;}
.act-row:last-child .act-line{display:none;}
.act-info{flex:1;min-width:0;}
.act-user{font-size:12px;font-weight:500;}
.act-book{font-size:11px;color:rgba(255,255,255,0.4);margin-top:1px;}
.act-date{font-size:11px;color:rgba(255,255,255,0.25);white-space:nowrap;}

.qa-list{display:flex;flex-direction:column;gap:7px;}
.qa-item{display:flex;align-items:center;gap:10px;padding:10px 12px;background:rgba(255,255,255,0.03);border:0.5px solid rgba(255,255,255,0.06);border-radius:9px;text-decoration:none;color:rgba(255,255,255,0.6);font-size:13px;transition:all 0.15s;}
.qa-item:hover{background:rgba(255,255,255,0.07);color:white;}
.qa-item.primary{background:rgba(167,139,250,0.1);border-color:rgba(167,139,250,0.2);color:#a78bfa;}
.qa-item.primary:hover{background:rgba(167,139,250,0.2);}
.qa-icon{width:26px;height:26px;border-radius:7px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
.qa-item.primary .qa-icon{background:rgba(167,139,250,0.15);}

.filler-panel{flex:1;display:flex;flex-direction:column;}
.filler-panel .qa-list{flex:1;}
</style>

<div class="users-shell">
  <!-- LEFT -->
  <div class="users-left">
    <div class="page-head">
      <div><h1>User Management</h1><p>Manage registered library members</p></div>
      <div style="display:flex;align-items:center;gap:10px;">
        <span class="member-badge">👥 <?=$totalUsers?> members</span>
        <a href="add_user.php" class="btn-primary" style="padding:10px 20px;">+ Add User</a>
      </div>
    </div>
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" id="userSearch" placeholder="Search by name or email…">
    </div>
    <div class="users-table-wrap">
      <table id="userTable">
        <thead><tr><th>#</th><th>Member</th><th>Email</th><th>Actions</th></tr></thead>
        <tbody>
          <?php
          $bgC=['#1a3a5c','#1a3d2b','#3d1a1a','#2d1a3d','#3d2e1a','#1a2d3d'];
          $acC=['#4f8ef7','#34d399','#f87171','#a78bfa','#fbbf24','#38bdf8'];
          $res=$conn->query("SELECT * FROM users");
          $idx=0;
          while($user=$res->fetch_assoc()):
            $bg=$bgC[$idx%count($bgC)]; $ac=$acC[$idx%count($acC)];
            $ini=strtoupper(substr($user['name'],0,1)); $idx++;
          ?>
          <tr>
            <td style="color:rgba(255,255,255,0.2);width:34px"><?=$idx?></td>
            <td><div class="user-cell"><div class="user-av" style="background:<?=$bg?>;border:1.5px solid <?=$ac?>44"><?=$ini?></div><div><div class="user-name-text"><?=htmlspecialchars($user['name'])?></div><div class="user-id-text">Member #<?=$idx?></div></div></div></td>
            <td style="color:rgba(255,255,255,0.45)"><?=htmlspecialchars($user['email'])?></td>
            <td><div class="row-actions"><a href="edit_user.php?id=<?=$user['id']?>" class="row-btn edit">Edit</a><a href="users.php?delete=<?=$user['id']?>" class="row-btn del" onclick="return confirm('Delete this user?')">Delete</a></div></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="users-right">

    <div class="panel">
      <div class="panel-label">📊 Summary</div>
      <div class="sum-grid">
        <div class="sum-stat"><div class="sum-num" style="color:#a78bfa"><?=$totalUsers?></div><div class="sum-label">Members</div></div>
        <div class="sum-stat"><div class="sum-num" style="color:#4f8ef7"><?=$totalBorrows?></div><div class="sum-label">Borrows</div></div>
        <div class="sum-stat" style="grid-column:1/-1"><div class="sum-num" style="color:#34d399"><?=$avgBorrows?></div><div class="sum-label">Avg borrows per member</div></div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-label">🏆 Top Borrower</div>
      <?php if($topBorrower): ?>
      <div class="top-borrower">
        <div class="top-bor-av"><?=strtoupper(substr($topBorrower['user_name'],0,1))?></div>
        <div><div class="top-bor-name"><?=htmlspecialchars($topBorrower['user_name'])?></div><div class="top-bor-sub">Most active member</div></div>
        <div class="top-bor-count"><div class="top-bor-num"><?=$topBorrower['cnt']?></div><div class="top-bor-label">borrows</div></div>
      </div>
      <?php else: ?><div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;padding:12px 0">No activity yet</div><?php endif; ?>
    </div>

    <div class="panel">
      <div class="panel-label">👤 All Members</div>
      <div class="avatar-cloud">
        <?php
        $au=$conn->query("SELECT id,name FROM users ORDER BY id"); $i2=0;
        while($u=$au->fetch_assoc()):
          $bg2=$bgC[$i2%count($bgC)]; $ac2=$acC[$i2%count($acC)]; $i2++;
        ?>
        <div class="av-chip"><div class="av-chip-icon" style="background:<?=$bg2?>;border:1px solid <?=$ac2?>44"><?=strtoupper(substr($u['name'],0,1))?></div><?=htmlspecialchars($u['name'])?></div>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="panel">
      <div class="panel-label">🕒 Recent Activity</div>
      <div class="activity-list">
        <?php
        $ra=$conn->query("SELECT br.user_name,b.title,br.borrow_date FROM borrow_records br JOIN books b ON br.book_id=b.id ORDER BY br.id DESC LIMIT 6");
        if($ra&&$ra->num_rows>0){while($a=$ra->fetch_assoc()):?>
        <div class="act-row"><div class="act-dot-wrap"><div class="act-dot"></div><div class="act-line"></div></div><div class="act-info"><div class="act-user"><?=htmlspecialchars($a['user_name'])?></div><div class="act-book">borrowed "<?=htmlspecialchars($a['title'])?>"</div></div><div class="act-date"><?=date('M j',(int)strtotime($a['borrow_date']))?></div></div>
        <?php endwhile;}else{echo '<div style="font-size:12px;color:rgba(255,255,255,0.3);text-align:center;padding:12px 0">No activity yet</div>';}?>
      </div>
    </div>

    <div class="panel filler-panel">
      <div class="panel-label">⚡ Quick Actions</div>
      <div class="qa-list">
        <a href="add_user.php" class="qa-item primary"><div class="qa-icon">👤</div>Add New User</a>
        <a href="borrow.php" class="qa-item"><div class="qa-icon">↩</div>Borrow a Book</a>
        <a href="return.php" class="qa-item"><div class="qa-icon">↪</div>Return a Book</a>
        <a href="books.php" class="qa-item"><div class="qa-icon">📚</div>Manage Books</a>
        <a href="dashboard.php" class="qa-item"><div class="qa-icon">⊞</div>Back to Dashboard</a>
      </div>
      <div style="margin-top:auto;padding-top:14px;border-top:0.5px solid rgba(255,255,255,0.06);">
        <div style="font-size:11px;color:rgba(255,255,255,0.25);line-height:1.6;">
          💡 <strong style="color:rgba(255,255,255,0.4)">Tip:</strong> Members can be searched by name or email using the search bar above.
        </div>
      </div>
    </div>

  </div>
</div>

<script>
const searchInput=document.getElementById('userSearch');
const rows=document.querySelectorAll('#userTable tbody tr');
searchInput.addEventListener('keyup',function(){const value=this.value.toLowerCase();rows.forEach(row=>{row.style.display=row.innerText.toLowerCase().includes(value)?'':'none';});});
</script>
<?php include 'footer.php'; ?>