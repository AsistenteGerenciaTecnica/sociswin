<?php

/**
 * CATEGORÍA
 * 
 * Clase para gestionar las categorías de documentos 
 * y carpetas
 */

require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';

class Categoria extends CommonObject
{
    var $db;
    var $id;
	var $nombre;
	var $modulo;
	var $tms;
	var $fk_user_modif;
    

    public function __construct($DB) 
    {
        $this->db = $DB;
        return 1;
    }    
    /**
     *    \brief      Load object in memory from database
     *    \param      id          id object
     *    \return     int         <0 if KO, >0 if OK
     */
	
	/**
	 *	Extrae la información de una categoría según su ID
	 *
	 *  @param		int		$id		ID de la categoría
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " c.rowid";
		$sql.= ", c.nombre";
		$sql.= ", c.modulo";
		$sql.= ", c.tms";
		$sql.= ", c.fk_user_modif";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_categoria as c";
        $sql.= " WHERE c.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->nombre = $obj->nombre;
                $this->modulo = $obj->modulo;
				$this->tms = $obj->tms;
                $this->fk_user_modif = $obj->fk_user_modif;
				
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
	 *	Crea la categoria en la base de datos
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

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."docplus_categoria (";
		$sql .= "nombre";
		$sql .= ", modulo";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->nombre ."'";
		$sql .= ", '". $this->modulo ."'";
		$sql .= ", '". $this->tms ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."docplus_categoria");
			
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
	 *	Actualizar la categoría en la base de datos
	 *
	 *  @param		int		$user 		User creator
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
    public function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."docplus_categoria SET";

		$sql.= " nombre='". $this->nombre ."'";
		$sql.= ", modulo='". $this->modulo ."'";
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
	 *	Eliminar categoría
	 *
	 *  @param		int 	$inter 		ID de la intervención
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete() {

		$error = 0;

		$this->db->begin();


		$doc = new Documento($this->db);
		$documentos = $doc->getAll($this->id, "categoria");

		foreach($documentos as $documento)
		{
			$res = $documento->delete();
			if ($res <= 0)
			{
				$error++;
				break;
			}
		}

		if ($error > 0)
		{
			return -1;
		}
		
		$carp = new Carpeta($this->db);
		$carpetas = $carp->getAll($this->id, "categoria");

		foreach($carpetas as $carpeta)
		{
			$res = $carpeta->delete();
			if ($res <= 0)
			{
				$error++;
				break;
			}
		}

		if ($error > 0)
		{
			return -1;
		}

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."docplus_categoria";
		$sql .= " WHERE rowid = ".$this->id;

		dol_syslog("DOCPLUS_CATEGORIA::delete", LOG_DEBUG);
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
	 *	Obtener todas las categorías según el tercero
	 *
	 *  @param		int 	$third	ID del tercero
	 * 
	 *	@return		int|array		<0 if KO, >0 if OK
	 */
	public function getAll($modulo = "") {

		$result = array();

        $sql = "SELECT";
		$sql.= " c.rowid";
		$sql.= ", c.nombre";
		$sql.= ", c.modulo";
		$sql.= ", c.tms";
		$sql.= ", c.fk_user_modif";
        $sql.= " FROM ".MAIN_DB_PREFIX."docplus_categoria as c";
        
        if ($modulo != "")
        {
            $sql.= " WHERE c.modulo = '". $modulo ."'";
        }

        $resql = $this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Categoria($this->db);
				
				$line->id = $obj->rowid;
                $line->nombre = $obj->nombre;
                $line->modulo = $obj->modulo;
				$line->tms = $obj->tms;
				$line->fk_user_modif = $obj->fk_user_modif;
				
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