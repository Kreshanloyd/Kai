<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // clean input
  $title = trim($_POST['title']);
  $author = trim($_POST['author']);
  $status = $_POST['status'];

  // validation
  if ($title == "" || $author == "") {
    $error = "All fields are required.";
  } else {

    // secure insert
    $stmt = $conn->prepare("
      INSERT INTO books (title, author, status) 
      VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sss", $title, $author, $status);

    if ($stmt->execute()) {
      header("Location: books.php");
      exit();
    } else {
      $error = "Failed to save book.";
    }
  }
}
?>

<div class="container">

  <div class="card form-card">

    <div class="form-header">
      <h3>➕ Add New Book</h3>
      <p>Fill in book details below</p>
    </div>

    <?php if($error): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="styled-form">

      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" placeholder="Enter book title" required>
      </div>

      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" placeholder="Enter author name" required>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" required>
          <option value="Available">Available</option>
          <option value="Borrowed">Borrowed</option>
        </select>
      </div>

      <div class="form-actions">
        <a href="books.php" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Save Book</button>
      </div>

    </form>

  </div>

</div>

<?php include 'footer.php'; ?>