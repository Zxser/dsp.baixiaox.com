CREATE DATABASE  IF NOT EXISTS `adease` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `adease`;
-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 192.168.16.136    Database: adease
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.20-MariaDB

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
-- Table structure for table `operate_log`
--

DROP TABLE IF EXISTS `operate_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operate_log` (
  `uid` char(16) NOT NULL DEFAULT '',
  `actions` varchar(155) NOT NULL DEFAULT '',
  `remarks` text,
  `ip` bigint(20) NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL DEFAULT '',
  `group` varchar(16) DEFAULT '' COMMENT '用户所属组',
  `permissions` char(5) NOT NULL DEFAULT 'r',
  `language` varchar(50) DEFAULT '' COMMENT '用户使用的浏览器语言。',
  `os` varchar(50) DEFAULT '' COMMENT '用户操作系统 ',
  `device` varchar(50) DEFAULT '' COMMENT '用户使用设备',
  `is_del` tinyint(2) NOT NULL DEFAULT '0',
  `timer` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operate_log`
--

LOCK TABLES `operate_log` WRITE;
/*!40000 ALTER TABLE `operate_log` DISABLE KEYS */;
INSERT INTO `operate_log` VALUES ('1483863319667052','用户注册','尊敬的用户[李鹏]:您于2017-01-08注册成功。<a href=\"https://www.adease.com\">www.adease.com</a>',2130706433,'','','rw','',NULL,'',0,1483863319),('1483863319667052','用户登录','尊敬的用户[李鹏]:您于2017-01-08登录成功。<a href=\"https://www.adease.com\">www.adease.com</a>',2130706433,'','','rw','',NULL,'',0,1483863336),('1483863319667052','用户登录','尊敬的用户[李鹏]:您于2017-02-09登录成功。<a href=\"https://www.adease.com\">www.adease.com</a>',3232239617,'','','rw','',NULL,'',0,1486654258);
/*!40000 ALTER TABLE `operate_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-10 15:16:20
