<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $user = trim($_POST['user']);
  $book_id = $_POST['book_id'];
  $date = $_POST['date'];

  // ✅ Check if book is available
  $stmt = $conn->prepare("SELECT status FROM books WHERE id=?");
  $stmt->bind_param("i", $book_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $book = $result->fetch_assoc();

  if ($book['status'] == "Borrowed") {
    $error = "Book is already borrowed!";
  } else {

    // ✅ Insert borrow record (safe)
    $stmt = $conn->prepare("INSERT INTO borrow_records (user_name, book_id, borrow_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $user, $book_id, $date);
    $stmt->execute();

    // ✅ Update book status
    $conn->query("UPDATE books SET status='Borrowed' WHERE id=$book_id");

    $success = "Book borrowed successfully!";
  }
}
?>

<div class="container">

  <div class="card form-card">

    <div class="form-header">
      <h3>📖 Borrow Book</h3>
      <p>Select user and book</p>
    </div>

    <?php if($error): ?>
      <div class="error-box"><?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="styled-form">

      <div class="form-group">
        <label>User</label>
        <input type="text" name="user" required>
      </div>

      <div class="form-group">
        <label>Book</label>
        <select name="book_id" required>

          <?php
          $books = $conn->query("SELECT * FROM books WHERE status='Available'");
          while($b = $books->fetch_assoc()):
          ?>
            <option value="<?= $b['id'] ?>">
              <?= $b['title'] ?> (<?= $b['author'] ?>)
            </option>
          <?php endwhile; ?>

        </select>
      </div>

      <div class="form-group">
        <label>Borrow Date</label>
        <input type="date" name="date" required>
      </div>

      <button type="submit" class="btn-primary">Borrow Book</button>

    </form>

  </div>

</div>

<?php include 'footer.php'; ?>