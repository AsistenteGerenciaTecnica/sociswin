CREATE TABLE `llx_c_cotinm_iva`
(
  `rowid` int(11) NOT NULL auto_increment,  
  `valor` float(10),  
  `active` TINYINT(4) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`rowid`)
) ENGINE=innodb;
insert into llx_c_cotinm_iva(rowid,valor) VALUES (1,0);
insert into llx_c_cotinm_iva(rowid,valor) VALUES (2,19);
