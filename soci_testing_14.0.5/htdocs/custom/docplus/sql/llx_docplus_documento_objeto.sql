CREATE TABLE `llx_docplus_documento_objeto` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT , 
`fk_objeto` INT(11) NOT NULL , 
`fk_documento` INT(11) NOT NULL , 
`renovable` BOOLEAN NOT NULL DEFAULT FALSE , 
`tipo_renovacion` VARCHAR(100) NULL , 
`valor_cada` INT(11) NULL , 
`tiempo_cada` VARCHAR(100) NULL , 
`fecha_renovacion` DATETIME NULL ,
`valor_aviso` INT(11) NULL , 
`tiempo_aviso` VARCHAR(100) NULL ,  
`aviso_renovacion` DATETIME NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_modif` INT(11) NOT NULL , 
`date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_creat` INT(11) NOT NULL , 
PRIMARY KEY (`rowid`)) 
ENGINE = InnoDB;