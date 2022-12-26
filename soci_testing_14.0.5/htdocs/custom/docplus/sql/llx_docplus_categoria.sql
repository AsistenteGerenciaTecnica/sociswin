CREATE TABLE `llx_docplus_categoria` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT , 
`nombre` VARCHAR(100) NOT NULL , 
`modulo` VARCHAR(100) NOT NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_modif` INT(11) NOT NULL , 
PRIMARY KEY (`rowid`)) 
ENGINE = InnoDB;