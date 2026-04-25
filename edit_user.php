<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

// GET USER
$id = $_GET['id'];
$res = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $res->fetch_assoc();

// UPDATE USER
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $name = $_POST['name'];
  $email = $_POST['email'];

  $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
  $stmt->bind_param("ssi", $name, $email, $id);
  $stmt->execute();

  header("Location: users.php");
  exit();
}
?>

<div class="container">
  <div class="card form-card">

    <div class="form-header">
      <h3>✏️ Edit User</h3>
      <p>Update user details</p>
    </div>

    <form method="POST" class="styled-form">

      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= $user['name'] ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>
      </div>

      <div class="form-actions">
        <a href="users.php" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Update</button>
      </div>

    </form>

  </div>
</div>

<?php include 'footer.php'; ?>