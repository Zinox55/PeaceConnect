-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 10:21 PM
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
-- Database: `mvc_charity`
--

CREATE DATABASE IF NOT EXISTS `mvc_charity` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mvc_charity`;

-- --------------------------------------------------------

--
-- Table structure for table `cause`
--

CREATE TABLE `cause` (
  `id_cause` int(11) NOT NULL,
  `nom_cause` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `don`
--

CREATE TABLE `don` (
  `id_don` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `devise` varchar(3) NOT NULL,
  `date_don` datetime NOT NULL DEFAULT current_timestamp(),
  `donateur_nom` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `methode_paiement` varchar(50) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `donateur_email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `don`
--

INSERT INTO `don` (`id_don`, `montant`, `devise`, `date_don`, `donateur_nom`, `message`, `methode_paiement`, `transaction_id`, `donateur_email`) VALUES
(1, 22.00, 'dt', '2025-11-22 00:00:00', 'gre', '', 'card', NULL, 'eh@gg.k'),
(2, 22.00, 'dt', '2025-11-14 00:00:00', 'gre', '', 'paypal', NULL, 'eh@gg.k'),
(3, 77.00, 'dt', '2025-11-13 00:00:00', 'yht', '', 'card', NULL, 'tytr@tu.o'),
(4, 55.00, 'dt', '2025-11-08 00:00:00', 'yht', '', 'paypal', NULL, 'tytr@tu.o');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cause`
--
ALTER TABLE `cause`
  ADD PRIMARY KEY (`id_cause`);

--
-- Indexes for table `don`
--
ALTER TABLE `don`
  ADD PRIMARY KEY (`id_don`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cause`
--
ALTER TABLE `cause`
  MODIFY `id_cause` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `don`
--
ALTER TABLE `don`
  MODIFY `id_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
