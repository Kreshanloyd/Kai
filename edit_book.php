<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

/* =========================
   GET ID SAFELY
========================= */
if (!isset($_GET['id'])) {
  header("Location: books.php");
  exit();
}

$id = (int) $_GET['id'];

/* =========================
   FETCH BOOK
========================= */
$stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();

if (!$current) {
  header("Location: books.php");
  exit();
}

/* =========================
   UPDATE BOOK
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $title = $_POST['title'];
  $author = $_POST['author'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("
    UPDATE books 
    SET title=?, author=?, status=? 
    WHERE id=?
  ");

  $stmt->bind_param("sssi", $title, $author, $status, $id);
  $stmt->execute();

  header("Location: books.php");
  exit();
}
?>

<div class="container">

  <div class="card form-card">

    <div class="form-header">
      <h3>✏️ Edit Book</h3>
      <p>Update book details below</p>
    </div>

    <form method="POST" class="styled-form">

      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title"
          value="<?= htmlspecialchars($current['title']) ?>" required>
      </div>

      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author"
          value="<?= htmlspecialchars($current['author']) ?>" required>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" required>
          <option value="Available" <?= $current['status']=="Available" ? "selected" : "" ?>>
            Available
          </option>
          <option value="Borrowed" <?= $current['status']=="Borrowed" ? "selected" : "" ?>>
            Borrowed
          </option>
        </select>
      </div>

      <div class="form-actions">
        <a href="books.php" class="btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Update Book</button>
      </div>

    </form>

  </div>

</div>

<?php include 'footer.php'; ?>