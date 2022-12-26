<?php
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

/**
 * 		\class      modcotInmobiliarias
 *      \brief      Descripcion del modulo AIU
 */
class modcotInmobiliarias extends DolibarrModules
{
	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	public function __construct($DB)
	{
		global $langs, $conf;
		$this->db = $DB;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 10030;
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'cotInmobiliarias';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "products";
		$this->module_position = 50;
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
		$this->description = "Cotizaciones de Inmobiliaria";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='icono@cotInmobiliarias';

		// Defined if the directory /mymodule/inc/triggers/ contains triggers or not
		
		$this->module_parts = array('triggers' => 0);
		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
		$this->dirs = array();
		$r=0;

		// Relative path to module style sheet if exists. Example: '/mymodule/css/mycss.css'.
		//$this->style_sheet = '/aiu/css/aiu.css';
		$this->module_parts = array('css' => array('/cotInmobiliarias/css/cotInmobiliarias.css'),'triggers' => 1);
		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array('acercade.php@cotInmobiliarias');

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->depends = array(
			"modSociete",
			"modProduct",
			"modFacture",
			"modPropale"
		);
		// List of modules id to disable if this one is disabled		
		$this->requiredby = array();	
		
		$this->langfiles = array("cotInmobiliarias@cotInmobiliarias");

		// Constants
		
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 0 or 'allentities')
		$this->const = array();			

		// Array to add new pages in new tabs
		$this->tabs = array(
			'supplier_order:+TabPresupuestoCotizacion:Cotizaciones de Inmobiliaria:@cotInmobiliarias:/cotInmobiliarias/tab_presupuesto.php?id=__ID__',
			'supplier_proposal:+TabCotizacionCotizacion:Cotizaciones de Inmobiliaria:@cotInmobiliarias:/cotInmobiliarias/tab_cotizacion.php?id=__ID__'
		);
		
		// dictionnarys
		if (!isset($conf->cotInmobiliarias->enabled))
        {
        	$conf->cotInmobiliarias = new stdClass();
        	$conf->cotInmobiliarias->enabled = 0;
        }
		$this->dictionaries = array(
			'langs'=>array('cotInmobiliarias@cotInmobiliarias'),
			'tabname'=>array(
							MAIN_DB_PREFIX."c_cotinm_administracion",
							MAIN_DB_PREFIX."c_cotinm_gravamen",
							MAIN_DB_PREFIX."c_cotinm_seguros"
							),
			'tablib'=>array(
							$this->name.": Valor en porcentaje administrativo",
							$this->name.": Valor en porcentaje del gravamen",
							$this->name.": Valor en porcentaje del seguro"	
							),
			'tabsql'=>array(
							"SELECT rowid,valor,active FROM ".MAIN_DB_PREFIX."c_cotinm_administracion",
							"SELECT rowid,valor,active FROM ".MAIN_DB_PREFIX."c_cotinm_gravamen",
							"SELECT rowid,valor,active FROM ".MAIN_DB_PREFIX."c_cotinm_seguros"
							),
			'tabsqlsort'=>array(
								"valor ASC",
								"valor ASC",
								"valor ASC"
								),
			'tabfield'=>array(
								'valor',
								'valor',
								'valor'
							 ),
			'tabfieldvalue'=>array(
									'valor',
									'valor',
									'valor'
								   ),
			'tabfieldinsert'=>array(
									'valor',
									'valor',
									'valor'
									), 
			'tabrowid'=>array(
								"rowid",
								"rowid",
								"rowid"
							  ),
			'tabcond'=>array(
								$conf->cotInmobiliarias->enabled,
								$conf->cotInmobiliarias->enabled,
								$conf->cotInmobiliarias->enabled
							)
		);

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		//generar los productos de las cotizaciones de inmobiliaria
		$this->generar_productos_cotinm();
		
	}

	/*
	Genera los productos iniciales que se van a vincular con las constantes
	*/

	private function generar_productos_cotinm()
	{
		global $langs, $conf, $user;
		// Create instance of object
		$product_administracion = new Product($this->db);
		
		if($product_administracion->fetch((isset(
			$conf->global->COTINM_PRODUCTO_ADMINISTRACION) ?
			$conf->global->COTINM_PRODUCTO_ADMINISTRACION :
			-1
			)))
		{
			// Definition of product instance properties
			$product_administracion->ref                = 'GRUPOSUKASA-1';
			$product_administracion->label              = 'Comisión Administración';
			$product_administracion->price              = '0';
			$product_administracion->price_base_type    = 'HT';
			$product_administracion->tva_tx             = '';
			$product_administracion->type               = Product::TYPE_PRODUCT;
			$product_administracion->status             = 1;
			$product_administracion->description        = '';
			$product_administracion->note               = '';
			$product_administracion->weight             = 0;
			$product_administracion->weight_units       = 0;
			
			// Create product in database
			$idobject_administracion = $product_administracion->create($user);
			if ($idobject_administracion > 0)
			{
				//Guardar el valor en constante
				dolibarr_set_const(
					$this->db, "COTINM_PRODUCTO_ADMINISTRACION", 
					$idobject_administracion,
					'chaine',
					0,
					"Producto para facturar la Cotización de Administración",
					$conf->entity
				);
			}
		}
		
		$product_gravamen = new Product($this->db);
		
		if($product_gravamen->fetch((isset(
			$conf->global->COTINM_PRODUCTO_GRAVAMEN) ?
			$conf->global->COTINM_PRODUCTO_GRAVAMEN :
			-1
			)))
		{
			// Definition of product instance properties
			$product_gravamen->ref                = 'GRUPOSUKASA-2';
			$product_gravamen->label              = 'Gravamen';
			$product_gravamen->price              = '0';
			$product_gravamen->price_base_type    = 'HT';
			$product_gravamen->tva_tx             = '';
			$product_gravamen->type               = Product::TYPE_PRODUCT;
			$product_gravamen->status             = 1;
			$product_gravamen->description        = '';
			$product_gravamen->note               = '';
			$product_gravamen->weight             = 0;
			$product_gravamen->weight_units       = 0;
			
			// Create product in database
			$idobject_gravamen = $product_gravamen->create($user);
			if ($idobject_gravamen > 0)
			{
				//Guardar el valor en constante
				dolibarr_set_const($this->db, 
				"COTINM_PRODUCTO_GRAVAMEN", 
				$idobject_gravamen,
				'chaine',
				0,
				"Producto para facturar la Cotización del Gravamen",
				$conf->entity
			);
			}
		}
		
		$product_costo_transaccion = new Product($this->db);
		
		if($product_costo_transaccion->fetch((isset(
			$conf->global->COTINM_PRODUCTO_COSTO_TRANSACCION) ?
			$conf->global->COTINM_PRODUCTO_COSTO_TRANSACCION :
			-1
			)))
		{
			// Definition of product instance properties
			$product_costo_transaccion->ref                = 'GRUPOSUKASA-3';
			$product_costo_transaccion->label              = 'Costo Transacción';
			$product_costo_transaccion->price              = '0';
			$product_costo_transaccion->price_base_type    = 'HT';
			$product_costo_transaccion->tva_tx             = '';
			$product_costo_transaccion->type               = Product::TYPE_PRODUCT;
			$product_costo_transaccion->status             = 1;
			$product_costo_transaccion->description        = '';
			$product_costo_transaccion->note               = '';
			$product_costo_transaccion->weight             = 0;
			$product_costo_transaccion->weight_units       = 0;
			
			// Create product in database
			$idobject_costo_transaccion = $product_costo_transaccion->create($user);
			if ($idobject_costo_transaccion > 0)
			{
				//Guardar el valor en constante
				dolibarr_set_const(
					$this->db, 
					"COTINM_PRODUCTO_COSTO_TRANSACCION", 
					$idobject_costo_transaccion,
					'chaine',
					0,
					"Producto para facturar la Cotización del Costo de Transacción",
					$conf->entity
				);
			}
		}

		$product_seguros = new Product($this->db);
		
		if($product_seguros->fetch((isset(
			$conf->global->COTINM_PRODUCTO_SEGUROS) ?
			$conf->global->COTINM_PRODUCTO_SEGUROS :
			-1
			)))
		{
			// Definition of product instance properties
			$product_seguros->ref                = 'GRUPOSUKASA-4';
			$product_seguros->label              = 'Seguros';
			$product_seguros->price              = '0';
			$product_seguros->price_base_type    = 'HT';
			$product_seguros->tva_tx             = '';
			$product_seguros->type               = Product::TYPE_PRODUCT;
			$product_seguros->status             = 1;
			$product_seguros->description        = '';
			$product_seguros->note               = '';
			$product_seguros->weight             = 0;
			$product_seguros->weight_units       = 0;
			
			// Create product in database
			$idobject_seguros = $product_seguros->create($user);
			if ($idobject_seguros > 0)
			{
				//Guardar el valor en constante
				dolibarr_set_const(
					$this->db, 
					"COTINM_PRODUCTO_SEGUROS", 
					$idobject_seguros,
					'chaine',
					0,
					"Producto para facturar la Cotización de los Seguros",
					$conf->entity
				);
			}
		}
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
		return $this->_load_tables('/cotInmobiliarias/sql/');
	}
}
?>