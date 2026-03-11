-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 02:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exampleapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationId` int(11) NOT NULL,
  `sender` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `recipient` int(11) DEFAULT NULL,
  `attachement` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notificationId`, `sender`, `subject`, `message`, `date`, `recipient`, `attachement`) VALUES
(1, 41, 'New session', 'New session planned. \r\n2026-02-27 at 12:00 with Andrzej Adamczewski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-02-27 18:01:54', 3, NULL),
(2, 40, 'New session', 'New session planned. \r\n2026-02-27 at 18:00 with Marek Mentorski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-02-27 18:04:45', 1, NULL),
(3, 41, 'New session', 'New session planned. \r\n2026-02-27 at 18:00 with Andrzej Adamczewski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-02-27 18:05:01', 2, NULL),
(4, 41, 'New session', 'New session planned. \r\n2026-02-27 at 18:00 with Andrzej Adamczewski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-02-27 18:05:14', 3, NULL),
(5, 40, 'New session', 'New session planned. \r\n2026-02-28 at 19:00 with Marek Mentorski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-02-28 18:33:27', 1, NULL),
(6, 40, 'New session', 'New session planned. \r\n2026-03-01 at 14:30 with Marek Mentorski \r\n\r\nTo accept, please login to MyELAB and go to Sessions section', '2026-03-01 14:42:38', 1, NULL),
(7, 40, 'fhfghfghfgh', 'hgfhfgh', '2026-03-01 15:03:41', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userLogin` varchar(255) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_token_hash` (`token_hash`),
  KEY `idx_login_used` (`userLogin`, `used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
