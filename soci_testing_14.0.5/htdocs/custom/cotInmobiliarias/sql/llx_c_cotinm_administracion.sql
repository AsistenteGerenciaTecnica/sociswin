CREATE TABLE `llx_c_cotinm_administracion`
(
  `rowid` int(11) NOT NULL auto_increment,  
  `valor` float(10),  
  `active` TINYINT(4) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`rowid`)
) ENGINE=innodb;
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (1,1.5);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (2,3);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (3,4);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (4,6);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (5,7);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (6,8);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (7,9);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (8,10);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (9,11);
insert into llx_c_cotinm_administracion (rowid,valor) VALUES (10,12);
