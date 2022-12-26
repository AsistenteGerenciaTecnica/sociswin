<?php
/**
 *      \file       htdocs/aiu/tab_factura.php
 *      \ingroup    facture
 *      \brief      Configuracion de las cotizaciones para la factura
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';		// Clase facturas a proveedores
require_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';					// Clase descuentos
require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';							// Librería proveedores
require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/lib/cotInmobiliarias.lib.php';	// Librería cotizaciones a inmobiliarias
require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/class/Values.class.php';			// Clase valores de los campos
require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/class/FacturaCotInm.class.php';	// Clase cotizaciones de facturas

$langs->load("companies");
$langs->load("bills");
$langs->load("cotInmobiliarias@cotInmobiliarias"); // modulo@nombre del archivo

$id=(GETPOST('id','int')?GETPOST('id','int'):GETPOST('facid','int'));  // For backward compatibility
$ref=GETPOST('ref','alpha');
$socid=GETPOST('socid','int');
$action=GETPOST('action','alpha');

// Security check
$socid=0;
if ($user->societe_id) $socid=$user->societe_id;
$result=restrictedArea($user,'facture',$id,'');

// Factura de Proveedor, se importa desde fournisseur.facture.class.php
$object = new FactureFournisseur($db); 
$object->fetch($id);

// Valores habilitados para las variables de la cotización, se importa de Values.class.php
$values = new Values($db);	

/*
 * Actions
 */

/* 
Cuando se le da al botón Guardar obtiene los valores de los campos y los envía
para actualizar los datos
*/
if($action == 'aplicar_cotinm') 
{
	if ($id > 0 || ! empty($ref))
	{	
		$objectFacturaCotInm = new FacturaCotInm($db);
		$objectFacturaCotInm->fetch($id);
		
		$objectFacturaCotInm->cotinm_administracion_tipo = GETPOST('cotinm_tipo_administracion','alpha');
		$objectFacturaCotInm->cotinm_administracion_valor = $objectFacturaCotInm->cotinm_administracion_tipo == '$'?GETPOST('cotinm_valor_administracion_m'):GETPOST('cotinm_valor_administracion_p');
		$objectFacturaCotInm->cotinm_administracion_iva = GETPOST('iva_valor_administracion');
		
		$objectFacturaCotInm->cotinm_seguros_tipo = GETPOST('cotinm_tipo_seguros','alpha');
		$objectFacturaCotInm->cotinm_seguros_valor = $objectFacturaCotInm->cotinm_seguros_tipo == '$'?GETPOST('cotinm_valor_seguros_m'):GETPOST('cotinm_valor_seguros_p');
		$objectFacturaCotInm->cotinm_seguros_iva = GETPOST('iva_valor_seguros');

		$objectFacturaCotInm->cotinm_gravamen_tipo = GETPOST('cotinm_tipo_gravamen','alpha');
		$objectFacturaCotInm->cotinm_gravamen_valor = $objectFacturaCotInm->cotinm_gravamen_tipo == '$'?GETPOST('cotinm_valor_gravamen_m'):GETPOST('cotinm_valor_gravamen_p');
		
		$objectFacturaCotInm->cotinm_costo_transaccion_tipo = GETPOST('cotinm_tipo_costo_transaccion','alpha');
		$objectFacturaCotInm->cotinm_costo_transaccion_valor = $objectFacturaCotInm->cotinm_costo_transaccion_tipo == '$'?GETPOST('cotinm_valor_costo_transaccion_m'):GETPOST('cotinm_valor_costo_transaccion_p');

		
		$objectFacturaCotInm->update_products_cotinm();
		
	}
}

/*
 * View
 */

$title = $langs->trans('InvoiceCustomer') . " - " . $langs->trans('cotinm');
$morejs=array("cotInmobiliarias/lib/js/jquery-numeric.js");
$helpurl = "ES:AIU";
llxHeader('', $title,$helpurl,'','','',$morejs);

$form = new Form($db);

if ($id > 0 || ! empty($ref))
{	
	$object = new FactureFournisseur($db); // Crear el objeto de la factura a proveedor
	$object->fetch($id,$ref);	// Traer la información según la id o la referencia

	$object->fetch_thirdparty();	// Obtener el tercero relacionado con la factura
	
	// Clase para la información relacionada con la factura
	// FacturaCotInm.class.php
	$objectFacturaCotInm = new FacturaCotInm($db);	
	$objectFacturaCotInm->fetch($object->id);	// Trae la información relevante de la factura

    $head = facturefourn_prepare_head($object);	// Prepara la información del encabezado

	/* 
	Genera las pestañas del encabezado
	'TabFacturaCotizacion' - Nombre de la pestaña creada en la clase del módulo
	'SupplierInvoice' - Factura de Proveedor	
	*/
    dol_fiche_head($head, 'TabFacturaCotizacion', $langs->trans('SupplierInvoice'), -1, 'bill');

    // Invoice content
	// Dirección dentro de la carpeta de dolibarr a la que va a regresar desde el botón "Volver al Listado"
    $linkback = '<a href="' . DOL_URL_ROOT . '/fourn/facture/list.php?restore_lastsearch_values=1' . (! empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

    $morehtmlref='<div class="refidno">';
    // Ref Supplier
    $morehtmlref.=$form->editfieldkey("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', 0, 1);
    $morehtmlref.=$form->editfieldval("RefSupplier", 'ref_supplier', $object->ref_supplier, $object, 0, 'string', '', null, null, '', 1);
    // Thirdparty
    $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);
    
    $morehtmlref.='</div>';

    dol_banner_tab($object, 'ref', $linkback, 1, 'facnumber', 'ref', $morehtmlref, '', 0);

			print '<div class="fichecenter fichecenterbis">';		

				print '<div class="div-table-responsive">';
				
				print '<form method="post">';
				print '<input type="hidden" name="action" value="aplicar_cotinm"/>';
					print '<table class="tagtable liste" width="100%">';
						print '<tr class="liste_titre">';
							print '<th class="liste_titre">'.$langs->trans('cotinm_factura_encabezado').'</th>';							
						print '</tr>';
						print '<tr>';
							print '<td class="oddeven">';

										/*
										Para las tarjetas de Administración, Seguros y Gravamen, 
										muestra una lista desplegable con los diferentes
										porcentajes que se pueden aplicar según la lista de valores que se 
										generó antes
										*/

							
										// Comisión Administración
										print '<fieldset class="cotinm_box administracion">';
											print '<legend>'.$langs->trans('cotinm_administracion').'</legend>';										
											print '<fieldset>';
											print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
											//print '<label for="cotinm_tipo_a_m"> $ </label>';
											//print '<input class="radio" data-target="a_valor_m" data-target-off="a_valor_p" type="radio" id="cotinm_tipo_a_m" name="cotinm_tipo_administracion" value="$" '.($objectFacturaCotInm->cotinm_administracion_tipo == '$'?'checked="checked"':'').'/> ';
											print '<label  for="cotinm_tipo_a_p"> % </label>';
											print '<input class="radio" data-target1="a_valor_p" data-target-off1="a_valor_m" data-target2="iva_valor_administracion" type="radio" id="cotinm_tipo_a_p" name="cotinm_tipo_administracion" value="%" '.($objectFacturaCotInm->cotinm_administracion_tipo == '%'?'checked="checked"':'').'/>';
											print '</fieldset>';
											print '<hr/>';
											print '<label>'.$langs->trans('Amount').'</label> ';
											print '<input type="text" id="a_valor_m" name="cotinm_valor_administracion_m" class="numeric" value="'.price2num($objectFacturaCotInm->cotinm_administracion_valor).'" '.($objectFacturaCotInm->cotinm_administracion_tipo == '$'?'':'style="display:none;"').'/>';

											print '<select id="a_valor_p" name="cotinm_valor_administracion_p" '.($objectFacturaCotInm->cotinm_administracion_tipo == '%'?'':'style="display:none;"').'>';
												$values_administracion = $values->getlist('a');
												foreach($values_administracion as $item)
												{
													print '<option '.($objectFacturaCotInm->cotinm_administracion_valor == $item->valor?'selected="selected"':'').' value="'.$item->valor.'">'.$item->valor.'</option>';
												}
											print '</select>';
											
											print '</br><label>IVA </label>';
											print '<select type="text" id="iva_valor_administracion" name="iva_valor_administracion" '.($objectFacturaCotInm->cotinm_administracion_tipo == '%'?'':'style="display:none;"').'>';

												print '<option '.($objectFacturaCotInm->cotinm_administracion_iva == "0"?'selected="selected"':'').' value="0">0</option>';	
												print '<option '.($objectFacturaCotInm->cotinm_administracion_iva == "19"?'selected="selected"':'').' value="19">19</option>';

											print '</select>';
											
										print '</fieldset>';
										
										// Seguros
										print '<fieldset class="cotinm_box seguros">';
											print '<legend>'.$langs->trans('cotinm_seguros').'</legend>';										
											print '<fieldset>';
											print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
											//print '<label for="aiu_tipo_s_m"> $ </label>';
											//print '<input class="radio" data-target="s_valor_m" data-target-off="s_valor_p" type="radio" id="aiu_tipo_s_m" name="cotinm_tipo_seguros" value="$" '.($objectFacturaCotInm->cotinm_gravamen_tipo == '$'?'checked="checked"':'').'/> ';
											print '<label  for="cotinm_tipo_s_p"> % </label>';
											print '<input class="radio" data-target1="s_valor_p" data-target-off1="s_valor_m" data-target2="iva_valor_seguros" type="radio" id="cotinm_tipo_s_p" name="cotinm_tipo_seguros" value="%" '.($objectFacturaCotInm->cotinm_seguros_tipo == '%'?'checked="checked"':'').'/>';
											print '</fieldset>';
											print '<hr/>';
											print '<label>'.$langs->trans('Amount').'</label> ';
											print '<input type="text" id="s_valor_m" name="cotinm_valor_seguros_m" class="numeric" value="'.price2num($objectFacturaCotInm->cotinm_seguros_valor).'" '.($objectFacturaCotInm->cotinm_seguros_tipo == '$'?'':'style="display:none;"').'/>';
											print '<select id="s_valor_p" name="cotinm_valor_seguros_p" '.($objectFacturaCotInm->cotinm_seguros_tipo == '%'?'':'style="display:none;"').'>';
												$values_administracion = $values->getlist('s');
												foreach($values_administracion as $item)
												{
													print '<option '.($objectFacturaCotInm->cotinm_seguros_valor == $item->valor?'selected="selected"':'').' value="'.$item->valor.'">'.$item->valor.'</option>';
												}
											print '</select>';
											
											print '</br><label>IVA </label>';
											print '<select type="text" id="iva_valor_seguros" name="iva_valor_seguros" '.($objectFacturaCotInm->cotinm_seguros_tipo == '%'?'':'style="display:none;"').'>';

												print '<option '.($objectFacturaCotInm->cotinm_seguros_iva == "0"?'selected="selected"':'').' value="0">0</option>';
												print '<option '.($objectFacturaCotInm->cotinm_seguros_iva == "19"?'selected="selected"':'').' value="19">19</option>';

											print '</select>';
											
										print '</fieldset>';	
											
										// Gravamen
										print '<fieldset class="cotinm_box gravamen">';
											print '<legend>'.$langs->trans('cotinm_gravamen').'</legend>';										
											print '<fieldset>';
											print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
											//print '<label for="cotinm_tipo_g_m"> $ </label>';
											//print '<input class="radio" data-target="g_valor_m" data-target-off="g_valor_p" type="radio" id="cotinm_tipo_g_m" name="cotinm_tipo_gravamen" value="$" '.($objectFacturaCotInm->cotinm_gravamen_tipo == '$'?'checked="checked"':'').'/> ';
											print '<label  for="cotinm_tipo_g_p"> % </label>';
											print '<input class="radio" data-target1="g_valor_p" data-target-off1="g_valor_m" type="radio" id="cotinm_tipo_g_p" name="cotinm_tipo_gravamen" value="%" '.($objectFacturaCotInm->cotinm_gravamen_tipo == '%'?'checked="checked"':'').'/>';
											print '</fieldset>';
											print '<hr/>';
											print '<label>'.$langs->trans('Amount').'</label> ';
											print '<input type="text" id="g_valor_m" name="cotinm_valor_gravamen_m" class="numeric" value="'.price2num($objectFacturaCotInm->cotinm_gravamen_valor).'" '.($objectFacturaCotInm->cotinm_gravamen_tipo == '$'?'':'style="display:none;"').'/>';
											print '<select id="g_valor_p" name="cotinm_valor_gravamen_p" '.($objectFacturaCotInm->cotinm_gravamen_tipo == '%'?'':'style="display:none;"').'>';
												$values_administracion = $values->getlist('g');
												foreach($values_administracion as $item)
												{
													print '<option '.($objectFacturaCotInm->cotinm_gravamen_valor == $item->valor?'selected="selected"':'').' value="'.$item->valor.'">'.$item->valor.'</option>';
												}
											print '</select>';
										print '</fieldset>';

										// Costo Transacción
										print '<fieldset class="cotinm_box costo_transaccion">';
											print '<legend>'.$langs->trans('cotinm_costo_transaccion').'</legend>';										
											print '<fieldset>';
											print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
											print '<label for="cotinm_tipo_costo_transaccion_m"> $ </label>';
											print '<input class="radio" data-target1="costo_transaccion_valor_m" data-target-off1="costo_transaccion_valor_p" type="radio" id="cotinm_tipo_costo_transaccion_m" name="cotinm_tipo_costo_transaccion" value="$" '.($objectFacturaCotInm->cotinm_costo_transaccion_tipo == '$'?'checked="checked"':'').'/> ';
											//print '<label  for="cotinm_tipo_costo_transaccion"> % </label>';
											//print '<input class="radio" data-target="costo_transaccion_valor_p" data-target-off="costo_transaccion_valor_m" type="radio" id="cotinm_tipo_costo_transaccion" name="cotinm_tipo_costo_transaccion" value="%" '.($objectFacturaCotInm->cotinm_costo_transaccion_tipo == '%'?'checked="checked"':'').'/>';
											print '</fieldset>';
											print '<hr/>';
											print '<label>'.$langs->trans('Amount').'</label> ';
											print '<input type="text" id="costo_transaccion_valor_m" name="cotinm_valor_costo_transaccion_m" class="numeric" value="'.(price2num($objectFacturaCotInm->cotinm_costo_transaccion_valor) > 0?price2num($objectFacturaCotInm->cotinm_costo_transaccion_valor):"0").'" '.($objectFacturaCotInm->cotinm_costo_transaccion_tipo == '$'?'':'style="display:none;"').'/>';
											print '<select id="costo_transaccion_valor_p" name="cotinm_valor_costo_transaccion_p" '.($objectFacturaCotInm->cotinm_costo_transaccion_tipo == '%'?'':'style="display:none;"').'>';
												$values_administracion = $values->getlist('i');
												foreach($values_administracion as $item)
												{
													print '<option '.($objectFacturaCotInm->cotinm_costo_transaccion_valor == $item->valor?'selected="selected"':'').' value="'.$item->valor.'">'.$item->valor.'</option>';
												}
											print '</select>';
										print '</fieldset>'; 
							
							print '</td>';
						print '</tr>';
						print '<tr>';	
							print '<td class="oddeven">';
								// Solo muestra la opción de guardar si la factura está en borrador
								if($object->statut < 1)
								{
									print '<input type="submit" value="'.$langs->trans('Save').'"/>';
								}
							print '</td>';						
						print '</tr>';
					print '</table>';
				print '</form>';		
				print '</div>';	
			print '</div>';
	?>
    <script>
  	$( function()
  	{
		$("input.radio").checkboxradio();
		$("input.radio").click(function(){
			$('#'+$(this).attr('data-target1')).show();
			$('#'+$(this).attr('data-target-off1')).hide();
			$('#'+$(this).attr('data-target2')).show();
		});		
  	});
  </script>
    <?php
	print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/cotInmobiliarias/lib/js/aiu.js"></script>';

	dol_fiche_end();
}


llxFooter();

$db->close();
