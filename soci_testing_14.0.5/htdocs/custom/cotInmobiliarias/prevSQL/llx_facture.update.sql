ALTER TABLE llx_facture ADD presupuesto_fk integer(11) DEFAULT NULL;
ALTER TABLE llx_facture ADD pedido_fk integer(11) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_administracion_tipo char(1) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_administracion_valor float(9,4) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_imprevisto_tipo char(1) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_imprevisto_valor float(9,4) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_utilidad_tipo char(1) DEFAULT NULL;
ALTER TABLE llx_facture ADD aiu_utilidad_valor float(9,4) DEFAULT NULL;
