/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : servicio

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-07-15 12:02:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for par_etiquetas
-- ----------------------------
DROP TABLE IF EXISTS `par_etiquetas`;
CREATE TABLE `par_etiquetas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `x` int(3) NOT NULL DEFAULT '0',
  `y` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_objetos
-- ----------------------------
DROP TABLE IF EXISTS `par_objetos`;
CREATE TABLE `par_objetos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `x` int(3) NOT NULL DEFAULT '0',
  `y` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
