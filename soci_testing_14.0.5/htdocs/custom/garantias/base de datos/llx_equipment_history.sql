

CREATE TABLE `llx_equipment_history` (
  `rowid` int(11) NOT NULL,
  `type` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `reference` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `ref` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `fk_facture` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `fk_commande_fournisseur` int(11) DEFAULT NULL,
  `fk_client` int(11) DEFAULT NULL,
  `fk_fournisseur` int(11) DEFAULT NULL,
  `date_reception` timestamp NULL DEFAULT NULL,
  `date_operation` date DEFAULT NULL,
  `date_facture` date DEFAULT NULL,
  `verify_code` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `control_port` int(128) DEFAULT NULL,
  `mac_address` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `mark` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `modelo` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `serial` int(128) DEFAULT NULL,
  `fk_projet` int(11) DEFAULT NULL,
  `national_type` smallint(6) DEFAULT NULL,
  `web_port` int(128) DEFAULT NULL,
  `category_operation` varchar(6) COLLATE utf8_bin DEFAULT NULL,
  `fk_product` int(6) DEFAULT NULL,
  `warranty_time` int(6) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `statut` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Volcado de datos para la tabla `llx_equipment_history`
--

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `llx_equipment_history`
--
ALTER TABLE `llx_equipment_history`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_fournisseur` (`fk_fournisseur`),
  ADD KEY `fk_projet` (`fk_projet`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `llx_equipment_history`
--
ALTER TABLE `llx_equipment_history`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `llx_equipment_history`
--
ALTER TABLE `llx_equipment_history`
  ADD CONSTRAINT `llx_equipment_history_ibfk_1` FOREIGN KEY (`fk_fournisseur`) REFERENCES `llx_societe` (`rowid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
