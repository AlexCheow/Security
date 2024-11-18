-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 08:51 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `action` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `role`, `action`, `timestamp`) VALUES
(8, 3, 'staff', 'Added Product : Keyboard', '2024-11-12 15:27:35'),
(9, 3, 'staff', 'Deleted Product ID: 1', '2024-11-12 15:29:33'),
(10, 3, 'staff', 'Updated Product ID: 11', '2024-11-12 15:29:56'),
(11, 3, 'staff', 'Added stock adjustment ID: 12', '2024-11-12 15:33:51'),
(12, 1, 'admin', 'Added Product : Headset', '2024-11-12 15:55:24'),
(13, 1, 'admin', 'Updated Product ID: 12', '2024-11-12 15:55:49'),
(14, 1, 'admin', 'Updated Product ID: 11', '2024-11-12 15:56:44'),
(15, 1, 'admin', 'Deleted Product ID: 2', '2024-11-12 15:57:10'),
(16, 1, 'admin', 'Updated Product ID: 12', '2024-11-12 15:57:31'),
(17, 1, 'admin', 'Added stock adjustment ID: 13', '2024-11-12 15:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `created_at`, `updated_at`) VALUES
(11, 'Keyboard', 'Logitech Keyboard', 200.00, 100, '2024-11-12 15:27:35', '2024-11-12 15:56:44'),
(12, 'Headset', 'Razor Headset version 1', 250.00, 50, '2024-11-12 15:55:24', '2024-11-12 15:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `stockadjustmentdetails`
--

CREATE TABLE `stockadjustmentdetails` (
  `id` int(11) NOT NULL,
  `adjustment_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `adjustment_type` enum('add','subtract') NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockadjustmentdetails`
--

INSERT INTO `stockadjustmentdetails` (`id`, `adjustment_id`, `product_id`, `adjustment_type`, `quantity`) VALUES
(10, 12, 11, '', 100),
(11, 13, 12, '', 50);

-- --------------------------------------------------------

--
-- Table structure for table `stockadjustments`
--

CREATE TABLE `stockadjustments` (
  `id` int(11) NOT NULL,
  `adjustment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stockadjustments`
--

INSERT INTO `stockadjustments` (`id`, `adjustment_date`, `description`) VALUES
(12, '2024-11-11 16:00:00', 'Add stock 100'),
(13, '2024-11-11 16:00:00', 'Add Stock');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$CbMJOiwGoV1jxgW31FF6aOTzoZ4dhAUIvN.sPbrScYcaOO34pwoBG', 'admin', '2024-11-11 05:19:40'),
(3, 'staff', '$2y$10$RdooH557eCkRihTk806dYOJ79xiGl2p3v.c/dJOgwJiI4Goe7V6SG', 'staff', '2024-11-12 12:05:35'),
(4, 'jiade', '$2y$10$rouMToJEUMRZHf6TvfwMK.UAxIQIOneFvUH6ioJK/b3BykLUTdi7q', 'staff', '2024-11-14 03:12:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockadjustmentdetails`
--
ALTER TABLE `stockadjustmentdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adjustment_id` (`adjustment_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stockadjustments`
--
ALTER TABLE `stockadjustments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stockadjustmentdetails`
--
ALTER TABLE `stockadjustmentdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stockadjustments`
--
ALTER TABLE `stockadjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stockadjustmentdetails`
--
ALTER TABLE `stockadjustmentdetails`
  ADD CONSTRAINT `stockadjustmentdetails_ibfk_1` FOREIGN KEY (`adjustment_id`) REFERENCES `stockadjustments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stockadjustmentdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
