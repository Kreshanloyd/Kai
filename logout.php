<?php
session_start();
session_destroy();
header("Location: index.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>
  <link rel="stylesheet" href="style.css">

  <!-- Auto redirect -->
  <meta http-equiv="refresh" content="3;url=index.php">
</head>

<body>

<div class="logout-wrapper">

  <div class="logout-card">

    <div class="logout-icon">👋</div>

    <h2>Logged Out Successfully</h2>
    <p>Thank you for using <strong>BookNest</strong></p>

    <p class="redirect-text">Redirecting<span class="dots"></span></p>

    <a href="index.php" class="btn-primary">Go Now</a>

  </div>

</div>

</body>
</html>

<?php include 'sidebar.php'; ?>

<div class="main-content">
  <!-- your existing page content here -->
</div>
<script>
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("collapsed");
}
</script>

<?php include 'header.php'; ?>

<!-- PAGE CONTENT ONLY -->
<div class="card">
  <h3>Dashboard</h3>
</div>

<?php include 'footer.php'; ?>