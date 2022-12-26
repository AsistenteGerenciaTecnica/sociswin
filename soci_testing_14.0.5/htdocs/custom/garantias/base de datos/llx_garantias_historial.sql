-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 04-09-2021 a las 09:56:25
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
-- Estructura de tabla para la tabla `llx_garantias_historial`
--

CREATE TABLE `llx_garantias_historial` (
  `rowid` int(11) NOT NULL,
  `ref` varchar(128) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `fk_facture` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL,
  `fk_project` int(11) DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `fk_commande_fournisseur` varchar(118) COLLATE utf8_bin NOT NULL,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_user_creat` int(11) NOT NULL,
  `fk_user_modif` int(11) DEFAULT NULL,
  `last_main_doc` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `import_key` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `model_pdf` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `status` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_garantias_historial`
--
ALTER TABLE `llx_garantias_historial`
  ADD PRIMARY KEY (`rowid`),
  ADD UNIQUE KEY `fk_user_creat_2` (`fk_user_creat`),
  ADD KEY `idx_garantias_historial_rowid` (`rowid`),
  ADD KEY `idx_garantias_historial_ref` (`ref`),
  ADD KEY `idx_garantias_historial_fk_soc` (`fk_soc`),
  ADD KEY `idx_garantias_historial_fk_project` (`fk_project`),
  ADD KEY `idx_garantias_historial_status` (`status`),
  ADD KEY `fk_facture` (`fk_facture`),
  ADD KEY `fk_commande_fournisseur` (`fk_commande_fournisseur`),
  ADD KEY `fk_user_creat` (`fk_user_creat`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_garantias_historial`
--
ALTER TABLE `llx_garantias_historial`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_garantias_historial`
--
ALTER TABLE `llx_garantias_historial`
  ADD CONSTRAINT `llx_garantias_historial_fk_user_creat` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
