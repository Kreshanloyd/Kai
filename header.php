<?php session_start(); ?>

<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<div class="layout">

  <!-- SIDEBAR -->
  <?php include 'sidebar.php'; ?>

  <!-- MAIN AREA -->
  <div class="main-wrapper">

    <!-- TOPBAR -->
    <div class="topbar">
      <button onclick="toggleSidebar()" class="menu-btn">☰</button>

      <h2>Library System</h2>

      <div class="user-box">
        👤 <?= $_SESSION['user'] ?? 'Guest' ?>
      </div>
    </div>

    <!-- PAGE CONTENT START -->
    <div class="main-content">