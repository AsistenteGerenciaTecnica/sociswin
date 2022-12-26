<?php
 /*
Clase para obtener y modificar los campos relacionados con las 
evaluaciones a proveedor
*/
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/custom/evalProv/class/pregunta.class.php");
require_once(DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.commande.class.php");
class Evaluacion
{
    var $db;
    var $id;
	var $fk_orden;
	var $ref;
	var $calificacion;
	var $fecha;
	var $date_creation;
	var $tms;
	var $fk_user_modif;

    function Evaluacion($DB) 
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
	 * Extrae la ínformación de la evaluación y la guarda en el objeto
	 * 
	 * @param	$id		ID del la pregunta
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
    function fetch($id, $ref)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " e.rowid";
		$sql.= ", e.fk_orden";
		$sql.= ", e.ref";
		$sql.= ", e.calificacion";
		$sql.= ", e.fecha";
		$sql.= ", e.fk_user_modif";
		$sql.= ", e.tms";  
		$sql.= ", e.date_creation";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_evaluacion as e";
        $sql.= " WHERE e.rowid = '". $id . "' OR e.ref = '". $ref . "'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
 
                $this->id = $obj->rowid;
                $this->fk_orden = $obj->fk_orden;
				$this->ref = $obj->ref;
				$this->calificacion = $obj->calificacion;
				$this->fecha = $obj->fecha;
				$this->fk_user_modif = $obj->fk_user_modif;
				$this->tms = $obj->tms;		
				$this->date_creation = $obj->date_creation;
				
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
	 *	Crea la evaluación en la base de datos
	 *
	 *  @param		User	$user 		User creador
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

		$sql = "INSERT INTO ". MAIN_DB_PREFIX ."evalprov_evaluacion (";
		$sql .= "fk_orden";
		$sql .= ", ref";
		$sql .= ", calificacion";
		$sql .= ", fecha";
		$sql .= ", fk_user_modif";
		$sql .= ", tms";
		$sql .= ", date_creation";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= $this->fk_orden;
		$sql .= ", '". $this->ref ."'";
		$sql .= ", '". $this->calificacion ."'";
		$sql .= ", '". $this->fecha ."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". $this->tms ."'";
		$sql .= ", '". $this->date_creation ."'";
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
	 *	Actualizar la evaluación
	 *
	 *  @param		int		$user 		Usuario actualizador
	 *	@param		int		$notrigger	Disable all triggers
	 *	@return		int		<0 if KO, >0 if OK
	 */
    public function update($user=0, $notrigger=0)
    {		
    	global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."evalprov_evaluacion SET";

		$sql.= " calificacion='". $this->calificacion ."'";
		$sql.= ", fecha='". $this->fecha ."'";
		$sql.= ", fk_user_modif='". $user ."'";
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
	 * 
	 * Eliminar evaluación
	 * Incluye eliminar las preguntas asociadas y cambiar
	 * el estado de la orden de compra
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function delete() {

		$error = 0;

		$this->db->begin();

		$del = $this->deletePregs();

		if ($del < 0)
		{
			return -1;
		}
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."evalprov_evaluacion";
		$sql .= " WHERE rowid = ".$this->id;
		

		dol_syslog("EVALUACION::delete", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (!$resql) {
			$error++;
		}

		$res = $this->setOrder(0, 0);

		if ($res < 0)
		{
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
	 * 
	 * Eliminar todas las preguntas
	 * de la evaluación
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function deletePregs()
	{
		$error = 0;

		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."evalprov_evaluacion_pregunta";
		$sql .= " WHERE fk_evaluacion = ".$this->id;

		dol_syslog("EVALUACION_PREGUNTA::delete", LOG_DEBUG);
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
	 * 
	 * Obtener todas las evaluaciones
	 * 
	 * @param 	array	$conditions		Listado con las condiciones enviadas para la consulta
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	public function getAll($conditions = array()) {

		$result = array();

        $sql = "SELECT";
		$sql.= " e.rowid";
		$sql.= ", e.fk_orden";
		$sql.= ", e.ref";
		$sql.= ", e.calificacion";
		$sql.= ", e.fecha";
		$sql.= ", e.fk_user_modif";
		$sql.= ", e.tms";  
		$sql.= ", e.date_creation";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_evaluacion as e";

		/**
		 * Se espera que las condiciones incluidas vengan separadas
		 * en paréntesis
		 */
		if (!empty($conditions))
		{
			$sql .= " WHERE ";
			for ($i = 0; $i < count($conditions); $i++)
			{
				$sql .= $conditions[$i];

				if (($i + 1) < count($conditions))
				{
					$sql .= " AND ";
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
				
				$line = new Evaluacion($this->db);
				
				$line->id = $obj->rowid;
                $line->fk_orden = $obj->fk_orden;
				$line->ref = $obj->ref;
				$line->calificacion = $obj->calificacion;
				$line->fecha = $obj->fecha;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->tms = $obj->tms;		
				$line->date_creation = $obj->date_creation;
				
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
	 *	Obtener todas las evaluaciones según el tercero
	 *
	 *  @param		int 	$third	ID del tercero
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function getAllFromThird($third) {

		$result = array();

        $sql = "SELECT";
		$sql.= " e.rowid";
		$sql.= ", e.fk_orden";
		$sql.= ", e.ref";
		$sql.= ", e.calificacion";
		$sql.= ", e.fecha";
		$sql.= ", e.fk_user_modif";
		$sql.= ", e.tms";  
		$sql.= ", e.date_creation";
		$sql.= ", cf.fk_soc";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_evaluacion as e";
        $sql.= " INNER JOIN llx_commande_fournisseur as cf ON cf.rowid = e.fk_orden";
		$sql.= " WHERE cf.fk_soc = '". $third ."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Evaluacion($this->db);
				
				$line->id = $obj->rowid;
                $line->fk_orden = $obj->fk_orden;
				$line->ref = $obj->ref;
				$line->calificacion = $obj->calificacion;
				$line->fecha = $obj->fecha;
				$line->fk_user_modif = $obj->fk_user_modif;
				$line->tms = $obj->tms;		
				$line->date_creation = $obj->date_creation;
				
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
	 *	Obtener todas las preguntas de la evaluación
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	public function getPreguntas() {

		$result = array();

        $sql = "SELECT";
		$sql.= " ep.rowid";
		$sql.= ", ep.fk_evaluacion";
		$sql.= ", ep.fk_pregunta";
		$sql.= ", ep.calificacion";
		$sql.= ", ep.comentario";
		$sql.= ", ep.fk_user_modif";  
		$sql.= ", ep.tms";
        $sql.= " FROM ".MAIN_DB_PREFIX."evalprov_evaluacion_pregunta as ep";
        $sql.= " WHERE ep.fk_evaluacion = '". $this->id ."'";

        $resql=$this->db->query($sql);
        if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num) {
				$obj = $this->db->fetch_object($resql);
				
				$line = new Pregunta($this->db);
				
				$line->id = $obj->rowid;
                $line->fk_evaluacion = $obj->fk_evaluacion;
				$line->fk_pregunta = $obj->fk_pregunta;
				$line->calificacion = $obj->calificacion;
				$line->comentario = $obj->comentario;
				$line->fk_user_modif = $obj->fk_user_modif;
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

	/**
	 * 
	 * Actualizar una pregunta asociada a la evaluación
	 * 
	 * @param	int		$user			id del usuario
	 * @param	int		$id_pregunta 	id de la pregunta
	 * @param	float 	$calificacion	nueva calificacion
	 * @param	string	$comentario		nuevo comentario
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function updateEvalPregunta($user, $id_pregunta, $calificacion, $comentario) {

		global $conf, $langs;
		$error=0;    	
		
        $sql = "UPDATE ".MAIN_DB_PREFIX."evalprov_evaluacion_pregunta SET";

		$sql.= " calificacion='". $calificacion ."'";
		$sql.= ", comentario='". $comentario ."'";
		$sql.= ", fk_user_modif='". $user ."'";
		$sql.= ", tms='". $this->tms ."'";

        $sql.= " WHERE fk_evaluacion='".$this->id."' AND fk_pregunta='". $id_pregunta ."'";

		$this->db->begin();
        
		dol_syslog("EVALUACION_PREGUNTA::update sql=".$sql, LOG_DEBUG);

        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog("EVALUACION_PREGUNTA::update ".$errmsg, LOG_ERR);
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
	 * 
	 * Crear una pregunta asociada a la evaluación
	 * 
	 * @param	int		$user			id del usuario
	 * @param	int		$id_pregunta 	id de la pregunta
	 * @param	float 	$calificacion	nueva calificacion
	 * @param	string	$comentario		nuevo comentario
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function createEvalPregunta($user, $id_pregunta, $calificacion, $comentario) {

		global $conf, $langs;

		$error = 0;

		dol_syslog("EVALUACION_PREGUNTA::create ref=".$this->ref);

		$now = dol_now();

		$this->db->begin();

		$sql = "INSERT INTO ".MAIN_DB_PREFIX."evalprov_evaluacion_pregunta (";
		$sql .= "fk_evaluacion";
		$sql .= ", fk_pregunta";
		$sql .= ", calificacion";
		$sql .= ", comentario";
		$sql .= ", fk_user_modif";
		$sql .= ", tms";
		$sql .= ") ";
		$sql .= " VALUES (";
		$sql .= "'". $this->id ."'";
		$sql .= ", '". $id_pregunta ."'";
		$sql .= ", '". $calificacion ."'";
		$sql .= ", '". $comentario ."'";
		$sql .= ", '". $user ."'";
		$sql .= ", '". $this->tms ."'";
		$sql .= ")";

		dol_syslog("EVALUACION_PREGUNTA::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			//$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."evalprov_evaluacion_pregunta");
			if (!$error) {
				$this->db->commit();
				return $this->id;
			} else {
				$this->db->rollback();
				$this->error = join(',', $this->errors);
				dol_syslog("EVALUACION_PREGUNTA::create ".$this->error, LOG_ERR);
				return -1;
			}
		} else {
			$this->error = $this->db->error();
			$this->db->rollback();
			return -1;
		}

	}


	/**
	 * 
	 * Crear el registro de evaluación en los campos extra de la orden de compra
	 * 
	 * @param	int		$status			Nuevo estado (0 o 1)
	 * @param	float	$calificacion	Nueva calificación
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function createOrderStatus($status, $calificacion)
	{
		$sql = "INSERT INTO llx_commande_fournisseur_extrafields (";
		$sql .= " tms";
		$sql .= ", fk_object";
		$sql .= ", orden_evaluada";
		$sql .= ", orden_calificacion";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .= "'". $this->tms ."'";
		$sql .= ", '". $this->fk_orden."'";
		$sql .= ", '". $status ."'";
		$sql .= ", '". $calificacion ."'";
		$sql .= " )";
		
		dol_syslog("FOURN_EXTRAFIELDS"."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			
			if (!$error) {
				$this->db->commit();
				return $this->id;
			} else {
				$this->db->rollback();
				$this->error = join(',', $this->errors);
				dol_syslog("FOURN_EXTRAFIELDS"."::create ".$this->error, LOG_ERR);
				return -1;
			}
		} else {
			$this->error = $this->db->error();
			$this->db->rollback();
			return -1;
		}

	}

	/**
	 * 
	 * Actualizar el registro de evaluación en los campos extra de la orden de compra
	 * 
	 * @param	int		$status			Nuevo estado (0 o 1)
	 * @param	float	$calificacion	Nueva calificación
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function updateOrderStatus($status, $calificacion)
	{
		$sql = "UPDATE llx_commande_fournisseur_extrafields";
		$sql .= " SET orden_evaluada = '". $status ."'";
		$sql .= ", orden_calificacion = '". $calificacion ."'";
		$sql .= " WHERE fk_object = '". $this->fk_orden ."'";

		$this->db->begin();
        
		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog("FOURN_EXTRAFIELDS"."::update ".$errmsg, LOG_ERR);
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
	 * 
	 * Actualizar el registro de evaluación en los campos extra del tercero
	 * 
	 * @param	int		$third	ID del tercero (0 o 1)
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function updateThirdCalificacion($third)
	{

		/**
		 * Obtiene todas las evaluaciones del tercero y 
		 * crea la nueva calificación a partir del promedio de estas
		 */ 
		$evaluaciones = $this->getAllFromThird($third);

		$thirdSum = 0;
		$calThird = 0;
		
		foreach($evaluaciones as $evaluacion)
		{
			$thirdSum += $evaluacion->calificacion;
		}

		if (count($evaluaciones) > 0)
		{
			$calThird = $thirdSum / count($evaluaciones);
		}

		$sql = "UPDATE llx_societe_extrafields";
		$sql .= " SET tercero_evaluacion = '". $calThird ."'";
		$sql .= " WHERE fk_object = '". $third ."'";

		$this->db->begin();
        
		dol_syslog("SOCIETE_EXTRAFIELDS"."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog("SOCIETE_EXTRAFIELDS"."::update ".$errmsg, LOG_ERR);
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
	 * 
	 * Crear el registro de evaluación en los campos extra del tercero
	 * 
	 * @param	int		$third	ID del tercero (0 o 1)
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function createThirdCalificacion($third)
	{
		$evaluaciones = $this->getAllFromThird($third);

		$thirdSum = 0;
		$calThird = 0;
		
		foreach($evaluaciones as $evaluacion)
		{
			$thirdSum += $evaluacion->calificacion;
		}

		if (count($evaluaciones) > 0)
		{
			$calThird = $thirdSum / count($evaluaciones);
		}

		$sql = "INSERT INTO llx_societe_extrafields (";
		$sql .= " tms";
		$sql .= ", fk_object";
		$sql .= ", tercero_evaluacion";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .= "'". $this->tms ."'";
		$sql .= ", '". $third ."'";
		$sql .= ", '". $calThird ."'";
		$sql .= " )";
		
		dol_syslog("SOCIETE_EXTRAFIELDS"."::create", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result) {
			
			if (!$error) {
				$this->db->commit();
				return $this->id;
			} else {
				$this->db->rollback();
				$this->error = join(',', $this->errors);
				dol_syslog("SOCIETE_EXTRAFIELDS"."::create ".$this->error, LOG_ERR);
				return -1;
			}
		} else {
			$this->error = $this->db->error();
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * 
	 * Redirige a la actualización o creación del 
	 * estado de calificación en la orden, aśi como
	 * de la calificación promedio del tercero
	 * 
	 * @param	int		$third	ID del tercero (0 o 1)
	 * 
	 * 
	 * @return		int		<0 if KO, >0 if OK
	 */
	function setOrder($status, $calificacion)
	{
		$order = new CommandeFournisseur($this->db);
		$order->fetch($this->fk_orden);


		$sql = "SELECT rowid, fk_object, orden_evaluada FROM llx_commande_fournisseur_extrafields";
		$sql .= " WHERE fk_object = '". $order->id ."'";		

		$field = $this->db->getRow($sql);

		if ($field != 0)
		{
			$res = $this->updateOrderStatus($status, $calificacion);
		}
		else
		{
			$res = $this->createOrderStatus($status, $calificacion);
		}

		if ($res > 0)
		{
			$sql = "SELECT rowid, fk_object, tercero_evaluacion FROM llx_societe_extrafields";
			$sql .= " WHERE fk_object = '". $order->socid ."'";
	
			$third = $this->db->getRow($sql);
	
			if ($third != 0)
			{
				$res = $this->updateThirdCalificacion($order->socid);
			}
			else
			{
				$res = $this->createThirdCalificacion($order->socid);
			}
		}

		return $res;
	}

	/**
	 * 
	 * Guarda y actualiza los registros de la tabla llx_evalprov_evaluacion_pregunta,
	 * y también los campos dentro de las tablas extrafileld de las
	 * órdenes de compra y los terceros
	 * 
	 * @param		int		$id_pregunta	ID de la pregunta calificada
	 * 
	 *	@return		int		<0 if KO, >0 if OK
	 */
	function evaluar($user, $id_pregunta, $calificacion, $comentario)
	{

		$preguntas = $this->getPreguntas();

		$exists = false;

		if (count($preguntas) > 0)
		{
			foreach($preguntas as $pregunta)
			{
				if ($pregunta->fk_pregunta == $id_pregunta) {
					
					$exists = true;
					break;
	
				}	
			}	
		}

		if ($exists) {

			$result = $this->updateEvalPregunta($user, $id_pregunta, $calificacion, $comentario);	
			
		} else {

			$result = $this->createEvalPregunta($user, $id_pregunta, $calificacion, $comentario);

		}


		if ($result <= 0) {

			return -1;
		}

		$preguntas = $this->getPreguntas();

		$calificacion_sum = 0;

		for ($i = 0; $i < count($preguntas); $i++) {
			
			$calificacion_sum = $calificacion_sum + $preguntas[$i]->calificacion;				
	
		}	

		echo "<br> sum:". $calificacion_sum;

		$calificacion_avg = $calificacion_sum / count($preguntas);

		$this->calificacion = $calificacion_avg;
		
		$res = $this->update($user);

		$res = $this->setOrder(1, $calificacion_avg);

		if ($res <= 0)
		{
			return -1;
		}

		return 1;
	}
}
?>