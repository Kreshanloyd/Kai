<?php include 'header.php'; include 'books_data.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $new = [
    "id" => $_SESSION['next_id']++,   // ✅ FIXED (no duplicates)
    "title" => $_POST['title'],
    "author" => $_POST['author'],
    "status" => $_POST['status']
  ];

  $_SESSION['books'][] = $new;

  header("Location: books.php");
  exit();
}
?>

<div class="container">

  <div class="card form-card">

    <!-- Header -->
    <div class="form-header">
      <h3>➕ Add New Book</h3>
      <p>Fill in book details below</p>
    </div>

    <!-- Form -->
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
          <option value="Available" selected>Available</option>
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