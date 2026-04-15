<?php include 'header.php'; ?>

<div class="container">

  <div class="card">

    <!-- Header -->
    <div class="table-header">
      <h3>👥 User Management</h3>

      <div class="table-actions">
        <input type="text" id="userSearch" placeholder="Search users...">
      </div>
    </div>

    <!-- Table -->
    <table id="userTable" class="styled-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>1</td>
          <td>Max Verstappen</td>
          <td>verstappen@gmail.com</td>
        </tr>

        <tr>
          <td>2</td>
          <td>Lewis Hamilton</td>
          <td>hamilton@gmail.com</td>
        </tr>

        <tr>
          <td>3</td>
          <td>Charles Leclerc</td>
          <td>leclerc@gmail.com</td>
        </tr>
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