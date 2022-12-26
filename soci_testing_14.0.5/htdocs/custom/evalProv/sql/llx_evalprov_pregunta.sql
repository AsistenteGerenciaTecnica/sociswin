CREATE TABLE `llx_evalprov_pregunta` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT, 
`ref` VARCHAR(20) NOT NULL , 
`pregunta` TEXT NOT NULL , 
`tipo` VARCHAR(20) NOT NULL DEFAULT 'PUNTAJE' , 
`fk_categoria` INT(11) NOT NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_modif` INT(11) NOT NULL , 
`date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`fk_user_creat` INT(11) NOT NULL , 
PRIMARY KEY (`rowid`), 
UNIQUE (`ref`)) 
ENGINE = InnoDB;