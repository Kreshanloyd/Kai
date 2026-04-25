<?php
session_start();
if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'db.php';
include 'header.php';

// 📚 BOOK STATS
$totalBooks = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];

$borrowedBooks = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='Borrowed'")->fetch_assoc()['total'];

$availableBooks = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='Available'")->fetch_assoc()['total'];

// 👤 USER STATS
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// 📖 BORROW STATS
$totalBorrows = $conn->query("SELECT COUNT(*) as total FROM borrow_records")->fetch_assoc()['total'];
?>

<div class="container">

  <!-- Welcome -->
  <div class="card dashboard-header">
    <h3>Welcome back, <?= $_SESSION['user']; ?> 👋</h3>
    <p>Library overview dashboard</p>
  </div>

  <!-- STATS GRID -->
  <div class="dashboard-grid">

    <div class="card stat-card classy">
      <div class="stat-icon">📚</div>
      <div class="stat-info">
        <h4>Total Books</h4>
        <p class="stat-number"><?= $totalBooks ?></p>
      </div>
    </div>

    <div class="card stat-card classy">
      <div class="stat-icon">📕</div>
      <div class="stat-info">
        <h4>Borrowed Books</h4>
        <p class="stat-number"><?= $borrowedBooks ?></p>
      </div>
    </div>

    <div class="card stat-card classy">
      <div class="stat-icon">📗</div>
      <div class="stat-info">
        <h4>Available Books</h4>
        <p class="stat-number"><?= $availableBooks ?></p>
      </div>
    </div>

    <div class="card stat-card classy">
      <div class="stat-icon">👤</div>
      <div class="stat-info">
        <h4>Total Users</h4>
        <p class="stat-number"><?= $totalUsers ?></p>
      </div>
    </div>

    <div class="card stat-card classy">
      <div class="stat-icon">📖</div>
      <div class="stat-info">
        <h4>Total Borrows</h4>
        <p class="stat-number"><?= $totalBorrows ?></p>
      </div>
    </div>

  </div>

</div>

<?php include 'footer.php'; ?>