<?php include 'header.php'; include 'books_data.php'; ?>

<?php
$id = $_GET['id'];

foreach ($_SESSION['books'] as $i => $book) {
  if ($book['id'] == $id) {
    $current = $book;
    $index = $i;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $_SESSION['books'][$index]['title'] = $_POST['title'];
  $_SESSION['books'][$index]['author'] = $_POST['author'];
  $_SESSION['books'][$index]['status'] = $_POST['status'];

  header("Location: books.php");
  exit();
}
?>

<div class="container">

  <div class="card form-card">

    <!-- Header -->
    <div class="form-header">
      <h3>✏️ Edit Book</h3>
      <p>Update book details below</p>
    </div>

    <!-- Form -->
    <form method="POST" class="styled-form">

      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?= $current['title'] ?>" required>
      </div>

      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" value="<?= $current['author'] ?>" required>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" required>
          <option value="Available" <?= $current['status']=="Available" ? "selected" : "" ?>>Available</option>
          <option value="Borrowed" <?= $current['status']=="Borrowed" ? "selected" : "" ?>>Borrowed</option>
        </select>
      </div>

      <div class="form-actions">
        <a href="books.php" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Update</button>
      </div>

    </form>

  </div>

</div>