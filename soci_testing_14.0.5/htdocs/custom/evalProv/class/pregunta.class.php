<?php
 /*
Clase para obtener y modificar los campos relacionados con las 
preguntas para las evaluaciones a proveedor
*/
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
class Pregunta
{
    var $db;
    var $id;
	var $ref;
	var $pregunta;
	var $tipo;
	var $fk_categoria;
	var $tms;
	var $fk_user_modif;
	var $date_creation;
	var $fk_user_creat;

    function Pregunta($DB) 
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
	 * 
	 * Extrae la ínformación de la pregunta y la guarda en el objeto
	 * 
	 * @param	$id		ID del la pregunta
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " p.rowid";
		$sql.= ", p.ref";
		$sql.= ", p.pregunta";
		$sql.= ", p.tipo";
		$sql.= ", p.fk_categoria";
		$sql.= ", p.tms";
		$sql.= ", p.fk_user_modif";  
		$sql.= ", p.date_creation";
		$sql.= ", p.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_pregunta as p";
        $sql.= " WHERE p.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->ref = $obj->ref;
				$this->pregunta = $obj->pregunta;
				$this->tipo = $obj->tipo;
				$this->fk_categoria = $obj->fk_categoria;
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
	 *	Crea la pregunta en la base de datos
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
		
		
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."evalprov_pregunta (";
		$sql .= "ref";
		$sql .= ", pregunta";
		$sql .= ", tipo";
		$sql .= ", fk_categoria";
		$sql .= ", tms";
		$sql .= ", fk_user_modif";
		$sql .= ", date_creation";
		$sql .= ", fk_user_creat";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->ref . "'";
		$sql .= ", '". $this->pregunta ."'";
		$sql .= ", '". $this->tipo ."'";
		$sql .= ", '". $this->fk_categoria ."'";
		$sql .= ", '". $this->tms ."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". $this->date_creation ."'";
		$sql .= ", '". $user ."'";
		$sql .= ")";
		
		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."evalprov_evaluacion");
			
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
	 *	Actualizar la pregunta en la base de datos
	 *
	 *  @param		int		$user 		User creator
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
    public function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."evalprov_pregunta SET";

		$sql.= " pregunta='". $this->pregunta ."'";
		$sql.= ", tipo='". $this->tipo ."'";
		$sql.= ", fk_categoria='". $this->fk_categoria ."'";
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
	 *	Eliminar la pregunta
	 *
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete() {

		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."evalprov_pregunta";
		$sql .= " WHERE rowid = '".$this->id."'";

		dol_syslog("PREGUNTA::delete", LOG_DEBUG);
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
	 *	Obtener todas las preguntas 
	 *
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function getAll() {

		$result = array();

        $sql = "SELECT";
		$sql.= " p.rowid";
		$sql.= ", p.ref";
		$sql.= ", p.pregunta";
		$sql.= ", p.tipo";
		$sql.= ", p.fk_categoria";
		$sql.= ", p.tms";
		$sql.= ", p.fk_user_modif";
		$sql.= ", p.date_creation";
		$sql.= ", p.fk_user_creat";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_pregunta as p";

        $resql=$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Pregunta($this->db);
				
				$line->id = $obj->rowid;
                $line->ref = $obj->ref;
				$line->pregunta = $obj->pregunta;
				$line->tipo = $obj->tipo;
				$line->fk_categoria = $obj->fk_categoria;
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
}
?>