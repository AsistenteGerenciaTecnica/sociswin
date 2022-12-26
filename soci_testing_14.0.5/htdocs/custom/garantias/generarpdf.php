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
// Se obtiene el ID de la hoja de vida
if  (isset($_GET['id'])) {
    $id = $_GET['id'];
    //Se consulta la informacion de la hoja de vida para imprimirla en el pdf
    $query = "SELECT * FROM llx_equipment_history WHERE rowid = $id";
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
    $idproduct=$row['fk_product'];
    //Se extrae las referencias de los campos conociendo el ID
    $query2="SELECT nom FROM llx_societe WHERE rowid=$proveedor";
    $proveedor = mysqli_fetch_assoc(mysqli_query($conn, $query2));
    //Se extrae la informacion del proyecto si el id del project guardado en la hoja de vida es 0 quiere decir que se trata de un proyecto de ventas
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
            <!-- Se imprime la informacion del equipo en laparte superior derecha-->
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
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                A:</p>
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
        </div></br></br>
        <link rel="stylesheet" href="styles.css"><br>
    </head>
    <body>
        <!-- Tabla donde se imprimen todos los mantenimientos del equipo-->
        <table class="table" style="text-align: center" width="100%" border-collapse="collapse">
            <thead><!-- Cabecera de la tabla-->
                <tr>
                    <th>Actividad</th>
                    <th>Frecuencia</th>
                    <th>Imagen</th>    
                </tr>
            </thead>
            <tbody><!-- Cuerpo de la Tabla Impresion de datos-->
                <?php
                //Se consulta en la base de datos todos los prcedimientos de mantenimientos pertenecientes a la hoja de vida
                $query = "SELECT * FROM llx_maintenance WHERE fk_product=$idproduct AND status=1 ORDER BY date";
                $result = mysqli_query($conn, $query);
                //Las variables date y date2 se usan para comparar las fechas, si hay elemencos con  fecha iguales, quiere decir que se subieron juntos y se deben imprimir en la misma celda de la tabla
                $date="";
                $date2="";
                //while para imprimir todas las actividades de mantenimiento
                while($row = mysqli_fetch_assoc($result)) {
                    $date=$row['date'];
                    $rowid=$row['rowid'];
                    //Se consulta las imagenes que tengan la misma fecha y que tengan el id diferente
                    $query="SELECT imagen FROM llx_maintenance WHERE date='$date' AND rowid!='$rowid' AND fk_product=$idproduct";
                    $result2 = mysqli_query($conn, $query);
                    //Se evalua si se encontro algun resultado de la consulta
                    if (mysqli_num_rows($result2) >= 1){?>
                        <tr>
                            <!-- se imprimen las actividades y la frecuencia -->
                            <td><?php echo $row['activities']; ?></td>
                            <td><?php echo $row['frecuency']; ?></td>
                            <td><?php
                                $cont=0;//Contador de imagenes
                                //Se imprime la imagen
                                echo '<img src="'.$row['imagen'].'" width="150" height="150">';
                                while($row2 = mysqli_fetch_assoc($result2)) {
                                    //se imprimen las demas imagenes que se encontraron con la misma fecha de subida
                                    echo '<img src="'.$row2['imagen'].'" width="150" height="150">';
                                    $cont=$cont+1;
                                    //Si el cociente de cont/2 es 0 quiere decir que se han impreso 2 imagenes y se dara un salto de linea para imprimir las demas dentro de la misma celda
                                    if(($cont%2)!=0){
                                        echo "<br></br>";
                                    }
                                }
                                //Se recorre el resultado de la primera consulta para que no se vuelvan a imprimir las imagenes que ya fueron impresas anteriormente
                                for($i=1;$i<=$cont;$i++) {
                                    $row = mysqli_fetch_assoc($result);
                                } ?>
                            </td>
                        </tr><!-- se cierra la fila -->
                        <!-- Si no se encontro otra imagen con la misma fecha, simplemente se imprime toda la informacion de esta imagen-->
                        <?php } else { ?>
                        <tr>
                            <td><?php echo $row['activities']; ?></td>
                            <td><?php echo $row['frecuency']; ?></td>
                            <td><?php echo '<img src="'.$row['imagen'].'" width="150" height="150">';?></td>
                        </tr><!-- se cierra la fila -->
                    <?php } ?>
                <?php } ?>
            </tbody><!-- fin del body de la tabla-->
        </table><!-- fin de la tabla-->
        <br><br><br>
        <div>
            <!-- Espacio para las firmas de los tecnicos -->
            <p><b>Firma Tecnico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;Firma Jefe Inmediato</b></p>
        </div>
    </body>
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
$mipdf ->load_html(ob_get_clean());
# Renderizamos el documento PDF.
$mipdf ->render();
// Se imprime en la parte inferior el pie de pagina con numero de pagina
$canvas = $mipdf->get_canvas(); 
$font = Font_Metrics::get_font("helvetica", "bold"); 
$canvas->page_text(500, 980, "Pagina: {PAGE_NUM} de {PAGE_COUNT}", $font, 6, array(0,0,0)); 

# Enviamos el fichero PDF al navegador.
$mipdf->stream("Procedimiento-$ref.pdf", array("Attachment" => false));

exit(0);
?>