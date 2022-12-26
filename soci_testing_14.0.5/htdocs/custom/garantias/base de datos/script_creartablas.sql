
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



CREATE TABLE `llx_garantia` (
    `rowid` int(11) NOT NULL,
    `ref` varchar(128) COLLATE utf8_bin DEFAULT NULL,
    `date_send` datetime DEFAULT NULL,
    `date_reception` datetime DEFAULT NULL,
    `status` int(6) NOT NULL,
    `statut` int(6) DEFAULT NULL,
    `fk_equipment_history` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
  



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




CREATE TABLE `llx_garantias_historial_extrafields` (
    `rowid` int(11) NOT NULL,
    `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `fk_object` int(11) NOT NULL,
    `import_key` varchar(14) COLLATE utf8_bin DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



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
  



CREATE TABLE `llx_observation` (
    `rowid` int(6) NOT NULL,
    `date` datetime DEFAULT NULL,
    `observation` text COLLATE utf8_bin,
    `responsable` varchar(128) COLLATE utf8_bin DEFAULT NULL,
    `imagen` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    `status` int(6) NOT NULL,
    `fk_revision` int(6) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
  





CREATE TABLE `llx_observation_fourn` (
    `rowid` int(11) NOT NULL,
    `date` datetime DEFAULT NULL,
    `observation` text COLLATE utf8_bin,
    `imagen` varchar(255) COLLATE utf8_bin DEFAULT NULL,
    `status` int(6) NOT NULL,
    `fk_garantia` int(6) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
  



CREATE TABLE `llx_revision` (
    `rowid` int(11) NOT NULL,
    `date_send` datetime DEFAULT NULL,
    `orden` int(6) DEFAULT NULL,
    `status` int(6) NOT NULL,
    `statut` int(6) NOT NULL,
    `fk_equipment_history` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
  





ALTER TABLE `llx_equipment_history`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_fournisseur` (`fk_fournisseur`),
  ADD KEY `fk_projet` (`fk_projet`);


ALTER TABLE `llx_equipment_history`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_equipment_history`
  ADD CONSTRAINT `llx_equipment_history_ibfk_1` FOREIGN KEY (`fk_fournisseur`) REFERENCES `llx_societe` (`rowid`);





ALTER TABLE `llx_garantia`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_equipment_history` (`fk_equipment_history`);


ALTER TABLE `llx_garantia`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_garantia`
  ADD CONSTRAINT `llx_garantia_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;




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


ALTER TABLE `llx_garantias_historial`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `llx_garantias_historial`
  ADD CONSTRAINT `llx_garantias_historial_fk_user_creat` FOREIGN KEY (`fk_user_creat`) REFERENCES `llx_user` (`rowid`);




ALTER TABLE `llx_garantias_historial_extrafields`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `idx_historial_fk_object` (`fk_object`);


ALTER TABLE `llx_garantias_historial_extrafields`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;




ALTER TABLE `llx_maintenance`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `llx_maintenance_ibfk_1` (`fk_equipment_history`);


ALTER TABLE `llx_maintenance`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_maintenance`
  ADD CONSTRAINT `llx_maintenance_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;




ALTER TABLE `llx_observation`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_revision` (`fk_revision`);


ALTER TABLE `llx_observation`
  MODIFY `rowid` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_observation`
  ADD CONSTRAINT `llx_observation_ibfk_1` FOREIGN KEY (`fk_revision`) REFERENCES `llx_revision` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `llx_observation_fourn`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `llx_observation_fourn_ibfk_1` (`fk_garantia`);


ALTER TABLE `llx_observation_fourn`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_observation_fourn`
  ADD CONSTRAINT `llx_observation_fourn_ibfk_1` FOREIGN KEY (`fk_garantia`) REFERENCES `llx_garantia` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;



ALTER TABLE `llx_revision`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `fk_equipment_history` (`fk_equipment_history`);


ALTER TABLE `llx_revision`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;

ALTER TABLE `llx_revision`
  ADD CONSTRAINT `llx_revision_ibfk_1` FOREIGN KEY (`fk_equipment_history`) REFERENCES `llx_equipment_history` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

