-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 05:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `status` enum('Available','Borrowed') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `status`) VALUES
(1, 'Formula 1', 'Max Verstappen', 'Available'),
(2, 'Formula 2', 'Lewis Hamilton', 'Borrowed'),
(3, 'Formula 3', 'Charles Leclerc', 'Borrowed'),
(4, 'GT World Challenge', 'George Russel', 'Borrowed'),
(5, 'World Rally Championship', 'Carlos Sainz', 'Borrowed'),
(6, 'Formula E', 'Oscar Piastri', 'Borrowed'),
(7, '24 Hours of Le Mans', 'Kimi Antonelli', 'Available'),
(8, 'IndyCar Series', 'Max Verstappen', 'Borrowed'),
(9, 'Super Formula', 'Lewis Hamilton', 'Available'),
(10, 'NASCAR Cup Series', 'Charles Leclerc', 'Borrowed'),
(11, 'British Touring Car Championship', 'Checo Perez', 'Available'),
(12, 'World Touring Car Cup', 'Carlos Sainz', 'Borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Borrowed','Returned') DEFAULT 'Borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `user_name`, `book_id`, `borrow_date`, `return_date`, `status`) VALUES
(1, 'Kaizen', 1, '2026-04-25', NULL, 'Borrowed'),
(2, 'Kaizen', 1, '2026-12-12', NULL, 'Borrowed'),
(3, 'Sho', 1, '2026-05-05', NULL, 'Borrowed'),
(4, 'Bro', 5, '2026-05-07', NULL, 'Borrowed'),
(5, 'akay', 3, '2026-05-07', NULL, 'Borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Kaizen', 'Kaizen@gmail.com', '$2y$10$LKFSQlpDLGdxEDBXIy33g.t04FwF.kofNvpP9yKprG/SEAbiBnlJq'),
(2, 'Alan', 'Alan@gmail.com', '$2y$10$ikTbe/3YxKYhe2N.G4TDz.XSedMDfkqARmMiluO3j5/K9AQHJfPAi'),
(3, 'Hisen', 'Hazen@gmail.com', '$2y$10$q2/xCUW8IFSqoDeB9DEi0e8EXORWjiFVRgJOhdUGYdCqCvxlKDpAC'),
(4, 'Debon', 'Devon@gmail.com', '$2y$10$TjK3AK1W/OU/jNdIcD0zVuv2.c0LsnwFbm/UeqqC5eW1mGdOju8BK'),
(5, 'Sho', 'Sho@gmail.com', '$2y$10$EHeZS1hgDOnfKQ/IRS6JtOi/Hy5pBvNQXDXSxMHRI7NSHAAtxjpg2'),
(6, 'Vrayl', 'Vrayl@gmail.com', '$2y$10$PrNj3kTipHM.GbtHnFmBBOFdYycJCFpYLYqiwGlCDv.qV/CpfTcya'),
(7, 'Frenks', 'Frenks@gmail.com', '$2y$10$sPq53MnsOwxVRuDDd0CIdedePIAh4OTH96beH4ptsUhEk/GE2FAty'),
(13, 'Natnat', 'Natnat@gmail.com', '$2y$10$FDS/ZPLmLQaU69uluxPeUe8SCYpTbgaalVyQ9UUVORNkD0NFcQTlq'),
(14, 'Ayban', 'Ayban@gmail.com', '$2y$10$oI4CFUMqyxHt8SFbqXhIiez.qKATieuLzjYn9WZURhe9FyB27fkVu'),
(15, 'John', 'John@gmail.com', '$2y$10$YNRGk79DtgPOoL2q9BARn.nxPBM5Wf4iilSN6.1.tRkLte1U3yS7a'),
(16, 'Earl', 'Earl@gmail.com', '$2y$10$LwX3Nb8QA034vqsAa1tWj.PNbOBeGr0UpPOaDLE0hY0KPn12aCsfy'),
(17, 'Kai', 'Kai@gmail.com', '$2y$10$uj9aSdJxIKtdf3vr3zn6ne6vuWfvFY1iXjhaK.cKmiEKLNIwKFtxq'),
(18, 'Luffy', 'Luffy@gmail.com', '$2y$10$.YErb/hLyiSR2lsFwyVyaeWQZUyC6tr0q/XrDwRjyWZ/OzgN2LbpO'),
(19, 'Iskay', 'Iskay@gmail.com', '$2y$10$Z1.Wh4ShQUoDjwQQDoLpYupkdNqoOGqaZiQramrK.YiyqJHgQQF2C'),
(20, 'Sukuna', 'Sukuna@gmail.com', '$2y$10$2KedhqeawFATTFz/qqWL2OXRk40cCYKW0BBNNXzWrdGFIa1yKiXxG'),
(21, 'Gojo', 'Gojo@gmail.com', '$2y$10$Nv4y59gh5ybK1FUZdwE4E.LlVFy0GmwRdGOQWISZy4LkF7DMkhl5C'),
(22, 'Bro', 'Bro@gmail.com', '$2y$10$gMCD1d.S6jTIO9p6l.wxjeAcIgvQjo.V6weuYXi8ybbVU3SrdDnhW');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
