-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 16, 2025 at 03:22 PM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sports_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `service_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_bookings_slot` (`service_id`,`booking_date`,`start_time`,`end_time`),
  KEY `user_id` (`user_id`),
 KEY `service_id` (`service_id`),
  CONSTRAINT `bookings_valid_time` CHECK ((`start_time` < `end_time`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `service_id`, `booking_date`, `start_time`, `end_time`, `customer_name`, `customer_phone`, `status`, `created_at`) VALUES
(1, 5, 3, '2025-12-17', '18:00:00', '20:00:00', 'raisal', '01000', 'confirmed', '2025-12-16 14:58:06'),
(2, 5, 3, '2025-12-17', '20:00:00', '22:00:00', 'raisal', '01010010', 'confirmed', '2025-12-16 15:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_hour` decimal(10,2) NOT NULL,
  `status` enum('available','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `type`, `price_per_hour`, `status`) VALUES
(1, 'Futsal Court 1', 'Futsal', 50.00, 'available'),
(2, 'Futsal Court 2', 'Futsal', 50.00, 'available'),
(3, 'Badminton Court 1', 'Badminton', 20.00, 'available'),
(4, 'Badminton Court 2', 'Badminton', 50.00, 'available'),
(5, 'Basketball Court 1', 'Basketball', 25.00, 'available'),
(6, 'Tennis Court 1', 'Tennis', 20.00, 'available'),
(7, 'Tennis Court 2', 'Tennis', 20.00, 'available'),
(8, 'Pickleball Court 1', 'Pickleball', 30.00, 'available'),
(9, 'Pickleball Court 2', 'Pickleball', 30.00, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'sham', 'shamdanial11@yahoo.com', '$2y$10$VQ2tIGNMaYIfMq7gjcGYBuF1SEn7lhhGzzec0L3GigA3VNhzvumSm', 'admin', '2025-12-06 06:34:42'),
(4, 'ihsan', 'ihsanhayyan2807@gmail.com', '$2y$10$7th2l3aib8UcQNqn/oFe7uAs8BptFjnuBpJzcAr3.kQ.LExCiTLJq', 'admin', '2025-12-16 13:57:07'),
(5, 'raisal', 'aziem@gmail.com', '$2y$10$naQKFlwiRMjYw1PO8tb/M.JbdeJTxLFv7IBcSD2J8TYEq2Qc5q2UW', 'customer', '2025-12-16 14:41:28');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
