	<?php

	/**
	 *      \file       htdocs/aiu/tab_presupuesto.php
	 *      \ingroup    comm
	 *      \brief      Configuracion de los valores de la cotización para el presupuesto
	 */

	require '../../main.inc.php';
	// Clase ordenes a proveedores
	require_once DOL_DOCUMENT_ROOT .'/fourn/class/fournisseur.commande.class.php';			
	// Librería funciones de los proveedores
	require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';								
	// Librería de cotizaciones de inmobiliarias
	require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/lib/cotInmobiliarias.lib.php';		
	// Clase de valores de los campos
	require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/class/Values.class.php';				
	// Clase de las cotizaciones para presupuestos
	require_once DOL_DOCUMENT_ROOT.'/custom/cotInmobiliarias/class/PresupuestoCotInm.class.php';	

	$langs->load("companies");
	$langs->load("bills");
	$langs->load("cotInmobiliarias@cotInmobiliarias");

	$id=GETPOST('id','int');  // For backward compatibility
	$ref=GETPOST('ref','alpha');
	$socid=GETPOST('socid','int');
	$action=GETPOST('action','alpha');


	// Security check
	$socid=0;
	if ($user->societe_id) $socid=$user->societe_id;
	$result=restrictedArea($user,'facture',$id,'');

	// Orden a Proveedor, se importa desde fournisseur.commande.class.php
	$object = new CommandeFournisseur($db);
	$object->fetch($id);

	for ($i = 0; $i < count($object->lines); $i++){
		print("<div>");
		print_r($object->lines[$i]->array_options);
		print("</div>");
	}

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
			$objectPresupuestoCotInm = new PresupuestoCotInm($db);
			$objectPresupuestoCotInm->fetch($id);
			
			$objectPresupuestoCotInm->cotinm_administracion_tipo = GETPOST('cotinm_tipo_administracion','alpha');
			$objectPresupuestoCotInm->cotinm_administracion_valor = $objectPresupuestoCotInm->cotinm_administracion_tipo == '$' ?
																	GETPOST('cotinm_valor_administracion_m') :
																	GETPOST('cotinm_valor_administracion_p');
			$objectPresupuestoCotInm->cotinm_administracion_iva = GETPOST('iva_valor_administracion');
			
			$objectPresupuestoCotInm->cotinm_seguros_tipo = GETPOST('cotinm_tipo_seguros','alpha');
			$objectPresupuestoCotInm->cotinm_seguros_valor = $objectPresupuestoCotInm->cotinm_seguros_tipo == '$' ?
															GETPOST('cotinm_valor_seguros_m') :
															GETPOST('cotinm_valor_seguros_p');
			$objectPresupuestoCotInm->cotinm_seguros_iva = GETPOST('iva_valor_seguros');

			$objectPresupuestoCotInm->cotinm_gravamen_tipo = GETPOST('cotinm_tipo_gravamen','alpha');
			$objectPresupuestoCotInm->cotinm_gravamen_valor = $objectPresupuestoCotInm->cotinm_gravamen_tipo == '$' ?
																GETPOST('cotinm_valor_gravamen_m') :
																GETPOST('cotinm_valor_gravamen_p');
			
			$objectPresupuestoCotInm->cotinm_costo_transaccion_tipo = GETPOST('cotinm_tipo_costo_transaccion','alpha');
			$objectPresupuestoCotInm->cotinm_costo_transaccion_valor = $objectPresupuestoCotInm->cotinm_costo_transaccion_tipo == '$' ?
																	GETPOST('cotinm_valor_costo_transaccion_m') :
																	GETPOST('cotinm_valor_costo_transaccion_p');

			$objectPresupuestoCotInm->update_products_cotinm();
			
		}
	}


	/*
	* View
	*/

	$title = $langs->trans('Proposal') . " - " . $langs->trans('AIU');
	$morejs=array("cotInmobiliarias/lib/js/jquery-numeric.js");
	$helpurl = "ES:AIU";

	// Header del sitio
	llxHeader('', $title,$helpurl,'','','',$morejs);

	$form = new Form($db);

	if ($id > 0 || ! empty($ref))
	{	
		$object = new CommandeFournisseur($db);	// Crear el objeto de la orden a proveedor
		$object->fetch($id,$ref);	// Traer la información según la ID o la referencia
		
		$object->fetch_thirdparty();	// Obtener el tercero relacionado con la orden
		
		// Clase para la información relacionada con la orden
		// PresupuestoCotInm.class.php
		$objectPresupuestoCotInm = new PresupuestoCotInm($db);
		$objectPresupuestoCotInm->fetch($object->id);
		
		$head = ordersupplier_prepare_head($object); // Prepara la información del encabezado
		
		/* 
		Genera las pestañas del encabezado
		'TabPresupuestoCotizacion' - Nombre de la pestaña creada en la clase del módulo
		'Proposal' - Propuesta
		*/	
		dol_fiche_head($head, 'TabPresupuestoCotizacion', $langs->trans('Proposal'), -1, 'bill');

		// Invoice content
		
		// Dirección dentro de la carpeta de dolibarr a la que va a regresar desde el botón "Volver al Listado"
		$linkback = '<a 
			href="' . 
				DOL_URL_ROOT . 
				'/fourn/commande/list.php?restore_lastsearch_values=1' . 
				(! empty($socid) ? 
				'&socid=' . $socid : 
				'') . 
				'">' . 
				$langs->trans("BackToList") . 
			'</a>';


		$morehtmlref='<div class="refidno">';
		// Ref Supplier
		$morehtmlref.=$form->editfieldkey(
			"RefSupplier", 
			'ref_supplier', 
			$object->ref_supplier, 
			$object, 
			0, 
			'string', 
			'', 
			0, 
			1
		);
		$morehtmlref.=$form->editfieldval(
			"RefSupplier", 
			'ref_supplier', 
			$object->ref_supplier, 
			$object, 
			0, 
			'string', 
			'', 
			null, 
			null, 
			'', 
			1
		);
		// Thirdparty
		$morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . $object->thirdparty->getNomUrl(1);
		
		$morehtmlref.='</div>';

		dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);
		
				print '<div class="fichecenter fichecenterbis">';		
					print '<div class="div-table-responsive">';
					print '<form method="post">';
					print '<input type="hidden" name="action" value="aplicar_cotinm"/>';
						print '<table class="tagtable liste" width="100%">';
							print '<tr class="liste_titre">';
								print '<th class="liste_titre">'.$langs->trans('cotinm_presupuesto_encabezado').'</th>';							
							print '</tr>';
							print '<tr>';
								print '<td class="oddeven">';

											/*
											Para las tarjetas de Administración, Seguros y Gravamen, 
											muestra una lista desplegable con los diferentes
											porcentajes que se pueden aplicar según la lista de valores que se 
											generó antes
											*/

											/*
											Para las selecciones de IVA, verifica el valor actualmente asignado
											a la orden, y lo muestra en caso de haberlo
											*/										

											// Comisión Administración
											print '<fieldset class="cotinm_box administracion">';
												print '<legend>'.$langs->trans('cotinm_administracion').'</legend>';
												print '<fieldset>';
												print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
												//print '<label for="cotinm_tipo_a_m"> $ </label>';
												/*print '<input 
													class="radio" 
													data-target="a_valor_m" 
													data-target-off="a_valor_p" 
													type="radio" id="cotinm_tipo_a_m" 
													name="cotinm_tipo_administracion" 
													value="$" '.($objectPresupuestoCotInm->cotinm_administracion_tipo == '$'?'
													checked="checked"':'').
													'/> ';*/
												print '<label  for="cotinm_tipo_a_p"> % </label>';

												print '<input 
													class="radio" 
													data-target1="a_valor_p" 
													data-target-off1="a_valor_m" 
													data-target2="iva_valor_administracion" 
													type="radio" 
													id="cotinm_tipo_a_p" 
													name="cotinm_tipo_administracion" 
													value="%" '.
													($objectPresupuestoCotInm->cotinm_administracion_tipo == '%' ?
													'checked="checked"' :
													'').
													'/>';

												print '</fieldset>';
												print '<hr/>';
												print '<label>'.$langs->trans('Amount').'</label> ';
												print '<input 
													type="text" 
													id="a_valor_m" 
													name="cotinm_valor_administracion_m" 
													class="numeric" 
													value="'.price2num($objectPresupuestoCotInm->cotinm_administracion_valor).'" '.
													($objectPresupuestoCotInm->cotinm_administracion_tipo == '$' ?
													'' :
													'style="display:none;"').
													'/>';
												print '<select 
													id="a_valor_p" 
													name="cotinm_valor_administracion_p" '.
													($objectPresupuestoCotInm->cotinm_administracion_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';
													$values_administracion = $values->getlist('a');
													foreach($values_administracion as $item)
													{
														print '<option '.
															($objectPresupuestoCotInm->cotinm_administracion_valor == $item->valor ?
															'selected="selected"' :
															'').
															' value="'.$item->valor.'">'.
															$item->valor.
															'</option>';
													}
												print '</select>';
												
												print '</br><label>IVA </label>';
												print '<select 
													type="text" 
													id="iva_valor_administracion" 
													name="iva_valor_administracion" '.
													($objectPresupuestoCotInm->cotinm_administracion_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';

													print '<option '.
														($objectPresupuestoCotInm->cotinm_administracion_iva == "0" ?
														'selected="selected"' :
														'').
														' value="0">0</option>';	
													print '<option '.
														($objectPresupuestoCotInm->cotinm_administracion_iva == "19" ?
														'selected="selected"' :
														'').
														' value="19">19</option>';

												print '</select>';
											print '</fieldset>';
											
											// Seguros
											print '<fieldset class="cotinm_box seguros">';
												print '<legend>'.$langs->trans('cotinm_seguros').'</legend>';										
												print '<fieldset>';
												print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';

												print '<label  for="cotinm_tipo_s_p"> % </label>';
												print '<input 
													class="radio" 
													data-target1="s_valor_p" 
													data-target-off1="s_valor_m" 
													data-target2="iva_valor_seguros" 
													type="radio" 
													id="cotinm_tipo_s_p" 
													name="cotinm_tipo_seguros" 
													value="%" '.
													($objectPresupuestoCotInm->cotinm_seguros_tipo == '%' ?
													'checked="checked"' :
													'').
													'/>';
												print '</fieldset>';
												print '<hr/>';

												print '<label>'.$langs->trans('Amount').'</label> ';
												print '<input 
													type="text" 
													id="s_valor_m" 
													name="cotinm_valor_seguros_m" 
													class="numeric" 
													value="'.price2num($objectPresupuestoCotInm->cotinm_seguros_valor).'" '.
													($objectPresupuestoCotInm->cotinm_seguros_tipo == '$' ?
													'' :
													'style="display:none;"').
													'/>';
												print '<select 
													id="s_valor_p" 
													name="cotinm_valor_seguros_p" '.
													($objectPresupuestoCotInm->cotinm_seguros_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';
													$values_administracion = $values->getlist('s');
													foreach($values_administracion as $item)
													{
														print '<option '.
															($objectPresupuestoCotInm->cotinm_seguros_valor == $item->valor ?
															'selected="selected"' :
															'').
															' value="'.$item->valor.'">'.
															$item->valor.
															'</option>';
													}
												print '</select>';

												print '</br><label>IVA </label>';
												print '<select 
													type="text" 
													id="iva_valor_seguros" 
													name="iva_valor_seguros" '.
													($objectPresupuestoCotInm->cotinm_seguros_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';

													print '<option '.
														($objectPresupuestoCotInm->cotinm_seguros_iva == "0" ?
														'selected="selected"' :
														'').' value="0">0</option>';
													print '<option '.
														($objectPresupuestoCotInm->cotinm_seguros_iva == "19" ?
														'selected="selected"': 
														'').' value="19">19</option>';
												print '</select>';											
											print '</fieldset>';
												
											// Gravamen
											print '<fieldset class="cotinm_box gravamen">';
												print '<legend>'.$langs->trans('cotinm_gravamen').'</legend>';										
												print '<fieldset>';
												print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';

												print '<label  for="cotinm_tipo_g_p"> % </label>';
												print '<input 
													class="radio" 
													data-target1="g_valor_p" 
													data-target-off1="g_valor_m" 
													type="radio" 
													id="cotinm_tipo_g_p" 
													name="cotinm_tipo_gravamen" 
													value="%" '.
													($objectPresupuestoCotInm->cotinm_gravamen_tipo == '%' ?
													'checked="checked"' :
													'').
													'/>';

												print '</fieldset>';
												print '<hr/>';
												print '<label>'.$langs->trans('Amount').'</label> ';
												print '<input 
													type="text" 
													id="g_valor_m" 
													name="cotinm_valor_gravamen_m" 
													class="numeric" 
													value="'.
													price2num($objectPresupuestoCotInm->cotinm_gravamen_valor).'" '.
													($objectPresupuestoCotInm->cotinm_gravamen_tipo == '$' ?
													'' :
													'style="display:none;"').
													'/>';
												print '<select 
													id="g_valor_p" 
													name="cotinm_valor_gravamen_p" '.
													($objectPresupuestoCotInm->cotinm_gravamen_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';
													$values_administracion = $values->getlist('g');
													foreach($values_administracion as $item)
													{
														print '<option '.
															($objectPresupuestoCotInm->cotinm_gravamen_valor == $item->valor ?
															'selected="selected"' :
															'').
															' value="'.$item->valor.'">'.
															$item->valor.
															'</option>';
													}
												print '</select>';
											print '</fieldset>';

											// Costo Transacción
											print '<fieldset class="cotinm_box costo_transaccion">';
												print '<legend>'.$langs->trans('cotinm_costo_transaccion').'</legend>';										
												print '<fieldset>';
												print '<legend>'.$langs->trans('cotinm_forma_aplicar').'</legend>';
												print '<label for="cotinm_tipo_costo_transaccion_m"> $ </label>';
												print '<input 
													class="radio" 
													data-target1="costo_transaccion_valor_m" 
													data-target-off1="costo_transaccion_valor_p" 
													type="radio" 
													id="cotinm_tipo_costo_transaccion_m" 
													name="cotinm_tipo_costo_transaccion" 
													value="$" '.
													($objectPresupuestoCotInm->cotinm_costo_transaccion_tipo == '$' ?
													'checked="checked"':
													'').
													'/> ';
												print '</fieldset>';
												print '<hr/>';
												print '<label>'.$langs->trans('Amount').'</label> ';
												print '<input 
													type="text" 
													id="costo_transaccion_valor_m" 
													name="cotinm_valor_costo_transaccion_m" 
													class="numeric" 
													value="'.
													(price2num($objectPresupuestoCotInm->cotinm_costo_transaccion_valor) > 0 ?
													price2num($objectPresupuestoCotInm->cotinm_costo_transaccion_valor) :
													"0").'" '.
													($objectPresupuestoCotInm->cotinm_costo_transaccion_tipo == '$' ?
													'' :
													'style="display:none;"').
													'/>';
												print '<select 
													id="costo_transaccion_valor_p" 
													name="cotinm_valor_costo_transaccion_p" '.
													($objectPresupuestoCotInm->cotinm_costo_transaccion_tipo == '%' ?
													'' :
													'style="display:none;"').
													'>';
													$values_administracion = $values->getlist('i');
													foreach($values_administracion as $item)
													{
														print '<option '.
															($objectPresupuestoCotInm->cotinm_costo_transaccion_valor == $item->valor ?
															'selected="selected"' :
															'').
															' value="'.$item->valor.'">'.
															$item->valor.
															'</option>';
													}
												print '</select>';
											print '</fieldset>';
								
								print '</td>';
							print '</tr>';
							print '<tr>';	
								print '<td class="oddeven">';
									// Solo muestra la opción de guardar si la orden está en borrador
									if($object->statut <1)
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
		// Controla la activación de los input para los valores
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
		print '<script type="text/javascript" src="'.DOL_URL_ROOT.'/cotInmobiliarias/lib/js/cotinm.js"></script>';

		dol_fiche_end();
	}

	llxFooter();

	$db->close();