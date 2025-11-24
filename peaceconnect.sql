-- xampp-lite https://sourceforge.net/projects/xampplite/
--
-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: peaceconnect
-- ------------------------------------------------------
-- Server version 	5.5.5-10.4.8-MariaDB
-- Date: Mon, 24 Nov 2025 17:13:21 +0100

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cause`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cause` (
  `id_cause` int(20) NOT NULL,
  `nom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(155) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL,
  PRIMARY KEY (`id_cause`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cause`
--


-- Dumped table `cause` with 0 row(s)
--

--
-- Table structure for table `don`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `don` (
  `id_don` int(20) NOT NULL,
  `montant` decimal(20,0) NOT NULL,
  `devise` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_don` datetime(6) NOT NULL,
  `donateur_nom` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `donateur_email` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `methode_paiments` int(20) NOT NULL,
  `message` int(255) NOT NULL,
  PRIMARY KEY (`id_don`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `don`
--


-- Dumped table `don` with 0 row(s)
--

--
-- Table structure for table `sign_in`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sign_in` (
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sign_in`
--


-- Dumped table `sign_in` with 0 row(s)
--

--
-- Table structure for table `sign_up`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sign_up` (
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `verify_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sign_up`
--

INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('','','','');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('achref','obbachref178@gmail.c','2566','2566');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('achref obba','achrefka256@yahoo.fr','256','256');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('bingo','bingo@esprit.tn','bingo','bingo');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('damn','damn@damn.tn','damn','damn');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('daz','obbaachref178@gmail.','747','747');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('fdada','','','');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('it works','itwork@gmail.com','it','it');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('reg','ger@gmail.com','ggg','ggg');
INSERT INTO `sign_up` (`name`, `email`, `password`, `verify_password`) VALUES ('zinox 99','obbaachref178@gmail.','77','77');

-- Dumped table `sign_up` with 10 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Mon, 24 Nov 2025 17:13:21 +0100
