<?php
session_start();

/* Initialize Books Data */
if (!isset($_SESSION['books'])) {

  $_SESSION['books'] = [
    [
      "id" => 1,
      "title" => "Harry Potter",
      "author" => "J.K. Rowling",
      "status" => "Available"
    ],
    [
      "id" => 2,
      "title" => "The Hobbit",
      "author" => "J.R.R. Tolkien",
      "status" => "Borrowed"
    ],
    [
      "id" => 3,
      "title" => "1984",
      "author" => "George Orwell",
      "status" => "Available"
    ]
  ];

  /* Track next ID (important for adding books later) */
  $_SESSION['next_id'] = 4;
}
?>