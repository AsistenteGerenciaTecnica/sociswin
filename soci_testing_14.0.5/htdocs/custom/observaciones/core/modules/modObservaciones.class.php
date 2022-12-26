<?php
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 		\class      modObservaciones
 *      \brief      Descripcion del modulo de observaciones
 */
class modObservaciones extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function __construct($DB)
	{
		global $langs, $conf;
		$this->db = $DB;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 10050;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'observaciones';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "products";
		$this->module_position = 50;
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Observaciones e Interventor Externo";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='logo@observaciones';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		
		$this->module_parts = array('triggers' => 0);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();
		$r=0;

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		//$this->style_sheet = '/aiu/css/aiu.css';
		$this->module_parts = array('css' => array('/observaciones/css/observaciones.css'),'triggers' => 1);
		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array('acercade.php@observaciones');

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->depends = array(
			"modSociete",
			"modProjet",
			"modContrat",
			"modFicheinter"
		);
		// List of modules id to disable if this one is disabled		
		$this->requiredby = array();	
		
		$this->langfiles = array("observaciones@observaciones");

		// Constants
		
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 0 or 'allentities')
		$this->const = array();			

		// Array to add new pages in new tabs
		$this->tabs = array(
			'intervention:+TabObservacion:Observaciones:@observaciones:/observaciones/tab_observaciones.php?id=__ID__',
		);
		
		// dictionnarys
		if (!isset($conf->observaciones->enabled))
        {
        	$conf->observaciones = new stdClass();
        	$conf->observaciones->enabled = 0;
        }
		$this->dictionaries = array();

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;
		
	}	

	/**
	 *		\brief      Function called when module is enabled.
	 *					The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *					It also creates data directories.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function init($options = "")
	{
		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *		\brief		Function called when module is disabled.
	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
	 *					Data directories are not deleted.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function remove($options = "")
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		\brief		Create tables, keys and data required by module
	 * 					Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 					and create data commands must be stored in directory /mymodule/sql/
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/observaciones/sql/');
	}
}
?>