<?php 
session_start();

if(isset($_SESSION['user'])){
  header("Location: dashboard.php");
  exit();
}

$error="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
  $u=trim($_POST['username']??"");
  $p=trim($_POST['password']??"");

  if($u==="admin" && $p==="1234"){
    $_SESSION['user']=$u;
    header("Location: dashboard.php");
    exit();
  }else{
    $error="Invalid username or password";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BookNest Login</title>

  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="login-wrapper">

  <div class="login-card">

    <!-- Logo / Title -->
    <div class="login-header">
      <div class="login-icon">📚</div>
      <h2>BookNest</h2>
      <p>Library Management System</p>
    </div>

    <!-- Form -->
    <form method="POST" class="login-form">

      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" class="login-btn">Login</button>

    </form>

    <!-- Error -->
    <?php if($error): ?>
      <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="login-footer">
      <small>Demo: admin / 1234</small>
    </div>

  </div>

</div>

</body>
</html>