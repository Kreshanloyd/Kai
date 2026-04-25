<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
  header("Location: index.php");
  exit();
}

include 'header.php';

/* =========================
   RETURN BOOK PROCESS
========================= */
if (isset($_GET['return'])) {

  $id = (int) $_GET['return'];

  // get borrow record safely
  $stmt = $conn->prepare("SELECT book_id FROM borrow_records WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();
    $book_id = $row['book_id'];

    // update borrow record
    $conn->query("
      UPDATE borrow_records 
      SET status='Returned', return_date=CURDATE()
      WHERE id=$id
    ");

    // update book status
    $conn->query("
      UPDATE books 
      SET status='Available' 
      WHERE id=$book_id
    ");
  }

  header("Location: return.php");
  exit();
}
?>

<div class="container">

  <div class="card">

    <div class="table-header">
      <h3>📚 Borrow Records</h3>
    </div>

    <table class="styled-table">

      <thead>
        <tr>
          <th>User</th>
          <th>Book</th>
          <th>Borrow Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>

        <?php
        $res = $conn->query("
          SELECT br.*, b.title 
          FROM borrow_records br
          JOIN books b ON br.book_id = b.id
          ORDER BY br.id DESC
        ");

        while($row = $res->fetch_assoc()):
        ?>

        <tr>
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= $row['borrow_date'] ?></td>
          <td>
            <span class="badge <?= $row['status']=='Borrowed' ? 'warn' : 'ok' ?>">
              <?= $row['status'] ?>
            </span>
          </td>

          <td>
            <?php if($row['status']=="Borrowed"): ?>
              <a href="return.php?return=<?= $row['id'] ?>" 
                 class="btn-primary"
                 onclick="return confirm('Return this book?')">
                 Return
              </a>
            <?php else: ?>
              <span class="badge ok">Returned</span>
            <?php endif; ?>
          </td>
        </tr>

        <?php endwhile; ?>

      </tbody>

    </table>

  </div>

</div>

<?php include 'footer.php'; ?>