CREATE TABLE `llx_docplus_carpeta` (
`rowid` INT(11) NOT NULL AUTO_INCREMENT,
`fk_parent` INT(11) NOT NULL,
`tipo_parent` VARCHAR(100) NOT NULL,
`nombre` VARCHAR(100) NOT NULL,
`tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`fk_user_modif` INT(11) NOT NULL,
PRIMARY KEY(`rowid`)) 
ENGINE = InnoDB 