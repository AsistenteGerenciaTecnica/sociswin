-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:57:41
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
-- Estructura de tabla para la tabla `llx_revision`
--

CREATE TABLE `llx_revision` (
  `rowid` int(11) NOT NULL,
  `date_send` datetime DEFAULT NULL,
  `orden` int(6) DEFAULT NULL,
  `status` int(6) NOT NULL,
  `statut` int(6) NOT NULL,
  `fk_equipment_history` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_revision`
--

INSERT INTO `llx_revision` (`rowid`, `date_send`, `orden`, `status`, `statut`, `fk_equipment_history`) VALUES
(1, '2021-03-24 00:00:00', 1, 1, 1, 1),
(7, '2021-04-07 00:00:00', NULL, 1, 0, 6),
(8, '2021-04-07 00:00:00', NULL, 1, 1, 6),
(9, '2021-04-08 00:00:00', NULL, 1, 0, 5),
(10, '2021-04-08 00:00:00', NULL, 1, 0, 12),
(11, '2021-04-09 11:20:14', NULL, 1, 0, 11),
(12, '2021-04-12 11:08:23', 2, 1, 0, 1),
(13, '2021-04-20 11:30:53', NULL, 1, 0, 11),
(14, '2021-04-21 12:13:46', NULL, 1, 1, 10),
(15, '2021-04-21 14:18:33', NULL, 1, 1, 10),
(16, '2021-04-27 16:37:00', NULL, 1, 1, 12),
(17, '2021-04-27 17:13:00', 2, 1, 1, 1),
(18, '2021-04-30 09:49:59', 1, 1, 1, 14),
(19, '2021-05-10 14:22:36', 4, 1, 0, 2),
(20, '2021-05-24 12:57:56', 5, 1, 1, 1),
(21, '2021-05-25 16:42:56', 6, 1, 1, 7),
(22, '2021-05-27 10:07:15', 6, 1, 1, 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_revision`
--
ALTER TABLE `llx_revision`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_equipment_history` (`fk_equipment_history`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_revision`
--
ALTER TABLE `llx_revision`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_revision`
--
ALTER TABLE `llx_revision`
  ADD CONSTRAINT `llx_revision_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
