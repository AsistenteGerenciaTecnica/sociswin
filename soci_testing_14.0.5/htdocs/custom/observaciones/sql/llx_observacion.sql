CREATE TABLE `llx_observacion` 
( 
  `rowid` INT(11) NOT NULL AUTO_INCREMENT , 
  `fk_intervention` INT(11) NOT NULL,
  `descripcion` TEXT NULL DEFAULT NULL , 
  `fecha` DATETIME NULL DEFAULT CURRENT_TIMESTAMP , 
  `duracion` INT NULL DEFAULT '0' , 
  `filename` VARCHAR(255) NULL DEFAULT NULL , 
  `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
  `fk_user_modif` INT(11) NULL , 
  `fk_user_creat` INT(11) NOT NULL , 
  PRIMARY KEY (`rowid`)) 
  ENGINE = InnoDB;