-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-07-2016 a las 08:13:04
-- Versión del servidor: 5.5.42-MariaDB-cll-lve
-- Versión de PHP: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ingeniam_servicio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `par_inv_cat`
--

CREATE TABLE IF NOT EXISTS `par_inv_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `par_inv_cat`
--

INSERT INTO `par_inv_cat` (`id`, `categoria`) VALUES
(2, 'insumos'),
(3, 'productos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `par_inv_gr`
--

CREATE TABLE IF NOT EXISTS `par_inv_gr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=55 ;

--
-- Volcado de datos para la tabla `par_inv_gr`
--

INSERT INTO `par_inv_gr` (`id`, `grupo`, `categoria`) VALUES
(1, 'Grupo_Prueba', 'Cat_Prueba'),
(3, 'harina', 'insumos'),
(4, 'huevo', 'insumos'),
(5, 'azucar', 'insumos'),
(6, 'sal', 'insumos'),
(10, 'frutos secos ', 'insumos'),
(12, 'pan', 'productos'),
(14, 'pasteleria de sal', 'productos'),
(18, 'mantequilla', 'insumos'),
(19, 'vegetales', 'insumos'),
(20, 'frutas desidratadas', 'insumos'),
(21, 'aceites', 'insumos'),
(23, 'frutas', 'insumos'),
(24, 'pulpas', 'insumos'),
(25, 'chocolates', 'insumos'),
(26, 'cafe', 'insumos'),
(29, 'galleteria', 'productos'),
(30, 'charcuterias', 'insumos'),
(31, 'aderezos', 'insumos'),
(32, 'aceitunas', 'insumos'),
(33, 'especias secas', 'insumos'),
(34, 'vainillas', 'insumos'),
(35, 'rellenos pasteleria', 'insumos'),
(36, 'bases para bebida', 'insumos'),
(37, 'levaduras', 'insumos'),
(38, 'varios pasteleria', 'insumos'),
(40, 'saborizantes', 'insumos'),
(41, 'licores', 'insumos'),
(42, 'conservas', 'insumos'),
(43, 'lacteos liquidos', 'insumos'),
(44, 'carnes', 'insumos'),
(45, 'arroces', 'insumos'),
(46, 'pastas', 'insumos'),
(47, 'postres', 'productos'),
(48, 'cocina', 'productos'),
(49, 'adicionales', 'productos'),
(50, 'bebidas frias', 'productos'),
(51, 'bebidas calientes', 'productos'),
(52, 'bebidas varias', 'insumos'),
(53, 'agua', 'insumos'),
(54, 'empaque', 'insumos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `par_inv_motivo`
--

CREATE TABLE IF NOT EXISTS `par_inv_motivo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motivo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `par_inv_motivo`
--

INSERT INTO `par_inv_motivo` (`id`, `motivo`) VALUES
(1, 'Inicial'),
(2, 'Ajuste'),
(3, 'Factura'),
(4, 'Compra'),
(5, 'Produccion'),
(6, 'Despunte');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `par_inv_subgr`
--

CREATE TABLE IF NOT EXISTS `par_inv_subgr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subgrupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `grupo` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=470 ;

--
-- Volcado de datos para la tabla `par_inv_subgr`
--

INSERT INTO `par_inv_subgr` (`id`, `subgrupo`, `grupo`) VALUES
(1, 'SubGrupo_Prueba', 'Grupo_Prueba'),
(5, 'huevo', 'huevo'),
(6, 'azucar blanca', 'azucar'),
(8, 'azucar morena', 'azucar'),
(11, 'harina panadera', 'harina'),
(12, 'harina de centeno', 'harina'),
(13, 'pulverizada', 'azucar'),
(14, 'sal', 'sal'),
(16, 'fecula', 'fecula'),
(17, 'almendra', 'frutos secos'),
(18, 'pistacho', 'frutos secos'),
(22, 'almendra, avena y datiles', 'pan'),
(23, 'avena canela', 'pan'),
(24, 'da vinci', 'pan'),
(30, 'naranja', 'jugo natural'),
(31, 'mandarina', 'jugo natural'),
(32, 'limon', 'jugo natural'),
(33, 'mora', 'jugo natural'),
(34, 'limon', 'te'),
(35, 'chai', 'te'),
(36, 'te caliente', 'te'),
(38, 'mantequilla', 'mantequilla'),
(39, 'cebolla', 'vegetales'),
(40, 'tomate', 'vegetales'),
(41, 'pimenton', 'vegetales'),
(42, 'lechuga verde crespa', 'vegetales'),
(43, 'nuez de nogal', 'frutos secos'),
(44, 'nuez de brasil', 'frutos secos'),
(45, 'nuez pecan', 'frutos secos'),
(46, 'nuez macadamia', 'frutos secos'),
(47, 'almendra fileteada', 'frutos secos'),
(48, 'uva pasa', 'frutas desidratadas'),
(49, 'uva pasa rubia', 'frutas desidratadas'),
(50, 'uva pasa mosquito', 'frutas desidratadas'),
(51, 'uva pasa verde', 'frutas desidratadas'),
(52, 'arandano seco', 'frutas desidratadas'),
(53, 'ciruela pasa', 'frutas desidratadas'),
(54, 'naranja confitada', 'frutas desidratadas'),
(55, 'limon confitado', 'frutas desidratadas'),
(56, 'lechuga salanova', 'vegetales'),
(57, 'rugula', 'vegetales'),
(58, 'champiñones', 'vegetales'),
(59, 'puerro', 'vegetales'),
(60, 'ajo', 'vegetales'),
(61, 'aceite de oliva', 'aceites'),
(62, 'aceite de girasol', 'aceites'),
(63, 'vinagre balsamico', 'vinagres'),
(64, 'vinagre blanco', 'vinagres'),
(65, 'fresa', 'frutas'),
(66, 'mora', 'frutas'),
(67, 'agraz', 'frutas'),
(68, 'manzana', 'frutas'),
(69, 'pera', 'frutas'),
(70, 'limon', 'frutas'),
(71, 'naranja', 'zumos extraidos'),
(72, 'mandarina', 'zumos extraidos'),
(73, 'limon', 'zumos extraidos'),
(74, 'harina pastelera', 'harina'),
(75, 'harina organica', 'harina'),
(76, 'guanabana', 'pulpas'),
(77, 'mango', 'pulpas'),
(78, 'luker negro', 'chocolates'),
(79, 'luker blanco', 'chocolates'),
(80, 'belcolade blanco', 'chocolates'),
(81, 'belcolade leche', 'chocolates'),
(82, 'belcolade amargo', 'chocolates'),
(83, 'reno bianco', 'chocolates'),
(84, 'reno latte', 'chocolates'),
(85, 'belcosticks', 'chocolates'),
(88, 'molde de mantequilla', 'pan'),
(89, 'petit vienes', 'pan'),
(90, 'siete granos', 'pan'),
(91, 'seis granos', 'pan'),
(92, 'corona del rey', 'pan'),
(93, 'corona de la reina', 'pan'),
(94, 'etoile', 'pan'),
(95, 'brioche a tete', 'pan'),
(96, 'brioche de noel', 'pan'),
(97, 'brioche especial', 'pan'),
(98, 'trenzado', 'pan'),
(99, 'trenzado nevado', 'pan'),
(100, 'trenza malaga', 'pan'),
(101, 'trinitario de coco', 'pan'),
(102, 'trinitario de pasas', 'pan'),
(103, 'ciruela', 'pan'),
(104, 'avena con pasas', 'pan'),
(105, 'milhoja', 'postres de masas'),
(106, 'milhoja de nutella', 'postres de masas'),
(107, 'vol au vent de peras', 'postres de masas'),
(108, 'vol au vent de macadamia', 'postres de masas'),
(109, 'hojaldre de manzana', 'postres de masas'),
(110, 'clafoutis de frutos rojos', 'postres de masas'),
(111, 'flan frances', 'postres de masas'),
(112, 'tarte tatin', 'postres de masas'),
(113, 'torta de nutella', 'tortas'),
(114, 'torta de nutella blanca', 'tortas'),
(115, 'torta de queso', 'tortas'),
(116, 'torta de zanahoria', 'tortas'),
(117, 'cuatro cuartos de natas', 'tortas'),
(118, 'cuatro cuartos de naranja y almendra', 'tortas'),
(119, 'tejas de almendra', 'galleteria'),
(120, 'chip de chocolate', 'galleteria'),
(121, 'galleta de mantequilla', 'galleteria'),
(122, 'merengues', 'galleteria'),
(123, 'financieros', 'galleteria'),
(124, 'caneles', 'galleteria'),
(125, 'tornillos de canela', 'galleteria'),
(126, 'corazones de hojaldre', 'galleteria'),
(127, 'mon cherry', 'postres de crema'),
(128, 'deseo', 'postres de crema'),
(129, 'oro crema', 'postres de crema'),
(130, 'mon amour nero', 'postres de crema'),
(131, 'mon amour rosso', 'postres de crema'),
(132, 'malibu', 'postres de crema'),
(133, 'amanecer', 'postres de crema'),
(134, 'creme brulee', 'postres de crema'),
(135, 'pana cotta', 'postres de crema'),
(136, 'eclipse', 'postres de crema'),
(137, 'eclair ', 'postres de masas'),
(138, 'eclair liguelois', 'postres de masas'),
(139, 'profiterol', 'postres de masas'),
(140, 'prosciutto', 'charcuterias'),
(141, 'sopresata', 'charcuterias'),
(142, 'chorizo español', 'charcuterias'),
(143, 'jamon corriente', 'charcuterias'),
(144, 'queso doblecrema tajado', 'charcuterias'),
(145, 'queso doblecrema bloque', 'charcuterias'),
(146, 'queso suizo', 'charcuterias'),
(147, 'queso azul', 'charcuterias'),
(148, 'queso manchego', 'charcuterias'),
(149, 'mayonesa', 'aderezos'),
(150, 'mostaza', 'aderezos'),
(151, 'vinagre balsamico', 'aderezos'),
(152, 'vinagre de shallots', 'aderezos'),
(153, 'chips de chocolate', 'chocolates'),
(154, 'aceitunas rellenas de pimenton', 'aceitunas'),
(155, 'aceitunas rellenas', 'aceitunas'),
(156, 'aceitunas negras tajadas', 'aceitunas'),
(157, 'aceitunas kalamata', 'aceitunas'),
(158, 'alcaparras', 'aceitunas'),
(159, 'canela', 'especias'),
(160, 'nuez moscada', 'especias'),
(161, 'pimienta', 'especias'),
(163, 'laurel', 'especias'),
(164, 'tomillo', 'especias'),
(165, 'oregano', 'especias secas'),
(166, 'albahaca', 'especias secas'),
(167, 'albahaca', 'especias secas'),
(168, 'tomillo', 'especias secas'),
(169, 'canela', 'especias secas'),
(170, 'nuez moscada', 'especias secas'),
(171, 'pimienta', 'especias secas'),
(172, 'pimienta rosada', 'especias secas'),
(173, 'azucar antihumedad', 'azucar'),
(174, 'vainas', 'vainillas'),
(175, 'purissima', 'vainillas'),
(176, 'pastaroma', 'vainillas'),
(177, 'sirope', 'vainillas'),
(178, 'finas hierbas', 'especias secas'),
(179, 'pimienta para decorar', 'especias secas'),
(180, 'nutella blanca', 'rellenos pasteleria'),
(181, 'nutella negra', 'rellenos pasteleria'),
(182, 'amarenas', 'rellenos pasteleria'),
(183, 'milo', 'bases para bebida'),
(184, 'toddy', 'bases para bebida'),
(185, 'te verde', 'bases para bebida'),
(186, 'te de limon', 'bases para bebida'),
(187, 'te chai', 'bases para bebida'),
(188, 'coco en cabello', 'frutas desidratadas'),
(189, 'coco en escama', 'frutas desidratadas'),
(190, 'levadura fresca', 'levaduras'),
(191, 'polvo de hornear', 'levaduras'),
(192, 'bicarbonato', 'levaduras'),
(193, 'glucosa', 'varios pasteleria'),
(194, 'gelatina sin sabor', 'varios pasteleria'),
(196, 'base de helado', 'varios pasteleria'),
(197, 'mascarpone en polvo', 'saborizantes'),
(198, 'pistacho', 'saborizantes'),
(199, 'pasta de almendra', 'saborizantes'),
(200, 'pino pinguino', 'saborizantes'),
(201, 'mango glaseado', 'saborizantes'),
(202, 'fortefrutto frambuesa', 'saborizantes'),
(203, 'fortefrutto fruto del bosque', 'saborizantes'),
(206, 'ron', 'licores'),
(207, 'frangelico', 'licores'),
(208, 'naranja convier', 'licores'),
(209, 'brandy', 'licores'),
(210, 'jerez', 'licores'),
(211, 'licor de frambuesa', 'licores'),
(212, 'trinitario de coco', 'licores'),
(213, 'trinitario de pasas', 'licores'),
(214, 'otoño', 'licores'),
(215, 'selva negra', 'licores'),
(216, 'amaretto', 'licores'),
(217, 'trablit', 'licores'),
(218, 'pasta de avellana', 'saborizantes'),
(220, 'farcitura albaricoque', 'saborizantes'),
(222, 'mermelada artesanal', 'conservas'),
(223, 'farcitura albaricoque', 'conservas'),
(224, 'pera en almibar', 'conservas'),
(225, 'torrijas de mandarina', 'conservas'),
(226, 'textura nordica', 'varios pasteleria'),
(227, 'base helado', 'varios pasteleria'),
(228, 'aceite de trufa', 'aderezos'),
(229, 'hongos porcini', 'aderezos'),
(230, 'nuez marañon', 'frutos secos'),
(231, 'ajonjoli', 'frutos secos'),
(232, 'semilla de amapola', 'frutos secos'),
(233, 'semilla de girasol', 'frutos secos '),
(234, 'semilla de calabaza', 'frutos secos '),
(235, 'semilla de chia', 'frutos secos '),
(236, 'semilla de linaza', 'frutos secos '),
(237, 'avellana', 'frutos secos '),
(238, 'azucar granela', 'azucar'),
(239, 'cereza seca', 'frutas desidratadas'),
(240, 'higos secos', 'frutas desidratadas'),
(241, 'datiles', 'frutas desidratadas'),
(242, 'leche entera bolsa', 'lacteos liquidos'),
(243, 'leche entera carton', 'lacteos liquidos'),
(244, 'leche deslactosada carton', 'lacteos liquidos'),
(245, 'crema de leche en bolsa', 'lacteos liquidos'),
(246, 'queso crema', 'charcuterias'),
(247, 'leche en polvo', 'varios pasteleria'),
(248, 'cremor tartaro', 'varios pasteleria'),
(249, 'fecula', 'varios pasteleria'),
(250, 'cacao en polvo', 'chocolates'),
(251, 'pechuga de pollo', 'carnes'),
(252, 'carne molida', 'carnes'),
(253, 'aguja', 'carnes'),
(254, 'chata', 'carnes'),
(255, 'ternera', 'carnes'),
(256, 'cerdo', 'carnes'),
(257, 'tocineta', 'charcuterias'),
(258, 'malaga', 'saborizantes'),
(259, 'pastaroma de naranja', 'saborizantes'),
(260, 'sal maldon', 'sal'),
(261, 'sal gruesa en escamas', 'sal'),
(262, 'alcachofas', 'conservas'),
(263, 'crema de leche en carton', 'lacteos liquidos'),
(264, 'arroz basmati', 'arroces'),
(265, 'arroz jasmin', 'arroces'),
(266, 'carnaroli', 'arroces'),
(267, 'arborio', 'arroces'),
(268, 'arroz corriente', 'arroces'),
(269, 'farfalle', 'pastas'),
(270, 'espaguetti', 'pastas'),
(271, 'penne', 'pastas'),
(272, 'milhoja', 'postres'),
(273, 'milhoja de nutella', 'postres'),
(274, 'mon cherry', 'postres'),
(275, 'oro crema', 'postres'),
(276, 'arandano', 'frutas'),
(277, 'frambuesa', 'frutas'),
(278, 'chocolate bebida', 'chocolates'),
(279, 'boconcini', 'charcuterias'),
(280, 'parmesano', 'charcuterias'),
(281, 'cuatro cuartos de naranja y almendra', 'postres'),
(282, 'cuatro cuartos de nata ', 'postres'),
(283, 'vol au vent de pera', 'postres'),
(284, 'hojaldre de manzanas', 'postres'),
(285, 'clafoutis', 'postres'),
(286, 'croissant', 'postres'),
(287, 'croissant de almendra', 'postres'),
(288, 'sandwich jamon y queso', 'cocina'),
(289, 'sandwich bourguignon', 'cocina'),
(290, 'croque monseiur', 'cocina'),
(291, 'croque madame', 'cocina'),
(292, 'pizza del panadero', 'cocina'),
(293, 'pizza don quichote', 'cocina'),
(294, 'pizza de la casa', 'cocina'),
(295, 'pizza paulie peninno', 'cocina'),
(296, 'ensalada de frutos secos', 'cocina'),
(297, 'ensalada de la casa', 'cocina'),
(298, 'quiche', 'pasteleria de sal'),
(299, 'hojaldre stroganoff', 'pasteleria de sal'),
(300, 'hojaldre filete mignon', 'pasteleria de sal'),
(301, 'pasteles de pollo', 'pasteleria de sal'),
(302, 'pasteles serranos', 'pasteleria de sal'),
(303, 'tornillos de queso', 'pasteleria de sal'),
(304, 'antipasto de boconcini', 'pasteleria de sal'),
(305, 'seleccion curados', 'cocina'),
(306, 'tapa española', 'cocina'),
(307, 'baguette', 'pan'),
(308, 'miche de campo', 'pan'),
(309, 'baguette parisien con oregano', 'pan'),
(310, 'rustico de masa madre', 'pan'),
(311, 'petit marcel', 'pan'),
(312, 'tartaleta', 'postres'),
(313, 'mon amour nero', 'postres'),
(314, 'mon amour rosso', 'postres'),
(315, 'malibu', 'postres'),
(316, 'amanecer', 'postres'),
(317, 'concorde', 'postres'),
(318, 'kinder', 'postres'),
(319, 'vol au vent nueces', 'postres'),
(320, 'macaron', 'galleteria'),
(321, 'huevos florentinos', 'cocina'),
(322, 'huevos al gusto', 'cocina'),
(323, 'huevos sergio frances', 'cocina'),
(324, 'huevos sergio italiano', 'cocina'),
(325, 'huevos sergio italiano con prosciutto', 'cocina'),
(326, 'huevos provenzal', 'cocina'),
(328, 'huevos campesinos', 'cocina'),
(329, 'huevos poche rellenos', 'cocina'),
(330, 'huevos benedictinos', 'cocina'),
(331, 'desayuno boulanger', 'cocina'),
(332, 'desayuno boulanger con huevos especiales', 'cocina'),
(333, 'sandwich bonapart', 'cocina'),
(334, 'napoleon', 'pan'),
(335, 'antipasto 1', 'pasteleria de sal'),
(336, 'antipasto 2', 'pasteleria de sal'),
(338, 'tostadas francesas', 'cocina'),
(339, 'omelette rellena', 'cocina'),
(340, 'omelette sola', 'cocina'),
(341, 'omelette de dieta', 'cocina'),
(342, 'tostadas francesas con nutella y helado', 'cocina'),
(343, 'croissant caliente con nutella y helado', 'cocina'),
(344, 'croissant relleno', 'cocina'),
(345, 'croissant con esparragos y queso azul', 'cocina'),
(346, 'canasta de panes', 'cocina'),
(348, 'volcan de chocolate con helado artesanal', 'cocina'),
(349, 'copa de helado artesanal', 'cocina'),
(350, 'jamon', 'adicionales'),
(351, 'queso', 'adicionales'),
(352, 'prosciutto', 'adicionales'),
(353, 'chorizo español', 'adicionales'),
(354, 'salami', 'adicionales'),
(355, 'helado', 'adicionales'),
(356, 'pan', 'adicionales'),
(357, 'panecillos de la casa', 'adicionales'),
(358, 'aceite de oliva', 'adicionales'),
(359, 'mermelada', 'adicionales'),
(360, 'mantequilla', 'adicionales'),
(361, 'queso importado', 'adicionales'),
(362, 'jugo de limon', 'adicionales'),
(363, 'crepes sucree', 'cocina'),
(364, 'crepes con suzette', 'cocina'),
(365, 'crepes de nutella', 'cocina'),
(366, 'pimiento asado', 'adicionales'),
(367, 'cebolla caramelizada', 'adicionales'),
(368, 'crepes seleccion', 'cocina'),
(369, 'crepes emperador', 'cocina'),
(370, 'crepe salami, pesto y rugula', 'cocina'),
(372, 'sopa de cebolla', 'cocina'),
(373, 'crema de tomate', 'cocina'),
(374, 'crostini', 'cocina'),
(375, 'ensalada nueva era', 'cocina'),
(376, 'panini tres quesos y aceitunas', 'cocina'),
(377, 'panini con prosciutto y parmesano', 'cocina'),
(378, 'sandwich de queso asado', 'cocina'),
(379, 'frappe de naranja', 'jugo natural'),
(380, 'frappe de mandarina', 'jugo natural'),
(381, 'frappe de limon', 'jugo natural'),
(385, 'jugo de naranja', 'bebidas frias'),
(386, 'jugo de mandarina', 'bebidas frias'),
(387, 'jugo de mora', 'bebidas frias'),
(388, 'limonada', 'bebidas frias'),
(389, 'frappe de naranja', 'bebidas frias'),
(390, 'frappe de mandarina', 'bebidas frias'),
(391, 'frappe de limon', 'bebidas frias'),
(393, 'te de limon', 'bebidas frias'),
(394, 'te chai', 'bebidas frias'),
(395, 'cafe al frappe', 'bebidas frias'),
(396, 'batido de frambuesa', 'bebidas frias'),
(397, 'milo frio', 'bebidas frias'),
(398, 'toddy frio', 'bebidas frias'),
(399, 'jugo de guanabana', 'bebidas frias'),
(400, 'jugo de mango', 'bebidas frias'),
(401, 'jugo de maracuya', 'bebidas frias'),
(402, 'jugo de  guanabana en leche', 'bebidas frias'),
(403, 'jugo de mango en leche', 'bebidas frias'),
(404, 'jugo de mora en leche', 'bebidas frias'),
(405, 'jugo de fesa', 'bebidas frias'),
(406, 'jugo de fresa en leche', 'bebidas frias'),
(407, 'capuccino', 'bebidas calientes'),
(408, 'espresso', 'bebidas calientes'),
(409, 'espresso doble', 'bebidas calientes'),
(410, 'americano', 'bebidas calientes'),
(411, 'manchado', 'bebidas calientes'),
(412, 'cafe moka', 'bebidas calientes'),
(413, 'te organico', 'bebidas calientes'),
(414, 'te corriente', 'bebidas calientes'),
(416, 'chocolate de la casa', 'bebidas calientes'),
(417, 'milo caliente', 'bebidas calientes'),
(418, 'toddy caliente', 'bebidas calientes'),
(419, 'coca cola', 'bebidas frias'),
(420, 'coca cola zero', 'bebidas frias'),
(421, 'coca cola light', 'bebidas frias'),
(422, 'agua', 'bebidas frias'),
(423, 'agua con gas', 'bebidas frias'),
(424, 'limonada con chia', 'bebidas frias'),
(425, 'melange de coco', 'bebidas frias'),
(426, 'melange de frutos rojos', 'bebidas frias'),
(427, 'melange mango, maracuya y guanabana', 'bebidas frias'),
(428, 'leche merengada', 'bebidas frias'),
(429, 'te verde bajo en calorias', 'bebidas frias'),
(430, 'petit vienes chocolate', 'pan'),
(431, 'fresa', 'pulpas'),
(432, 'mora', 'pulpas'),
(433, 'maracuya', 'pulpas'),
(434, 'naranja', 'pulpas'),
(435, 'mandarina', 'pulpas'),
(436, 'limon', 'pulpas'),
(437, 'coca cola zero', 'bebidas varias'),
(438, 'coca cola light', 'bebidas varias'),
(439, 'coca cola', 'bebidas varias'),
(440, 'agua', 'bebidas varias'),
(441, 'agua con gas', 'bebidas varias'),
(442, 'splenda sobre', 'azucar'),
(443, 'azucar organico sobre', 'azucar'),
(444, 'azucar light sobre', 'azucar'),
(445, 'azucar blanca sobre', 'azucar'),
(446, 'harina de chia', 'harina'),
(447, 'harina de quinua', 'harina'),
(448, 'agua', 'agua'),
(449, 'bolsa baguette', 'empaque'),
(450, 'bolsa 12 libras', 'empaque'),
(451, 'bolsa 6 libras', 'empaque'),
(452, 'bolsa 3 libras', 'empaque'),
(453, 'disco dorado monoporcion', 'empaque'),
(454, 'base negra personalizad', 'empaque'),
(455, 'base dorada 22 cm', 'empaque'),
(456, 'caja dos milhojas', 'empaque'),
(457, 'caja 5 milhojas', 'empaque'),
(458, 'caja torta', 'empaque'),
(459, 'cafe devotion', 'cafe'),
(460, 'cafe frontera', 'cafe'),
(461, 'cafe sello rojo', 'cafe'),
(462, 'mantequilla de mani', 'mantequilla'),
(463, 'tahini de ajonjoli', 'mantequilla'),
(464, 'masa madre', 'levaduras'),
(465, 'caja bebe', 'empaque'),
(466, 'caja macaron', 'empaque'),
(467, 'arequipe alpina', 'varios pasteleria'),
(468, 'manjar blanco', 'varios pasteleria'),
(469, 'miel', 'varios pasteleria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `par_inv_und`
--

CREATE TABLE IF NOT EXISTS `par_inv_und` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad` varchar(100) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `unidad` (`unidad`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=29 ;

--
-- Volcado de datos para la tabla `par_inv_und`
--

INSERT INTO `par_inv_und` (`id`, `unidad`) VALUES
(26, 'bolsa'),
(11, 'Botella'),
(18, 'botella 10 oz'),
(23, 'botella 250 ml'),
(22, 'botella 300 ml'),
(20, 'botella 350 ml'),
(19, 'botella 6.5 oz'),
(25, 'botella 750 ml'),
(4, 'carton x 30'),
(24, 'copa'),
(6, 'gramos'),
(17, 'grande'),
(2, 'kg'),
(5, 'libra'),
(28, 'litro'),
(27, 'LT'),
(8, 'Mediano'),
(9, 'Personal'),
(10, 'Porcion'),
(21, 'rebanada'),
(16, 'sobre'),
(1, 'und'),
(15, 'Vaso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(18) NOT NULL AUTO_INCREMENT,
  `cod_fab` varchar(100) NOT NULL DEFAULT '',
  `categoria` varchar(100) NOT NULL DEFAULT '',
  `grupo` varchar(100) NOT NULL DEFAULT '',
  `subgrupo` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(100) NOT NULL DEFAULT '',
  `und_med` varchar(100) NOT NULL DEFAULT '',
  `peso` decimal(20,3) NOT NULL DEFAULT '0.000',
  `k` decimal(20,3) NOT NULL DEFAULT '0.000',
  `uso` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ultimo_costo` decimal(20,2) NOT NULL DEFAULT '0.00',
  `costo_promedio` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista1` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista2` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista3` decimal(20,2) NOT NULL DEFAULT '0.00',
  `lista4` decimal(20,2) NOT NULL DEFAULT '0.00',
  `existencia` decimal(20,3) NOT NULL DEFAULT '0.000',
  `existencia_peso` decimal(20,3) NOT NULL DEFAULT '0.000',
  `stock_minimo` decimal(20,3) NOT NULL DEFAULT '0.000',
  `stock_maximo` decimal(20,3) NOT NULL DEFAULT '0.000',
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
  PRIMARY KEY (`id`),
  KEY `Codigo` (`cod_fab`) USING BTREE,
  KEY `existencia_peso` (`existencia_peso`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=112 ;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `cod_fab`, `categoria`, `grupo`, `subgrupo`, `nombre`, `und_med`, `peso`, `k`, `uso`, `costo`, `ultimo_costo`, `costo_promedio`, `lista1`, `lista2`, `lista3`, `lista4`, `existencia`, `existencia_peso`, `stock_minimo`, `stock_maximo`, `notas`, `vencimiento`, `factura_sin_existencia`, `codigo_barra`, `image`, `produccion`, `digitado_por`, `fecha_digitado`, `modificado_por`, `ultima_actualizacion`, `activo`, `motivo`) VALUES
(12, 'hp50', 'insumos', 'harina', 'harina panadera', 'harina panadera', 'kg', '1.000', '0.000', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '20.000', '100.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:00:31', '', '0000-00-00 00:00:00', 'true', ''),
(13, 'hv30', 'insumos', 'huevo', 'huevo', 'huevo', 'und', '0.060', '0.000', '0.00', '300.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '30.000', '500.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:02:54', '', '0000-00-00 00:00:00', 'true', ''),
(14, 'azb50', 'insumos', 'azucar', 'azucar blanca', 'azucar blanca', 'kg', '1.000', '0.000', '0.00', '2950.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '20.000', '100.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:04:01', '', '0000-00-00 00:00:00', 'true', ''),
(15, 'slb3', 'insumos', 'sal', 'sal', 'sal comun', 'kg', '1.000', '0.000', '0.00', '800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '3.000', '15.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:05:12', '', '0000-00-00 00:00:00', 'true', ''),
(16, 'lv500', 'insumos', 'levaduras', 'levadura fresca', 'levadura fresca', 'kg', '1.000', '0.000', '0.00', '12000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.050', '3.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:07:07', '', '0000-00-00 00:00:00', 'true', ''),
(17, 'mama', 'insumos', 'harina', 'harina panadera', 'masa madre', 'kg', '1.000', '0.000', '0.00', '820.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '4.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:10:31', '', '0000-00-00 00:00:00', 'true', ''),
(18, 'ag01', 'insumos', 'agua', 'agua', 'agua', 'kg', '1.000', '0.000', '0.00', '0.01', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '1.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:14:08', '', '0000-00-00 00:00:00', 'true', ''),
(19, 'bg01', 'productos', 'pan', 'baguette', 'baguette', 'und', '0.390', '0.000', '0.00', '528.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '2016-05-31', 'false', '', '', 'true', 'SISTEMA', '2016-05-31 19:17:53', '', '0000-00-00 00:00:00', 'true', ''),
(20, 'bolb01', 'insumos', 'empaque', 'bolsa baguette', 'bolsa baguette', 'und', '0.010', '0.000', '0.00', '90.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '200.000', '15000.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:27:13', '', '0000-00-00 00:00:00', 'true', ''),
(21, 'bagpo', 'productos', 'pan', 'baguette parisien con oregano', 'baguette parisien con oregano', 'und', '0.250', '0.000', '0.00', '866.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '0000-00-00', '', '', '', 'true', 'SISTEMA', '2016-05-31 19:31:52', '', '0000-00-00 00:00:00', 'true', ''),
(22, 'or01', 'insumos', 'especias secas', 'oregano', 'oregano seco', 'kg', '1.000', '0.000', '0.00', '40000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '2.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:35:00', '', '0000-00-00 00:00:00', 'true', ''),
(23, 'juicenar', 'productos', 'bebidas frias', 'jugo de naranja', 'jugo de naranja', 'botella 250 ml', '0.250', '0.000', '0.00', '1500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '5.000', '15.000', '', '2016-05-31', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 19:44:06', '', '0000-00-00 00:00:00', 'true', ''),
(24, 'mant01', 'insumos', 'mantequilla', 'mantequilla', 'mantequilla', 'kg', '1.000', '0.000', '0.00', '11500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '10.000', '300.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:11:21', '', '0000-00-00 00:00:00', 'true', ''),
(25, 'alm01', 'insumos', 'frutos secos ', 'almendra', 'almendra natural', 'kg', '1.000', '0.000', '0.00', '35000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '4.000', '100.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:12:46', '', '0000-00-00 00:00:00', 'true', ''),
(26, 'alm02', 'insumos', 'frutos secos ', 'almendra fileteada', 'almendra fileteada', 'kg', '1.000', '0.000', '0.00', '50000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '20.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:13:33', '', '0000-00-00 00:00:00', 'true', ''),
(27, 'pist01', 'insumos', 'frutos secos ', 'pistacho', 'pistacho pelado', 'kg', '1.000', '0.000', '0.00', '90000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:14:35', '', '0000-00-00 00:00:00', 'true', ''),
(28, 'nogal01', 'insumos', 'frutos secos ', 'nuez de nogal', 'nuez de nogal', 'kg', '1.000', '0.000', '0.00', '50000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.000', '50.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:15:36', '', '0000-00-00 00:00:00', 'true', ''),
(29, 'nuezbr01', 'insumos', 'frutos secos ', 'nuez de brasil', 'nuez de brasil', 'kg', '1.000', '0.000', '0.00', '30000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:16:21', '', '0000-00-00 00:00:00', 'true', ''),
(30, 'pecan01', 'insumos', 'frutos secos ', 'nuez pecan', 'nuez pecan', 'kg', '1.000', '0.000', '0.00', '94000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.050', '2.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:17:28', '', '0000-00-00 00:00:00', 'true', ''),
(31, 'macad01', 'insumos', 'frutos secos ', 'nuez macadamia', 'nuez macadamia', 'kg', '1.000', '0.000', '0.00', '75000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.020', '2.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:18:51', '', '0000-00-00 00:00:00', 'true', ''),
(32, 'marañ01', 'insumos', 'frutos secos ', 'nuez marañon', 'nuez marañon', 'kg', '1.000', '0.000', '0.00', '56000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '0000-00-00', '', '', '', 'false', 'SISTEMA', '2016-05-31 22:23:22', '', '0000-00-00 00:00:00', 'true', ''),
(33, 'ajon01', 'insumos', 'frutos secos ', 'ajonjoli', 'ajonjoli', 'kg', '1.000', '0.000', '0.00', '11000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:28:13', '', '0000-00-00 00:00:00', 'true', ''),
(34, 'amap01', 'insumos', 'frutos secos ', 'semilla de amapola', 'semilla de amapola', 'kg', '1.000', '0.000', '0.00', '46000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:30:25', '', '0000-00-00 00:00:00', 'true', ''),
(35, 'gira01', 'insumos', 'frutos secos ', 'semilla de girasol', 'semilla de girasol', 'kg', '1.000', '0.000', '0.00', '19000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:32:36', '', '0000-00-00 00:00:00', 'true', ''),
(36, 'calab01', 'insumos', 'frutos secos ', 'semilla de calabaza', 'semilla de calabaza', 'kg', '1.000', '0.000', '0.00', '102000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.250', '1.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:34:38', '', '0000-00-00 00:00:00', 'true', ''),
(37, 'chia01', 'insumos', 'frutos secos ', 'semilla de chia', 'semilla de chia', 'kg', '1.000', '0.000', '0.00', '19500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '4.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:36:53', '', '0000-00-00 00:00:00', 'true', ''),
(38, 'lin01', 'insumos', 'frutos secos ', 'semilla de linaza', 'linaza', 'kg', '1.000', '0.000', '0.00', '7000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.250', '4.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:38:48', '', '0000-00-00 00:00:00', 'true', ''),
(39, 'ave01', 'insumos', 'frutos secos ', 'avellana', 'avellana', 'kg', '1.000', '0.000', '0.00', '99000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.010', '1.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 22:40:45', '', '0000-00-00 00:00:00', 'true', ''),
(40, 'upasa01', 'insumos', 'frutas desidratadas', 'uva pasa', 'uva pasa', 'kg', '1.000', '0.000', '0.00', '7000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.000', '100.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:06:16', '', '0000-00-00 00:00:00', 'true', ''),
(41, 'upasa03', 'insumos', 'frutas desidratadas', 'uva pasa rubia', 'uva pasa rubia', 'kg', '1.000', '0.000', '0.00', '12000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '40.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:07:56', '', '0000-00-00 00:00:00', 'true', ''),
(42, 'upasa02', 'insumos', 'frutas desidratadas', 'uva pasa mosquito', 'uva pasa mosquito', 'kg', '1.000', '0.000', '0.00', '8000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '10.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:09:33', '', '0000-00-00 00:00:00', 'true', ''),
(43, 'upasa04', 'insumos', 'frutas desidratadas', 'uva pasa verde', 'uva pasa verde', 'kg', '1.000', '0.000', '0.00', '13000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:10:36', '', '0000-00-00 00:00:00', 'true', ''),
(44, 'arsec01', 'insumos', 'frutas desidratadas', 'arandano seco', 'arandano seco', 'kg', '1.000', '0.000', '0.00', '24100.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '2.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:12:28', '', '0000-00-00 00:00:00', 'true', ''),
(45, 'cirpas01', 'insumos', 'frutas desidratadas', 'ciruela pasa', 'ciruela pasa', 'kg', '1.000', '0.000', '0.00', '100000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '30.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:13:54', '', '0000-00-00 00:00:00', 'true', ''),
(46, 'nconf01', 'insumos', 'frutas desidratadas', 'naranja confitada', 'naranja confitada en casa', 'kg', '1.000', '0.000', '0.00', '13000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '25.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:15:12', '', '0000-00-00 00:00:00', 'true', ''),
(47, 'lconf01', 'insumos', 'frutas desidratadas', 'limon confitado', 'limon confitado en casa', 'kg', '1.000', '0.000', '0.00', '15000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '15.000', '', '0000-00-00', '', '', '', 'false', 'SISTEMA', '2016-05-31 23:16:15', '', '0000-00-00 00:00:00', 'true', ''),
(48, 'coc01', 'insumos', 'frutas desidratadas', 'coco en cabello', 'coco en cabello', 'kg', '1.000', '0.000', '0.00', '21000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:17:52', '', '0000-00-00 00:00:00', 'true', ''),
(49, 'coc02', 'insumos', 'frutas desidratadas', 'coco en escama', 'coco en escama', 'kg', '1.000', '0.000', '0.00', '25600.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '4.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:19:24', '', '0000-00-00 00:00:00', 'true', ''),
(50, 'cer01', 'insumos', 'frutas desidratadas', 'cereza seca', 'cereza confitada', 'kg', '1.000', '0.000', '0.00', '93500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '0.500', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:20:50', '', '0000-00-00 00:00:00', 'true', ''),
(51, 'hsec01', 'insumos', 'frutas desidratadas', 'higos secos', 'higos secos', 'kg', '1.000', '0.000', '0.00', '40000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.000', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:22:05', '', '0000-00-00 00:00:00', 'true', ''),
(52, 'dat01', 'insumos', 'frutas desidratadas', 'datiles', 'datiles sin semilla', 'kg', '1.000', '0.000', '0.00', '22000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '20.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:22:50', '', '0000-00-00 00:00:00', 'true', ''),
(53, 'aov01', 'insumos', 'aceites', 'aceite de oliva', 'aceite de oliva extra virgen', 'kg', '1.000', '0.000', '0.00', '22500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.000', '20.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:27:55', '', '0000-00-00 00:00:00', 'true', ''),
(54, 'acgir01', 'insumos', 'aceites', 'aceite de girasol', 'aceite de girasol', 'kg', '1.000', '0.000', '0.00', '7500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2.000', '0.000', '0.500', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:31:06', '', '0000-00-00 00:00:00', 'true', ''),
(55, 'mmani01', 'insumos', 'mantequilla', 'mantequilla de mani', 'mantequilla de mani', 'kg', '1.000', '0.000', '0.00', '18900.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '1.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:40:52', '', '0000-00-00 00:00:00', 'true', ''),
(56, 'tah01', 'insumos', 'mantequilla', 'tahini de ajonjoli', 'tahini de ajonjoli', 'kg', '1.000', '0.000', '0.00', '24400.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '4.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:42:36', '', '0000-00-00 00:00:00', 'true', ''),
(57, 'cf01', 'insumos', 'cafe', 'cafe frontera', 'cafe frontera', 'kg', '1.000', '0.000', '0.00', '24000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.000', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:43:56', '', '0000-00-00 00:00:00', 'true', ''),
(58, 'cd01', 'insumos', 'cafe', 'cafe devotion', 'cafe devotion', 'kg', '1.000', '0.000', '0.00', '44000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '2.500', '10.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:45:19', '', '0000-00-00 00:00:00', 'true', ''),
(59, 'csr01', 'insumos', 'cafe', 'cafe sello rojo', 'cafe sello rojo para el personal', 'kg', '1.000', '0.000', '0.00', '14600.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:47:11', '', '0000-00-00 00:00:00', 'true', ''),
(60, 'ak01', 'insumos', 'aceitunas', 'aceitunas kalamata', 'aceitunas kalamata', 'kg', '1.000', '0.000', '0.00', '24500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:52:35', '', '0000-00-00 00:00:00', 'true', ''),
(61, 'acr01', 'insumos', 'aceitunas', 'aceitunas rellenas', 'aceitunas rellenas', 'kg', '1.000', '0.000', '0.00', '18000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.350', '40.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:56:49', '', '0000-00-00 00:00:00', 'true', ''),
(62, 'anr01', 'insumos', 'aceitunas', 'aceitunas negras tajadas', 'aceitunas negras en rodajas', 'kg', '1.000', '0.000', '0.00', '10000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.300', '8.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-05-31 23:58:32', '', '0000-00-00 00:00:00', 'true', ''),
(63, 'arp01', 'insumos', 'aceitunas', 'aceitunas rellenas de pimenton', 'aceitunas rellenas de pimenton', 'kg', '1.000', '0.000', '0.00', '13500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '20.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:01:37', '', '0000-00-00 00:00:00', 'true', ''),
(64, 'alca01', 'insumos', 'aceitunas', 'alcaparras', 'alcaparras', 'kg', '1.000', '0.000', '0.00', '13500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:02:49', '', '0000-00-00 00:00:00', 'true', ''),
(65, 'bals01', 'insumos', 'aderezos', 'vinagre balsamico', 'vinagre balsamico', 'kg', '1.000', '0.000', '0.00', '19030.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '12.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:05:42', '', '0000-00-00 00:00:00', 'true', ''),
(66, 'vsh01', 'insumos', 'aderezos', 'vinagre de shallots', 'vinagre de shallots', 'kg', '1.000', '0.000', '0.00', '18000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:09:22', '', '0000-00-00 00:00:00', 'true', ''),
(67, 'md01', 'insumos', 'aderezos', 'mostaza', 'mostaza dijon', 'kg', '1.000', '0.000', '0.00', '22000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '10.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:10:29', '', '0000-00-00 00:00:00', 'true', ''),
(68, 'my01', 'insumos', 'aderezos', 'mayonesa', 'mayonesa kraft', 'kg', '1.000', '0.000', '0.00', '11500.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '4.000', '20.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:11:18', '', '0000-00-00 00:00:00', 'true', ''),
(69, 'atb01', 'insumos', 'aderezos', 'aceite de trufa', 'aceite de trufa blanca', 'kg', '1.000', '0.000', '0.00', '640000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.050', '0.200', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:14:01', '', '0000-00-00 00:00:00', 'true', ''),
(70, 'hps01', 'insumos', 'aderezos', 'hongos porcini', 'hongos porcini secos', 'kg', '1.000', '0.000', '0.00', '162000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.150', '1.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 00:17:07', '', '0000-00-00 00:00:00', 'true', ''),
(71, 'gl01', 'insumos', 'varios pasteleria', 'glucosa', 'glucosa', 'kg', '1.000', '0.000', '0.00', '6800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '2.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 09:13:21', '', '0000-00-00 00:00:00', 'true', ''),
(72, 'fff01', 'insumos', 'saborizantes', 'fortefrutto frambuesa', 'fortefruto frambuesa', 'kg', '1.000', '0.000', '0.00', '45200.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '3.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 09:58:39', '', '0000-00-00 00:00:00', 'true', ''),
(74, 'pavep01', 'insumos', 'saborizantes', 'pasta de avellana', 'panacrema de avellana piamonte', 'kg', '1.000', '0.000', '0.00', '83670.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '0.100', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:04:55', '', '0000-00-00 00:00:00', 'true', ''),
(75, 'fdls01', 'insumos', 'varios pasteleria', 'base de helado', 'fior di latte soft', 'kg', '1.000', '0.000', '0.00', '43740.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '1.600', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:11:24', '', '0000-00-00 00:00:00', 'true', ''),
(76, 'tn01', 'insumos', 'varios pasteleria', 'textura nordica', 'textura nordica', 'kg', '1.000', '0.000', '0.00', '35330.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '12.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:13:01', '', '0000-00-00 00:00:00', 'true', ''),
(77, 'lp01', 'insumos', 'varios pasteleria', 'leche en polvo', 'leche en polvo', 'kg', '1.000', '0.000', '0.00', '11800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '30.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:14:18', '', '0000-00-00 00:00:00', 'true', ''),
(78, 'fffb01', 'insumos', 'saborizantes', 'fortefrutto fruto del bosque', 'fortefruto fruto del bosque', 'kg', '1.000', '0.000', '0.00', '41180.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.200', '6.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:19:13', '', '0000-00-00 00:00:00', 'true', ''),
(79, 'mp30', 'insumos', 'saborizantes', 'mascarpone en polvo', 'mascarpone en polvo', 'kg', '1.000', '0.000', '0.00', '55765.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '9.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:22:29', '', '0000-00-00 00:00:00', 'true', ''),
(80, 'ama01', 'insumos', 'rellenos pasteleria', 'amarenas', 'amarenas', 'kg', '1.000', '0.000', '0.00', '33700.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '3.000', '60.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:24:59', '', '0000-00-00 00:00:00', 'true', ''),
(81, 'vai101', 'insumos', 'vainillas', 'pastaroma', 'pastaroma de vainilla', 'kg', '1.000', '0.000', '0.00', '49230.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '3.000', '30.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:50:38', '', '0000-00-00 00:00:00', 'true', ''),
(82, 'vai103', 'insumos', 'vainillas', 'purissima', 'vainilla purissima', 'kg', '1.000', '0.000', '0.00', '53750.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '6.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:52:59', '', '0000-00-00 00:00:00', 'true', ''),
(83, 'mal01', 'insumos', 'saborizantes', 'malaga', 'malaga', 'kg', '1.000', '0.000', '0.00', '31725.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '12.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 10:55:04', '', '0000-00-00 00:00:00', 'true', ''),
(84, 'vai102', 'insumos', 'vainillas', 'sirope', 'sirope de vainilla', 'kg', '1.000', '0.000', '0.00', '40832.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '8.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 11:02:42', '', '0000-00-00 00:00:00', 'true', ''),
(85, 'nn01', 'insumos', 'rellenos pasteleria', 'nutella negra', 'nutella negra', 'kg', '1.000', '0.000', '0.00', '29580.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '5.000', '100.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 11:11:28', '', '0000-00-00 00:00:00', 'true', ''),
(86, 'nb01', 'insumos', 'rellenos pasteleria', 'nutella blanca', 'nutella blanca', 'kg', '1.000', '0.000', '0.00', '33180.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '5.000', '100.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 11:13:11', '', '0000-00-00 00:00:00', 'true', ''),
(87, 'frf01', 'insumos', 'frutas', 'fresa', 'fresa', 'kg', '1.000', '0.000', '0.00', '8000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '2.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:47:09', '', '0000-00-00 00:00:00', 'true', ''),
(88, 'agr01', 'insumos', 'frutas', 'agraz', 'agraz congelado', 'kg', '1.000', '0.000', '0.00', '18000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '2.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:47:56', '', '0000-00-00 00:00:00', 'true', ''),
(89, 'frfr01', 'insumos', 'frutas', 'frambuesa', 'frambuesa en fruta', 'kg', '1.000', '0.000', '0.00', '120000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.010', '1.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:49:56', '', '0000-00-00 00:00:00', 'true', ''),
(90, 'tmt01', 'insumos', 'vegetales', 'tomate', 'tomate chonto', 'kg', '1.000', '0.000', '0.00', '2000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '40.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:50:59', '', '0000-00-00 00:00:00', 'true', ''),
(91, 'pmr01', 'insumos', 'vegetales', 'pimenton', 'pimenton ', 'kg', '1.000', '0.000', '0.00', '5000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '10.000', '', '2016-06-01', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:52:11', '', '0000-00-00 00:00:00', 'true', ''),
(92, 'cbb01', 'insumos', 'vegetales', 'cebolla', 'cebolla blanca', 'kg', '1.000', '0.000', '0.00', '4400.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '20.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:54:01', '', '0000-00-00 00:00:00', 'true', ''),
(93, 'champ01', 'insumos', 'vegetales', 'champiñones', 'champiñon ', 'kg', '1.000', '0.000', '0.00', '13000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '5.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 21:55:06', '', '0000-00-00 00:00:00', 'true', ''),
(94, 'qdcb01', 'insumos', 'charcuterias', 'queso doblecrema bloque', 'queso doble crema bloque', 'kg', '1.000', '0.000', '0.00', '16850.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '10.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:05:37', '', '0000-00-00 00:00:00', 'true', ''),
(95, 'jam01', 'insumos', 'charcuterias', 'jamon corriente', 'jamon tajado', 'kg', '1.000', '0.000', '0.00', '15200.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '60.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:07:32', '', '0000-00-00 00:00:00', 'true', ''),
(96, 'lbol01', 'insumos', 'lacteos liquidos', 'leche entera bolsa', 'leche entera en bolsa', 'kg', '1.000', '0.000', '0.00', '2100.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '5.000', '25.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:10:33', '', '0000-00-00 00:00:00', 'true', ''),
(97, 'crema01', 'insumos', 'lacteos liquidos', 'crema de leche en bolsa', 'crema de leche', 'kg', '1.000', '0.000', '0.00', '6546.36', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '4.000', '30.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:13:12', '', '0000-00-00 00:00:00', 'true', ''),
(98, 'cmolida01', 'insumos', 'carnes', 'carne molida', 'carne molida', 'kg', '1.000', '0.000', '0.00', '14000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.010', '7.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:16:00', '', '0000-00-00 00:00:00', 'true', ''),
(99, 'azpulv01', 'insumos', 'azucar', 'pulverizada', 'azucar pulverizada', 'kg', '1.000', '0.000', '0.00', '3360.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '5.000', '30.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:23:11', '', '0000-00-00 00:00:00', 'true', ''),
(100, 'lmn01', 'insumos', 'frutas', 'limon', 'limon natural', 'kg', '1.000', '0.000', '0.00', '4800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.010', '6.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:25:35', '', '0000-00-00 00:00:00', 'true', ''),
(101, 'manjb01', 'insumos', 'varios pasteleria', 'manjar blanco', 'manjar blanco', 'kg', '1.000', '0.000', '0.00', '6700.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '6.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:28:47', '', '0000-00-00 00:00:00', 'true', ''),
(102, 'qc01', 'insumos', 'charcuterias', 'queso crema', 'queso crema', 'kg', '1.000', '0.000', '0.00', '12476.31', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '1.000', '16.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:32:25', '', '0000-00-00 00:00:00', 'true', ''),
(103, 'hp01', 'insumos', 'harina', 'harina pastelera', 'harina pastelera', 'kg', '1.000', '0.000', '0.00', '2140.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '10.000', '120.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-01 22:35:31', '', '0000-00-00 00:00:00', 'true', ''),
(106, 'dav01', 'productos', 'pan', 'da vinci', 'da vinci estandar', 'und', '0.440', '0.000', '0.00', '4528.00', '0.00', '0.00', '8000.00', '7500.00', '0.00', '0.00', '1.000', '0.000', '1.000', '5.000', '', '0000-00-00', '', '', '', 'true', 'SISTEMA', '2016-06-02 18:17:51', '', '0000-00-00 00:00:00', 'true', ''),
(107, 'cuatroc', 'productos', 'postres', 'cuatro cuartos de nata ', 'cuatro cuartos de nata', 'und', '0.230', '0.000', '0.00', '2199.76', '0.00', '0.00', '19000.00', '1118000.00', '0.00', '0.00', '1.000', '0.000', '2.000', '8.000', '', '0000-00-00', '', '', '', 'true', 'SISTEMA', '2016-06-02 18:20:12', '', '0000-00-00 00:00:00', 'true', ''),
(108, 'cuatroc01', 'productos', 'postres', 'cuatro cuartos de naranja y almendra', 'cuatro cuartos de naranja', 'und', '0.230', '0.000', '0.00', '3355.36', '0.00', '0.00', '19000.00', '18000.00', '0.00', '0.00', '1.000', '0.000', '2.000', '8.000', '', '0000-00-00', '', '', '', 'true', 'SISTEMA', '2016-06-02 18:21:20', '', '0000-00-00 00:00:00', 'true', ''),
(109, 'ml01', 'insumos', 'varios pasteleria', 'miel', 'miel de abeja natural', 'kg', '1.000', '0.000', '0.00', '32000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.500', '4.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-02 18:31:51', '', '0000-00-00 00:00:00', 'true', ''),
(110, 'ph01', 'insumos', 'levaduras', 'polvo de hornear', 'polvo de hornear', 'kg', '1.000', '0.000', '0.00', '8000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.000', '0.000', '0.100', '1.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-02 18:32:42', '', '0000-00-00 00:00:00', 'true', ''),
(111, 'coke350', 'productos', 'bebidas frias', 'coca cola', 'coca cola 350', 'botella 350 ml', '0.350', '0.000', '0.00', '1420.00', '0.00', '0.00', '3500.00', '3300.00', '0.00', '0.00', '1.000', '0.000', '12.000', '48.000', '', '2016-06-02', 'false', '', '', 'false', 'SISTEMA', '2016-06-02 18:57:05', '', '0000-00-00 00:00:00', 'true', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_elem`
--

CREATE TABLE IF NOT EXISTS `productos_elem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(100) NOT NULL DEFAULT '',
  `codigo2` varchar(100) NOT NULL DEFAULT '',
  `cantidad` decimal(20,3) NOT NULL DEFAULT '0.000',
  `cantidad2` decimal(20,3) NOT NULL DEFAULT '0.000',
  `valor` decimal(20,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `CodigoUpdate` (`codigo`),
  KEY `CodigoUpdate2` (`codigo2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

--
-- Volcado de datos para la tabla `productos_elem`
--

INSERT INTO `productos_elem` (`id`, `codigo`, `codigo2`, `cantidad`, `cantidad2`, `valor`) VALUES
(9, 'bg01', 'ag01', '0.170', '0.000', '0.01'),
(10, 'bg01', 'hp50', '0.250', '0.000', '1240.00'),
(11, 'bg01', 'slb3', '0.010', '0.000', '800.00'),
(12, 'bg01', 'lv500', '0.010', '0.000', '12000.00'),
(13, 'bg01', 'bolb01', '1.000', '0.000', '90.00'),
(14, 'bagpo', 'hp50', '0.200', '0.000', '1240.00'),
(15, 'bagpo', 'ag01', '0.120', '0.000', '0.01'),
(16, 'bagpo', 'slb3', '0.010', '0.000', '800.00'),
(17, 'bagpo', 'lv500', '0.010', '0.000', '12000.00'),
(18, 'bagpo', 'or01', '0.010', '0.000', '40000.00'),
(19, 'bagpo', 'bolb01', '1.000', '0.000', '90.00'),
(41, 'cuatroc01', 'hv30', '2.000', '0.000', '300.00'),
(44, 'cuatroc01', 'mant01', '0.090', '0.000', '11500.00'),
(47, 'cuatroc01', 'nconf01', '0.020', '0.000', '13000.00'),
(48, 'cuatroc01', 'alm02', '0.020', '0.000', '50000.00'),
(50, 'cuatroc01', 'ph01', '0.010', '0.000', '8000.00'),
(53, 'cuatroc01', 'hp01', '0.084', '0.000', '2140.00'),
(55, 'cuatroc01', 'lp01', '0.017', '0.000', '11800.00'),
(56, 'cuatroc', 'hv30', '2.000', '0.000', '300.00'),
(57, 'cuatroc', 'mant01', '0.090', '0.000', '11500.00'),
(58, 'cuatroc', 'nconf01', '0.020', '0.000', '13000.00'),
(60, 'cuatroc', 'ph01', '0.010', '0.000', '8000.00'),
(61, 'cuatroc', 'hp01', '0.084', '0.000', '2140.00'),
(63, 'cuatroc', 'juicenar', '0.030', '0.000', '1500.00'),
(64, 'dav01', 'ag01', '0.170', '0.000', '0.01'),
(65, 'dav01', 'hp50', '0.250', '0.000', '1240.00'),
(66, 'dav01', 'slb3', '0.010', '0.000', '800.00'),
(67, 'dav01', 'lv500', '0.010', '0.000', '12000.00'),
(68, 'dav01', 'bolb01', '1.000', '0.000', '90.00'),
(71, 'dav01', 'pist01', '0.030', '0.000', '90000.00'),
(72, 'dav01', 'nconf01', '0.100', '0.000', '13000.00');

--
-- Disparadores `productos_elem`
--
DROP TRIGGER IF EXISTS `valor_a_d`;
DELIMITER //
CREATE TRIGGER `valor_a_d` AFTER DELETE ON `productos_elem`
 FOR EACH ROW BEGIN
   DECLARE total decimal(20, 2);
   SELECT SUM(cantidad * valor) INTO total FROM productos_elem WHERE codigo = OLD.codigo;
   UPDATE productos SET costo = total WHERE cod_fab = OLD.codigo;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `valor_a_i`;
DELIMITER //
CREATE TRIGGER `valor_a_i` AFTER INSERT ON `productos_elem`
 FOR EACH ROW BEGIN
   DECLARE total decimal(20, 2);
   SELECT SUM(cantidad * valor) INTO total FROM productos_elem WHERE codigo = NEW.codigo;
   UPDATE productos SET costo = total WHERE cod_fab = NEW.codigo;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `valor_a_u`;
DELIMITER //
CREATE TRIGGER `valor_a_u` AFTER UPDATE ON `productos_elem`
 FOR EACH ROW BEGIN
   DECLARE total decimal(20, 2);
   SELECT SUM(cantidad * valor) INTO total FROM productos_elem WHERE codigo = NEW.codigo;
   UPDATE productos SET costo = total WHERE cod_fab = NEW.codigo;
END
//
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos_elem`
--
ALTER TABLE `productos_elem`
  ADD CONSTRAINT `CodigoUpdate` FOREIGN KEY (`codigo`) REFERENCES `productos` (`cod_fab`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `CodigoUpdate2` FOREIGN KEY (`codigo2`) REFERENCES `productos` (`cod_fab`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
