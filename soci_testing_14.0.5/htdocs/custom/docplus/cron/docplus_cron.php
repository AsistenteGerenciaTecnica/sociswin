<?php

if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', '1');
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined("NOLOGIN")) {
	define("NOLOGIN", '1'); // If this page is public (can be called outside logged session)
}
if (!defined('NOIPCHECK')) {
	define('NOIPCHECK', '1'); // Do not check IP defined into conf $dolibarr_main_restrict_ip
}
if (!defined('NOBROWSERNOTIF')) {
	define('NOBROWSERNOTIF', '1');
}

require '../../../main.inc.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/categoria.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/carpeta.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/documento.class.php';
require_once DOL_DOCUMENT_ROOT.'/custom/docplus/class/campo.class.php';

require_once DOL_DOCUMENT_ROOT.'/custom/docplus/lib/docplus.lib.php';

$sql = "SELECT";
$sql .= " ddo.rowid";
$sql .= ", ddo.fk_objeto";
$sql .= ", ddo.fk_documento";
$sql .= ", dd.nombre";
$sql .= ", dd.modulo";
$sql .= ", ddo.renovable";
$sql .= ", ddo.tipo_renovacion";
$sql .= ", ddo.valor_cada";
$sql .= ", ddo.tiempo_cada";
$sql .= ", ddo.fecha_renovacion";
$sql .= ", ddo.valor_aviso";
$sql .= ", ddo.tiempo_aviso";
$sql .= ", ddo.aviso_renovacion";
$sql .= ", ddo.tms";
$sql .= ", ddo.fk_user_modif";
$sql .= ", ddo.date_creation";
$sql .= ", ddo.fk_user_creat";
$sql .= " FROM llx_docplus_documento_objeto as ddo";
$sql .= " INNER JOIN llx_docplus_documento as dd";
$sql .= " ON dd.rowid = ddo.fk_documento";

$resql= $db->query($sql);

$documentos = array();

if ($resql)
{
	$num = $db->num_rows($resql);
	$i = 0;
	while ($i < $num) {
		$obj = $db->fetch_object($resql);
        
        if ($obj->renovable)
        {
            $documentos[$i] = $obj;
        }
		
		$i++;
	}
	$db->free($resql);		
}

echo 'Cantidad:' . count($documentos) . '<br>';

$por_renovar = array();
$fecha = strtotime(date("Y-m-d"));

foreach ($documentos as $documento)
{
    if ($documento->renovable)
    {

        $fecha_renovacion = strtotime($documento->fecha_renovacion);
        $aviso_renovacion = strtotime($documento->aviso_renovacion);

        echo '<br>doc_obj: ' . $documento->rowid;
        echo '<br>DOC_ID: ' . $documento->fk_documento;
        echo '<br>Nombre: ' . $documento->nombre;
        echo '<br>Fecha de renovación: ' . $fecha_renovacion;
        echo '<br>Aviso renovación: ' . $aviso_renovacion;
        echo '<br>Fecha: ' . $fecha;
        echo '<br>tms: ' . $documento->tms;

        if ($documento->tipo_renovacion == "cada")
        {
            echo '<br>Cada: ' . $documento->valor_cada . ' ' . $documento->tiempo_cada;
        }

        if ($fecha_renovacion <= $fecha || $aviso_renovacion == $fecha)
        {
            if ($fecha_renovacion <= $fecha)
            {
                echo '<br>Sin Actualizar';
            }
            else
            {
                echo '<br>Aviso Renovación';
            }

            $por_renovar[] = $documento;

            $open_doc = $documento->fk_documento;
            $doc = new Documento($db);
            $doc->fetch($documento->fk_documento);
            $parents = get_parents($doc);

            $href = DOL_MAIN_URL_ROOT.'/custom/docplus/tab_'. $documento->modulo .'.php?id='. $documento->fk_objeto . '&open=' . $parents . '&open_doc=' . $open_doc;

            echo '<br><a href='. $href .'>Link</a>';
            

        }
        else
        {
            echo '<br>Actualizado';
        }

        echo '<br>';
    }
}

$mail = new PHPMailer;

$remitente = $conf->global->DOCUMENTOS_EMAIL_REMITENTE;
$receptor = $conf->global->DOCUMENTOS_EMAIL_RECEPTOR;
// Información de envío
$mail->Username = $remitente;
$mail->setFrom($remitente);
$mail->addAddress($receptor);
$mail->isHTML(true);
// Asunto
$mail->Subject = "RENOVAR DOCUMENTOS";

// Cuerpo
foreach ($por_renovar as $documento)
{

    $open_doc = $documento->fk_documento;
    $doc = new Documento($db);
    $doc->fetch($documento->fk_documento);
    $parents = get_parents($doc);

    $href = DOL_MAIN_URL_ROOT.'/custom/docplus/tab_'. $documento->modulo .'.php?id='. $documento->fk_objeto . '&open=' . $parents . '&open_doc=' . $open_doc;

    if (strtotime($documento->fecha_renovacion) <= $fecha)
    {
        $estado = 'Sin Actualizar';
    }
    else
    {
        $estado = 'Aviso Renovación';
    }

    $mail->Body .= '<br>';
    $mail->Body .= '<br>Documento: <b><a href="'. $href .'">' . $documento->nombre . '</a></b>';
    $mail->Body .= '<br>Fecha de renovación: <b>'. date('Y-m-d', $documento->fecha_renovacion) .'</b>';
    $mail->Body .= '<br>Estado: <b>'. $estado .'</b>';
    $mail->Body .= '<div></div>';
    $mail->Body .= '<br>';
}


// Configuración de seguridad del envío
$mail->IsSMTP();
$mail->SMTPSecure = 'ssl';
$mail->Host = 'ssl://smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Port = 465;
$mail->Password = $conf->global->DOCUMENTOS_PASSWORD;
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
// Enviar
$mail->send();