-- MySQL dump 10.13  Distrib 5.1.67, for pc-linux-gnu (x86_64)
--
-- Host: localhost    Database: church
-- ------------------------------------------------------
-- Server version	5.1.67-log

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
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varbinary(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','testpass');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `church_id` bigint(20) unsigned NOT NULL,
  `cat_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_on` datetime NOT NULL,
  `is_visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `html` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'<a href=\"http://google.com/\"><img src=\"http://freelancer-desk.com/bprojects/clist/img/samp.png\" /></a>'),(3,'<a href=\"http://google.com/\"><img src=\"http://freelancer-desk.com/bprojects/clist/img/samp2.png\" /></a>'),(4,'<a href=\"http://karateofmansfield.com/\"><img src=\"http://www.karateofmansfield.com/images/logo.png\" /></a>');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners_churches`
--

DROP TABLE IF EXISTS `banners_churches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners_churches` (
  `banner_id` bigint(20) unsigned NOT NULL,
  `church_id` bigint(20) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners_churches`
--

LOCK TABLES `banners_churches` WRITE;
/*!40000 ALTER TABLE `banners_churches` DISABLE KEYS */;
INSERT INTO `banners_churches` VALUES (1,19),(1,7),(1,6),(1,2),(3,24),(3,19),(3,29),(3,28),(4,29);
/*!40000 ALTER TABLE `banners_churches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cats`
--

DROP TABLE IF EXISTS `cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `col` bigint(20) unsigned NOT NULL,
  `row` bigint(20) unsigned NOT NULL,
  `price` decimal(6,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cats`
--

LOCK TABLES `cats` WRITE;
/*!40000 ALTER TABLE `cats` DISABLE KEYS */;
INSERT INTO `cats` VALUES (1,0,'free',1,1,'0.00'),(2,1,'goods',0,0,'0.00'),(3,1,'services',0,0,'0.00'),(4,1,'pets',0,0,'5.75'),(5,1,'wanted free',0,0,'0.00'),(6,0,'community',1,2,'0.00'),(7,6,'announcements',0,0,'0.00'),(8,6,'volunteer',0,0,'0.00'),(10,0,'relationship',1,3,'0.00'),(11,10,'friendship',0,0,'0.00'),(12,10,'men seeking men',0,0,'0.00'),(13,10,'women seeking women',0,0,'0.00'),(14,0,'real estate',1,4,'0.00'),(15,14,'residential 4 sale',0,0,'0.00'),(16,14,'commercial 4 sale',0,0,'0.00'),(17,14,'rooms / shared',0,0,'0.00'),(18,14,'need a shelter',0,0,'0.00'),(19,0,'for sale',2,1,'0.00'),(20,19,'barter',0,0,'0.00'),(21,19,'automotive',0,0,'0.00'),(22,19,'furniture',0,0,'0.00'),(23,19,'electronics',0,0,'0.00'),(24,19,'pets',0,0,'0.00'),(25,19,'garage sale',0,0,'0.00'),(26,19,'general for sale',0,0,'0.00'),(27,19,'general wanted',0,0,'0.00'),(28,0,'services',2,2,'0.00'),(29,28,'athletic',0,0,'0.00'),(30,28,'beauty',0,0,'0.00'),(31,28,'computer',0,0,'0.00'),(32,28,'creative',0,0,'0.00'),(33,28,'event',0,0,'0.00'),(34,28,'realtor',0,0,'0.00'),(35,28,'financial',0,0,'0.00'),(36,28,'legal',0,0,'0.00'),(37,28,'labor',0,0,'0.00'),(38,28,'lessons',0,0,'0.00'),(39,28,'repair',0,0,'0.00'),(40,0,'jobs',3,1,'0.00'),(41,40,'job offered',0,0,'0.00'),(42,40,'job wanted',0,0,'0.00');
/*!40000 ALTER TABLE `cats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `churches`
--

DROP TABLE IF EXISTS `churches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `churches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `city_id` (`city_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `churches`
--

LOCK TABLES `churches` WRITE;
/*!40000 ALTER TABLE `churches` DISABLE KEYS */;
INSERT INTO `churches` VALUES (1,1,'Calvary Chapel'),(2,2,'Calvary Community Church'),(3,3,'Central Christian Church of Arizona'),(4,4,'Christ\'s Church of the Valley'),(5,3,'The Living Word Bible Church'),(6,2,'Phoenix First Assembly of God'),(7,5,'Scottsdale Bible Church'),(8,7,'Acts Full Gospel Church'),(9,6,'Angelus Temple'),(10,8,'Bayside Covenant Church'),(11,6,'Bel Air Presbyterian Church'),(12,9,'Bethel Church'),(13,10,'Big Valley Grace Community Church'),(14,11,'Calvary Chapel Chino Valley'),(15,12,'Calvary Chapel Costa Mesa'),(16,13,'Calvary Chapel Downey'),(17,14,'Calvary Chapel Golden Springs'),(18,15,'Calvary Chapel South Bay'),(19,16,'Cathedral of Faith'),(20,17,'Church of the Good Shepherd'),(21,6,'First African Methodist Episcopal Church'),(22,6,'West Ageles Cathedral'),(23,6,'Young Nak Presbyterian Church'),(24,16,'Jubilee Christian Center'),(25,17,'Horizon Christian Fellowship'),(26,17,'Maranatha Chappel'),(27,17,'The Rock Church'),(28,18,'creekwood church'),(29,20,'<p>church of monkey butts</ p>( 1956 cantrell rd. #177)');
/*!40000 ALTER TABLE `churches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `state_id` (`state_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,1,'Tucson'),(2,1,'Phoenix'),(3,1,'Mesa'),(4,1,'Peoria'),(5,1,'Scottsdale'),(6,2,'Los Angeles'),(7,2,'Oakland'),(8,2,'Roseville'),(9,2,'Redding'),(10,2,'Modesto'),(11,2,'Chino'),(12,2,'Costa Mesa'),(13,2,'Downey'),(14,2,'Diamond Bar'),(15,2,'Gardena'),(16,2,'San Jose'),(17,2,'San Diego'),(18,4,'mansfield'),(19,1,'little rock'),(20,5,'little rock');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `church_id` bigint(20) unsigned NOT NULL,
  `evt_from` date NOT NULL,
  `evt_to` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'Arizona'),(2,'California'),(4,'texas'),(5,'arkansas');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `static`
--

DROP TABLE IF EXISTS `static`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static` (
  `skey` varchar(255) NOT NULL,
  `sval` text NOT NULL,
  UNIQUE KEY `skey` (`skey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `static`
--

LOCK TABLES `static` WRITE;
/*!40000 ALTER TABLE `static` DISABLE KEYS */;
INSERT INTO `static` VALUES ('about','About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. \r\n\r\n\r\n<iframe width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/5gUKZpgVfMo\" frameborder=\"0\" allowfullscreen></iframe> \r\n\r\nAbout page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. About page text. '),('terms','Terms page text'),('feature','Feature page text'),('feedback','Feedback page text'),('partner','Partner page text'),('contact','Contact page text'),('admin_email','md5xxx1@gmail.com');
/*!40000 ALTER TABLE `static` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-02 23:38:09
