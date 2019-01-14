# Host: localhost  (Version: 5.5.53)
# Date: 2019-01-14 17:37:59
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "find_adb"
#

CREATE TABLE `find_adb` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `adb_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Structure for table "find_content"
#

CREATE TABLE `find_content` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `c_userid` varchar(255) DEFAULT NULL,
  `c_type` varchar(255) DEFAULT NULL,
  `c_text` varchar(255) DEFAULT NULL,
  `c_address` varchar(255) DEFAULT NULL,
  `c_phone` varchar(255) DEFAULT NULL,
  `c_cardid` varchar(255) DEFAULT NULL,
  `c_uptime` varchar(255) DEFAULT NULL,
  `c_pushtime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

#
# Structure for table "find_email"
#

CREATE TABLE `find_email` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `email_from` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `email_text` varchar(255) DEFAULT NULL,
  `email_time` varchar(255) DEFAULT NULL,
  `email_status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

#
# Structure for table "find_school"
#

CREATE TABLE `find_school` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `s_wx` varchar(255) DEFAULT NULL,
  `s_cid` varchar(255) DEFAULT NULL,
  `s_time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Structure for table "find_users"
#

CREATE TABLE `find_users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `user_wx` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_aurl` varchar(255) DEFAULT NULL,
  `user_uptime` varchar(255) DEFAULT NULL,
  `user_regtime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
