<?php

ob_start();
include("db.php");
//-- Se definen variables
$ref='';
$referencia = '';
$serial = '';
$fecharecepcion = '';
$cliente='';
$proveedor='';
$marca='';
$project='';
$id='';
$responsable='';
// Se obtiene el ID de la intervencion interna
if  (isset($_GET['id'])) {
  $id = $_GET['id'];
}
// Se obtiene el ID de la hoja de vida del equipo
if  (isset($_GET['idh'])) {
    $idh = $_GET['idh'];
    //Se consulta la informacion de la hoja de vida para imprimirla en el pdf
    $query = "SELECT * FROM llx_equipment_history WHERE rowid = $idh";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $ref=$row['ref'];
    $referencia = $row['reference'];
    $serial = $row['serial'];
    $fecharecepcion = $row['date_reception'];
    $proveedor=$row['fk_fournisseur'];
    $project=$row['fk_projet'];
    $cliente=$row['fk_client'];
    $marca=$row['mark'];
    //Se extrae las referencias de los campos conociendo el ID
    $query2="SELECT nom FROM llx_societe WHERE rowid=$proveedor";
    $proveedor = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //Se extrae la informaion del proyecto si el id del project guardado en la hoja de vida es 0 quiere decir que se trata de un proyecto de ventas
    if($project==0){
        $project="Ventas";
    }else{
        $query2="SELECT title FROM llx_projet WHERE rowid=$project";
        $project = mysqli_fetch_assoc(mysqli_query($conn, $query2));
        $project=$project['title'];
    }
    //se extrae la informacion del cliente
    $query2="SELECT * FROM llx_societe WHERE rowid=$cliente";
    //$cliente = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    if(mysqli_query($conn, $query2)!=NULL){        
        $cliente = mysqli_fetch_assoc(mysqli_query($conn, $query2));     
    }
}
?>  
<html>
    <head>
        <!-- Hoja de estilos de CSS para mostrar lineas de las tablas-->
        <style>
        table {
            border-collapse: collapse;
        }
        table,
        th,
        td {
            border: 1px solid black;
        }
        
        th,
        td {
            padding: 5px;
        }
        </style>
        <div>
            <!-- Se imprime el logo empresarial en la parte superior izquierda-->
            <p><img align="left" src="images/logo.png" width="130" height="110"></p>
            <!-- Se imprime la informacion del equipo en la parte superior derecha-->
            <div align="right" style="font-size:12px;">
                <h3><br>Informacion del Equipo: <?php echo $ref; ?></b></h3>
                <div><?php echo "<b>Cliente:  </b>"; echo $cliente['nom']; ?></div>
                <div><?php echo "<b>Marca:  </b>"; echo $marca;?></div>
                <div><?php echo "<b>Referencia:  </b>"; echo $referencia;?></div>
                <div><?php echo "<b>Serial:  </b>"; echo $serial;?></div>
                <div><?php echo "<b>Proveedor:  </b>"; echo $proveedor['nom'];?></div>
                <div><?php echo "<b>Proyecto:  </b>"; echo $project;?></div>
            </div>
            <p align="left" style="font-size:14px;">De: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;A:</p>
            <!-- Se imprime dos columnas la izquierda con informacion de la empresa y la derecha con informacion del cliente-->
            <table class="table" width="100%" style="font-size:13px;">
            <thead>
                <tr>
                    <td width="50%" align="left" style="background-color: rgb(229, 231, 233);">
                            <div><b>ULTIMATE TECHNOLOGY SAS </b></div>
                            <div>Calle 11 # 15-53 Barrio Los Alpes</div>
                            <div>No somos grandes contribuyentes ni autoretenedores</div></br>
                            <div>PEREIRA</div>
                            <div>Telefono: 3242637 - Cel: 3015528499</div>
                            <div>Correo: facturacion@ultimate.com.co</div>
                            <div>Web: www.ultimate.com.co</div>
                    </td>
                    <td width="50%" VALIGN="TOP">
                        <div><b><?php echo $cliente['nom']; ?></b></div>
                        <div><?php echo $cliente['address']; ?></div>
                        <div><?php echo $cliente['town']; ?></div></br>
                    </td>    
                </tr>
            </thead>
            </table>
        </div>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <?php
        //Se consulta en la base de datos todos las intervenciones pertennecientes a la hoja de vida
        $query = "SELECT * FROM llx_revision WHERE rowid=$id AND fk_equipment_history = $idh AND status=1 ORDER BY date_send";
        $result = mysqli_query($conn, $query);              
        //while para imprimir todas las revisiones
        while($row = mysqli_fetch_assoc($result)) { ?>
            <br></br>
            <?php echo "<b>Fecha de Intervencion: </b>".$row['date_send']; 

            $idrev=$row['rowid'];
            //Se consulta la orden de servicio enlazada a esta intervencion
            $query="SELECT llx_fichinter.ref as ref FROM llx_fichinter, llx_revision WHERE llx_fichinter.rowid=llx_revision.orden AND llx_revision.rowid=$idrev";
            $result2 = mysqli_fetch_assoc(mysqli_query($conn, $query));?>
            <br></br>
            <!-- Se imprime la orden de servicio-->
            <?php echo "<b>Orden de Servicio: </b>".$result2['ref']; ?>
            <br></br><br></br>
            <table class="table" VALIGN="TOP" style="text-align: center" width="100%" border-collapse="collapse"><!-- inicio de la subtabla de observaciones-->
                <thead>
                    <tr>
                        <th>Fecha de Procedimiento</th>
                        <th>Responsable</th>
                        <th>Procedimiento</th>
                        <th>Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $idrevision=$row['rowid'];
                    //Se consultan todas las observaciones/procedimientos que tiene esta intervencion
                    $query2 = "SELECT * FROM llx_observation WHERE fk_revision = $idrevision AND status=1 ORDER BY date";
                    $result2 = mysqli_query($conn, $query2);
                    //Las variables date y date2 se usan para comparar las fechas, si hay elemencos con  fecha iguales, quiere decir que se subieron juntos y se deben imprimir en la misma celda de la tabla
                    $date="";
                    $date2="";
                    //while para imprimir todas las actividades de mantenimiento
                    while($row2 = mysqli_fetch_assoc($result2)) {
                        $date=$row2['date'];
                        $rowid=$row2['rowid'];
                        //Se consulta las imagenes que tengan la misma fecha y que tengan el id diferente
                        $query="SELECT imagen FROM llx_observation WHERE date='$date' AND rowid!='$rowid' AND status=1 AND fk_revision=$idrevision";
                        $result3 = mysqli_query($conn, $query);
                        //Se evalua si se encontro algun resultado de la consulta
                        if (mysqli_num_rows($result3) >= 1){ ?>
                            <tr VALIGN="TOP">
                                <!-- se imprimen la fecha, responsable y observacion -->
                                <td><?php echo $row2['date']; ?></td>
                                <td><?php echo $responsable=$row2['responsable']; ?></td>
                                <td style="text-align: justify"><?php echo $row2['observation']; ?></td>
                                <td><?php
                                $cont=0;//Contador de imagenes
                                //se imprime imagen
                                echo '<img src="'.$row2['imagen'].'" width="150" height="150">';
                                while($row3 = mysqli_fetch_assoc($result3)) {
                                    echo "<br></br>";
                                    //se imprimen las demas imagenes que se encontraron con la misma fecha de subida
                                    echo '<img src="'.$row3['imagen'].'" width="150" height="150">';
                                    $cont=$cont+1;
                                }
                                //Se recorre el resultado de la primera consulta para que no se vuelvan a imprimir las imagenes que ya fueron impresas anteriormente
                                for($i=1;$i<=$cont;$i++) {
                                    $row2 = mysqli_fetch_assoc($result2);
                                }?>
                                </td>
                            </tr><!-- se cierra la fila -->
                            <!-- Si no se encontro otra imagen con la misma fecha, simplemente se imprime toda la informacion de esta imagen-->
                            <?php } else { ?>
                            <tr VALIGN="TOP">
                                <td><?php echo $row2['date']; ?></td>
                                <td><?php echo $responsable=$row2['responsable']; ?></td>
                                <td style="text-align: justify"><?php echo $row2['observation']; ?></td>
                                <td><?php echo '<img src="'.$row2['imagen'].'" width="150" height="150">';?></td>
                            </tr><!-- se cierra la fila -->
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table><!-- fin de la subtabla-->
            
        <?php }?>
        <br><br><br>
        <div>
            <!-- Espacio para las firmas-->
            <p>Firma Tecnico<br><b><?php echo $responsable; ?></b></p>
        </div>
    </br>
</html>

<?php
# Cargamos la librería dompdf.
require_once 'dompdf/dompdf_config.inc.php';
require_once 'dompdf/dompdf_config.custom.inc.php';

# Instanciamos un objeto de la clase DOMPDF.
$mipdf = new DOMPDF();

# Definimos el tamaño y orientación del papel que queremos.
# O por defecto cogerá el que está en el fichero de configuración.
$mipdf ->set_paper("LEGAL");

# Cargamos el contenido HTML.
$mipdf ->load_html(ob_get_clean(),'UTF-8');
# Renderizamos el documento PDF.
$mipdf ->render();
// Se imprime en la parte inferior el pie de pagina con numero de pagina
$canvas = $mipdf->get_canvas(); 
$font = Font_Metrics::get_font("helvetica", "bold"); 
$canvas->page_text(500, 980, "Pagina: {PAGE_NUM} de {PAGE_COUNT}", $font, 6, array(0,0,0)); 

# Enviamos el fichero PDF al navegador.
$mipdf->stream("Intervencion-$ref.pdf", array("Attachment" => false));

exit(0);
?>