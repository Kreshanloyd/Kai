<?php include 'header.php'; ?>

<div class="container">

  <!-- Welcome Section -->
  <div class="card dashboard-header">
    <div>
      <h3>Welcome, <?php echo $_SESSION['user']; ?> 👋</h3>
      <p>Here’s your library overview</p>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="dashboard-grid">

    <div class="card stat-card">
      <div class="stat-icon">📚</div>
      <div class="stat-info">
        <h4>Total Books</h4>
        <p class="stat-number">120</p>
      </div>
    </div>

    <div class="card stat-card">
      <div class="stat-icon">📖</div>
      <div class="stat-info">
        <h4>Borrowed Books</h4>
        <p class="stat-number">35</p>
      </div>
    </div>

    <div class="card stat-card">
      <div class="stat-icon">👥</div>
      <div class="stat-info">
        <h4>Users</h4>
        <p class="stat-number">50</p>
      </div>
    </div>

  </div>

</div>