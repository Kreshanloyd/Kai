<?php include 'header.php'; ?>

<div class="container">

  <div class="card form-card">

    <!-- Header -->
    <div class="form-header">
      <h3>📖 Borrow Book</h3>
      <p>Fill in the details to borrow a book</p>
    </div>

    <!-- Form -->
    <form onsubmit="return borrowSuccess(event)" class="styled-form">

      <div class="form-group">
        <label>User</label>
        <input type="text" name="user" placeholder="Enter user name" required>
      </div>

      <div class="form-group">
        <label>Book Title</label>
        <input type="text" name="book" placeholder="Enter book title" required>
      </div>

      <div class="form-group">
        <label>Borrow Date</label>
        <input type="date" name="date" required>
      </div>

      <button type="submit" class="btn-primary">Submit</button>

    </form>

    <!-- Success Message -->
    <div id="successMsg" class="success-box" style="display:none;">
      ✅ Book borrowed successfully!
    </div>

  </div>

</div>

<script>
function borrowSuccess(e){
  e.preventDefault();

  const msg = document.getElementById("successMsg");
  msg.style.display = "block";

  e.target.reset();

  setTimeout(() => {
    msg.style.display = "none";
  }, 2000);
}
</script>