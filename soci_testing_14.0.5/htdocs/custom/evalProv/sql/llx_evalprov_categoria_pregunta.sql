CREATE TABLE `llx_evalprov_categoria_pregunta` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT, 
`nombre` VARCHAR(100) NOT NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
PRIMARY KEY (`rowid`), 
UNIQUE (`nombre`)) 
ENGINE = InnoDB;