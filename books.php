<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';
?>

<?php
// DELETE BOOK
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  $conn->query("DELETE FROM books WHERE id=$id");

  header("Location: books.php");
  exit();
}
?>

<div class="container">

  <div class="card">

    <!-- Header -->
    <div class="table-header">
      <h3>📚 Book Management</h3>

      <div class="table-actions">
        <input type="text" id="bookSearch" placeholder="Search books...">
        <a href="add_book.php" class="btn-primary">+ Add Book</a>
      </div>
    </div>

    <!-- Table -->
    <table id="bookTable" class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Author</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM books");

        while($book = $result->fetch_assoc()):
        ?>
          <tr>
            <td><?= $book['id'] ?></td>
            <td><?= $book['title'] ?></td>
            <td><?= $book['author'] ?></td>

            <td>
              <?php if($book['status'] == "Available"): ?>
                <span class="badge ok">Available</span>
              <?php else: ?>
                <span class="badge warn">Borrowed</span>
              <?php endif; ?>
            </td>

            <td class="actions">
              <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn-edit">Edit</a>

              <a href="books.php?delete=<?= $book['id'] ?>" 
                 class="btn-delete"
                 onclick="return confirm('Delete this book?')">
                 Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>

    </table>

  </div>

</div>

<script>
const searchInput = document.getElementById('bookSearch');
const rows = document.querySelectorAll('#bookTable tbody tr');

searchInput.addEventListener('keyup', function () {
  const value = this.value.toLowerCase();

  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
  });
});
</script>

<?php include 'footer.php'; ?>