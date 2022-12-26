CREATE TABLE `llx_evalprov_evaluacion_pregunta` ( 
`rowid` INT(11) NOT NULL AUTO_INCREMENT, 
`fk_evaluacion` INT(11) NOT NULL , 
`fk_pregunta` INT(11) NOT NULL , 
`calificacion` FLOAT(6,3) NOT NULL DEFAULT '0' ,
`comentario` TEXT NULL ,
`fk_user_modif` INT(11) NOT NULL , 
`tms` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
PRIMARY KEY (`rowid`)) 
ENGINE = InnoDB;