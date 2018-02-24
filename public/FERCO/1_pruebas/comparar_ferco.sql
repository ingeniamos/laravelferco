/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : ferco

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-07-22 17:21:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bancos
-- ----------------------------
DROP TABLE IF EXISTS `bancos`;
CREATE TABLE `bancos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `caja_interno` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tipo` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `numero` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `banco` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `estado` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `tipo_mov` enum('Entrada','Salida') COLLATE utf8_spanish_ci NOT NULL DEFAULT 'Entrada',
  `digitado_por` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8850 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for biocontrol
-- ----------------------------
DROP TABLE IF EXISTS `biocontrol`;
CREATE TABLE `biocontrol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `horas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `turno1_hora_ini` time NOT NULL DEFAULT '00:00:00',
  `turno1_hora_fin` time NOT NULL DEFAULT '00:00:00',
  `turno2_hora_ini` time NOT NULL DEFAULT '00:00:00',
  `turno2_hora_fin` time NOT NULL DEFAULT '00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for caja_final
-- ----------------------------
DROP TABLE IF EXISTS `caja_final`;
CREATE TABLE `caja_final` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  `cxp` varchar(100) NOT NULL DEFAULT '',
  `categoria` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo2` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `aplicado_a` varchar(100) NOT NULL DEFAULT '',
  `observaciones` varchar(255) NOT NULL DEFAULT '',
  `efectivo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cheque` decimal(20,2) NOT NULL DEFAULT '0.00',
  `consignacion` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rete_iva` decimal(20,2) NOT NULL DEFAULT '0.00',
  `rete_ica` decimal(20,2) NOT NULL DEFAULT '0.00',
  `rete_fuente` decimal(20,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(20,2) NOT NULL DEFAULT '0.00',
  `concepto_dcto` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34475 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for cartera_aplicar_log
-- ----------------------------
DROP TABLE IF EXISTS `cartera_aplicar_log`;
CREATE TABLE `cartera_aplicar_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `orden_compra` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `abono` decimal(20,2) NOT NULL DEFAULT '0.00',
  `saldo_nuevo` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3022 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for cheques
-- ----------------------------
DROP TABLE IF EXISTS `cheques`;
CREATE TABLE `cheques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cheque` varchar(100) NOT NULL DEFAULT '',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `banco` varchar(100) NOT NULL DEFAULT '',
  `cuenta` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `estado_cheque` varchar(100) NOT NULL DEFAULT '',
  `fecha_cheque` date NOT NULL DEFAULT '0000-00-00',
  `banco_destino` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4428 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for clientes
-- ----------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `tipo_sociedad` varchar(100) NOT NULL DEFAULT '',
  `tipo_doc` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `foto` varchar(100) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `barrio` varchar(100) NOT NULL DEFAULT '',
  `ciudad` varchar(100) NOT NULL DEFAULT '',
  `departamento` varchar(100) NOT NULL DEFAULT '',
  `pais` varchar(100) NOT NULL DEFAULT 'Colombia',
  `contacto_p` varchar(100) NOT NULL DEFAULT '',
  `telefono_cp` varchar(100) NOT NULL DEFAULT '',
  `contacto_s` varchar(100) NOT NULL DEFAULT '',
  `telefono_cs` varchar(100) NOT NULL DEFAULT '',
  `telefono` varchar(100) NOT NULL DEFAULT '',
  `fax` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `email2` varchar(100) NOT NULL DEFAULT '',
  `notas` varchar(255) NOT NULL DEFAULT '',
  `vigencia_notas` date NOT NULL DEFAULT '0000-00-00',
  `terminos` varchar(255) NOT NULL DEFAULT '',
  `credito` decimal(20,2) NOT NULL DEFAULT '0.00',
  `vigencia_credito` date NOT NULL DEFAULT '0000-00-00',
  `adicional` decimal(20,2) NOT NULL DEFAULT '0.00',
  `vigencia_adicional` date NOT NULL DEFAULT '0000-00-00',
  `credito_activo` enum('false','true') NOT NULL DEFAULT 'true',
  `cupo_asignado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_asignado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `garantia` varchar(100) NOT NULL DEFAULT '',
  `estado_cuenta` enum('Al Dia','Mora','Juridico') NOT NULL DEFAULT 'Al Dia',
  `lista_precio` int(11) NOT NULL DEFAULT '1',
  `proveedor_codigo` varchar(100) NOT NULL DEFAULT '',
  `vendedor_codigo` varchar(100) NOT NULL DEFAULT '',
  `cobrador_codigo` varchar(100) NOT NULL DEFAULT '',
  `ultima_actualizacion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ultimo_movimiento` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fecha_creacion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `creado_por` varchar(100) NOT NULL DEFAULT '',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `activo` enum('true','false') NOT NULL DEFAULT 'true',
  `motivo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cliente_id` (`cliente_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8364 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for clientes_garant
-- ----------------------------
DROP TABLE IF EXISTS `clientes_garant`;
CREATE TABLE `clientes_garant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL,
  `garantia` varchar(100) NOT NULL,
  `ok` enum('true','false') NOT NULL DEFAULT 'false',
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29888 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for cliente_grupo
-- ----------------------------
DROP TABLE IF EXISTS `cliente_grupo`;
CREATE TABLE `cliente_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `clasificacion` varchar(100) NOT NULL DEFAULT '',
  `tipo` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7090 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for compras_final
-- ----------------------------
DROP TABLE IF EXISTS `compras_final`;
CREATE TABLE `compras_final` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entrada` varchar(100) NOT NULL DEFAULT '',
  `doc_transp` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `pedido` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `fecha_compra` date NOT NULL DEFAULT '0000-00-00',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `forma_pago` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `peso_bascula` decimal(20,2) NOT NULL DEFAULT '0.00',
  `peso_remision` decimal(20,2) NOT NULL DEFAULT '0.00',
  `conductor` varchar(100) NOT NULL DEFAULT '',
  `placa` varchar(100) NOT NULL DEFAULT '',
  `notas` varchar(100) NOT NULL DEFAULT '',
  `observaciones` varchar(100) NOT NULL DEFAULT '',
  `tipo_servicio` varchar(100) NOT NULL DEFAULT '',
  `tipo_servicio_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` varchar(100) NOT NULL DEFAULT '',
  `tipo_descuento_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `sub_total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(255) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `autorizado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_autorizado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11716 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for compras_movs
-- ----------------------------
DROP TABLE IF EXISTS `compras_movs`;
CREATE TABLE `compras_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ultimo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `nuevo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `desc` decimal(20,2) NOT NULL,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `entrada` varchar(100) NOT NULL DEFAULT '',
  `doc_transp` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4430 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for contratos
-- ----------------------------
DROP TABLE IF EXISTS `contratos`;
CREATE TABLE `contratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `charge` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `starts` date NOT NULL DEFAULT '0000-00-00',
  `ends` date NOT NULL DEFAULT '0000-00-00',
  `turn1_starts` time NOT NULL DEFAULT '00:00:00',
  `turn1_ends` time NOT NULL DEFAULT '00:00:00',
  `turn2_starts` time NOT NULL DEFAULT '00:00:00',
  `turn2_ends` time NOT NULL DEFAULT '00:00:00',
  `hlab` int(11) NOT NULL DEFAULT '240',
  `basic` decimal(20,2) NOT NULL DEFAULT '0.00',
  `transp` decimal(20,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(20,2) NOT NULL DEFAULT '0.00',
  `pension` varchar(100) NOT NULL DEFAULT '',
  `health` varchar(100) NOT NULL DEFAULT '',
  `rh` varchar(100) NOT NULL DEFAULT '',
  `outgoing` varchar(100) NOT NULL DEFAULT '',
  `active` enum('true','false') NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for cxp_movs
-- ----------------------------
DROP TABLE IF EXISTS `cxp_movs`;
CREATE TABLE `cxp_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_interno` varchar(100) NOT NULL DEFAULT '',
  `compra_entrada` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `doc_transp` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `tipo_movimiento` varchar(100) NOT NULL DEFAULT '',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(255) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `origen1` varchar(100) NOT NULL DEFAULT '',
  `origen2` varchar(100) NOT NULL DEFAULT '',
  `origen3` varchar(100) NOT NULL DEFAULT '',
  `origen_documento` varchar(100) NOT NULL DEFAULT '',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo2` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36071 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for fact_final
-- ----------------------------
DROP TABLE IF EXISTS `fact_final`;
CREATE TABLE `fact_final` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `fecha_remision` date NOT NULL DEFAULT '0000-00-00',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `fecha_factura` date NOT NULL DEFAULT '0000-00-00',
  `orden_compra` varchar(100) NOT NULL DEFAULT '',
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `ruta` varchar(100) NOT NULL DEFAULT '',
  `direccion_entrega` varchar(255) NOT NULL DEFAULT '',
  `forma_pago` varchar(100) NOT NULL DEFAULT '',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  `vendedor_codigo` varchar(100) NOT NULL DEFAULT '',
  `cobrador_codigo` varchar(100) NOT NULL DEFAULT '',
  `chofer` varchar(100) NOT NULL DEFAULT '',
  `placa` varchar(100) NOT NULL DEFAULT '',
  `notas` varchar(255) NOT NULL DEFAULT '',
  `observaciones` varchar(255) NOT NULL DEFAULT '',
  `tipo_servicio` varchar(100) NOT NULL DEFAULT '',
  `tipo_servicio_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` varchar(100) NOT NULL DEFAULT '',
  `tipo_descuento_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `sub_total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(20,2) NOT NULL DEFAULT '0.00',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `autorizado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_autorizado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tipo_pedido` enum('Pedido','Produccion') NOT NULL DEFAULT 'Pedido',
  `beneficiario_id` varchar(100) NOT NULL DEFAULT '',
  `enviado` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`,`remision`)
) ENGINE=InnoDB AUTO_INCREMENT=36081 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for fact_movs
-- ----------------------------
DROP TABLE IF EXISTS `fact_movs`;
CREATE TABLE `fact_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL,
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cantidad_despachada` decimal(20,2) NOT NULL DEFAULT '0.00',
  `desc` decimal(20,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `orden_compra` varchar(255) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2113200 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for figuracion_cartilla
-- ----------------------------
DROP TABLE IF EXISTS `figuracion_cartilla`;
CREATE TABLE `figuracion_cartilla` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `venta_interno` varchar(100) NOT NULL DEFAULT '',
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `obra` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_fig` varchar(1000) NOT NULL DEFAULT '',
  `desperdicio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for figuracion_cartilla_movs
-- ----------------------------
DROP TABLE IF EXISTS `figuracion_cartilla_movs`;
CREATE TABLE `figuracion_cartilla_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `figura` varchar(100) NOT NULL DEFAULT '',
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cantidad2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `dimensiones` varchar(1000) NOT NULL DEFAULT '',
  `longitud` decimal(20,2) NOT NULL DEFAULT '0.00',
  `peso` decimal(20,3) NOT NULL DEFAULT '0.000',
  `ubicacion` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `venta_interno` varchar(100) NOT NULL DEFAULT '',
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7239 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for figuracion_cartilla_tickets
-- ----------------------------
DROP TABLE IF EXISTS `figuracion_cartilla_tickets`;
CREATE TABLE `figuracion_cartilla_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `ticket` int(11) NOT NULL DEFAULT '0',
  `figura` varchar(100) NOT NULL DEFAULT '',
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `detalle` varchar(100) NOT NULL DEFAULT '',
  `cantidad1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cantidad2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cartilla` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for figuracion_figuras
-- ----------------------------
DROP TABLE IF EXISTS `figuracion_figuras`;
CREATE TABLE `figuracion_figuras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fig` varchar(100) NOT NULL DEFAULT '',
  `img` varchar(100) NOT NULL DEFAULT '',
  `dimensiones` int(11) NOT NULL DEFAULT '0',
  `estribo` tinyint(1) NOT NULL DEFAULT '0',
  `semicirculo` tinyint(1) NOT NULL DEFAULT '0',
  `circular` tinyint(1) NOT NULL DEFAULT '0',
  `vueltas` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for import_log
-- ----------------------------
DROP TABLE IF EXISTS `import_log`;
CREATE TABLE `import_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `peso_anterior` decimal(20,2) NOT NULL DEFAULT '0.00',
  `peso_nuevo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo_anterior` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo_nuevo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista1_1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista1_2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista2_1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista2_2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista3_1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista3_2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista4_1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista4_2` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2045 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for inventario_movs
-- ----------------------------
DROP TABLE IF EXISTS `inventario_movs`;
CREATE TABLE `inventario_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_fab` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `viejo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo_promedio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `viejo_costo_promedio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ultimo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `existencia` decimal(20,2) NOT NULL DEFAULT '0.00',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `motivo` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tipo` enum('Entrada','Salida') NOT NULL DEFAULT 'Entrada',
  `observacion` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142773 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for login
-- ----------------------------
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL DEFAULT '',
  `user_pass` varchar(100) NOT NULL DEFAULT '',
  `user_code` varchar(100) NOT NULL DEFAULT '',
  `user_lvl` enum('General','Administrador','Vendedor','Cliente') NOT NULL DEFAULT 'General',
  `active` enum('true','false') NOT NULL DEFAULT 'true',
  `session_id` varchar(100) NOT NULL DEFAULT '',
  `pause` enum('true','false') NOT NULL DEFAULT 'false',
  `last_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_change` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `email` varchar(100) NOT NULL DEFAULT '',
  `db` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for login_access
-- ----------------------------
DROP TABLE IF EXISTS `login_access`;
CREATE TABLE `login_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL DEFAULT '',
  `modulo` varchar(100) NOT NULL DEFAULT '',
  `sub_modulo` varchar(100) NOT NULL DEFAULT '',
  `guardar` enum('true','false') NOT NULL DEFAULT 'false',
  `modificar` enum('true','false') NOT NULL DEFAULT 'false',
  `supervisor` enum('true','false') NOT NULL DEFAULT 'false',
  `imprimir` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=709 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for login_log
-- ----------------------------
DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL DEFAULT '',
  `type` enum('login','logout') NOT NULL DEFAULT 'login',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17554 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for login_system
-- ----------------------------
DROP TABLE IF EXISTS `login_system`;
CREATE TABLE `login_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments` varchar(100) NOT NULL,
  `status` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for maquinaria_final
-- ----------------------------
DROP TABLE IF EXISTS `maquinaria_final`;
CREATE TABLE `maquinaria_final` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado_maquina` varchar(100) NOT NULL DEFAULT '',
  `ord_reparacion` varchar(100) NOT NULL DEFAULT '',
  `operador` varchar(100) NOT NULL DEFAULT '',
  `mecanico` varchar(100) NOT NULL DEFAULT '',
  `fecha_ini` date NOT NULL DEFAULT '0000-00-00',
  `fecha_fin` date NOT NULL DEFAULT '0000-00-00',
  `clasificacion` varchar(100) NOT NULL DEFAULT '',
  `tipo` varchar(100) NOT NULL DEFAULT '',
  `motivo` varchar(100) NOT NULL DEFAULT '',
  `diagnostico` varchar(100) NOT NULL DEFAULT '',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `causa` varchar(100) NOT NULL DEFAULT '',
  `procedimiento` varchar(100) NOT NULL DEFAULT '',
  `observaciones` varchar(256) NOT NULL DEFAULT '',
  `proveedor1` varchar(100) NOT NULL DEFAULT '',
  `factura1` varchar(100) NOT NULL DEFAULT '',
  `total1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `proveedor2` varchar(100) NOT NULL DEFAULT '',
  `factura2` varchar(100) NOT NULL DEFAULT '',
  `total2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `proveedor3` varchar(100) NOT NULL DEFAULT '',
  `factura3` varchar(100) NOT NULL DEFAULT '',
  `total3` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for maquinaria_movs
-- ----------------------------
DROP TABLE IF EXISTS `maquinaria_movs`;
CREATE TABLE `maquinaria_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_reparacion` varchar(100) NOT NULL DEFAULT '',
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `unitario` decimal(20,2) NOT NULL DEFAULT '0.00',
  `proveedor` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=492 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for maquinaria_partes
-- ----------------------------
DROP TABLE IF EXISTS `maquinaria_partes`;
CREATE TABLE `maquinaria_partes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ord_reparacion` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `parte` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `problema` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `diagnostico` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for module_access
-- ----------------------------
DROP TABLE IF EXISTS `module_access`;
CREATE TABLE `module_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL DEFAULT '',
  `sub_module` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for module_access_type
-- ----------------------------
DROP TABLE IF EXISTS `module_access_type`;
CREATE TABLE `module_access_type` (
  `tipo` varchar(100) NOT NULL DEFAULT '',
  `modulo` varchar(100) NOT NULL,
  `sub_modulo` varchar(100) NOT NULL DEFAULT '',
  `guardar` enum('true','false') NOT NULL DEFAULT 'false',
  `modificar` enum('true','false') NOT NULL DEFAULT 'false',
  `supervisor` enum('true','false') NOT NULL DEFAULT 'false',
  `imprimir` enum('true','false') NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for module_par_access_type
-- ----------------------------
DROP TABLE IF EXISTS `module_par_access_type`;
CREATE TABLE `module_par_access_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for mov_clientes
-- ----------------------------
DROP TABLE IF EXISTS `mov_clientes`;
CREATE TABLE `mov_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `tipo_movimiento` enum('Credito','Debito') NOT NULL DEFAULT 'Debito',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `saldo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `orden_compra` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(255) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL,
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vendedor_codigo` varchar(100) NOT NULL DEFAULT '',
  `cobrador_codigo` varchar(100) NOT NULL DEFAULT '',
  `caja_interno` varchar(100) NOT NULL DEFAULT '',
  `caja_recibo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141868 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nomina
-- ----------------------------
DROP TABLE IF EXISTS `nomina`;
CREATE TABLE `nomina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_ini` date NOT NULL DEFAULT '0000-00-00',
  `fecha_fin` date NOT NULL DEFAULT '0000-00-00',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `basico` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas_desc` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas_rep` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas_ext` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas_lab` decimal(20,2) NOT NULL DEFAULT '0.00',
  `basico_final` decimal(20,2) NOT NULL DEFAULT '0.00',
  `transporte` decimal(20,2) NOT NULL DEFAULT '0.00',
  `bono` decimal(20,2) NOT NULL DEFAULT '0.00',
  `extras` decimal(20,2) NOT NULL DEFAULT '0.00',
  `licencias` decimal(20,2) NOT NULL DEFAULT '0.00',
  `devengado` decimal(20,2) NOT NULL DEFAULT '0.00',
  `salud` decimal(20,2) NOT NULL DEFAULT '0.00',
  `pension` decimal(20,2) NOT NULL DEFAULT '0.00',
  `retencion` decimal(20,2) NOT NULL DEFAULT '0.00',
  `prestamo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `libranza` decimal(20,2) NOT NULL DEFAULT '0.00',
  `anticipo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `donacion` decimal(20,2) NOT NULL DEFAULT '0.00',
  `multa` decimal(20,2) NOT NULL DEFAULT '0.00',
  `deducido` decimal(20,2) NOT NULL DEFAULT '0.00',
  `neto` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cesantia` decimal(20,2) NOT NULL DEFAULT '0.00',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_extras
-- ----------------------------
DROP TABLE IF EXISTS `nom_extras`;
CREATE TABLE `nom_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `empleado_id` varchar(100) NOT NULL DEFAULT '',
  `autorizador_id` varchar(100) NOT NULL DEFAULT '',
  `justificacion` varchar(100) NOT NULL DEFAULT '',
  `comentario` varchar(100) NOT NULL DEFAULT '',
  `observacion` varchar(100) NOT NULL DEFAULT '',
  `total_diurnas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_nocturnas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL,
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_extras_movs
-- ----------------------------
DROP TABLE IF EXISTS `nom_extras_movs`;
CREATE TABLE `nom_extras_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `turno` date NOT NULL DEFAULT '0000-00-00',
  `hora_ini` time NOT NULL DEFAULT '00:00:00',
  `hora_fin` time NOT NULL DEFAULT '00:00:00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `nocturno` enum('true','false') NOT NULL DEFAULT 'false',
  `festivo` enum('true','false') NOT NULL DEFAULT 'false',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2177 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_novedades
-- ----------------------------
DROP TABLE IF EXISTS `nom_novedades`;
CREATE TABLE `nom_novedades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `empleado_id` varchar(100) NOT NULL DEFAULT '',
  `reemplazo_id` varchar(100) NOT NULL DEFAULT '',
  `autorizador_id` varchar(100) NOT NULL DEFAULT '',
  `novedad` varchar(100) NOT NULL DEFAULT '',
  `fecha_ini` date NOT NULL DEFAULT '0000-00-00',
  `hora_ini` time NOT NULL DEFAULT '00:00:00',
  `fecha_fin` date NOT NULL DEFAULT '0000-00-00',
  `hora_fin` time NOT NULL DEFAULT '00:00:00',
  `justificacion` varchar(100) NOT NULL DEFAULT '',
  `comentario` varchar(100) NOT NULL DEFAULT '',
  `reposicion` varchar(100) NOT NULL DEFAULT '',
  `observacion` varchar(100) NOT NULL DEFAULT '',
  `horas_novedad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `horas_reposicion` decimal(20,2) NOT NULL DEFAULT '0.00',
  `descontable` enum('true','false') NOT NULL DEFAULT 'false',
  `remunerado100` enum('true','false') NOT NULL DEFAULT 'false',
  `remunerado66` enum('true','false') NOT NULL DEFAULT 'false',
  `cesantia` enum('true','false') NOT NULL DEFAULT 'false',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL,
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1496 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_novedades_movs
-- ----------------------------
DROP TABLE IF EXISTS `nom_novedades_movs`;
CREATE TABLE `nom_novedades_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `hora_ini` time NOT NULL DEFAULT '00:00:00',
  `hora_fin` time NOT NULL DEFAULT '00:00:00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=821 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_prestamos
-- ----------------------------
DROP TABLE IF EXISTS `nom_prestamos`;
CREATE TABLE `nom_prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `beneficiario_id` varchar(100) NOT NULL DEFAULT '',
  `acreedor_id` varchar(100) NOT NULL DEFAULT '',
  `tipo_mov` varchar(100) NOT NULL DEFAULT '',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `cuotas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor_cuotas` decimal(20,2) NOT NULL DEFAULT '0.00',
  `caja` varchar(100) NOT NULL DEFAULT '',
  `forma_pago` varchar(100) NOT NULL DEFAULT '',
  `observacion` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL,
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=800 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for nom_prestamos_movs
-- ----------------------------
DROP TABLE IF EXISTS `nom_prestamos_movs`;
CREATE TABLE `nom_prestamos_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `cuota` tinyint(2) NOT NULL DEFAULT '0',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4242 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_caja_bancos
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_bancos`;
CREATE TABLE `par_caja_bancos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `numero` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `cuenta` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `banco` (`banco`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_caja_bancos_numero
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_bancos_numero`;
CREATE TABLE `par_caja_bancos_numero` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banco` varchar(100) NOT NULL DEFAULT '',
  `numero` varchar(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_caja_cat
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_cat`;
CREATE TABLE `par_caja_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_caja_dcto
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_dcto`;
CREATE TABLE `par_caja_dcto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descuentos` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_caja_estado_banco
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_estado_banco`;
CREATE TABLE `par_caja_estado_banco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_caja_estado_cheque
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_estado_cheque`;
CREATE TABLE `par_caja_estado_cheque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_caja_gr
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_gr`;
CREATE TABLE `par_caja_gr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_caja_subgr
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_subgr`;
CREATE TABLE `par_caja_subgr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subgrupo` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `grupo` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_caja_subgr2
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_subgr2`;
CREATE TABLE `par_caja_subgr2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subgrupo2` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_caja_tipo
-- ----------------------------
DROP TABLE IF EXISTS `par_caja_tipo`;
CREATE TABLE `par_caja_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_estado
-- ----------------------------
DROP TABLE IF EXISTS `par_estado`;
CREATE TABLE `par_estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_fac_anula
-- ----------------------------
DROP TABLE IF EXISTS `par_fac_anula`;
CREATE TABLE `par_fac_anula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `concepto` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_fac_otr_ser
-- ----------------------------
DROP TABLE IF EXISTS `par_fac_otr_ser`;
CREATE TABLE `par_fac_otr_ser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_fac_ruta
-- ----------------------------
DROP TABLE IF EXISTS `par_fac_ruta`;
CREATE TABLE `par_fac_ruta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barrio` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `ruta` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_fac_tipo_dcto
-- ----------------------------
DROP TABLE IF EXISTS `par_fac_tipo_dcto`;
CREATE TABLE `par_fac_tipo_dcto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_descuento` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_fac_vehiculo
-- ----------------------------
DROP TABLE IF EXISTS `par_fac_vehiculo`;
CREATE TABLE `par_fac_vehiculo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `placa` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `modelo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `tipo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `chofer` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `chofer_id` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_inv_cat
-- ----------------------------
DROP TABLE IF EXISTS `par_inv_cat`;
CREATE TABLE `par_inv_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_inv_gr
-- ----------------------------
DROP TABLE IF EXISTS `par_inv_gr`;
CREATE TABLE `par_inv_gr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_inv_motivo
-- ----------------------------
DROP TABLE IF EXISTS `par_inv_motivo`;
CREATE TABLE `par_inv_motivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motivo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_inv_subgr
-- ----------------------------
DROP TABLE IF EXISTS `par_inv_subgr`;
CREATE TABLE `par_inv_subgr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subgrupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_inv_und
-- ----------------------------
DROP TABLE IF EXISTS `par_inv_und`;
CREATE TABLE `par_inv_und` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_maq_estado
-- ----------------------------
DROP TABLE IF EXISTS `par_maq_estado`;
CREATE TABLE `par_maq_estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_nom
-- ----------------------------
DROP TABLE IF EXISTS `par_nom`;
CREATE TABLE `par_nom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `valor` decimal(20,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_cargos
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_cargos`;
CREATE TABLE `par_nom_cargos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cargo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_carnet
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_carnet`;
CREATE TABLE `par_nom_carnet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc1` varchar(1000) NOT NULL DEFAULT '',
  `desc2` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_cesantia
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_cesantia`;
CREATE TABLE `par_nom_cesantia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_contrato
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_contrato`;
CREATE TABLE `par_nom_contrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_ext
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_ext`;
CREATE TABLE `par_nom_ext` (
  `id` int(11) NOT NULL,
  `tipo` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_nom_fest
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_fest`;
CREATE TABLE `par_nom_fest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_horario
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_horario`;
CREATE TABLE `par_nom_horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('Nocturno','Diurno','Laboral_Dia','Laboral_Tarde') NOT NULL DEFAULT 'Diurno',
  `hora_ini` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_justificacion
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_justificacion`;
CREATE TABLE `par_nom_justificacion` (
  `id` int(11) NOT NULL,
  `justificacion` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_nom_nov
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_nov`;
CREATE TABLE `par_nom_nov` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novedad` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `descontable` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  `remunerado1` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  `remunerado2` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  `cesantia` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_nom_pension
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_pension`;
CREATE TABLE `par_nom_pension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_prestamos
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_prestamos`;
CREATE TABLE `par_nom_prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_reposicion
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_reposicion`;
CREATE TABLE `par_nom_reposicion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reposicion` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `reponer` enum('true','false') COLLATE utf8_bin NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_nom_retencion
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_retencion`;
CREATE TABLE `par_nom_retencion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salario` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_salud
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_salud`;
CREATE TABLE `par_nom_salud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_nom_saludyp - se dividio
-- ----------------------------
DROP TABLE IF EXISTS `par_nom_saludyp - se dividio`;
CREATE TABLE `par_nom_saludyp - se dividio` (
  `id` int(11) NOT NULL,
  `salud` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pension` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `cesantias` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_req_prioridad
-- ----------------------------
DROP TABLE IF EXISTS `par_req_prioridad`;
CREATE TABLE `par_req_prioridad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prioridad` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_barrio
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_barrio`;
CREATE TABLE `par_ter_barrio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `barrio` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_ciudad
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_ciudad`;
CREATE TABLE `par_ter_ciudad` (
  `ciudad` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_ciudad_dep
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_ciudad_dep`;
CREATE TABLE `par_ter_ciudad_dep` (
  `departamento` varchar(100) DEFAULT '',
  `ciudad` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_clasificacion
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_clasificacion`;
CREATE TABLE `par_ter_clasificacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clasificacion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_departamento
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_departamento`;
CREATE TABLE `par_ter_departamento` (
  `departamento` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`departamento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_departamento_pais
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_departamento_pais`;
CREATE TABLE `par_ter_departamento_pais` (
  `pais` varchar(100) DEFAULT '',
  `departamento` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_gara
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_gara`;
CREATE TABLE `par_ter_gara` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `garantia` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_ter_gprov - se cambio por otro
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_gprov - se cambio por otro`;
CREATE TABLE `par_ter_gprov - se cambio por otro` (
  `Grupo proveedor` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`Grupo proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_ter_grupo
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_grupo`;
CREATE TABLE `par_ter_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clasificacion` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_motivo
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_motivo`;
CREATE TABLE `par_ter_motivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motivo` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_ter_novedad
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_novedad`;
CREATE TABLE `par_ter_novedad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `novedad` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for par_ter_otroid - se cambio por otro
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_otroid - se cambio por otro`;
CREATE TABLE `par_ter_otroid - se cambio por otro` (
  `Id` int(11) NOT NULL,
  `Otro doc identidad` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for par_ter_prov_grupo - se cambio por otro
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_prov_grupo - se cambio por otro`;
CREATE TABLE `par_ter_prov_grupo - se cambio por otro` (
  `grupo` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tipo
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tipo`;
CREATE TABLE `par_ter_tipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clasificacion` varchar(100) DEFAULT '',
  `tipo` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tipo_doc
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tipo_doc`;
CREATE TABLE `par_ter_tipo_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_doc` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tipo_nomina
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tipo_nomina`;
CREATE TABLE `par_ter_tipo_nomina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tipo_proveedor
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tipo_proveedor`;
CREATE TABLE `par_ter_tipo_proveedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tipo_sociedad
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tipo_sociedad`;
CREATE TABLE `par_ter_tipo_sociedad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_sociedad` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for par_ter_tprov - se cambio por otro
-- ----------------------------
DROP TABLE IF EXISTS `par_ter_tprov - se cambio por otro`;
CREATE TABLE `par_ter_tprov - se cambio por otro` (
  `Tipo proveedor` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`Tipo proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- ----------------------------
-- Table structure for presupuesto
-- ----------------------------
DROP TABLE IF EXISTS `presupuesto`;
CREATE TABLE `presupuesto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `subtotal` decimal(20,2) NOT NULL DEFAULT '0.00',
  `administracion1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `administracion2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `imprevistos1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `imprevistos2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `utilidades1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `utilidades2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva1_check` enum('true','false') NOT NULL DEFAULT 'true',
  `iva2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva2_check` enum('true','false') NOT NULL DEFAULT 'false',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `notas` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for presupuesto_movs
-- ----------------------------
DROP TABLE IF EXISTS `presupuesto_movs`;
CREATE TABLE `presupuesto_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `item` varchar(100) NOT NULL DEFAULT '',
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `clasificacion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for presupuesto_productos
-- ----------------------------
DROP TABLE IF EXISTS `presupuesto_productos`;
CREATE TABLE `presupuesto_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `categoria` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `unidad` varchar(100) NOT NULL,
  `precio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `notas` varchar(100) NOT NULL DEFAULT '',
  `proveedor` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for produccion_final
-- ----------------------------
DROP TABLE IF EXISTS `produccion_final`;
CREATE TABLE `produccion_final` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `solicitud` varchar(100) NOT NULL DEFAULT '',
  `orden_compra` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `cartilla` varchar(100) NOT NULL DEFAULT '',
  `destino` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `operario_trefilado` varchar(100) NOT NULL DEFAULT '',
  `operario_enderezado` varchar(100) NOT NULL DEFAULT '',
  `operario_electrosoldado` varchar(100) NOT NULL DEFAULT '',
  `operario_figurado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3682 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for produccion_movs
-- ----------------------------
DROP TABLE IF EXISTS `produccion_movs`;
CREATE TABLE `produccion_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo` varchar(100) NOT NULL DEFAULT '',
  `origen` varchar(100) NOT NULL DEFAULT '',
  `destino` varchar(100) NOT NULL DEFAULT '',
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `orden_compra` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19985 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for produccion_proc
-- ----------------------------
DROP TABLE IF EXISTS `produccion_proc`;
CREATE TABLE `produccion_proc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `solicitud` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `proceso` varchar(100) NOT NULL DEFAULT '',
  `rendimiento` decimal(20,2) NOT NULL DEFAULT '0.00',
  `avance` decimal(20,2) NOT NULL DEFAULT '0.00',
  `desperdicio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `observaciones` varchar(100) NOT NULL DEFAULT '',
  `iniciado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_ini` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finalizado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_fin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3736 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for produccion_proc_movs
-- ----------------------------
DROP TABLE IF EXISTS `produccion_proc_movs`;
CREATE TABLE `produccion_proc_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo` varchar(100) NOT NULL DEFAULT '',
  `origen` varchar(100) NOT NULL DEFAULT '',
  `destino` varchar(100) NOT NULL DEFAULT '',
  `maquinaria` varchar(100) NOT NULL DEFAULT '',
  `operario` varchar(100) NOT NULL DEFAULT '',
  `orden_produccion` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20950 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `cod_fab` varchar(100) NOT NULL DEFAULT '',
  `categoria` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `und_med` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `k` decimal(20,3) NOT NULL DEFAULT '0.000',
  `uso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ultimo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo_promedio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista3` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista4` decimal(20,2) NOT NULL DEFAULT '0.00',
  `existencia` decimal(20,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `venta_promedio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `notas` varchar(255) NOT NULL DEFAULT '',
  `vencimiento` date NOT NULL DEFAULT '0000-00-00',
  `factura_sin_existencia` enum('false','true') NOT NULL DEFAULT 'false',
  `codigo_barra` varchar(100) NOT NULL DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '',
  `produccion` enum('false','true') NOT NULL DEFAULT 'false',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `ultima_actualizacion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activo` enum('true','false') NOT NULL DEFAULT 'true',
  `motivo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3088 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for productos_prov
-- ----------------------------
DROP TABLE IF EXISTS `productos_prov`;
CREATE TABLE `productos_prov` (
  `cod_fab` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for repuestos
-- ----------------------------
DROP TABLE IF EXISTS `repuestos`;
CREATE TABLE `repuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `parte` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for repuestos_partes
-- ----------------------------
DROP TABLE IF EXISTS `repuestos_partes`;
CREATE TABLE `repuestos_partes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parte` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Table structure for requerimientos_inventario
-- ----------------------------
DROP TABLE IF EXISTS `requerimientos_inventario`;
CREATE TABLE `requerimientos_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `viejo_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `existencia` decimal(20,2) NOT NULL DEFAULT '0.00',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `motivo` varchar(100) NOT NULL DEFAULT '',
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tipo` enum('Entrada','Salida') NOT NULL DEFAULT 'Entrada',
  `observacion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for req_compras
-- ----------------------------
DROP TABLE IF EXISTS `req_compras`;
CREATE TABLE `req_compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo2` varchar(100) NOT NULL DEFAULT '',
  `subtotal` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo_servicio` varchar(100) NOT NULL DEFAULT '',
  `tipo_servicio_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tipo_descuento` varchar(100) NOT NULL DEFAULT '',
  `tipo_descuento_valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total` decimal(20,2) NOT NULL DEFAULT '0.00',
  `observaciones` varchar(100) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(255) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for req_compras_movs
-- ----------------------------
DROP TABLE IF EXISTS `req_compras_movs`;
CREATE TABLE `req_compras_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  `interno` varchar(100) NOT NULL DEFAULT '',
  `factura` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for req_solicitud
-- ----------------------------
DROP TABLE IF EXISTS `req_solicitud`;
CREATE TABLE `req_solicitud` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `fecha` date NOT NULL DEFAULT '0000-00-00',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  `observaciones` varchar(255) NOT NULL DEFAULT '',
  `estado` varchar(100) NOT NULL DEFAULT '',
  `prioridad` varchar(100) NOT NULL DEFAULT '',
  `digitado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `anulado_por` varchar(100) NOT NULL DEFAULT '',
  `motivo_anulado` varchar(100) NOT NULL DEFAULT '',
  `fecha_anulado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modificado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aprobado_por` varchar(100) NOT NULL DEFAULT '',
  `fecha_aprobado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`,`remision`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for req_solicitud_movs
-- ----------------------------
DROP TABLE IF EXISTS `req_solicitud_movs`;
CREATE TABLE `req_solicitud_movs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL,
  `cantidad` decimal(20,2) NOT NULL DEFAULT '0.00',
  `valor` decimal(20,2) NOT NULL,
  `interno` varchar(100) NOT NULL DEFAULT '',
  `remision` varchar(100) NOT NULL DEFAULT '',
  `cliente_id` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
