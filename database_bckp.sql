-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 09:13 AM
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `userLogin` varchar(255) NOT NULL,
  `userPass` varchar(255) NOT NULL,
  `userName` varchar(255) DEFAULT NULL,
  `userSurname` varchar(255) DEFAULT NULL,
  `userEmail` varchar(255) DEFAULT NULL,
  `userPrivilege` int(11) DEFAULT NULL,
  `joiningDate` datetime DEFAULT NULL,
  `recordId` int(11) DEFAULT NULL,
  `blocked` tinyint(4) DEFAULT 0,
  `first` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `userLogin`, `userPass`, `userName`, `userSurname`, `userEmail`, `userPrivilege`, `joiningDate`, `recordId`, `blocked`, `first`) VALUES
(1, 'admin', '$2y$10$INNqRnEPi09OuaYOYeVxq.XAll584ZPjlzpLdNGs4iuXELzKt8Jv2', 'Adam', 'Admin', 'admin@adminowski.pl', 1, '2026-02-27 10:00:00', NULL, 0, 0),
(37, 'jan', '$2y$10$fT1IhpKX552SZHwNMI3rUe3nzANLULGo3RMgbpEg3a2CRToAUx7Uq', 'Jan', 'Kowalski', 'marek.bakalarczuk@gmail.com', 6, '2026-02-27 16:52:36', 1, 0, 1),
(38, 'adrian@zarzeczny.pl', '$2y$10$jvxTvmIQXRh2v3KNzWDMk.A9Oq3l5fRgXt6zhf5fnq/aiAyy/3kfm', 'Adrian', 'Zarzeczny', 'adrian@zarzeczny.pl', 6, '2026-02-27 16:53:54', 2, 0, 1),
(39, 'krzysztof@zajkowski.pl', '$2y$10$KYFdh.jJ228U5NQDg8djCebhLrTQ4P6qTpS3uww8fwXuw2nEPICNO', 'Krzysztof', 'Zajkowski', 'krzysztof@zajkowski.pl', 6, '2026-02-27 16:55:09', 3, 0, 1),
(40, 'mentor1', '$2y$10$INNqRnEPi09OuaYOYeVxq.XAll584ZPjlzpLdNGs4iuXELzKt8Jv2', 'Marek', 'Mentorski', 'marek@mentorski.pl', 5, '2026-02-27 17:02:04', 1, 0, 0),
(41, 'andrzej@adamczewski.pl', '$2y$10$IaWcj.pC3/T8meOb.XXX9ej1K3ltazYaGm4A4yuXFu5wKGyARaRZu', 'Andrzej', 'Adamczewski', 'andrzej@adamczewski.pl', 5, '2026-02-27 17:06:04', 2, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `userEmail` (`userEmail`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
