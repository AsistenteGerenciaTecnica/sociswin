-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:58:19
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
-- Estructura de tabla para la tabla `llx_observation`
--

CREATE TABLE `llx_observation` (
  `rowid` int(6) NOT NULL,
  `date` datetime DEFAULT NULL,
  `observation` text COLLATE utf8_bin,
  `responsable` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(6) NOT NULL,
  `fk_revision` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_observation`
--

INSERT INTO `llx_observation` (`rowid`, `date`, `observation`, `responsable`, `imagen`, `status`, `fk_revision`) VALUES
(1, '2021-04-09 00:00:00', 'observacion', 'Carlos', 'images/logo.png', 1, 1),
(2, '2021-04-07 00:00:00', 'Se observan rupturas y golpes en los lentes de la cámara, además de descalibración y ausencia de 2 tornillos.', 'Carlos', 'images/logo.png', 1, 1),
(5, '2021-04-07 00:00:00', 'observacion3', 'Carlos', 'images/logo.png', 1, 1),
(9, '2021-04-07 00:00:00', 'ob1', 'Alejandro', 'images/logo.png', 1, 9),
(10, '2021-04-07 00:00:00', 'observacion eliminar', 'Alejandro', 'images/logo.png', 1, 10),
(11, '2021-04-07 00:00:00', 'cambio', 'Carlos', 'images/logo.png', 1, 10),
(12, '2021-04-09 15:17:49', 'ob1', 'Carlos', 'images/logo.png', 1, 11),
(13, '2021-04-09 16:17:23', 'v', 'Alejandro Martinez', 'images/logo.png', 1, 11),
(14, '2021-04-12 11:08:48', 'obervacion 1', 'Alejandro Martinez', 'images/logo.png', 0, 12),
(16, '2021-04-16 10:24:01', 'observacion', 'Alejandro Martinez', 'images/logo.png', 1, 7),
(17, '2021-04-16 10:24:18', 'observacion', 'Carlos Martinez', 'images/logo.png', 1, 8),
(18, '2021-04-20 11:31:01', 'g', 'Alejandro Martinez', 'images/logo.png', 1, 13),
(19, '2021-04-21 12:14:03', 'b', 'Alejandro Martinez', 'images/logo.png', 1, 14),
(20, '2021-04-21 12:15:52', 'n', 'Alejandro Martinez', 'images/logo.png', 1, 14),
(22, '2021-04-21 14:22:58', 'g', 'Alejandro Martinez', 'images/DS_Modificar.jpg', 1, 14),
(23, '2021-04-30 11:24:40', 'procedimiento1', 'Alejandro Martinez', 'images/DS_Filtrar.jpg', 0, 17),
(24, '2021-05-11 11:22:41', 'Limpieza', 'Alejandro Martinez', 'images/logoultm2.jpg', 1, 7),
(25, '2021-05-13 12:09:51', 'revision y limpieza', 'Alejandro Martinez', 'images/DS_Eliminar.jpg', 1, 16),
(26, '2021-05-13 12:09:51', 'revision y limpieza', 'Alejandro Martinez', 'images/CasoUsoGarantias.jpg', 1, 16),
(27, '2021-05-24 13:00:02', 'Revision de daño', 'Carlos Martinez', 'images/logoultm2.jpg', 1, 20),
(28, '2021-05-25 16:44:11', 'Se realizo revision y limpieza', 'Alejandro Martinez', 'images/logo.png', 1, 21),
(33, '2021-05-26 12:14:39', 'Reparación de daños.', 'Alejandro Martinez', 'images/logo.png', 1, 21),
(34, '2021-05-26 12:41:53', 'Reparación.', 'Alejandro Martinez', 'images/logo.png', 0, 21),
(35, '2021-05-27 10:07:31', 'Revisión.', 'Alejandro Martinez', 'images/logo.png', 1, 22);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_observation`
--
ALTER TABLE `llx_observation`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_revision` (`fk_revision`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_observation`
--
ALTER TABLE `llx_observation`
  MODIFY `rowid` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_observation`
--
ALTER TABLE `llx_observation`
  ADD CONSTRAINT `llx_observation_ibfk_1` FOREIGN KEY (`fk_revision`) REFERENCES `llx_revision` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
