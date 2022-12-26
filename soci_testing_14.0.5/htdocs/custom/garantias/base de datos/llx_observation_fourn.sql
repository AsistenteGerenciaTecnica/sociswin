-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:58:50
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
-- Estructura de tabla para la tabla `llx_observation_fourn`
--

CREATE TABLE `llx_observation_fourn` (
  `rowid` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `observation` text COLLATE utf8_bin,
  `imagen` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` int(6) NOT NULL,
  `fk_garantia` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_observation_fourn`
--

INSERT INTO `llx_observation_fourn` (`rowid`, `date`, `observation`, `imagen`, `status`, `fk_garantia`) VALUES
(2, '2021-04-07 00:00:00', 'Se observan rupturas y golpes en los lentes de la cámara, además de descalibración y ausencia de 2 tornillos.', 'images/logo.png', 1, 3),
(3, '2021-04-07 00:00:00', 'observacion 2', 'images/logo.png', 1, 3),
(4, '2021-04-07 12:00:00', 'eliminar', 'images/logo.png', 1, 13),
(5, '2021-04-21 12:25:02', 'b', 'images/logo.png', 1, 17),
(6, '2021-04-30 11:25:36', 'ob', 'images/Clases.jpg', 1, 3),
(7, '2021-05-11 09:27:57', 'Observacion Proveedor', 'images/logo.png', 1, 11),
(8, '2021-05-13 12:15:59', 'nota proveedor', 'images/EntidadRelacion.jpg', 1, 13),
(9, '2021-05-13 12:15:59', 'nota proveedor', 'images/DS_Modificar.jpg', 1, 13),
(12, '2021-05-26 12:19:12', 'Reparación de daños.', 'images/logo.png', 1, 18),
(13, '2021-05-26 12:23:27', 'Se revisó daños en lentes.', 'images/logo.png', 1, 18),
(14, '2021-05-26 12:44:53', 'Reaparación.ñ', 'images/logo.png', 0, 18),
(15, '2021-05-27 10:04:23', 'Reparación.', 'images/logo.png', 1, 19),
(16, '2021-05-27 12:48:31', 'Reparación de daño.', 'images/logo.png', 1, 20);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_observation_fourn`
--
ALTER TABLE `llx_observation_fourn`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `llx_observation_fourn_ibfk_1` (`fk_garantia`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_observation_fourn`
--
ALTER TABLE `llx_observation_fourn`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_observation_fourn`
--
ALTER TABLE `llx_observation_fourn`
  ADD CONSTRAINT `llx_observation_fourn_ibfk_1` FOREIGN KEY (`fk_garantia`) REFERENCES `llx_garantia` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
