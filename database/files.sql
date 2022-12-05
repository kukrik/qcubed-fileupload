/*
 Navicat MySQL Data Transfer

 Source Server         : LOCAL
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : qcubed-4

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 26/11/2022 19:13:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for files
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `description` text,
  `extension` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `mtime` int DEFAULT NULL,
  `dimensions` varchar(255) DEFAULT NULL,
  `locked_file` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
