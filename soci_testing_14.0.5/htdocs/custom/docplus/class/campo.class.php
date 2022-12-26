<?php

/**
 * CAMPO
 * 
 * Clase para gestionar los campos de los documentos
 */

require_once (DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once (DOL_DOCUMENT_ROOT."/custom/docplus/class/documento.class.php");

class Campo extends CommonObject
{
    var $db;
    var $fk_documento;
	var $valores;
    var $nombre;
    var $tipo;
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
		$sql.= " c.rowid";
		$sql.= ", c.fk_documento";
		$sql.= ", c.valores";
		$sql.= ", c.nombre";
		$sql.= ", c.tipo";
		$sql.= ", c.tms";
		$sql.= ", c.fk_user_modif";
		$sql.= ", c.date_creation";
		$sql.= ", c.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_campo as c";
        $sql.= " WHERE c.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->fk_documento = $obj->fk_documento;
                $this->valores = $obj->valores;
                $this->nombre = $obj->nombre;
                $this->tipo = $obj->tipo;
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

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."docplus_campo (";
		$sql .= "fk_documento";
		$sql .= ", valores";
		$sql .= ", nombre";
		$sql .= ", tipo";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", date_creation";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->fk_documento ."'";
		$sql .= ", '". $this->valores ."'";
		$sql .= ", '". $this->nombre ."'";
		$sql .= ", '". $this->tipo ."'";
		$sql .= ", '". $this->tms."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". $this->date_creation ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."docplus_campo");
			
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
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."docplus_campo SET";

		$sql.= " fk_documento='". $this->fk_documento ."'";
		$sql.= ", valores='". $this->valores ."'";
		$sql.= ", nombre='". $this->nombre ."'";
		$sql.= ", tipo='". $this->tipo ."'";
		$sql.= ", tms='". $this->tms ."'";
		$sql.= ", fk_user_modif='". $user ."'";

        $sql.= " WHERE rowid='".$this->id."'";

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
	public function delete() 
	{

		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."docplus_campo";
		$sql .= " WHERE rowid = '".$this->id."'";

		dol_syslog("docplus_campo::delete", LOG_DEBUG);
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
	 *	Obtener todos los campos
	 *
	 *  @param		int 	$documento 		ID del documento al que pertenece el campo
	 * 
	 *	@return		int|array		<0 if KO, >0 if OK
	 */
	public function getAll($documento = "") 
	{

		$result = array();
		
        $sql = "SELECT";
		$sql.= " c.rowid";
		$sql.= ", c.fk_documento";
		$sql.= ", c.valores";
		$sql.= ", c.nombre";
		$sql.= ", c.tipo";
		$sql.= ", c.tms";
		$sql.= ", c.fk_user_modif";
		$sql.= ", c.date_creation";
		$sql.= ", c.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_campo as c";
        
        if ($documento != "")
        {
			$sql .= " WHERE c.fk_documento = '". $documento ."'";
        }
		
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Campo($this->db);
				
				$line->id = $obj->rowid;
				$line->fk_documento = $obj->fk_documento;
				$line->valores = $obj->valores;
				$line->nombre = $obj->nombre;
				$line->tipo = $obj->tipo;
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
	 *	Obtener la relación entre el campo y el documento_objeto
	 * 
	 *	@param		int		$fk_objeto		ID del objeto de la relación

	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function fetch_doc_objeto_campo($objeto_id)
	{
		global $langs;

		$documento = new Documento($this->db);		
		$documento->fetch($this->fk_documento);

		$doc_objeto = $documento->fetch_doc_objeto($objeto_id);

		if ($doc_objeto)
		{
			$sql = "SELECT";
			$sql .= " dc.rowid";
			$sql .= ", dc.fk_do";
			$sql .= ", dc.fk_campo";
			$sql .= ", dc.valor";
			$sql .= ", dc.tms";
			$sql .= ", dc.fk_user_modif";
			$sql .= ", dc.date_creation";
			$sql .= ", dc.fk_user_creat";
			$sql.= " FROM ".MAIN_DB_PREFIX."docplus_documento_objeto_campo as dc";
			$sql.= " WHERE dc.fk_do = '". $doc_objeto->rowid ."' AND dc.fk_campo = '". $this->id ."'";
	
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
		return 0;
	}

	/**
	 *	Crear la relación entre el campo y el documento_objeto
	 *
	 *  @param		int 	$user 				ID del usuario que crea el registro
	 *  @param		int 	$fk_objeto			ID de la relación del objeto y el documento del campo
	 *  @param		string 	$valor 				Valor a guardar en el campo
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function create_doc_objeto_campo($user, $fk_do, $valor)
	{

		global $conf, $langs;

		$error = 0;

		dol_syslog(get_class($this)."::create ref=".$this->ref);

		$now = dol_now();

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."docplus_documento_objeto_campo (";
		$sql .= "fk_do";
		$sql .= ", fk_campo";
		$sql .= ", valor";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", date_creation";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $fk_do ."'";
		$sql .= ", '". $this->id ."'";
		$sql .= ", '". $valor ."'";
		$sql .= ", '". date("Y-m-d H:i:s") ."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". date("Y-m-d H:i:s") ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."_OBJETO_CAMPO::create", LOG_DEBUG);
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
	 *	Actualizar la relación entre el campo y el documento_objeto
	 *
	 * 	@param		int 	$user 				ID del usuario que crea el registro
	 *  @param		int 	$fk_objeto			ID de la relación del objeto y el documento del campo
	 *  @param		string 	$valor 				Valor a guardar en el campo
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function update_doc_objeto_campo($user, $fk_do, $valor)
	{

		global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."docplus_documento_objeto_campo SET";

		$sql .= " valor='". $valor ."'";
		$sql .= ", tms='". date("Y-m-d H:i:s") ."'";
		$sql .= ", fk_user_modif='". $user ."'";

        $sql.= " WHERE fk_do='". $fk_do . "' AND fk_campo='". $this->id ."'";

		$this->db->begin();
        
		dol_syslog(get_class($this)."_OBJETO_CAMPO::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."_OBJETO_CAMPO::update ".$errmsg, LOG_ERR);
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
	 *	Eliminar la relación entre el campo y el documento_objeto
	 * 
	 *	@param		int		$fk_objeto		ID del objeto de la relación

	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete_doc_objeto_campo($fk_do) {

		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."docplus_documento_objeto_campo";
		$sql .= " WHERE fk_campo='". $this->id ."' AND fk_do='". $fk_do ."'";

		dol_syslog("DOCUMENTO_OBJETO_CAMPO::delete", LOG_DEBUG);
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