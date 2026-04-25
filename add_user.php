<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // ✅ SAFE INSERT (basic improvement)
  $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $name, $email, $password);
  $stmt->execute();

  header("Location: index.php");
  exit();
}
?>

<link rel="stylesheet" href="style.css">

<div class="login-wrapper">

  <div class="login-card">

    <h2>👤 Register User</h2>
    <p>Create new library account</p>

    <form method="POST" class="login-form">

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

    <div class="login-footer">
      <small>Already have an account? Login instead</small>
    </div>

  </div>

</div>