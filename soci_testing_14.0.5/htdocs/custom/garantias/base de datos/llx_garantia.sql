-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:56:01
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
-- Estructura de tabla para la tabla `llx_garantia`
--

CREATE TABLE `llx_garantia` (
  `rowid` int(11) NOT NULL,
  `ref` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `date_send` datetime DEFAULT NULL,
  `date_reception` datetime DEFAULT NULL,
  `status` int(6) NOT NULL,
  `statut` int(6) DEFAULT NULL,
  `fk_equipment_history` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_garantia`
--

INSERT INTO `llx_garantia` (`rowid`, `ref`, `date_send`, `date_reception`, `status`, `statut`, `fk_equipment_history`) VALUES
(2, NULL, '2021-04-03 00:00:00', NULL, 1, 1, 2),
(3, 'GR1703-2', '2021-03-17 00:00:00', '2021-03-17 00:00:00', 1, 0, 1),
(4, 'GR3004-4', '2021-04-30 00:00:00', NULL, 1, 1, 6),
(9, NULL, '2021-04-07 00:00:00', '2021-04-07 00:00:00', 0, 1, 7),
(11, NULL, '2021-05-10 00:00:00', NULL, 0, 1, 5),
(13, NULL, '2021-03-30 00:00:00', '2021-05-11 15:55:30', 1, 1, 12),
(14, NULL, '2021-04-09 15:18:05', '2021-04-09 15:19:39', 1, 1, 11),
(15, NULL, '2021-04-15 12:49:22', NULL, 1, 1, 14),
(16, NULL, '2021-04-20 15:46:33', NULL, 1, 1, 11),
(17, NULL, '2021-04-21 12:24:48', NULL, 1, 1, 10),
(18, NULL, '2021-05-25 16:46:09', NULL, 1, 1, 7),
(19, 'GR2705-12', '2021-05-27 10:01:36', '2021-05-27 10:04:50', 1, 0, 5),
(20, 'GR2705-13', '2021-05-27 10:08:39', NULL, 1, 1, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_garantia`
--
ALTER TABLE `llx_garantia`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_equipment_history` (`fk_equipment_history`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_garantia`
--
ALTER TABLE `llx_garantia`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_garantia`
--
ALTER TABLE `llx_garantia`
  ADD CONSTRAINT `llx_garantia_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
