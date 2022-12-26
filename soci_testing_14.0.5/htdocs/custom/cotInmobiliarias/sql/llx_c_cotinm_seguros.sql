CREATE TABLE `llx_c_cotinm_seguros`
(
  `rowid` int(11) NOT NULL auto_increment,  
  `valor` float(10),  
  `active` TINYINT(4) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`rowid`)
) ENGINE=innodb;
insert into llx_c_cotinm_seguros(rowid,valor) VALUES (1,0.5);
insert into llx_c_cotinm_seguros(rowid,valor) VALUES (2,1);
insert into llx_c_cotinm_seguros(rowid,valor) VALUES (3,2);
insert into llx_c_cotinm_seguros(rowid,valor) VALUES (4,2.5);
insert into llx_c_cotinm_seguros(rowid,valor) VALUES (5,3);
