-- MySQL dump 10.13  Distrib 8.1.0, for Linux (x86_64)
--
-- Host: localhost    Database: php-contact-db
-- ------------------------------------------------------
-- Server version	8.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP DATABASE IF EXISTS `php-contact-db`;
CREATE DATABASE `php-contact-db`;
USE `php-contact-db`;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `iduser` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_surname` varchar(255) NOT NULL,
  `user_email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `user_pw` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_date` varchar(255) DEFAULT NULL,
  `password_token` varchar(255) DEFAULT NULL,
  `password_date` int DEFAULT NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `user_email_UNIQUE` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `idcontact` int NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) NOT NULL,
  `contact_surname` varchar(255) NOT NULL,
  `contact_email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `user_iduser` int NOT NULL,
  PRIMARY KEY (`idcontact`,`user_iduser`),
  KEY `fk_contact_user_idx` (`user_iduser`),
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Joelle','Moureu','joellecoudray@hotmail.fr','$2y$10$teG0U07iKYrh5uIdQjdbleYQZr74gQJm4pjt8NuJPPPX//EoEyI3m',NULL,NULL,NULL,NULL),(2,'Pippo','Pluto','Pippo.pluto@gmail.com','$2y$10$ToNLE0zLWyekqkT7XBIzbOOc6hV23HE6ZQUgGO1F7STvA83wjGkAS',NULL,NULL,NULL,NULL),(3,'Test','Test','test@gmail.com','$2y$10$AZu4rDApDwuVDaLwNwwHKOTemTInzVopGwkDY07LHa1TFFforyMua',NULL,NULL,NULL,0),(4,'Truc','Machin','trucmachin@yahoo.com','$2y$10$5xSd73eZMu0llwrXuYw5VejGr6OZqxWYpTeDVOk/MlzDpj7jj7h4W',NULL,NULL,NULL,NULL),(5,'autre','test','testencore@gmail.com','$2y$10$3YNyxA9rivbmjm6TKdVuTO.6BxibbD63uDiU2FRubwyIJZ7Cf63mu',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,'Pippo','Pluto','pippo.pluto@gmail.com',1),(2,'Pippo','Pluto2','pippo2.pluto@gmail.com',1),(3,'Test','Test','test@hotmail.fr',1),(4,'Pippo','Pluto','plutopippo70@gmail.com',3),(5,'Test','Test','test@gmail.com',3);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;


-- Dump completed on 2023-09-19 11:30:29
