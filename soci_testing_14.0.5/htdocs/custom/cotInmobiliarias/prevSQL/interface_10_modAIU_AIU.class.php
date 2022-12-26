<?php
class IntefaceCotInm
{
    var $db;
	var $cotinm_products = array();
    /**
     *   \brief      Constructeur.
     *   \param      DB      Handler d'acces base
     */
    function IntefaceCotInm($DB)
    {
		global $conf;
        $this->db = $DB ;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "consumoenergia";
        $this->description = "Triggers para el tratamiento del AIU.";
        $this->version = 'dolibarr';                        // 'experimental' or 'dolibarr' or version
		$this->cotinm_products = array(
										$conf->global->COTINM_PRODUCTO_ADMINISTRACION,
										$conf->global->COTINM_PRODUCTO_GRAVAMEN,
										$conf->global->COTINM_PRODUCTO_COSTO_TRANSACCION,
										$conf->global->COTINM_PRODUCTO_SEGUROS
									);
    }
    /**
     *   \brief      Renvoi nom du lot de triggers
     *   \return     string      Nom du lot de triggers
     */
    function getName()
    {
        return $this->name;
    }
    /**
     *   \brief      Renvoi descriptif du lot de triggers
     *   \return     string      Descriptif du lot de triggers
     */
    function getDesc()
    {
        return $this->description;
    }
    /**
     *   \brief      Renvoi version du lot de triggers
     *   \return     string      Version du lot de triggers
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }
    /**
     *      \brief      Fonction appelee lors du declenchement d'un evenement Dolibarr.
     *                  D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
     *      \param      action      Code de l'evenement
     *      \param      object      Objet concerne
     *      \param      user        Objet user
     *      \param      lang        Objet lang
     *      \param      conf        Objet conf
     *      \return     int         <0 si ko, 0 si aucune action faite, >0 si ok
     */
	function run_trigger($action,$object,$user,$langs,$conf)
    {
		if ($conf->cotInmobiliarias->enabled)
		{
			require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/PresupuestoCotInm.class.php';
			//require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/PedidoAIU.class.php';
			require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/FacturaCotInm.class.php';
			if($action == 'ORDER_CREATE')
        	{
				/* if($object->origin == 'propal')
				{
					$objectPresupuestoCotInm = new PresupuestoCotInm($this->db);
					$objectPresupuestoCotInm->fetch($object->origin_id);
					//Se crea a partir de un pedido
					$objectPedidoAIU = new PedidoAIU($this->db);
					$objectPedidoAIU->id = $object->id;
					$objectPedidoAIU->presupuesto_fk = $object->origin_id;
					$objectPedidoAIU->cotinm_administracion_tipo = $objectPresupuestoCotInm->cotinm_administracion_tipo;
					$objectPedidoAIU->cotinm_administracion_valor = $objectPresupuestoCotInm->cotinm_administracion_valor;
					$objectPedidoAIU->cotinm_gravamen_tipo = $objectPresupuestoCotInm->cotinm_gravamen_tipo;
					$objectPedidoAIU->cotinm_gravamen_valor = $objectPresupuestoCotInm->cotinm_gravamen_valor;
					$objectPedidoAIU->cotinm_iva_tipo = $objectPresupuestoCotInm->cotinm_iva_tipo;
					$objectPedidoAIU->cotinm_iva_valor = $objectPresupuestoCotInm->cotinm_iva_valor;
					$objectPedidoAIU->update($user);					
				} */
            	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);					 
		 	} 
			if($action == 'BILL_SUPPLIER_CREATE') 
        	{
				
				if($object->origin == 'supplier_order')
				{					
					$objectPresupuestoCotInm = new PresupuestoCotInm($this->db);
					$objectPresupuestoCotInm->fetch($object->origin_id);
					//Se crea a partir de un presupuesto
					$objectFacturaCotInm = new FacturaCotInm($this->db);
					$objectFacturaCotInm->id = $object->id;
					$objectFacturaCotInm->presupuesto_fk = $object->origin_id;
					$objectFacturaCotInm->cotinm_administracion_tipo = $objectPresupuestoCotInm->cotinm_administracion_tipo;
					$objectFacturaCotInm->cotinm_administracion_valor = $objectPresupuestoCotInm->cotinm_administracion_valor;
					$objectFacturaCotInm->cotinm_administracion_iva = $objectPresupuestoCotInm->cotinm_administracion_iva;
					$objectFacturaCotInm->cotinm_gravamen_tipo = $objectPresupuestoCotInm->cotinm_gravamen_tipo;
					$objectFacturaCotInm->cotinm_gravamen_valor = $objectPresupuestoCotInm->cotinm_gravamen_valor;
					$objectFacturaCotInm->cotinm_seguro_tipo = $objectPresupuestoCotInm->cotinm_seguro_tipo;
					$objectFacturaCotInm->cotinm_seguro_valor = $objectPresupuestoCotInm->cotinm_seguro_valor;
					$objectFacturaCotInm->cotinm_seguro_iva = $objectPresupuestoCotInm->cotinm_seguro_iva;
					$objectFacturaCotInm->cotinm_costo_transaccion_tipo = $objectPresupuestoCotInm->cotinm_costo_transaccion_tipo;
					$objectFacturaCotInm->cotinm_costo_transaccion_valor = $objectPresupuestoCotInm->cotinm_costo_transaccion_valor;
					$objectFacturaCotInm->update($user);					
				}
				/* if($object->origin == 'commande')
				{
					$objectPedidoAIU = new PedidoAIU($this->db);
					$objectPedidoAIU->fetch($object->origin_id);
					//Se crea a partir de un pedido
					$objectFacturaCotInm = new FacturaCotInm($this->db);
					$objectFacturaCotInm->id = $object->id;
					$objectFacturaCotInm->pedido_fk = $object->origin_id;
					$objectFacturaCotInm->cotinm_administracion_tipo = $objectPedidoAIU->cotinm_administracion_tipo;
					$objectFacturaCotInm->cotinm_administracion_valor = $objectPedidoAIU->cotinm_administracion_valor;
					$objectFacturaCotInm->cotinm_gravamen_tipo = $objectPedidoAIU->cotinm_gravamen_tipo;
					$objectFacturaCotInm->cotinm_gravamen_valor = $objectPedidoAIU->cotinm_gravamen_valor;
					$objectFacturaCotInm->cotinm_iva_tipo = $objectPedidoAIU->cotinm_iva_tipo;
					$objectFacturaCotInm->cotinm_iva_valor = $objectPedidoAIU->cotinm_iva_valor;
					$objectFacturaCotInm->update($user);					
				} */
            	dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);					 
		 	}
			if($action == 'LINEBILL_SUPPLIER_CREATE' || $action == 'LINEBILL_SUPPLIER_UPDATE' || $action == 'LINEBILL_SUPPLIER_DELETE')
			{				
				require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/FacturaCotInm.class.php';
				if(!in_array($object->fk_product,$this->cotinm_products))
				{
					//No se actualiza si proviene de la creacion de la factura desde un pedido o presupuesto
					if(($object->origin != 'order_supplier'/*  || $object->origin != 'propal' */) && $object->origin_id == '')
					{					
						//Update los productos AIU aplicados
						$objectFacturaCotInm = new FacturaCotInm($this->db);
						$objectFacturaCotInm->fetch($object->fk_facture_fourn);
						$objectFacturaCotInm->update_products_cotinm(($action == 'LINEBILL_SUPPLIER_DELETE'?$object->rowid:NULL));
					}
				}
			}
			/* if($action == 'LINEORDER_INSERT' || $action == 'LINEORDER_UPDATE' || $action == 'LINEORDER_DELETE')
			{
				require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/PedidoAIU.class.php';
				if(!in_array($object->fk_product,$this->cotinm_products))
				{					
					//Update los productos AIU aplicados
					$objectPedidoAIU = new PedidoAIU($this->db);
					$objectPedidoAIU->fetch($object->fk_commande);
					$objectPedidoAIU->update_products_aiu(($action == 'LINEORDER_DELETE'?$object->rowid:NULL));
				}
			} */
			if($action == 'LINEORDER_SUPPLIER_CREATE' || $action == 'LINEORDER_SUPPLIER_UPDATE' || $action == 'LINEORDER_SUPPLIER_DELETE')
			{
				require_once DOL_DOCUMENT_ROOT.'/cotInmobiliarias/class/PresupuestoCotInm.class.php';
				if(!in_array($object->fk_product,$this->cotinm_products))
				{					
					//Update los productos AIU aplicados
					$objectPresupuestoCotInm = new PresupuestoCotInm($this->db);
					$objectPresupuestoCotInm->fetch($object->fk_commande_fourn);
					$objectPresupuestoCotInm->update_products_cotinm(($action == 'LINEORDER_SUPPLIER_UPDATE'?$object->rowid:NULL));
				}
			}
		}
		return 0;
    }
}
?>