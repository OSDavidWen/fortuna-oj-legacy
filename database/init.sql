-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: OnlineJudgeSystem
-- ------------------------------------------------------
-- Server version	5.5.28-1

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
-- Table structure for table `Board`
--

DROP TABLE IF EXISTS `Board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Board` (
  `idPost` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) DEFAULT '0',
  `title` varchar(64) NOT NULL,
  `content` mediumtext NOT NULL,
  `replyTo` int(11) NOT NULL DEFAULT '0',
  `postTime` datetime NOT NULL,
  PRIMARY KEY (`idPost`),
  KEY `fk_table1_ProblemSet1_idx` (`pid`),
  KEY `fk_Board_User1_idx` (`uid`),
  KEY `title_INDEX` (`title`),
  CONSTRAINT `fk_Board_User1` FOREIGN KEY (`uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_table1_ProblemSet1` FOREIGN KEY (`pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Board`
--

LOCK TABLES `Board` WRITE;
/*!40000 ALTER TABLE `Board` DISABLE KEYS */;
/*!40000 ALTER TABLE `Board` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Categorization`
--

DROP TABLE IF EXISTS `Categorization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Categorization` (
  `pid` int(11) NOT NULL,
  `idCategory` int(11) NOT NULL,
  PRIMARY KEY (`pid`,`idCategory`),
  KEY `fk_ProblemSet_has_Category_Category1_idx` (`idCategory`),
  KEY `fk_ProblemSet_has_Category_ProblemSet1_idx` (`pid`),
  CONSTRAINT `fk_ProblemSet_has_Category_Category1` FOREIGN KEY (`idCategory`) REFERENCES `Category` (`idCategory`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ProblemSet_has_Category_ProblemSet1` FOREIGN KEY (`pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Categorization`
--

LOCK TABLES `Categorization` WRITE;
/*!40000 ALTER TABLE `Categorization` DISABLE KEYS */;
/*!40000 ALTER TABLE `Categorization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `idCategory` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`idCategory`),
  KEY `name_INDEX` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contest`
--

DROP TABLE IF EXISTS `Contest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Contest` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `description` mediumtext NOT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `contestMode` enum('OI','ACM','Codeforces','codejam') DEFAULT 'OI',
  `isShowed` tinyint(1) DEFAULT '0',
  `language` set('C','C++','C++11','Pascal','Java','Python') DEFAULT 'C,C++,Pascal',
  `private` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contest`
--

LOCK TABLES `Contest` WRITE;
/*!40000 ALTER TABLE `Contest` DISABLE KEYS */;
/*!40000 ALTER TABLE `Contest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contest_has_ProblemSet`
--

DROP TABLE IF EXISTS `Contest_has_ProblemSet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Contest_has_ProblemSet` (
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `scoreDecreaseSpeed` int(11) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`cid`,`pid`),
  KEY `fk_Contest_has_ProblemSet_ProblemSet1_idx` (`pid`),
  KEY `fk_Contest_has_ProblemSet_Contest1_idx` (`cid`),
  CONSTRAINT `fk_Contest_has_ProblemSet_Contest1` FOREIGN KEY (`cid`) REFERENCES `Contest` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Contest_has_ProblemSet_ProblemSet1` FOREIGN KEY (`pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contest_has_ProblemSet`
--

LOCK TABLES `Contest_has_ProblemSet` WRITE;
/*!40000 ALTER TABLE `Contest_has_ProblemSet` DISABLE KEYS */;
/*!40000 ALTER TABLE `Contest_has_ProblemSet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Declaration`
--

DROP TABLE IF EXISTS `Declaration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Declaration` (
  `idDeclaration` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `declaration` mediumtext NOT NULL,
  `postTime` datetime NOT NULL,
  PRIMARY KEY (`idDeclaration`),
  KEY `fk_Declaration_Contest_has_ProblemSet1` (`cid`,`pid`),
  CONSTRAINT `fk_Declaration_Contest_has_ProblemSet1` FOREIGN KEY (`cid`, `pid`) REFERENCES `Contest_has_ProblemSet` (`cid`, `pid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Declaration`
--

LOCK TABLES `Declaration` WRITE;
/*!40000 ALTER TABLE `Declaration` DISABLE KEYS */;
/*!40000 ALTER TABLE `Declaration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Group`
--

DROP TABLE IF EXISTS `Group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`gid`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Group`
--

LOCK TABLES `Group` WRITE;
/*!40000 ALTER TABLE `Group` DISABLE KEYS */;
/*!40000 ALTER TABLE `Group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Group_has_Task`
--

DROP TABLE IF EXISTS `Group_has_Task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Group_has_Task` (
  `Group_gid` int(11) NOT NULL,
  `Task_idTask` int(11) NOT NULL,
  PRIMARY KEY (`Group_gid`,`Task_idTask`),
  KEY `fk_Group_has_Task_Task1` (`Task_idTask`),
  KEY `fk_Group_has_Task_Group1` (`Group_gid`),
  CONSTRAINT `fk_Group_has_Task_Group1` FOREIGN KEY (`Group_gid`) REFERENCES `Group` (`gid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_has_Task_Task1` FOREIGN KEY (`Task_idTask`) REFERENCES `Task` (`idTask`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Group_has_Task`
--

LOCK TABLES `Group_has_Task` WRITE;
/*!40000 ALTER TABLE `Group_has_Task` DISABLE KEYS */;
/*!40000 ALTER TABLE `Group_has_Task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Group_has_User`
--

DROP TABLE IF EXISTS `Group_has_User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Group_has_User` (
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`gid`,`uid`),
  KEY `fk_Group_has_User_User1_idx` (`uid`),
  KEY `fk_Group_has_User_Group1_idx` (`gid`),
  CONSTRAINT `fk_Group_has_User_Group1` FOREIGN KEY (`gid`) REFERENCES `Group` (`gid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_has_User_User1` FOREIGN KEY (`uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Group_has_User`
--

LOCK TABLES `Group_has_User` WRITE;
/*!40000 ALTER TABLE `Group_has_User` DISABLE KEYS */;
/*!40000 ALTER TABLE `Group_has_User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Mail`
--

DROP TABLE IF EXISTS `Mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Mail` (
  `idMail` int(11) NOT NULL AUTO_INCREMENT,
  `from_uid` int(11) NOT NULL,
  `to_uid` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `content` mediumtext,
  `sendTime` datetime NOT NULL,
  `isRead` tinyint(1) DEFAULT NULL,
  `readTime` datetime DEFAULT NULL,
  PRIMARY KEY (`idMail`),
  KEY `fk_Mail_User1_idx` (`from_uid`),
  KEY `fk_Mail_User2_idx` (`to_uid`),
  CONSTRAINT `fk_Mail_User1` FOREIGN KEY (`from_uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Mail_User2` FOREIGN KEY (`to_uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Mail`
--

LOCK TABLES `Mail` WRITE;
/*!40000 ALTER TABLE `Mail` DISABLE KEYS */;
/*!40000 ALTER TABLE `Mail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Miscellaneousness`
--

DROP TABLE IF EXISTS `Miscellaneousness`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Miscellaneousness` (
  `noticeBoard` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Miscellaneousness`
--

LOCK TABLES `Miscellaneousness` WRITE;
/*!40000 ALTER TABLE `Miscellaneousness` DISABLE KEYS */;
/*!40000 ALTER TABLE `Miscellaneousness` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ProblemSet`
--

DROP TABLE IF EXISTS `ProblemSet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProblemSet` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `problemDescription` mediumtext NOT NULL,
  `inputDescription` mediumtext NOT NULL,
  `outputDescription` mediumtext NOT NULL,
  `inputSample` mediumtext NOT NULL,
  `outputSample` mediumtext NOT NULL,
  `dataConstraint` mediumtext NOT NULL,
  `dataConfiguration` mediumtext NOT NULL,
  `hint` mediumtext,
  `source` varchar(64) DEFAULT NULL,
  `submitCount` int(11) DEFAULT '0',
  `solvedCount` int(11) DEFAULT '0',
  `isShowed` tinyint(1) DEFAULT '0',
  `codeSizeLimit` int(11) DEFAULT '-1',
  `scoreSum` int(11) DEFAULT '0',
  PRIMARY KEY (`pid`),
  KEY `title_INDEX` (`title`),
  KEY `source_INDEX` (`source`),
  KEY `score_INDEX` (`scoreSum`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ProblemSet`
--

LOCK TABLES `ProblemSet` WRITE;
/*!40000 ALTER TABLE `ProblemSet` DISABLE KEYS */;
/*!40000 ALTER TABLE `ProblemSet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `School`
--

DROP TABLE IF EXISTS `School`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `School` (
  `idSchool` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`idSchool`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `School`
--

LOCK TABLES `School` WRITE;
/*!40000 ALTER TABLE `School` DISABLE KEYS */;
/*!40000 ALTER TABLE `School` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Solution`
--

DROP TABLE IF EXISTS `Solution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Solution` (
  `idSolution` int(11) NOT NULL AUTO_INCREMENT,
  `solution` mediumtext NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `postTime` datetime NOT NULL,
  PRIMARY KEY (`idSolution`),
  KEY `fk_Solution_ProblemSet1_idx` (`pid`),
  KEY `fk_Solution_User1_idx` (`uid`),
  CONSTRAINT `fk_Solution_ProblemSet1` FOREIGN KEY (`pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Solution_User1` FOREIGN KEY (`uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Solution`
--

LOCK TABLES `Solution` WRITE;
/*!40000 ALTER TABLE `Solution` DISABLE KEYS */;
/*!40000 ALTER TABLE `Solution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Submission`
--

DROP TABLE IF EXISTS `Submission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Submission` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `cid` int(11) DEFAULT NULL,
  `code` mediumtext NOT NULL,
  `codeLength` int(11) NOT NULL,
  `language` enum('C','C++','C++11','Pascal','Java','Python') NOT NULL,
  `status` int(11) DEFAULT '-1',
  `judgeResult` text,
  `time` int(11) DEFAULT NULL,
  `memory` int(11) DEFAULT NULL,
  `score` double DEFAULT '0',
  `submitTime` datetime DEFAULT NULL,
  `isShowed` tinyint(1) DEFAULT '1',
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`sid`),
  KEY `fk_Submission_User1_idx` (`uid`),
  KEY `fk_Submission_ProblemSet1_idx` (`pid`),
  KEY `language_INDEX` (`language`),
  KEY `status_INDEX` (`status`),
  KEY `fk_Submission_Contest1` (`cid`),
  CONSTRAINT `fk_Submission_Contest1` FOREIGN KEY (`cid`) REFERENCES `Contest` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_ProblemSet1` FOREIGN KEY (`pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_User1` FOREIGN KEY (`uid`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Submission`
--

LOCK TABLES `Submission` WRITE;
/*!40000 ALTER TABLE `Submission` DISABLE KEYS */;
/*!40000 ALTER TABLE `Submission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Task`
--

DROP TABLE IF EXISTS `Task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Task` (
  `idTask` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `startTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `description` mediumtext,
  PRIMARY KEY (`idTask`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Task`
--

LOCK TABLES `Task` WRITE;
/*!40000 ALTER TABLE `Task` DISABLE KEYS */;
/*!40000 ALTER TABLE `Task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Task_has_ProblemSet`
--

DROP TABLE IF EXISTS `Task_has_ProblemSet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Task_has_ProblemSet` (
  `Task_idTask` int(11) NOT NULL,
  `ProblemSet_pid` int(11) NOT NULL,
  PRIMARY KEY (`Task_idTask`,`ProblemSet_pid`),
  KEY `fk_Task_has_ProblemSet_ProblemSet1` (`ProblemSet_pid`),
  KEY `fk_Task_has_ProblemSet_Task1` (`Task_idTask`),
  CONSTRAINT `fk_Task_has_ProblemSet_ProblemSet1` FOREIGN KEY (`ProblemSet_pid`) REFERENCES `ProblemSet` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Task_has_ProblemSet_Task1` FOREIGN KEY (`Task_idTask`) REFERENCES `Task` (`idTask`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Task_has_ProblemSet`
--

LOCK TABLES `Task_has_ProblemSet` WRITE;
/*!40000 ALTER TABLE `Task_has_ProblemSet` DISABLE KEYS */;
/*!40000 ALTER TABLE `Task_has_ProblemSet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Team`
--

DROP TABLE IF EXISTS `Team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Team` (
  `idTeam` int(11) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `idParticipant0` int(11) NOT NULL,
  `idParticipant1` int(11) DEFAULT '0',
  `idParticipant2` int(11) DEFAULT '0',
  `cid` int(11) NOT NULL,
  `registrationTime` datetime DEFAULT NULL,
  `score` int(11) DEFAULT '0',
  `penalty` int(11) DEFAULT '0',
  `isFormal` tinyint(1) NOT NULL,
  PRIMARY KEY (`idTeam`),
  KEY `fk_Team_Contest1` (`cid`),
  KEY `fk_Team_User1` (`idParticipant0`),
  KEY `fk_Team_User2` (`idParticipant1`),
  KEY `fk_Team_User3` (`idParticipant2`),
  CONSTRAINT `fk_Team_Contest1` FOREIGN KEY (`cid`) REFERENCES `Contest` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User1` FOREIGN KEY (`idParticipant0`) REFERENCES `User` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User2` FOREIGN KEY (`idParticipant1`) REFERENCES `User` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User3` FOREIGN KEY (`idParticipant2`) REFERENCES `User` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Team`
--

LOCK TABLES `Team` WRITE;
/*!40000 ALTER TABLE `Team` DISABLE KEYS */;
/*!40000 ALTER TABLE `Team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `description` mediumtext,
  `email` varchar(128) DEFAULT NULL,
  `idSchool` int(11) DEFAULT NULL,
  `isEnabled` tinyint(1) NOT NULL DEFAULT '0',
  `submitCount` int(11) DEFAULT '0',
  `solvedCount` int(11) DEFAULT '0',
  `acCount` int(11) DEFAULT '0',
  `priviledge` enum('user','admin') NOT NULL DEFAULT 'user',
  `lastPage` int(11) NOT NULL DEFAULT '1',
  `language` enum('C','C++','C++11','Pascal','Java','Python') NOT NULL DEFAULT 'C++',
  `avatar` mediumtext,
  `userPicture` varchar(128) DEFAULT NULL,
  `showCategory` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `uid_UNIQUE` (`uid`),
  KEY `fk_User_School1_idx` (`idSchool`),
  KEY `name_INDEX` (`name`),
  CONSTRAINT `fk_User_School1` FOREIGN KEY (`idSchool`) REFERENCES `School` (`idSchool`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,'root','2adba4c447f5e8027572b4e2212f3c3e',NULL,'',NULL,0,0,3,1,'admin',1,'C++',NULL,NULL,1);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-16 10:36:59
