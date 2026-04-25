<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = $conn->query("SELECT * FROM users WHERE email='$email'");

  if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    if ($password == $user['password']) {

      $_SESSION['user'] = $user['name'];
      $_SESSION['user_id'] = $user['id'];

      header("Location: dashboard.php");
      exit();

    } else {
      $error = "Invalid password";
    }

  } else {
    $error = "User not found";
  }
}
?>

<link rel="stylesheet" href="style.css">

<div class="login-wrapper">

  <div class="login-card">

    <h2>📚 Library Login</h2>
    <p>Access your system</p>

    <form method="POST" class="login-form">

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="login-btn">Login</button>

    </form>

    <?php if($error): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

  </div>

</div>