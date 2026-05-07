<div class="sidebar" id="sidebar">

  <!-- LOGO -->
  <div class="sidebar-header">
    <div class="sidebar-logo-icon">📚</div>
    <div class="sidebar-logo-text">Book<em>Nest</em></div>
  </div>

  <!-- NAV LINKS -->
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
      <span class="link-icon">⊞</span>
      <span class="link-label">Dashboard</span>
    </a>
    <a href="books.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'books.php' ? 'active' : '' ?>">
      <span class="link-icon">▤</span>
      <span class="link-label">Books</span>
    </a>
    <a href="users.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
      <span class="link-icon">◉</span>
      <span class="link-label">Users</span>
    </a>
    <a href="borrow.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'borrow.php' ? 'active' : '' ?>">
      <span class="link-icon">↩</span>
      <span class="link-label">Borrow</span>
    </a>
  </nav>

  <!-- LOGOUT -->
  <div class="sidebar-footer">
    <a href="logout.php" class="sidebar-link logout">
      <span class="link-icon">↖</span>
      <span class="link-label">Sign out</span>
    </a>
  </div>

</div>

<!-- SIDEBAR TOGGLE SCRIPT -->
<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
  }
</script>