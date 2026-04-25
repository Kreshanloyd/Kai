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
// DELETE USER
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  $conn->query("DELETE FROM users WHERE id=$id");

  header("Location: users.php");
  exit();
}
?>

<div class="container">

  <div class="card">

    <!-- Header -->
    <div class="table-header">
      <h3>👤 User Management</h3>

      <div class="table-actions">
        <input type="text" id="userSearch" placeholder="Search users...">
        <a href="add_user.php" class="btn-primary">+ Add User</a>
      </div>
    </div>

    <!-- Table -->
    <table id="userTable" class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $res = $conn->query("SELECT * FROM users");

        while($user = $res->fetch_assoc()):
        ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= $user['name'] ?></td>
          <td><?= $user['email'] ?></td>

          <td class="actions">
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-edit">Edit</a>

            <a href="users.php?delete=<?= $user['id'] ?>"
               class="btn-delete"
               onclick="return confirm('Delete this user?')">
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
const searchInput = document.getElementById('userSearch');
const rows = document.querySelectorAll('#userTable tbody tr');

searchInput.addEventListener('keyup', function () {
  const value = this.value.toLowerCase();

  rows.forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
  });
});
</script>

<?php include 'footer.php'; ?>