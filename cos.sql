-- MySQL dump 10.14  Distrib 5.5.63-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: mlyl2019
-- ------------------------------------------------------
-- Server version	5.5.63-MariaDB

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
-- Table structure for table `hl_black_qq`
--

DROP TABLE IF EXISTS `hl_black_qq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hl_black_qq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qq` bigint(20) NOT NULL DEFAULT '0',
  `tel` int(11) NOT NULL DEFAULT '0',
  `wx` varchar(255) NOT NULL DEFAULT '0',
  `ali` varchar(255) NOT NULL DEFAULT '0',
  `remark` longtext NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_del` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1573 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;



--
-- 表的结构 `hl_user_login`
--

CREATE TABLE `hl_user_login` (
  `user_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `username` varchar(32) DEFAULT NULL COMMENT '账号',
  `password` char(32) DEFAULT NULL COMMENT '密码',
  `login_ip` char(15) DEFAULT NULL COMMENT '上次登录IP',
  `login_time` int(11) DEFAULT '0' COMMENT '上次登录时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '渠道状态 0-无效 1-有效',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



