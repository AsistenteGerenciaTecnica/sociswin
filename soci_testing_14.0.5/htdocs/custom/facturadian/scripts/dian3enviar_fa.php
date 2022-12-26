<?php
define("USERAPP", $argv[1]);
define("PASSWORDAPP", $argv[2]);
define("LIMITE", $argv[3]);
define("DOCUMENT", $argv[4]);
define("CONFIG_PATH", $argv[5]);
require_once "config/myDBC3.php";
require_once "config/dbconfig3.php";

$dolibarr = new Dolibarr(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

// DIGITO DE VERIFICACION
function digitover ($nit) {
	$nit = trim($nit) ;
	$nit = '000000000000000'.$nit ;
	$largo = strlen($nit) - 1 ;
	$dv = ( substr($nit, $largo - 0, 1) * 3 +
	substr($nit, $largo - 1, 1) * 7 +
	substr($nit, $largo - 2, 1) * 13 +
	substr($nit, $largo - 3, 1) * 17 +
	substr($nit, $largo - 4, 1) * 19 +
	substr($nit, $largo - 5, 1) * 23 +
	substr($nit, $largo - 6, 1) * 29 +
	substr($nit, $largo - 7, 1) * 37 +
	substr($nit, $largo - 8, 1) * 41 +
	substr($nit, $largo - 9, 1) * 43 +
	substr($nit, $largo - 10, 1) * 47 +
	substr($nit, $largo - 11, 1) * 53 +
	substr($nit, $largo - 12, 1) * 59 +
	substr($nit, $largo - 13, 1) * 67 +
	substr($nit, $largo - 14, 1) * 71 ) % 11 ;
	if ($dv > 1) $dv = 11 - $dv ;
	return $dv;
}

//FACTURAS A ENVIAR
$cadena = "
SELECT fac.rowid AS rowid,
fac.type AS type,
fac.ref AS facnumber,
fac.entity AS entity,
fac.datef AS datef,
fac.date_lim_reglement AS date_lim_reglement,
fac.datec AS datec,
fac.fk_soc AS fk_soc,
RIGHT(extra.invoicetypecode,1) AS invoicetypecode,
CASE
    WHEN payment.code = '1'           THEN '1'
    WHEN payment.code = 'RECEP'       THEN '1'
    WHEN payment.code = 'alarep'      THEN '1'
    WHEN payment.code = 'PT_DELIVERY' THEN '1'
    WHEN payment.code = '2'            THEN '2'
    WHEN payment.code = '30D'          THEN '2'
    WHEN payment.code = '60D'          THEN '2'
    WHEN payment.code = '10D'          THEN '2'
    WHEN payment.code = '14D'          THEN '2'
    WHEN payment.code = '45 DIAS'      THEN '2'
    WHEN payment.code = '90 DIAS'      THEN '2'
    WHEN payment.code = '30DENDMONTH'  THEN '2'
    WHEN payment.code = '60DENDMONTH'  THEN '2'
    WHEN payment.code = '14DENDMONTH'  THEN '2'
    WHEN payment.code = '10DENDMONTH'  THEN '2'
    WHEN payment.code = 'PT_5050'      THEN '2'
    ELSE ''
END as PaymentMeansID,
CASE
    WHEN paiement.code = 'LIQ' THEN '10'
    WHEN paiement.code = '10'  THEN '10'
    WHEN paiement.code = 'CHQ' THEN '20'
    WHEN paiement.code = '20'  THEN '20'
    WHEN paiement.code = 'PRE' THEN '42'
    WHEN paiement.code = '42'  THEN '42'
    WHEN paiement.code = 'VIR' THEN '45'
    WHEN paiement.code = '45'  THEN '45'
    WHEN paiement.code = 'VAD' THEN '48'
    WHEN paiement.code = '48'  THEN '48'
    WHEN paiement.code = '49'  THEN '49'
    ELSE ''
END as PaymentMeansCode,
fac.date_lim_reglement AS PaymentDueDate,
'0000' AS PaymentID,
soc.address AS ReceptorLine,
soc.email AS email,
depa.departamento AS ReceptorAddressCountrySubentity,
depa.code_departement AS ReceptorAddressCountrySubentityCode,
depa.cod_municipio_dian AS ReceptorAddressId,
depa.municipio AS ReceptorAddressCityName,
SUBSTRING_INDEX(soc.name_alias,' ', 1) AS FirstName,
SUBSTRING_INDEX(soc.name_alias,' ', -1) AS FamilyName,
CASE
    WHEN soc.tva_assuj = 1 THEN '48'
    WHEN soc.tva_assuj = 0 THEN '49'
END AS TaxLevelCodelistName,
REPLACE(UCASE(soc.nom),'&','Y') AS ContactName,
soc.phone AS ContactTelephone, 
soc.email AS ContactElectronicMail, 
TRUNCATE(fac.total, 2)  AS LineExtensionAmount, 
TRUNCATE(fac.tva, 2) AS tva, 
TRUNCATE(fac.total_ttc, 2) AS PayableAmount, 
TRUNCATE(fac.tva, 2) AS TaxAmount1, 
TRUNCATE(fac.total_ttc, 2) AS TaxInclusiveAmount, 
'0.00' AS AllowanceTotalAmount, 
'0.00' AS ChargeTotalAmount, 
REPLACE(UCASE(soc.nom),'&','Y') AS ReceptorName,
TipoId.code AS ReceptorTipoId, 
soc.siren AS ReceptorId,
RIGHT(soc.fk_forme_juridique,1) AS AdditionalAccountID
FROM ".PREFIJO."facture AS fac
LEFT JOIN ".PREFIJO."societe AS soc
ON fac.fk_soc = soc.rowid
LEFT JOIN ".PREFIJO."c_effectif AS TipoId
ON soc.fk_effectif = TipoId.id
LEFT JOIN ".PREFIJO."c_departements AS depa
ON soc.fk_departement = depa.rowid
LEFT JOIN ".PREFIJO."facture_extrafields AS extra
ON fac.rowid = extra.fk_object
LEFT JOIN ".PREFIJO."c_paiement AS paiement
ON fac.fk_mode_reglement = paiement.id
LEFT JOIN ".PREFIJO."c_payment_term AS payment
ON fac.fk_cond_reglement = payment.rowid
WHERE (NOT extra.envio OR extra.envio is NULL) AND fac.type = '0' AND LEFT(fac.ref,1) <> '(' AND (extra.isvalid <> 'true' OR extra.isvalid is NULL) AND fac.ref LIKE '".DOCUMENT."%' ORDER BY fac.ref
 LIMIT ".LIMITE;

$nohayfacturas = true;
$primeravez = true;
$result = $dolibarr->leerdatosarray($cadena);
while($row = $result->fetch_array(MYSQLI_ASSOC)) {
	
	if($primeravez) {
		echo "************ OBTENIENDO TOKEN *************** \n\n <br /><br />";
		$token = shell_exec("curl --request POST -d '{ \"username\":\"".USERAPP."\", \"password\":\"".PASSWORDAPP."\"}' ".URLTOKEN);
		echo "\n\n";
		$primeravez = false;
	}
	
	$nohayfacturas = false;
	//VARIABLES CALCULADAS
	$IssueTime = substr($row['datec'], 11, 18);
	$InvoiceTypeCode = $row['invoicetypecode'];
	$prefijonumero = explode("-",$row['facnumber']);
	$prefijo = $prefijonumero[0];
	$numero = $prefijonumero[1];
	$numero = (int) $numero; 
	
	$MiddleName="";
	$DocumentTypeCode = "FA";
	$IDInvoiceDocumentReference = "";
	$IDInvoiceDocumentReferenceCufe = "";
	$IDInvoiceDocumentReferenceIssueDate = "";

	// HALLA EL TaxExclusiveAmount
	$cadenaTaxExclusiveAmount = "
		SELECT TRUNCATE(SUM(total_ht), 2) AS TaxExclusiveAmount
		FROM   ".PREFIJO."facturedet
		WHERE tva_tx > 0 AND fk_facture = ".$row['rowid'];
	$resultTaxExclusiveAmount = $dolibarr->leerdatos($cadenaTaxExclusiveAmount);
	if($resultTaxExclusiveAmount['TaxExclusiveAmount'] == NULL) {
		$TaxExclusiveAmount = "0.00";
		$Percent1="0.00";
	} else {
		$TaxExclusiveAmount =  $resultTaxExclusiveAmount['TaxExclusiveAmount'];
		$Percent1="19.00";
	}
	
	//Obtenemos las responsabilidades fiscales
	$cadenaRF = "
	SELECT categorie.label AS TaxLevelCode
	FROM ".PREFIJO."categorie AS categorie
	LEFT JOIN ".PREFIJO."categorie_societe AS categorie_societe
	ON categorie.rowid = categorie_societe.fk_categorie
	WHERE categorie_societe.fk_soc = '{$row['fk_soc']}'
	"; 
	$responsabilidadesfiscales = "";
	$resultRF = $dolibarr->leerdatosarray($cadenaRF);
	while($rowRF = $resultRF->fetch_array(MYSQLI_ASSOC)) {
			$responsabilidadesfiscales.= $rowRF['TaxLevelCode'].";";
	}

	//Validaciones
	$validaciones = true;
	$errores = array();
	if(!isset($row['email']) 					OR strlen(trim($row['email'])) == 0) 				{ $validaciones = false; $errores['email'] = 'Falta email destino'; }
	if(!isset($row['PaymentMeansCode'])			OR strlen(trim($row['PaymentMeansCode'])) == 0) 	{ $validaciones = false; $errores['PaymentMeansCode'] = 'Falta tipo de pago (Ej. 45=Transferencia bancaria) '; }
	if(!isset($row['PaymentMeansID'])			OR strlen(trim($row['PaymentMeansID'])) == 0) 		{ $validaciones = false; $errores['PaymentMeansID'] = 'Falta Condicion de pago (Ej. 1=contado, 2 = Credito)'; }
	if(!isset($row['AdditionalAccountID'])		OR strlen(trim($row['AdditionalAccountID'])) == 0)  { $validaciones = false; $errores['AdditionalAccountID'] = 'Falta tipo de persona (Ej. 1 = Persona Juridica /2 = Persona Natural)'; }
	if(!isset($row['ReceptorAddressId'])		OR strlen(trim($row['ReceptorAddressId'])) == 0) 	{ $validaciones = false; $errores['ReceptorAddressId'] = 'Falta codigo del municipio (Ej. 11001 = Bogota D.C.)'; }
	if(!isset($row['ReceptorAddressCityName'])		OR strlen(trim($row['ReceptorAddressCityName']))== 0 ) 						{ $validaciones = false; $errores['ReceptorAddressCityName'] = 'Falta Nombre del municipio (Ej. Bogota D.C.) '; }
	if(!isset($row['ReceptorAddressCountrySubentity']) OR strlen(trim($row['ReceptorAddressCountrySubentity'])) == 0)			{ $validaciones = false; $errores['ReceptorAddressCountrySubentity'] = 'Falta Nombre del departamento (Ej. Bogota D.C. / Medellin )'; }
	if(!isset($row['ReceptorAddressCountrySubentityCode']) OR strlen(trim($row['ReceptorAddressCountrySubentityCode'])) == 0)	{ $validaciones = false; $errores['ReceptorAddressCountrySubentityCode'] = 'Falta Codigo del departamento (Ej. 11=Bogota D.C.)'; }
	if(!isset($row['PaymentDueDate']) OR strlen(trim($row['PaymentDueDate'])) == 0)     { $validaciones = false; $errores['PaymentDueDate'] = 'Falta Fecha limite de pago (Ej. 2020-01-01)'; }
	if(!isset($row['ReceptorTipoId']) OR strlen(trim($row['ReceptorTipoId'])) == 0) 	{ $validaciones = false; $errores['ReceptorTipoId'] = 'Falta Tipo de documento (Ej. 13 = cedula,  31 = Nit)'; }
	if(!isset($row['ReceptorId']) 	  OR strlen(trim($row['ReceptorId'])) == 0)    		{ $validaciones = false; $errores['ReceptorId'] = 'Falta el Numero de documento (Cedula / Nit sin DV)'; }
	if(!isset($row['ReceptorLine'])   OR strlen(trim($row['ReceptorLine'])) == 0)       { $validaciones = false; $errores['ReceptorLine'] = 'Falta la Direccion del cliente'; }
	if(!isset($row['FirstName'])      OR strlen(trim($row['FirstName'])) == 0)          { $validaciones = false; $errores['FirstName'] = 'Falta el Nombre del Contacto en el cliente'; }
	if(!isset($row['FamilyName'])     OR strlen(trim($row['FamilyName'])) == 0)  		{ $validaciones = false; $errores['FamilyName'] = 'Falta el Apellido del Cotacto en el cliente'; }
	if(!isset($row['ContactTelephone'])      OR strlen(trim($row['ContactTelephone'])) == 0) 		{ $validaciones = false; $errores['ContactTelephone'] = 'Falta el Telefono del cliente'; }
	if(!isset($row['ContactElectronicMail']) OR strlen(trim($row['ContactElectronicMail'])) == 0)	{ $validaciones = false; $errores['ContactElectronicMail'] = 'Falta el Email del cliente'; }
	if(!isset($row['TaxLevelCodelistName'])  OR strlen(trim($row['TaxLevelCodelistName'])) == 0)  	{ $validaciones = false; $errores['TaxLevelCodelistName'] = 'Falta Regimen fiscal del cliente (48=Responsable Iva / 49=No responsable Iva)'; }
	if(!isset($responsabilidadesfiscales)    OR strlen(trim($responsabilidadesfiscales)) == 0)    	{ $validaciones = false; $errores['TaxLevelCode'] = 'Falta las Responsabilidades del cliente (Ej. O-48  / O-99)'; }

	if($validaciones) {
		
		//Dian3
		$fecha = new DateTime();
		$Timedate=$fecha->format('YmdHis');
		$nameStepFunction=$Timedate."-".$prefijo.$numero;
		
		// CREA FACTURA
		$curl = 'curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/json" -d \'{';
		$curl.= '"Email":"'.$row['email'];
		$curl.= '","nameStepFunction":"'.$nameStepFunction;
		$curl.= '","Prefix":"'.$prefijo;
		$curl.= '","Numero":"'.$numero;
		$curl.= '","Entity":"'.$row['entity'];
		$curl.= '","facturasistema":"'.$row['rowid'];
		$curl.= '","IssueDate":"'.$row['datef'];
		$curl.= '","IssueTime":"'.$IssueTime;
		$curl.= '","InvoicePeriodStartDate":"'.$row['datef'];
		$curl.= '","InvoicePeriodEndDate":"'.$row['date_lim_reglement'];
		$curl.= '","OrderReference":"';
		$curl.= '","PaymentMeansID":"'.$row['PaymentMeansID'];   		                // 1=contado, 2 = Credito
		$curl.= '","PaymentMeansCode":"'.$row['PaymentMeansCode'];  	                // 45=Transferencia bancaria
		$curl.= '","PaymentDueDate":"'.$row['PaymentDueDate'];			                // fecha limite de pago
		$curl.= '","PaymentID":"'.$row['PaymentID'];					                // Referencia de pago si la hay
		$curl.= '","DocumentTypeCode":"'.$DocumentTypeCode;                             // FA = Factura, NC = NotaCredito, ND=NotaDebito 
		$curl.= '","InvoiceTypeCode":"'.$InvoiceTypeCode;                               // 1 Factura de Venta  2 = NC x anulacion, 1 = NC x devolucion de mercancia, 1 = ND Interese, 2 = ND MayorValor       
		$curl.= '","IDInvoiceDocumentReference":"'.$IDInvoiceDocumentReference; 					// Invoice que afectara la Nota
		$curl.= '","IDInvoiceDocumentReferenceCufe":"'.$IDInvoiceDocumentReferenceCufe; 			// Cufe del Invoice que afectara la Nota
		$curl.= '","IDInvoiceDocumentReferenceIssueDate":"'.$IDInvoiceDocumentReferenceIssueDate; 	// Fecha del Invoice que afectara la Nota
		$curl.= '","AdditionalAccountID":"'.$row['AdditionalAccountID'];                            // 1 Persona Juridica, 2 Persona Natural
		$curl.= '","ReceptorTipoId":"'.$row['ReceptorTipoId'];                                      // 13 = cedula,  31 = Nit
		$curl.= '","ReceptorId":"'.$row['ReceptorId'];                                              // Cedula / Nit
		$curl.= '","CustomerPartyDV":"'.digitover($row['ReceptorId']);                              //calcula el digito de verificacion
		$curl.= '","ReceptorName":"'.$row['ReceptorName'];
		$curl.= '","ReceptorAddressId":"'.$row['ReceptorAddressId'];
		$curl.= '","ReceptorAddressCityName":"'.$row['ReceptorAddressCityName'];
		$curl.= '","ReceptorAddressCountrySubentity":"'.$row['ReceptorAddressCountrySubentity'];            //(Bogota / Medellin)
		$curl.= '","ReceptorAddressCountrySubentityCode":"'.$row['ReceptorAddressCountrySubentityCode'];    //(11=Bogota)
		$curl.= '","ReceptorLine":"'.preg_replace('/[^a-zA-Z0-9 ._-]/','', $row['ReceptorLine']);
		$curl.= '","FirstName":"'.$row['FirstName'];
		$curl.= '","MiddleName": "'.$MiddleName;
		$curl.= '","FamilyName":"'.$row['FamilyName'];
		$curl.= '","TaxLevelCodelistName":"'.$row['TaxLevelCodelistName'];
		$curl.= '","TaxLevelCode":"'.trim($responsabilidadesfiscales,';');
		$curl.= '","ContactName":"'.$row['ContactName'];
		$curl.= '","ContactTelephone":"'.$row['ContactTelephone'];
		$curl.= '","ContactElectronicMail":"'.$row['ContactElectronicMail'];

		//impuestos
		$curl.= '","TaxAmount1":"'.$row['TaxAmount1'].'","TaxEvidenceIndicator1":"false","TaxableAmount1":"'.$TaxExclusiveAmount.'","Percent1":"'.$Percent1;
		$curl.= '","TaxAmount2":"0.00","TaxEvidenceIndicator2":"false","TaxableAmount2":"0.00","Percent2":"0.00';
		$curl.= '","TaxAmount3":"0.00","TaxEvidenceIndicator3":"false","TaxableAmount3":"0.00","Percent3":"0.00';

		//totales
		$curl.= '","LineExtensionAmount":"'.$row['LineExtensionAmount'];
		$curl.= '","TaxExclusiveAmount":"'.$TaxExclusiveAmount;
		$curl.= '","PayableAmount":"'.$row['PayableAmount'];
		$curl.= '","TaxInclusiveAmount":"'.$row['TaxInclusiveAmount'];
		$curl.= '","AllowanceTotalAmount":"'.$row['AllowanceTotalAmount'];
		$curl.= '","ChargeTotalAmount":"'.$row['ChargeTotalAmount'];

		$curl.= '","InvoiceLine" : [ ';

		//detalles
		$cadena2 = "
		SELECT
		det.qty AS InvoicedQuantity,
		TRUNCATE(det.total_ht, 2) AS LineExtensionAmount,
		TRUNCATE(det.total_tva, 2) AS TaxAmount,
		'false' AS TaxEvidenceIndicator,
		CASE
			WHEN det.total_tva > 0 THEN TRUNCATE(total_ht, 2)
			ELSE TRUNCATE(total_tva, 2)
		END AS TaxableAmount,
		TRUNCATE(det.tva_tx, 2) AS Percent,
		'01' AS TaxSchemeID,
		TRUNCATE(det.subprice, 2) AS PriceAmount,
		'EA' AS unitCode,
		det.qty AS BaseQuantity,
		det.description AS description
		FROM ".PREFIJO."facturedet AS det
		WHERE det.fk_facture = ".$row['rowid'];

		$result2 = $dolibarr->leerdatosarray($cadena2);

		$item=0;
		while($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
		$item++;
			$curl.= '{"ID":"'.$item.'","InvoicedQuantity":"'.$row2['InvoicedQuantity'].'","LineExtensionAmount":"'.$row2['LineExtensionAmount'].'","TaxAmount":"'.$row2['TaxAmount'].'","TaxEvidenceIndicator":"'.$row2['TaxEvidenceIndicator'].'","TaxableAmount":"'.$row2['TaxableAmount'].'","Percent":"'.$row2['Percent'].'","TaxSchemeID":"'.$row2['TaxSchemeID'].'","Description":"'.preg_replace('/[^a-zA-Z0-9 ._-]/','', $row2['description']).'","PriceAmount":"'.$row2['PriceAmount'].'","BaseQuantity":"'.$row2['BaseQuantity'].'","unitCode":"'.$row2['unitCode'].'","UsabilityPeriodStartDate":"'.$row['datef'].'","UsabilityPeriodEndDate":"'.$row['date_lim_reglement'].'"},' ;
		}

		$curl.=' ]}\' '.DIAN3URLEXECUTE;
	}

	if($validaciones) {
		//Envia la factura
		shell_exec($curl." > /dev/null 2>/dev/null &");
		$cadenaf = " UPDATE ".PREFIJO."facture_extrafields SET envio='1',cufe=NULL,zipkey=NULL,isvalid=0,statuscode=NULL,statusdescription=NULL,success=NULL,pdf21=0,messageid=NULL,processedmessage=NULL,errormessage=NULL WHERE fk_object = '$row[rowid]' ";
		$dolibarr->grabardatos($cadenaf);
		echo "<br />===> Documento: (".$prefijo.$numero.") enviando a la Dian,  ";
		
		//Subir el PDF 20 segundos despues con una tarea que se ejecuta ahora
		$curlPdf = 'sleep 20; curl --request POST -H "Authorization: '.$token.'" -H "Content-Type:application/octet-stream" --data-binary "'.DOCUMENTOS.'"'.$row['facnumber'].'"/"'.$row['facnumber'].'".pdf" "'.URLSUBIRS3.'?invoice="'.$prefijo.$numero.'"&carpeta="'.substr($row['datef'],0,7).'" " ';
		$ejecutar= "echo '$curlPdf' | at now";
		shell_exec($ejecutar." > /dev/null 2>/dev/null &");

		echo " y subiendo pdf... \n ";	
	}
	else {
		$cadenaf = " UPDATE ".PREFIJO."facture_extrafields SET errormessage='".json_encode($errores)."' WHERE fk_object = '$row[rowid]' ";
		$dolibarr->grabardatos($cadenaf);
		echo "<br />===> Error en la FACTURA: ".$prefijo.$numero."\n";
		echo var_dump($errores);
	}
}
if($nohayfacturas) { echo "<br /><font color='red'>**** NO SE ENVIO NINGUN DOCUMENTO **** </font> "; } else {
	echo "<br /><font color='blue'><h3>Los resultados se reflejaran en 3 minutos en Dolibarr...</h3></font>";
}

?>
