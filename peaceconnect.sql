-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 09:36 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `peaceconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `cause`
--

CREATE TABLE `cause` (
  `id_cause` int(20) NOT NULL,
  `nom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `don`
--

CREATE TABLE `don` (
  `id_don` int(20) NOT NULL,
  `montant` decimal(20,0) NOT NULL,
  `devise` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_don` datetime(6) NOT NULL,
  `donateur_nom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `donateur_email` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `methode_paiments` int(20) NOT NULL,
  `message` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sign_up`
--

CREATE TABLE `sign_up` (
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `verify_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sign_up`
--

INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`, `reset_token`, `token_expiry`) VALUES
('achref', 'obbachref178@gmail.c', '2566', '2566', NULL, NULL),
('achref obba', 'achrefka256@yahoo.fr', '256', '256', 'b8cb52ade1634f0c7dffd8c6c2c2327ef85afb8c52687c7e5431e44f12189713', '2025-12-01 22:03:27'),
('bingo', 'bingo@esprit.tn', 'ooo', 'ooo', '01406756dc2e1601392fba95e5bd5725e3063ddbe4284fd35e82e395fe9d446f', '2025-12-01 22:05:37'),
('damn', 'damn@damn.tn', 'damn', 'damn', 'acf600017d259a3e674e1313ee307fdd075ec7c5a84b639fea0feb68d41116f0', '2025-12-01 21:48:47'),
('daz', 'obbaachref178@gmail.', '747', '747', NULL, NULL),
('HERE', 'here@esprit.tn', 'aaa', 'aaa', NULL, NULL),
('it works', 'itwork@gmail.com', 'it', 'it', NULL, NULL),
('mouhamed', 'mouhamed@esprit.tn', 'achrefsemnyo', 'achrefsemnyo', NULL, NULL),
('reg', 'ger@gmail.com', '1234', '1234', NULL, NULL),
('test', 'tttt@gmail.com', 'aze', 'aze', NULL, NULL),
('zinox 99', 'obbaachref178@gmail.', '77', '77', NULL, NULL);

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
-- Indexes for table `sign_up`
--
ALTER TABLE `sign_up`
  ADD PRIMARY KEY (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
