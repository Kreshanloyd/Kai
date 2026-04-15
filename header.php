<?php
session_start();
if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}
?>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<header class="main-header">
  
  <!-- Logo Section -->
  <div class="logo">
    <div class="icon">📚</div>
    <h2>BookNest</h2>
  </div>

  <!-- Navigation -->
  <nav class="nav-menu">
    <a href="dashboard.php" class="nav-link active">Dashboard</a>
    <a href="books.php" class="nav-link">Books</a>
    <a href="users.php" class="nav-link">Users</a>
    <a href="borrow.php" class="nav-link">Borrow</a>
  </nav>

  <!-- User Section -->
  <div class="user-section">
    <div class="user-info">
      <span class="user-label">Welcome,</span>
      <span class="username"><?php echo $_SESSION['user']; ?></span>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

</header>