<?php
/* Copyright (C) 2015 Alexis Jose Turruella <alexturruella@gmail.com>
 */

/**
 *  \file       dev/skeletons/caiuadministracion.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2018-06-01 03:22
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Values extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
    var $sources = array(
        'a'=>'c_cotinm_administracion',
        'g'=>'c_cotinm_gravamen',
        'i'=>'c_cotinm_iva', 
        's'=>'c_cotinm_seguros'
    );
	var $valor;
	var $active;  


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


   

    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
    function getlist($table)
    {
    	global $langs;
		$list = array();
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.valor,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->sources[$table]." as t";
        $sql.= " WHERE t.active = true";

    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
			$nump = $this->db->num_rows($resql);
            if ($nump)
            {
                $i = 0;
                while ($i < $nump)
                {
                    $list[] = $this->db->fetch_object($resql);
                    $i++;
                }
            }            
            $this->db->free($resql);
            return $list;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
             return $list;
        }
    }

}
