-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: symfony
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.16.04.1

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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D64992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_8D93D649A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_8D93D649C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(0,'admin','admin','agora.dev.test@gmail.com','agora.dev.test@gmail.com',1,'ZNQ47xY9i69QrnOR8XjqaLDt/U2ggZPEOsY7QIo6uSE','tzCv7nPocweDKNY+17zPVL1QFf77F/2PIYW1w7v0xiMiKUfkAbdK7wxaBIboAsB1+oIBzwATwARlIbsWca3cSg==','2018-05-31 11:46:05',NULL,NULL,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(1, 'test0', 'test0', '[test0]', '[test0]', 1, 'aE2iqQ17ih1rWhtk1EpydjQYKKVpeJ765MQW2d5TswA', 'S3YX9FTQwI2A98RbWkwMhxxBo5BkbBoyR6JkLbcLg2XTThMKJiKwb2glOmkBW92VQv/fYVl7ZksWsW6riuVlng==', '2020-03-14 16:26:49', NULL, NULL, 'a:1:{i:0;s:9:"ROLE_MODO";}'),
(2, 'test1', 'test1', '[test1]', '[test1]', 1, 'JThZGxwWxu02vXUB2n5.I669p3qNsIBi066EFLbvg60', '2mzwtCsUe8uqUVKMFhskMhNrED/AbCQ8Te6ssarAZoZvDHlsu1cFU0r3OBWHs4Niil84aA8PGwOtYUrtIsTUKA==', '2020-03-14 16:36:49', NULL, NULL, 'a:1:{i:0;s:9:"ROLE_MODO";}'),
(3, 'test2', 'test2', '[test2]', '[test2]', 1, 'POWQX5ux87uZ7IhG5p0WH5oj6IZ4oLwONerJ1f9wW60', '9LuVhLwqDHFPZ1n/0do3Vx4iQNBkXUIpDEcr1BvaKav+HhJnv3GqSo7HRNWl7yC366ZzAdKd0dmUYiwE5eg4Zw==', '2020-03-14 16:46:49', NULL, NULL, 'a:1:{i:0;s:9:"ROLE_MODO";}');

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

-- Dump completed on 2018-05-31 13:52:19
