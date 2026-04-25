<?php
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // check connection first (IMPORTANT)
  if (!$conn) {
    die("Database connection failed");
  }

  // check duplicate email
  $check = $conn->query("SELECT * FROM users WHERE email='$email'");

  if ($check && $check->num_rows > 0) {
    $error = "Email already exists!";
  } else {

    $sql = "INSERT INTO users (name, email, password)
            VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql)) {
      $success = "Account created successfully!";
    } else {
      $error = "SQL Error: " . $conn->error;
    }
  }
}
?>

<link rel="stylesheet" href="style.css">

<div class="login-wrapper">

  <div class="login-card">

    <h2>👤 Register</h2>

    <form method="POST">

      <div class="input-group">
        <label>Name</label>
        <input type="text" name="name" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" class="login-btn">Register</button>

    </form>

    <?php if($error): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

  </div>

</div>