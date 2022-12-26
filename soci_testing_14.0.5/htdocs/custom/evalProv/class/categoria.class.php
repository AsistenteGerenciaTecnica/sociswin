<?php
/*
Clase para obtener y modificar los campos relacionados con las 
categorias para las preguntas de las evaluaciones a proveedor
*/
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
class Categoria
{
    var $db;
    var $id;
	var $nombre;
	var $tms;
    

    function Categoria($DB) 
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
		$sql.= ", c.tms";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_categoria_pregunta as c";
        $sql.= " WHERE c.rowid = ".$id;

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->nombre = $obj->nombre;
				$this->tms = $obj->tms;
				
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

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."evalprov_categoria_pregunta (";
		$sql .= "nombre";
		$sql .= ", tms";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->nombre ."'";
		$sql .= ", '". $this->tms ."'";
		$sql .= ")";

		dol_syslog(get_class($this)."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."evalprov_categoria_pregunta");
			
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
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."evalprov_categoria_pregunta SET";

		$sql.= " nombre='". $this->nombre ."'";
		$sql.= ", tms='". $this->tms ."'";

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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."evalprov_categoria_pregunta";
		$sql .= " WHERE rowid = ".$this->id;

		dol_syslog("CATEGORIA::delete", LOG_DEBUG);
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
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function getAll() {

		$result = array();

        $sql = "SELECT";
		$sql.= " c.rowid,";
		$sql.= " c.nombre,";
		$sql.= " c.tms";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_categoria_pregunta as c";

        $resql=$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Categoria($this->db);
				
				$line->id = $obj->rowid;
                $line->nombre = $obj->nombre;
				$line->tms = $obj->tms;
				
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