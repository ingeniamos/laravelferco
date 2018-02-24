/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : ferco_new

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2016-03-18 16:56:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for requerimientos_productos
-- ----------------------------
DROP TABLE IF EXISTS `requerimientos_productos`;
CREATE TABLE `requerimientos_productos` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `categoria` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `unidad` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `existencia` decimal(20,2) NOT NULL DEFAULT '0.00',
  `stock` decimal(20,2) NOT NULL DEFAULT '0.00',
  `notas` varchar(255) NOT NULL DEFAULT '',
  `vencimiento` date NOT NULL DEFAULT '0000-00-00',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of requerimientos_productos
-- ----------------------------
INSERT INTO `requerimientos_productos` VALUES ('1', '00011A', 'Alambre', 'Alambre', 'Negro', 'Alambre de Pruebas', 'Rollo', '1.00', '15000.00', '100.00', '100.00', '', '2016-04-18', 'SISTEMA', '2016-03-18 09:26:21', '', '0000-00-00 00:00:00');
INSERT INTO `requerimientos_productos` VALUES ('3', '45222', 'Cemento', 'Cemento Gris', 'cemento * 1 kg', 'Cemento de Pruebas', 'kilo', '1.00', '5000.00', '10.00', '50.00', '', '2016-03-18', 'SISTEMA', '2016-03-18 09:50:02', '', '0000-00-00 00:00:00');
