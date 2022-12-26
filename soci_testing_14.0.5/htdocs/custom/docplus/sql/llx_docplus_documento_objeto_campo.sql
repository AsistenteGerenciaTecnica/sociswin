CREATE TABLE `llx_docplus_documento_objeto_campo` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT , 
`fk_do` INT(11) NOT NULL , 
`fk_campo` INT(11) NOT NULL , 
`valor` VARCHAR(100) NOT NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_modif` INT(11) NOT NULL , 
`date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_creat` INT(11) NOT NULL , 
PRIMARY KEY (`rowid`)) 
ENGINE = InnoDB;