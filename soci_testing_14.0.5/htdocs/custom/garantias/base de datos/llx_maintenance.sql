-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:55:01
-- Versión del servidor: 5.7.35-0ubuntu0.18.04.1
-- Versión de PHP: 7.2.24-0ubuntu0.18.04.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dolibarr`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_maintenance`
--

CREATE TABLE `llx_maintenance` (
  `rowid` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `activities` text COLLATE utf8_bin,
  `frecuency` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(6) NOT NULL,
  `fk_equipment_history` int(11) NOT NULL,
  `fk_product` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_maintenance`
--

INSERT INTO `llx_maintenance` (`rowid`, `date`, `activities`, `frecuency`, `imagen`, `status`, `fk_equipment_history`, `fk_product`) VALUES
(2, '2021-04-06 00:00:00', 'actividad1', 'semestral', 'images/logo.png', 1, 1, NULL),
(3, '2021-04-06 00:00:00', 'actividad2', 'mensual', 'image/logo.png', 1, 2, NULL),
(4, '2021-04-06 00:00:00', 'actividad2', 'mensual', 'images/logo.png', 1, 1, NULL),
(5, '2021-04-06 00:00:00', 'actividad4', 'semanal', 'image/logo.png', 0, 1, NULL),
(8, '2021-04-06 00:00:00', 'actividad23', 'semestral', 'image/logo.png', 1, 7, 1),
(9, '2021-04-06 00:00:00', 'Elimnarac', 'semanal', 'image/logo.png', 1, 12, NULL),
(10, '2021-04-09 16:14:39', '', '', 'image/logo.png', 1, 2, NULL),
(11, '2021-04-09 16:17:33', '', '', 'image/logo.png', 1, 11, NULL),
(12, '2021-04-15 17:44:53', 'actividad2', 'semestral', 'image/logo.png', 1, 6, NULL),
(13, '2021-04-15 17:45:13', 'actividad2', 'mensual', 'image/logo.png', 1, 6, NULL),
(14, '2021-04-15 17:45:26', 'actividad4', 'semanal', 'image/logo.png', 1, 6, NULL),
(15, '2021-04-19 17:23:57', 'limpieza', 'semestral', 'images/logo.png', 1, 1, NULL),
(16, '2021-04-19 17:26:18', 'limpieza', 'semanal', 'images/logo.png', 1, 1, NULL),
(18, '2021-04-20 09:30:40', 'actividad1', 'semanal', 'images/logo.png', 1, 1, NULL),
(20, '2021-04-21 11:23:27', 'limpieza', '', 'images/CasoUsoGarantias.jpg', 0, 1, NULL),
(22, '2021-04-21 12:37:37', '', '', 'images/logo_web_ULTIMATE-TECHNOLOGY-SAS.png', 1, 1, NULL),
(23, '2021-04-21 12:40:23', '', '', 'images/logo_web_ULTIMATE-TECHNOLOGY-SAS.png', 1, 1, NULL),
(24, '2021-04-21 12:51:17', '', '', 'images/Clases.jpg', 1, 1, NULL),
(25, '2021-04-21 12:53:29', '', '', 'images/Clases.jpg', 1, 1, NULL),
(26, '2021-04-21 14:25:19', 'Elimnarac', 'semestral', 'images/logo.png', 1, 1, NULL),
(27, '2021-04-21 14:25:52', '', '', 'images/logo.png', 1, 1, NULL),
(28, '2021-04-23 12:53:33', 'acti', 'semestral', 'images/logo.png', 1, 6, NULL),
(31, '2021-05-13 11:18:14', '', '', 'images/logo.png', 1, 12, NULL),
(32, '2021-05-13 11:18:14', '', '', 'images/DS_Modificar.jpg', 1, 12, NULL),
(38, '2021-05-13 12:03:13', 'img2', 'semestral', 'images/DS_Eliminar.jpg', 1, 12, NULL),
(39, '2021-05-13 12:33:59', 'Repuestos', 'semestral', 'images/logo.png', 1, 17, NULL),
(40, '2021-05-13 12:33:59', 'Repuestos', 'semestral', 'images/EntidadRelacion.jpg', 1, 17, NULL),
(41, '2021-05-13 12:33:59', 'Repuestos', 'semestral', 'images/DS_Modificar.jpg', 1, 17, NULL),
(42, '2021-05-13 12:34:21', 'limpieza', 'semestral', 'images/logo_web_ULTIMATE-TECHNOLOGY-SAS.png', 1, 17, NULL),
(43, '2021-05-13 12:34:21', 'limpieza', 'semestral', 'images/DS_Eliminar.jpg', 1, 17, NULL),
(44, '2021-05-14 12:54:41', 'procedimiento', 'anual', 'images/CasoUsoGarantias.jpg', 1, 17, NULL),
(45, '2021-05-25 16:38:28', 'Revision de lentes', 'Anual', 'images/logo.png', 1, 7, 1),
(46, '2021-05-26 15:18:28', 'a', 'a', NULL, 0, 7, 1),
(47, '2021-05-26 17:57:25', 'Procedimiento nuevo', 'Anual', NULL, 0, 8, 1),
(48, '2021-05-26 17:57:56', 'Procedimiento nuevo', 'anual', NULL, 0, 8, 1),
(49, '2021-05-26 18:01:16', 'Procedimiento nuevo', 'semestral', 'images/logo.png', 1, 8, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_maintenance`
--
ALTER TABLE `llx_maintenance`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `llx_maintenance_ibfk_1` (`fk_equipment_history`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_maintenance`
--
ALTER TABLE `llx_maintenance`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_maintenance`
--
ALTER TABLE `llx_maintenance`
  ADD CONSTRAINT `llx_maintenance_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
