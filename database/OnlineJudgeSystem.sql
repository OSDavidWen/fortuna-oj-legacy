SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `OnlineJudgeSystem` DEFAULT CHARACTER SET utf8 ;
USE `OnlineJudgeSystem` ;

-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`School`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`School` (
  `idSchool` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  PRIMARY KEY (`idSchool`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1000;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`User`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`User` (
  `uid` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `password` VARCHAR(32) NOT NULL ,
  `description` MEDIUMTEXT NULL ,
  `email` VARCHAR(128) NULL ,
  `idSchool` INT NULL ,
  `isEnabled` TINYINT(1) NOT NULL DEFAULT 0 ,
  `submitCount` INT NULL DEFAULT 0 ,
  `solvedCount` INT NULL DEFAULT 0 ,
  `acCount` INT NULL DEFAULT 0 ,
  `priviledge` ENUM('user', 'admin') NOT NULL DEFAULT 'user' ,
  `lastPage` INT NOT NULL DEFAULT 1 ,
  `language` ENUM('C', 'C++', 'C++11', 'Pascal', 'Java', 'Python') NOT NULL DEFAULT 'C++' ,
  `avatar` MEDIUMTEXT NULL ,
  `userPicture` VARCHAR(128) NULL ,
  `showCategory` TINYINT(1) NULL DEFAULT 1 ,
  `LastIP` VARCHAR(64) NULL ,
  `lastLogin` DATETIME NULL ,
  PRIMARY KEY (`uid`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  UNIQUE INDEX `uid_UNIQUE` (`uid` ASC) ,
  INDEX `fk_User_School1_idx` (`idSchool` ASC) ,
  INDEX `name_INDEX` (`name` ASC) ,
  CONSTRAINT `fk_User_School1`
    FOREIGN KEY (`idSchool` )
    REFERENCES `OnlineJudgeSystem`.`School` (`idSchool` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB PARTITION BY HASH() PARTITIONS 1;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Contest`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Contest` (
  `cid` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(128) NOT NULL ,
  `description` MEDIUMTEXT NOT NULL ,
  `startTime` DATETIME NOT NULL ,
  `endTime` DATETIME NOT NULL ,
  `contestMode` ENUM('OI', 'ACM', 'Codeforces', 'codejam') NULL DEFAULT 'OI' ,
  `isShowed` TINYINT(1) NULL DEFAULT 0 ,
  `language` SET('C', 'C++', 'C++11', 'Pascal', 'Java', 'Python') NULL DEFAULT 'C,C++,Pascal' ,
  `private` TINYINT(1) NULL DEFAULT 0 ,
  `teamMode` TINYINT(1) NULL DEFAULT 0 ,
  PRIMARY KEY (`cid`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1000;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`ProblemSet`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`ProblemSet` (
  `pid` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(128) NOT NULL ,
  `problemDescription` MEDIUMTEXT NOT NULL ,
  `inputDescription` MEDIUMTEXT NOT NULL ,
  `outputDescription` MEDIUMTEXT NOT NULL ,
  `inputSample` MEDIUMTEXT NOT NULL ,
  `outputSample` MEDIUMTEXT NOT NULL ,
  `dataConstraint` MEDIUMTEXT NOT NULL ,
  `dataConfiguration` MEDIUMTEXT NOT NULL ,
  `hint` MEDIUMTEXT NULL ,
  `source` VARCHAR(64) NULL ,
  `submitCount` INT NULL DEFAULT 0 ,
  `solvedCount` INT NULL DEFAULT 0 ,
  `isShowed` TINYINT(1) NULL DEFAULT FALSE ,
  `codeSizeLimit` INT NULL DEFAULT -1 ,
  `scoreSum` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`pid`) ,
  INDEX `title_INDEX` (`title` ASC) ,
  INDEX `source_INDEX` (`source` ASC) ,
  INDEX `score_INDEX` (`scoreSum` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 1000;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Group`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Group` (
  `gid` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(128) NOT NULL ,
  `avatar` MEDIUMTEXT NULL ,
  `groupPicture` VARCHAR(128) NULL ,
  `description` MEDIUMTEXT NULL ,
  `private` TINYINT(1) NULL DEFAULT 0 ,
  `count` INT NULL DEFAULT 0 ,
  `invitationCode` VARCHAR(64) NULL ,
  PRIMARY KEY (`gid`) ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Task`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Task` (
  `tid` INT NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR(128) NOT NULL ,
  `description` MEDIUMTEXT NULL ,
  `language` SET('C', 'C++', 'C++11', 'Pascal', 'Java', 'Python') NULL DEFAULT 'C,C++,Pascal' ,
  PRIMARY KEY (`tid`) )
ENGINE = InnoDB
AUTO_INCREMENT = 1000;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Group_has_Task`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Group_has_Task` (
  `gid` INT NOT NULL ,
  `tid` INT NOT NULL ,
  `startTime` DATETIME NOT NULL ,
  `endTime` DATETIME NOT NULL ,
  `title` VARCHAR(128) NULL ,
  PRIMARY KEY (`gid`, `tid`) ,
  INDEX `fk_Group_has_Task_Task1` (`tid` ASC) ,
  INDEX `fk_Group_has_Task_Group1` (`gid` ASC) ,
  CONSTRAINT `fk_Group_has_Task_Group1`
    FOREIGN KEY (`gid` )
    REFERENCES `OnlineJudgeSystem`.`Group` (`gid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_has_Task_Task1`
    FOREIGN KEY (`tid` )
    REFERENCES `OnlineJudgeSystem`.`Task` (`tid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Submission`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Submission` (
  `sid` INT NOT NULL AUTO_INCREMENT ,
  `uid` INT NOT NULL ,
  `pid` INT NOT NULL ,
  `name` VARCHAR(64) NULL ,
  `cid` INT NULL ,
  `tid` INT NULL ,
  `code` MEDIUMTEXT NOT NULL ,
  `codeLength` INT NOT NULL ,
  `language` ENUM('C', 'C++', 'C++11', 'Pascal', 'Java', 'Python') NOT NULL ,
  `status` TINYINT NULL DEFAULT -1 ,
  `judgeResult` TEXT NULL ,
  `time` INT NULL ,
  `memory` INT NULL ,
  `score` DOUBLE NULL DEFAULT 0 ,
  `submitTime` DATETIME NULL ,
  `isShowed` TINYINT(1) NULL DEFAULT 1 ,
  `private` TINYINT(1) NULL DEFAULT 1 ,
  PRIMARY KEY (`sid`) ,
  INDEX `fk_Submission_User1_idx` (`uid` ASC) ,
  INDEX `fk_Submission_ProblemSet1_idx` (`pid` ASC) ,
  INDEX `language_INDEX` (`language` ASC) ,
  INDEX `status_INDEX` (`status` ASC) ,
  INDEX `fk_Submission_Contest1` (`cid` ASC) ,
  INDEX `fk_Submission_Group_has_Task1` (`tid` ASC) ,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_Contest1`
    FOREIGN KEY (`cid` )
    REFERENCES `OnlineJudgeSystem`.`Contest` (`cid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Submission_Group_has_Task1`
    FOREIGN KEY (`tid` )
    REFERENCES `OnlineJudgeSystem`.`Group_has_Task` (`tid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1000;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Contest_has_ProblemSet`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Contest_has_ProblemSet` (
  `cid` INT NOT NULL ,
  `pid` INT NOT NULL ,
  `score` INT NULL ,
  `scoreDecreaseSpeed` INT NULL ,
  `title` VARCHAR(64) NULL ,
  `id` INT NOT NULL ,
  PRIMARY KEY (`cid`, `pid`) ,
  INDEX `fk_Contest_has_ProblemSet_ProblemSet1_idx` (`pid` ASC) ,
  INDEX `fk_Contest_has_ProblemSet_Contest1_idx` (`cid` ASC) ,
  CONSTRAINT `fk_Contest_has_ProblemSet_Contest1`
    FOREIGN KEY (`cid` )
    REFERENCES `OnlineJudgeSystem`.`Contest` (`cid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Contest_has_ProblemSet_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Group_has_User`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Group_has_User` (
  `gid` INT NOT NULL ,
  `uid` INT NOT NULL ,
  `isAccepted` TINYINT(1) NULL DEFAULT 0 ,
  `priviledge` ENUM('user', 'admin') NULL DEFAULT 'user' ,
  PRIMARY KEY (`gid`, `uid`) ,
  INDEX `fk_Group_has_User_User1_idx` (`uid` ASC) ,
  INDEX `fk_Group_has_User_Group1_idx` (`gid` ASC) ,
  CONSTRAINT `fk_Group_has_User_Group1`
    FOREIGN KEY (`gid` )
    REFERENCES `OnlineJudgeSystem`.`Group` (`gid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Group_has_User_User1`
    FOREIGN KEY (`uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Solution`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Solution` (
  `idSolution` INT NOT NULL AUTO_INCREMENT ,
  `solution` MEDIUMTEXT NOT NULL ,
  `pid` INT NOT NULL ,
  `uid` INT NOT NULL ,
  `postTime` DATETIME NOT NULL ,
  `recommand` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`idSolution`) ,
  INDEX `fk_Solution_ProblemSet1_idx` (`pid` ASC) ,
  INDEX `fk_Solution_User1_idx` (`uid` ASC) ,
  CONSTRAINT `fk_Solution_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Solution_User1`
    FOREIGN KEY (`uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Miscellaneousness`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Miscellaneousness` (
  `noticeBoard` TEXT NULL )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Board`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Board` (
  `idPost` INT NOT NULL AUTO_INCREMENT ,
  `uid` INT NOT NULL ,
  `pid` INT NULL DEFAULT 0 ,
  `title` VARCHAR(64) NOT NULL ,
  `content` MEDIUMTEXT NOT NULL ,
  `replyTo` INT NOT NULL DEFAULT 0 ,
  `postTime` DATETIME NOT NULL ,
  INDEX `fk_table1_ProblemSet1_idx` (`pid` ASC) ,
  PRIMARY KEY (`idPost`) ,
  INDEX `fk_Board_User1_idx` (`uid` ASC) ,
  INDEX `title_INDEX` (`title` ASC) ,
  CONSTRAINT `fk_table1_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Board_User1`
    FOREIGN KEY (`uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Mail`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Mail` (
  `idMail` INT NOT NULL AUTO_INCREMENT ,
  `from_uid` INT NOT NULL ,
  `to_uid` INT NOT NULL ,
  `title` VARCHAR(64) NOT NULL ,
  `content` MEDIUMTEXT NULL ,
  `sendTime` DATETIME NOT NULL ,
  `isRead` TINYINT(1) NULL ,
  `readTime` DATETIME NULL ,
  PRIMARY KEY (`idMail`) ,
  INDEX `fk_Mail_User1_idx` (`from_uid` ASC) ,
  INDEX `fk_Mail_User2_idx` (`to_uid` ASC) ,
  CONSTRAINT `fk_Mail_User1`
    FOREIGN KEY (`from_uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Mail_User2`
    FOREIGN KEY (`to_uid` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Category`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Category` (
  `idCategory` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  PRIMARY KEY (`idCategory`) ,
  INDEX `name_INDEX` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Categorization`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Categorization` (
  `pid` INT NOT NULL ,
  `idCategory` INT NOT NULL ,
  PRIMARY KEY (`pid`, `idCategory`) ,
  INDEX `fk_ProblemSet_has_Category_Category1_idx` (`idCategory` ASC) ,
  INDEX `fk_ProblemSet_has_Category_ProblemSet1_idx` (`pid` ASC) ,
  CONSTRAINT `fk_ProblemSet_has_Category_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_ProblemSet_has_Category_Category1`
    FOREIGN KEY (`idCategory` )
    REFERENCES `OnlineJudgeSystem`.`Category` (`idCategory` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Team`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Team` (
  `idTeam` INT NOT NULL ,
  `name` VARCHAR(64) NULL ,
  `idParticipant0` INT NOT NULL ,
  `idParticipant1` INT NULL DEFAULT 0 ,
  `idParticipant2` INT NULL DEFAULT 0 ,
  `cid` INT NOT NULL ,
  `registrationTime` DATETIME NULL ,
  `score` INT NULL DEFAULT 0 ,
  `penalty` INT NULL DEFAULT 0 ,
  `isFormal` TINYINT(1) NOT NULL ,
  PRIMARY KEY (`idTeam`) ,
  INDEX `fk_Team_Contest1` (`cid` ASC) ,
  INDEX `fk_Team_User1` (`idParticipant0` ASC) ,
  INDEX `fk_Team_User2` (`idParticipant1` ASC) ,
  INDEX `fk_Team_User3` (`idParticipant2` ASC) ,
  CONSTRAINT `fk_Team_Contest1`
    FOREIGN KEY (`cid` )
    REFERENCES `OnlineJudgeSystem`.`Contest` (`cid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User1`
    FOREIGN KEY (`idParticipant0` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User2`
    FOREIGN KEY (`idParticipant1` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Team_User3`
    FOREIGN KEY (`idParticipant2` )
    REFERENCES `OnlineJudgeSystem`.`User` (`uid` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Declaration`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Declaration` (
  `idDeclaration` INT NOT NULL ,
  `cid` INT NOT NULL ,
  `pid` INT NOT NULL ,
  `title` VARCHAR(64) NOT NULL ,
  `declaration` MEDIUMTEXT NOT NULL ,
  `postTime` DATETIME NOT NULL ,
  PRIMARY KEY (`idDeclaration`) ,
  INDEX `fk_Declaration_Contest_has_ProblemSet1` (`cid` ASC, `pid` ASC) ,
  CONSTRAINT `fk_Declaration_Contest_has_ProblemSet1`
    FOREIGN KEY (`cid` , `pid` )
    REFERENCES `OnlineJudgeSystem`.`Contest_has_ProblemSet` (`cid` , `pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OnlineJudgeSystem`.`Task_has_ProblemSet`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `OnlineJudgeSystem`.`Task_has_ProblemSet` (
  `tid` INT NOT NULL ,
  `pid` INT NOT NULL ,
  `title` VARCHAR(128) NULL ,
  PRIMARY KEY (`tid`, `pid`) ,
  INDEX `fk_Task_has_ProblemSet_ProblemSet1` (`pid` ASC) ,
  INDEX `fk_Task_has_ProblemSet_Task1` (`tid` ASC) ,
  CONSTRAINT `fk_Task_has_ProblemSet_Task1`
    FOREIGN KEY (`tid` )
    REFERENCES `OnlineJudgeSystem`.`Task` (`tid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Task_has_ProblemSet_ProblemSet1`
    FOREIGN KEY (`pid` )
    REFERENCES `OnlineJudgeSystem`.`ProblemSet` (`pid` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
