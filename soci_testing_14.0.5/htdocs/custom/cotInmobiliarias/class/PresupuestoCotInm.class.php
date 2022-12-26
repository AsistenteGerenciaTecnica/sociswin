<?php
 /*
Clase para obtener y modificar los campos relacionados con las cotizaciones
dentro de las órdenes a proveedores
*/
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
class PresupuestoCotInm  
{
    var $db;
    var $id;
	var $cotinm_administracion_tipo;
	var $cotinm_administracion_valor;
	var $cotinm_administracion_iva;
	var $cotinm_seguros_tipo;
	var $cotinm_seguros_valor;
	var $cotinm_seguros_iva;
	var $cotinm_gravamen_tipo;
	var $cotinm_gravamen_valor;
	var $cotinm_costo_transaccion_valor;
	var $cotinm_costo_transaccion_tipo;	

    function PresupuestoCotInm($DB) 
    {
        $this->db = $DB;
        return 1;
    }    
    /**
     *    \brief      Load object in memory from database
     *    \param      id          id object
     *    \return     int         <0 if KO, >0 if OK
     */
	
	/*
	Extrae la información de la cotización relacionada con la orden, y las guarda
	en las variables de la clase
	*/
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " p.rowid,";
		$sql.= " p.cotinm_administracion_tipo,";
		$sql.= " p.cotinm_administracion_valor,";
		$sql.= " p.cotinm_administracion_iva,";
		$sql.= " p.cotinm_gravamen_tipo,";
		$sql.= " p.cotinm_gravamen_valor,";
		$sql.= " p.cotinm_seguros_tipo,";
		$sql.= " p.cotinm_seguros_valor,";  
		$sql.= " p.cotinm_seguros_iva,";     
		$sql.= " p.cotinm_costo_transaccion_valor,";
		$sql.= " p.cotinm_costo_transaccion_tipo"; 
        $sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as p";
        $sql.= " WHERE p.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id    = $obj->rowid;
                $this->cotinm_administracion_tipo = $obj->cotinm_administracion_tipo;
				$this->cotinm_administracion_valor = $obj->cotinm_administracion_valor;
				$this->cotinm_administracion_iva = $obj->cotinm_administracion_iva;
				$this->cotinm_gravamen_tipo = $obj->cotinm_gravamen_tipo;
				$this->cotinm_gravamen_valor = $obj->cotinm_gravamen_valor;
				$this->cotinm_seguros_tipo = $obj->cotinm_seguros_tipo;
				$this->cotinm_seguros_valor = $obj->cotinm_seguros_valor;		
				$this->cotinm_seguros_iva = $obj->cotinm_seguros_iva;
				$this->cotinm_costo_transaccion_valor = $obj->cotinm_costo_transaccion_valor;
				$this->cotinm_costo_transaccion_tipo = $obj->cotinm_costo_transaccion_tipo;			
            }
            $this->db->free($resql);
            
            return 1;
        }
        else
        {
            return -1;
        }
    }    

	// Actualizar los campos dentro de la orden
    function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		// Clean parameters
        if (isset($this->numero_contador)) $this->numero_contador=trim($this->numero_contador);
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."commande_fournisseur SET";

		$sql.= " cotinm_administracion_tipo='".$this->cotinm_administracion_tipo."',";
		$sql.= " cotinm_administracion_valor='".$this->cotinm_administracion_valor."',";
		$sql.= " cotinm_administracion_iva='".$this->cotinm_administracion_iva."',";
		$sql.= " cotinm_gravamen_tipo='".$this->cotinm_gravamen_tipo."',";
		$sql.= " cotinm_gravamen_valor='".$this->cotinm_gravamen_valor."',";
		$sql.= " cotinm_seguros_tipo='".$this->cotinm_seguros_tipo."',";
		$sql.= " cotinm_seguros_valor='".$this->cotinm_seguros_valor."',";
		$sql.= " cotinm_seguros_iva='".$this->cotinm_seguros_iva."',";
		$sql.= " cotinm_costo_transaccion_tipo='".$this->cotinm_costo_transaccion_tipo."',";
		$sql.= " cotinm_costo_transaccion_valor='".$this->cotinm_costo_transaccion_valor."'";

        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}	
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}		
    }

	// Actualizar los productos relacionados con la orden
	function update_products_cotinm($linea_id_eliminar = NULL)
	{
		global $conf;
		$object = new CommandeFournisseur($this->db);
		$object->fetch($this->id);		
		$object->info($this->id);
		$object->fetch_thirdparty();
		
		//Eliminar los productos existentes de la cotización que estén en la orden
		$importe_pedido = 0;				
		foreach ($object->lines as $line)
		{			
			if($line->fk_product == $conf->global->COTINM_PRODUCTO_ADMINISTRACION)
			{
				$object->deleteline($line->id);				
			}
			elseif($line->fk_product == $conf->global->COTINM_PRODUCTO_GRAVAMEN)
			{
				$object->deleteline($line->id);
			}
			elseif($line->fk_product == $conf->global->COTINM_PRODUCTO_COSTO_TRANSACCION)
			{
				$object->deleteline($line->id);
			}
			elseif($line->fk_product == $conf->global->COTINM_PRODUCTO_SEGUROS)
			{
				$object->deleteline($line->id);
			}
			else
			{
				if($linea_id_eliminar != $line->id)
				{
					//Obtener el importe total de la orden
					$importe_pedido+= $line->total_ht;
				}
			}
		}
		if($importe_pedido > 0)
		{			
			$object->fetch($this->id);
			//Crear los productos de la cotización con los nuevos valores si el importe es mayor que cero

			//INICIO ADMINISTRACION
			$cotinm_valor_administracion_aplicar = $this->cotinm_administracion_valor;
			if($this->cotinm_administracion_tipo == '%')
			{
				$cotinm_valor_administracion_aplicar = $importe_pedido*$cotinm_valor_administracion_aplicar/100;
			}			
			if($cotinm_valor_administracion_aplicar > 0)
			{
						
				$producto_administracion = new Product($this->db);
							
				$producto_administracion->fetch($conf->global->COTINM_PRODUCTO_ADMINISTRACION);
				if($producto_administracion->id > 0)
				{
					$desc = '';
					$label = $producto_administracion->label;
					$qty = 1;
					$tva_tx = $this->cotinm_administracion_iva;					
					$price_base_type = $producto_administracion->price_base_type;
					$type = $producto_administracion->type;
					
					$pu_ht	= price2num(-$cotinm_valor_administracion_aplicar, 'MU');
					$pu_ttc	= price2num($pu_ht * (1 + ($tva_tx/100)), 'MU');					
				
					// Función de la clase CommandeFournisseur para añadir el producto a la orden de proveedor
					$object->addline(
							$desc,							// $desc
							$pu_ht,							// $pu_ht
							$qty,							// $qty
							$tva_tx,						// $txtva
							0,								// $txlocaltax1
							0,								// $txlocaltax2
							$producto_administracion->id,	// $fk_product
							0,								// $fk_prod_fourn_price
							'',								// $ref_supplier
							0,								// $remise_percent
							$price_base_type,				// $price_base_type
							0,								// $pu_ttc
							$type,							// $type
							0,								// $info_bits
							false,							// $notrigger
							null,							// $date_start
							null,							// $date_end
							0,								// $array_options
							null,							// $fk_unit
							0,								// $pu_ht_devise
							$label,							// $origin
							0								// $origin_id
						);
				}
			}			
			//FIN ADMINISTRACION

			//INICIO SEGUROS
			$cotinm_valor_seguros_aplicar = $this->cotinm_seguros_valor;		
			if($this->cotinm_seguros_tipo == '%')
			{
				$cotinm_valor_seguros_aplicar = $importe_pedido*$cotinm_valor_seguros_aplicar/100;
			}
			if($cotinm_valor_seguros_aplicar > 0)
			{					
				$producto_seguros = new Product($this->db);				
				$producto_seguros->fetch($conf->global->COTINM_PRODUCTO_SEGUROS);
				if($producto_seguros->id > 0)
				{
					$desc = '';
					$label = $producto_seguros->label;
					$qty = 1;
					$tva_tx = $this->cotinm_seguros_iva;					
					$price_base_type = $producto_seguros->price_base_type;
					$type = $producto_seguros->type;
					
					$pu_ht	= price2num(-$cotinm_valor_seguros_aplicar, 'MU');
					$pu_ttc	= price2num($pu_ht * (1 + ($tva_tx/100)), 'MU');					
				
					// Función de la clase CommandeFournisseur para añadir el producto a la orden de proveedor
					$object->addline(
						$desc,							// $desc
						$pu_ht,							// $pu_ht
						$qty,							// $qty
						$tva_tx,						// $txtva
						0,								// $txlocaltax1
						0,								// $txlocaltax2
						$producto_seguros->id,			// $fk_product
						0,								// $fk_prod_fourn_price
						'',								// $ref_supplier
						0,								// $remise_percent
						$price_base_type,				// $price_base_type
						0,								// $pu_ttc
						$type,							// $type
						0,								// $info_bits
						false,							// $notrigger
						null,							// $date_start
						null,							// $date_end
						0,								// $array_options
						null,							// $fk_unit
						0,								// $pu_ht_devise
						$label,							// $origin
						0								// $origin_id
					);
				}
			}
			//FIN SEGUROS	
			
			//INICIO GRAVAMEN
			/*
			Para calcular el gravamen, se obtienen los valores de la comisión de administración y 
			de los seguros, junto con el iva de los mismos, para calcular el valor al que se le
			va a aplicar el 4x1000
			*/
			$iva_administracion = $cotinm_valor_administracion_aplicar * $this->cotinm_administracion_iva/100;
			$iva_seguros = $cotinm_valor_seguros_aplicar * $this->cotinm_seguros_iva/100;
			$importe_descontado = (
				$importe_pedido - 
				($cotinm_valor_seguros_aplicar + $iva_seguros) - 
				($cotinm_valor_administracion_aplicar + $iva_administracion)
			);
			$cotinm_valor_gravamen_aplicar = $this->cotinm_gravamen_valor;		
			if($this->cotinm_gravamen_tipo == '%')
			{
				$cotinm_valor_gravamen_aplicar = $importe_descontado*$cotinm_valor_gravamen_aplicar/100;
			}
			if($cotinm_valor_gravamen_aplicar > 0)
			{					
				$producto_gravamen = new Product($this->db);				
				$producto_gravamen->fetch($conf->global->COTINM_PRODUCTO_GRAVAMEN);
				if($producto_gravamen->id > 0)
				{
					$desc = '';
					$label = $producto_gravamen->label;
					$qty = 1;
					$tva_tx = $producto_gravamen->tva_tx;					
					$price_base_type = $producto_gravamen->price_base_type;
					$type = $producto_gravamen->type;
					
					$pu_ht	= price2num(-$cotinm_valor_gravamen_aplicar, 'MU');
					$pu_ttc	= price2num($pu_ht * (1 + ($tva_tx/100)), 'MU');					
				
					// Función de la clase CommandeFournisseur para añadir el producto a la orden de proveedor
					$object->addline(
						$desc,							// $desc
						$pu_ht,							// $pu_ht
						$qty,							// $qty
						$tva_tx,						// $txtva
						0,								// $txlocaltax1
						0,								// $txlocaltax2
						$producto_gravamen->id,			// $fk_product
						0,								// $fk_prod_fourn_price
						'',								// $ref_supplier
						0,								// $remise_percent
						$price_base_type,				// $price_base_type
						0,								// $pu_ttc
						$type,							// $type
						0,								// $info_bits
						false,							// $notrigger
						null,							// $date_start
						null,							// $date_end
						0,								// $array_options
						null,							// $fk_unit
						0,								// $pu_ht_devise
						$label,							// $origin
						0								// $origin_id
					);
				}
			}
			//FIN GRAVAMEN

			//INICIO COSTO TRANSACCION
			$cotinm_valor_costo_transaccion_aplicar = $this->cotinm_costo_transaccion_valor;		
			if($this->cotinm_costo_transaccion_tipo == '%')
			{
				$cotinm_valor_costo_transaccion_aplicar = $importe_pedido*$cotinm_valor_costo_transaccion_aplicar/100;
			}
			if($cotinm_valor_costo_transaccion_aplicar > 0)
			{					
				$producto_costo_transaccion = new Product($this->db);				
				$producto_costo_transaccion->fetch($conf->global->COTINM_PRODUCTO_COSTO_TRANSACCION);
				if($producto_costo_transaccion->id > 0)
				{
					$desc = '';
					$label = $producto_costo_transaccion->label;
					$qty = 1;
					$tva_tx = $producto_costo_transaccion->tva_tx;					
					$price_base_type = $producto_costo_transaccion->price_base_type;
					$type = $producto_costo_transaccion->type;
					
					$pu_ht	= price2num(-$cotinm_valor_costo_transaccion_aplicar, 'MU');
					$pu_ttc	= price2num($pu_ht * (1 + ($tva_tx/100)), 'MU');					
					
					// Función de la clase CommandeFournisseur para añadir el producto a la orden de proveedor
					$object->addline(
						$desc,							// $desc
						$pu_ht,							// $pu_ht
						$qty,							// $qty
						$tva_tx,						// $txtva
						0,								// $txlocaltax1
						0,								// $txlocaltax2
						$producto_costo_transaccion->id,// $fk_product
						0,								// $fk_prod_fourn_price
						'',								// $ref_supplier
						0,								// $remise_percent
						$price_base_type,				// $price_base_type
						0,								// $pu_ttc
						$type,							// $type
						0,								// $info_bits
						false,							// $notrigger
						null,							// $date_start
						null,							// $date_end
						0,								// $array_options
						null,							// $fk_unit
						0,								// $pu_ht_devise
						$label,							// $origin
						0								// $origin_id
					);
				}
			} 
			//FIN COSTO TRANSACCION

			//Guardar la configuracion del AIU en la orden
		}
		$this->update();
	}
}
?>