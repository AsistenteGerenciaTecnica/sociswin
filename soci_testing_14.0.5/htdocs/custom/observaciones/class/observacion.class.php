<?php
 /*
Clase para obtener y modificar los campos relacionados con las cotizaciones
dentro de las órdenes a proveedores
*/
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
class Observacion
{
    var $db;
    var $id;
	var $fk_intervention;
	//var $descripcion;
	var $fecha;
	var $duracion;
	var $filename;
	var $date_creation;
	var $tms;
	var $fk_user_modif;
	var $fk_user_creat;

    function Observacion($DB) 
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
		$sql.= " o.rowid,";
		$sql.= " o.fk_intervention,";
		$sql.= " o.descripcion,";
		$sql.= " o.fecha,";
		//$sql.= " o.duracion,";
		$sql.= " o.filename,";
		$sql.= " o.date_creation,";
		$sql.= " o.tms,";  
		$sql.= " o.fk_user_modif,";     
		$sql.= " o.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."observacion as o";
        $sql.= " WHERE o.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->fk_intervention = $obj->fk_intervention;
				$this->descripcion = $obj->descripcion;
				$this->fecha = $obj->fecha;
				//$this->duracion = $obj->duracion;
				$this->filename = $obj->filename;
				$this->date_creation = $obj->date_creation;
				$this->tms = $obj->tms;		
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->fk_user_creat = $obj->fk_user_creat;
            }
            $this->db->free($resql);
            
            return 1;
        }
        else
        {
            return -1;
        }
    }    

	/**
	 *	Create an observation into data base
	 *
	 *  @param		User	$user 		Objet user that make creation
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function create($user, $notrigger = 0)
	{
		global $conf, $langs;

		$error = 0;

		dol_syslog(get_class($this)."::create ref=".$this->ref);

		if (!is_numeric($this->duracion)) {
			$this->duracion = 0;
		}

		//$soc = new Societe($this->db);
		//$result = $soc->fetch($this->socid);

		$now = dol_now();

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."observacion (";
		$sql .= "fk_intervention";
		$sql .= ", descripcion";
		$sql .= ", fecha";
		//$sql .= ", duracion";
		$sql .= ", filename";
		$sql .= ", date_creation";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= $this->fk_intervention;
		$sql .= ", '".$this->descripcion . "'";
		$sql .= ", '".$this->fecha . "'";
		//$sql .= ", '".$this->duracion . "'";
		$sql .= ", '".$this->filename . "'";
		$sql .= ", '".$this->date_creation . "'";
		$sql .= ", '".$this->tms . "'";
		$sql .= ", '".$user . "'";
		$sql .= ", '".$user . "'";
		$sql .= ")";

		echo "sql creado <br>";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."observacion");
			
			if (!$error) {
				$this->db->commit();
				return $this->id;
			} else {
				$this->db->rollback();
				$this->error = join(',', $this->errors);
				dol_syslog(get_class($this)."::create ".$this->error, LOG_ERR);
				return -1;
			}
		} else {
			$this->error = $this->db->error();
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Update an observation in the database
	 *
	 *  @param		int		$user 		ID of user that make creation
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
    public function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."observacion SET";

		$sql.= " descripcion='".$this->descripcion ."',";
		$sql.= " fecha='".$this->fecha."',";
		//$sql.= " duracion='".$this->duracion."',";
		$sql.= " filename='".$this->filename."',";
		$sql.= " tms='". date("Y-m-d H:i:s") ."',";
		$sql.= " fk_user_modif='".$user."'";

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

	/**
	 *	Obtener todas las observaciones según una intervención
	 *
	 *  @param		int 	$inter 		ID de la intervención
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete() {

		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."observacion";
		$sql .= " WHERE rowid = ".$this->id;

		//echo "here";

		dol_syslog("Observacion::delete", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
		}

		if (!$error) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Obtener todas las observaciones según una intervención
	 *
	 *  @param		int 	$inter 		ID de la intervención
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function getAll($inter) {

		$result = array();

        $sql = "SELECT";
		$sql.= " o.rowid,";
		$sql.= " o.fk_intervention,";
		$sql.= " o.descripcion,";
		$sql.= " o.fecha,";
		//$sql.= " o.duracion,";
		$sql.= " o.filename,";
		$sql.= " o.date_creation,";
		$sql.= " o.tms,";  
		$sql.= " o.fk_user_modif,";     
		$sql.= " o.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."observacion as o";
        $sql.= " WHERE o.fk_intervention = '". $inter . "'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Observacion($this->db);
				
				$line->id = $obj->rowid;
                $line->fk_intervention = $obj->fk_intervention;
				$line->descripcion = $obj->descripcion;
				$line->fecha = $obj->fecha;
				//$line->duracion = $obj->duracion;
				$line->filename = $obj->filename;
				$line->date_creation = $obj->date_creation;
				$line->tms = $obj->tms;		
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->fk_user_creat = $obj->fk_user_creat;
				
				$result[$i] = $line;

				$i++;
			}
            $this->db->free($resql);
            
            return $result;
        }
        else
        {
            return -1;
        }
	}
}
?>