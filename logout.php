<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>

  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

  <!-- Auto Redirect after 2 seconds -->
  <meta http-equiv="refresh" content="2;url=index.php">
</head>

<body>

<div class="logout-wrapper">

  <div class="logout-card">
    <div class="logout-icon">👋</div>
    <h2>Logged Out</h2>
    <p>You have been successfully logged out.</p>
    <p class="redirect-text">Redirecting to login...</p>
  </div>

</div>

</body>
</html>