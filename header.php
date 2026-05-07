<link rel="stylesheet" href="style.css">

<div class="layout">

  <!-- SIDEBAR -->
  <?php include 'sidebar.php'; ?>

  <!-- MAIN AREA -->
  <div class="main-wrapper">

    <!-- TOPBAR -->
    <div class="topbar" id="topbar">

      <div class="topbar-left">
        <button onclick="toggleSidebar()" class="menu-btn">☰</button>
        <span class="topbar-title">Library System</span>
      </div>

      <div class="user-box">
        <?php
          $name = $_SESSION['user'] ?? 'Guest';
          $initial = strtoupper(substr($name, 0, 1));
        ?>
        <div class="user-avatar"><?= $initial ?></div>
        <?= htmlspecialchars($name) ?>
      </div>

    </div>

    <!-- PAGE CONTENT START -->
    <div class="main-content" id="main-content">