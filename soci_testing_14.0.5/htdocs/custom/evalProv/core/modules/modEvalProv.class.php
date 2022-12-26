<?php
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 		\class      evalProv
 *      \brief      Descripcion del modulo de evaluaciones a proveedores
 */
class modEvalProv extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 10060;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'evalProv';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "srm";
		$this->module_position = '50';

		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));

		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Evaluaciones a proveedores";
		$this->descriptionlong = "Evaluaciones a proveedores";

		// Author
		$this->editor_name = 'Editor name';
		$this->editor_url = 'https://www.example.com';

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// <a href="https://www.flaticon.es/iconos-gratis/evaluacion" title="evaluación iconos">Evaluación iconos creados por Freepik - Flaticon</a>
		$this->picto='logo@evalProv';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 0,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				'/evalProv/css/evalProv.css'
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/socinexus/js/socinexus.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array("/evalProv/temp");
		$r=0;

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		//$this->style_sheet = '/aiu/css/aiu.css';
		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array('acercade.php@evalProv');

		// Dependencies

		// A condition to hide module
		$this->hidden = false;

		// List of modules id that must be enabled if this module is enabled
		$this->depends = array(
			"modSociete",
			"modCommande"
		);
		// List of modules id to disable if this one is disabled		
		$this->requiredby = array();	
		
		$this->langfiles = array("evalProv@evalProv");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','ES'='textes'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','ES'='textes'...)

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 0 or 'allentities')
		$this->const = array();			

		if (!isset($conf->evalProv) || !isset($conf->evalProv->enabled)) {
			$conf->evalProv = new stdClass();
			$conf->evalProv->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array(
			'supplier_order:+TabOrdenEvaluacion:Evaluación:@evalProv:/evalProv/tab_orden_evaluacion.php?id=__ID__',
			'thirdparty:+TabTerceroEvaluacion:Evaluaciones:@evalProv:/evalProv/tab_tercero_evaluaciones.php?id=__ID__'			
		);
		
		// dictionnarys
		/* if (!isset($conf->observaciones->enabled))
        {
        	$conf->observaciones = new stdClass();
        	$conf->observaciones->enabled = 0;
        } */
		$this->dictionaries = array();

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		$this->cronjobs = array();
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// $user->rights->evalProv->formato->read
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); 
		$this->rights[$r][1] = 'Ver y Editar Formato de Evaluación'; 
		$this->rights[$r][4] = 'formato';
		$this->rights[$r][5] = 'read'; 
		$r++;

		// $user->rights->evalProv->evaluacion->read
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); 
		$this->rights[$r][1] = 'Ver Evaluaciones de Proveedores'; 
		$this->rights[$r][4] = 'evaluacion';
		$this->rights[$r][5] = 'read'; 
		$r++;

		// $user->rights->evalProv->evaluacion->write
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); 
		$this->rights[$r][1] = 'Realizar Evaluaciones'; 
		$this->rights[$r][4] = 'evaluacion';
		$this->rights[$r][5] = 'write'; 
		$r++;

		// $user->rights->evalProv->evaluacion->update
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); 
		$this->rights[$r][1] = 'Actualizar Evaluaciones'; 
		$this->rights[$r][4] = 'evaluacion';
		$this->rights[$r][5] = 'update'; 
		$r++;
		
		// $user->rights->evalProv->evaluacion->delete
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); 
		$this->rights[$r][1] = 'Eliminar Evaluaciones'; 
		$this->rights[$r][4] = 'evaluacion';
		$this->rights[$r][5] = 'delete'; 
		$r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		// Menú para el listado de evaluaciones en el módulo de terceros
		$this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=companies,fk_leftmenu=suppliers',
            'type'=>'left',
            'titre'=>'Evaluaciones',
            'mainmenu'=>'companies',
            'leftmenu'=>'evalProv',
            'url'=>'/custom/evalProv/evalProv_index.php',
            'langs'=>'evalProv@evalProv',
            'position'=>1100+$r,
            'enabled'=>'1',
            'perms'=>'$user->rights->evalProv->evaluacion->read',
            'target'=>'',
            'user'=>2,
        );

		// Submenú para el formato de evaluaciones
		$this->menu[$r++]=array(
            'fk_menu'=>'fk_mainmenu=companies,fk_leftmenu=evalProv',
            'type'=>'left',
            'titre'=>'Formato',
            'mainmenu'=>'companies',
            'leftmenu'=>'evalProv_format',
            'url'=>'/custom/evalProv/evalProv_format.php',
            'langs'=>'evalProv@evalProv',
            'position'=>1100+$r,
            'enabled'=>'1',
            'perms'=>'$user->rights->evalProv->formato->read',
            'target'=>'',
            'user'=>2,
        );

		$r = 1;
		
	}	

	/**
	 *		\brief      Function called when module is enabled.
	 *					The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *					It also creates data directories.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function init($options = "")
	{
		global $conf, $langs;

		$result = $this->_load_tables('/evalProv/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Permissions
		$this->remove($options);

		$sql = array();

		return $this->_init($sql, $options);
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

		return $this->_remove($sql, $options);
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
		return $this->_load_tables('/evalProv/sql/');
	}
}
?>