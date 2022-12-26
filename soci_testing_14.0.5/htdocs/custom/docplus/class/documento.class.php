<?php

/**
 * DOCUMENTO
 * 
 * Clase para gestionar los documentos
 */

require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

class Documento extends CommonObject
{
    var $db;
    var $modulo;
    var $fk_parent;
	var $tipo_parent;
    var $nombre;
	var $tms;
	var $fk_user_modif;
	var $date_creation;
    var $fk_user_creat;
    

    public function __construct($DB) 
    {
        $this->db = $DB;
        return 1;
    }    
	
	/**
	 *	Extrae la información de un documento según su ID
	 *
	 *  @param		int		$id		ID del documento
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " d.rowid";
		$sql.= ", d.modulo";
		$sql.= ", d.fk_parent";
		$sql.= ", d.tipo_parent";
		$sql.= ", d.nombre";
		$sql.= ", d.tms";
		$sql.= ", d.fk_user_modif";
		$sql.= ", d.date_creation";
		$sql.= ", d.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_documento as d";
        $sql.= " WHERE d.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->modulo = $obj->modulo;
                $this->fk_parent = $obj->fk_parent;
                $this->tipo_parent = $obj->tipo_parent;
                $this->nombre = $obj->nombre;
				$this->tms = $obj->tms;
                $this->fk_user_modif = $obj->fk_user_modif;
                $this->date_creation = $obj->date_creation;
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
	 *	Crea el documento en la base de datos
	 *
	 *  @param		User	$user 		User creator
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function create($user, $notrigger = 0)
	{
		global $conf, $langs;

		$error = 0;

		dol_syslog(get_class($this)."::create ref=".$this->ref);

		$now = dol_now();

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."docplus_documento (";
		$sql .= "modulo";
		$sql .= ", fk_parent";
		$sql .= ", tipo_parent";
		$sql .= ", nombre";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", date_creation";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->modulo ."'";
		$sql .= ", '". $this->fk_parent ."'";
		$sql .= ", '". $this->tipo_parent ."'";
		$sql .= ", '". $this->nombre ."'";
		$sql .= ", '". $this->tms."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". $this->date_creation ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."docplus_documento");
			
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
	 *	Actualizar el documento en la base de datos
	 *
	 *  @param		int		$user 		User creator
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
    public function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."docplus_documento SET";

		$sql.= " fk_parent='". $this->fk_parent ."'";
		$sql.= ", tipo_parent='". $this->tipo_parent ."'";
		$sql.= ", nombre='". $this->nombre ."'";
		$sql.= ", tms='". $this->tms ."'";
		$sql.= ", fk_user_modif='". $user ."'";

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
	 *	Eliminar documento
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete() {

		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."docplus_documento";
		$sql .= " WHERE rowid = ".$this->id;

		dol_syslog("DOCPLUS_DOCUMENTO::delete", LOG_DEBUG);
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
	 *	Obtener todos los documentos
	 *
	 *  @param		int 	$parent	ID del elemento padre (carpeta o categoría)
	 * 
	 *	@return		int|array		<0 if KO, >0 if OK
	 */
	public function getAll($parent = "", $tipo_parent = "", $modulo = "") {

		$result = array();
		
        $sql = "SELECT";
		$sql.= " d.rowid";
		$sql.= ", d.modulo";
		$sql.= ", d.fk_parent";
		$sql.= ", d.tipo_parent";
		$sql.= ", d.nombre";
		$sql.= ", d.tms";
		$sql.= ", d.fk_user_modif";
		$sql.= ", d.date_creation";
		$sql.= ", d.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_documento as d";
        
		$conditions = array(
			array(
				"key" => "fk_parent", 
				"value" => $parent
			),
			array(
				"key" => "tipo_parent", 
				"value" => $tipo_parent
			),
			array(
				"key" => "modulo", 
				"value" => $modulo
			)
		);

		$conds = false;
		foreach($conditions as $cond)
		{
			if ($cond["value"] != "")
			{
				$conds = true;
				break;
			}
		}

		if ($conds)
		{
			$sql .= " WHERE";

			$printed = false;
			for ($i = 0; $i < count($conditions); $i++)
			{
				$cond = $conditions[$i];

				if ($cond["value"] != "")
				{
					if ($printed)
					{
						$sql .= " AND";
					}
					else
					{
						$printed = true;
					}
					
					$sql .= " d.". $cond["key"] ." = '". $cond["value"] ."'"; 
				}
			}
		}

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Documento($this->db);
				
				$line->id = $obj->rowid;
				$line->fk_parent = $obj->fk_parent;
				$line->tipo_parent = $obj->tipo_parent;
				$line->nombre = $obj->nombre;
				$line->tms = $obj->tms;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->date_creation = $obj->date_creation;
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


	/**
	 *	Obtener la relación entre el documento y el objeto
	 * 
	 *	@param		int		$fk_objeto		ID del objeto de la relación

	 *	@return		int|object		<0 if KO, Objeto con el resultado if OK
	 */
	public function fetch_doc_objeto($fk_objeto)
	{
		global $langs;
        $sql = "SELECT";
		$sql .= " do.rowid";
		$sql .= ", do.fk_objeto";
		$sql .= ", do.fk_documento";
		$sql .= ", do.renovable";
		$sql .= ", do.tipo_renovacion";
		$sql .= ", do.valor_cada";
		$sql .= ", do.tiempo_cada";
		$sql .= ", do.fecha_renovacion";
		$sql .= ", do.valor_aviso";
		$sql .= ", do.tiempo_aviso";
		$sql .= ", do.aviso_renovacion";
		$sql .= ", do.tms";
		$sql .= ", do.fk_user_modif";
		$sql .= ", do.date_creation";
		$sql .= ", do.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_documento_objeto as do";
        $sql.= " WHERE do.fk_objeto = '". $fk_objeto ."' AND do.fk_documento = '". $this->id ."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
				
				$this->db->free($resql);
				return $obj;
            }

			$this->db->free($resql);
			return 0;
        }
        else
        {
            return -1;
        }
	}

	/**
	 * Crear la relación entre el documento y el objeto
	 *
	 * @param		int 	$user 				ID del usuario que actualiza el registro
	 * @param		int 	$fk_objeto 			ID del objeto al que pertenece el documento
	 * @param		int 	$renovable 			0 o 1, si es renovable o no
	 * @param		int 	$valor_cada 		Cada cuanto se renueva el documento
	 * @param		string 	$tiempo_cada 		Escala de tiempo en la que se renueva (days, months, years)
	 * @param		string	tipo_renovacion 	cada/fecha
	 * @param		int 	$valor_aviso	 	Tiempo antes de enviar la notificación de renovación
	 * @param		string 	$tiempo_aviso	 	Escala de tiempo para notificar la renovación
	 * @param		string 	$tipo_renovacion 	cada/fecha
	 * @param		date 	$fecha_renovacion	Fecha de la siguiente renovacion
	 * @param		date 	$aviso_renovacion	Fecha del próximo aviso de renovación
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function create_doc_objeto(
		$user, 
		$fk_objeto, 
		$renovable = '0', 
		$tipo_renovacion = '', 
		$valor_cada = '',
		$tiempo_cada = '',
		$valor_aviso = '',
		$tiempo_aviso = '',
		$fecha_renovacion = '', 
		$aviso_renovacion = ''
		)
	{

		global $conf, $langs;

		$error = 0;

		dol_syslog(get_class($this)."::create ref=".$this->ref);

		$now = dol_now();

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."docplus_documento_objeto (";
		$sql .= "fk_objeto";
		$sql .= ", fk_documento";
		$sql .= ", renovable";
		$sql .= ", tipo_renovacion";
		$sql .= ", valor_cada";
		$sql .= ", tiempo_cada";
		$sql .= ", fecha_renovacion";
		$sql .= ", valor_aviso";
		$sql .= ", tiempo_aviso";
		$sql .= ", aviso_renovacion";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", date_creation";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $fk_objeto ."'";
		$sql .= ", '". $this->id ."'";
		$sql .= ", '". $renovable ."'";
		$sql .= ", ". ($tipo_renovacion == '' ? "NULL" : "'". $tipo_renovacion ."'");
		$sql .= ", ". ($valor_cada == '' ? "NULL" : "'". $valor_cada ."'");
		$sql .= ", ". ($tiempo_cada == '' ? "NULL" : "'". $tiempo_cada ."'");
		$sql .= ", ". ($fecha_renovacion == '' ? "NULL" : "'". $fecha_renovacion ."'");
		$sql .= ", ". ($valor_aviso == '' ? "NULL" : "'". $valor_aviso ."'");
		$sql .= ", ". ($tiempo_aviso == '' ? "NULL" : "'". $tiempo_aviso ."'");
		$sql .= ", ". ($aviso_renovacion == '' ? "NULL" : "'". $aviso_renovacion  ."'");
		$sql .= ", '". date("Y-m-d H:i:s") ."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". date("Y-m-d H:i:s") ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."_OBJETO::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			
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
	 *	Actualizar la relación entre el documento y el objeto
	 *
	 *  @param		int 	$user 				ID del usuario que actualiza el registro
	 *  @param		int 	$fk_objeto 			ID del objeto al que pertenece el documento
	 *  @param		int 	$renovable 			0 o 1, si es renovable o no
	 *  @param		int 	$valor_cada 		Cada cuanto se renueva el documento
	 *  @param		string 	$tiempo_cada 		Escala de tiempo en la que se renueva (days, months, years)
	 *  @param		string	tipo_renovacion 	cada/fecha
	 *  @param		int 	$valor_aviso	 	Tiempo antes de enviar la notificación de renovación
	 *  @param		string 	$tiempo_aviso	 	Escala de tiempo para notificar la renovación
	 *  @param		string 	$tipo_renovacion 	cada/fecha
	 *  @param		date 	$fecha_renovacion	Fecha de la siguiente renovacion
	 *  @param		date 	$aviso_renovacion	Fecha del próximo aviso de renovación
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function update_doc_objeto(
		$user, 
		$fk_objeto, 
		$renovable = '0', 
		$valor_cada = '', 
		$tiempo_cada = '', 
		$tipo_renovacion = '', 
		$valor_aviso = '',
		$tiempo_aviso = '',
		$fecha_renovacion = '', 
		$aviso_renovacion = '')
	{

		global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."docplus_documento_objeto SET";

		$sql .= " renovable='". $renovable ."'";
		$sql .= ", tipo_renovacion=". ($tipo_renovacion == '' ? "NULL" : "'". $tipo_renovacion ."'");
		$sql .= ", valor_cada=". ($valor_cada == '' ? "NULL" : "'". $valor_cada ."'");
		$sql .= ", tiempo_cada=". ($tiempo_cada == '' ? "NULL" : "'". $tiempo_cada ."'");
		$sql .= ", fecha_renovacion=". ($fecha_renovacion == '' ? "NULL" : "'". $fecha_renovacion ."'");
		$sql .= ", valor_aviso=". ($valor_aviso == '' ? "NULL" : "'". $valor_aviso ."'");
		$sql .= ", tiempo_aviso=". ($tiempo_aviso == '' ? "NULL" : "'". $tiempo_aviso ."'");
		$sql .= ", aviso_renovacion=". ($aviso_renovacion == '' ? "NULL" : "'". $aviso_renovacion  ."'");
		$sql .= ", tms='". date("Y-m-d H:i:s") ."'";
		$sql .= ", fk_user_modif='". $user ."'";

        $sql.= " WHERE fk_documento='". $this->id . "' AND fk_objeto='". $fk_objeto ."'";

		$this->db->begin();
        
		dol_syslog(get_class($this)."_OBJETO::update sql=".$sql, LOG_DEBUG);
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
	 *	Eliminar la relación entre el documento y el objeto
	 * 
	 *	@param		int		$fk_objeto		ID del objeto de la relación

	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete_doc_objeto($fk_objeto) {

		$error = 0;

		$this->db->begin();

		$doc_objeto = $this->fetch_doc_objeto($fk_objeto);

		$cam = new Campo($this->db);
		$campos = $cam->getAll($this->id);

		foreach($campos as $campo)
		{

			$res = $campo->delete_doc_objeto_campo($doc_objeto->rowid);

			if ($res <= 0)
			{
				$error++;
				break;
			}
		}

		$sql = "DELETE FROM llx_docplus_documento_objeto";
		$sql .= " WHERE rowid='". $doc_objeto->rowid ."'";

		dol_syslog("DOCUMENTO_OBJETO::delete", LOG_DEBUG);
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
}