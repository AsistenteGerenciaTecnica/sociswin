<?php 
ini_set("default_charset", "UTF-8");
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
// Se obtiene el ID de la hoja de vida del equipo
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
    $date_fac=strtotime($row['date_facture']);
    $warranty_time=$row['warranty_time'];
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
//Funcion para calular Años Meses y dias que faltan para vencimineto de garantia de un equipo
function calcular($dias){
  $lista=[0,0,0];
  while($dias>=365){
    $dias=$dias-365;
    $lista[0]+=1;
  }
  while($dias<365 && $dias>=30){
    $dias=$dias-30;
    $lista[1]+=1;
  }
  $lista[2]=$dias;
  return $lista;
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
            <!-- Se imprime dos columnas, la izquierda con informacion de la empresa y la derecha con informacion del cliente-->
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
      //SE GENERA LA FECHA ACTUAL PARA LA IMPRESION DE LOS DÍAS DE REVISION EN GARANTÍA
      $fecha2 = strtotime(date("Y-m-d H:i:s"));
      //Se cuentan los dias que han pasado desde la fecha de facturacion del equipo
      $dias_garantia=5;
      $fecha2 = strtotime(date("Y-m-d H:i:s"));
      for($date_fac;$date_fac<=$fecha2;$date_fac=strtotime('+1 day ' . date('Y-m-d',$date_fac))){ 
          $dias_garantia=$dias_garantia+1;
      }
      //Se consulta la garantia pertenecientes a la hoja de vida del equipo
      $query = "SELECT * FROM llx_garantia WHERE fk_equipment_history = $id AND status=1 ORDER BY date_send";
      $result = mysqli_query($conn, $query);
      //
      $fecharecepcion='0000-00-00 00:00:00';
      //while para imprimir todas las revisiones
      while($row = mysqli_fetch_assoc($result)) {
        $fecha1=strtotime($row['date_send']);
        //Se cuentan los días que han pasado desde la fecha de envío de revision de garantía Sin contar sabados y domingos
        $dias=-1;
        for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
          if((strcmp(date('D',$fecha1),'Sun')!=0) && (strcmp(date('D',$fecha1),'Sat')!=0)){
            $dias=$dias+1;
          }
        }
        
        ?>
        <br></br>
        <!--Se imprimen las fecha de envío y Recepción si ya se recibió-->
        <?php echo "<b>Consecutivo: ".$row['ref']."</b>"; ?><br></br>
        <?php echo "<b>Fecha de Envio: </b>".$row['date_send']; $fechaenvio=$row['date_send'];?><br></br>
        <?php echo "<b>Fecha de Recepcion: </b>"; if($row['date_reception']!=NULL){echo $row['date_reception'];} else { echo "Por Recibir"; } ?><br></br>
        <!--Se imprimen los días que ha pasado en revision aun no se han recibido-->
        <?php echo "<b>Tiempo en Garantia: </b>"; if($row['date_reception'] == NULL){echo $dias; echo utf8_decode(" días");}else{echo "Recibido";} ?><br></br>
        <?php echo "<b>Tiempo Restante: </b>"; if($warranty_time!=NULL && $date_fac!=NULL){ $lista=calcular(($warranty_time*365)-$dias_garantia); if($lista[0]!=0){echo $lista[0].utf8_decode(" años ");} if($lista[1]!=0){echo $lista[1]." meses ";} if($lista[2]>=0){ echo $lista[2].utf8_decode(" días");} if($lista[2]<0){echo "Garantia Vencida";}} //Se calculan el tiempo que falta para que termine la garantia de fabricante?>
        <br></br><br></br>
        <!-- Tabla para imprimir la garantia del equipo con todos las observaciones del proveedor-->
        <table class="table" width="100%" border-collapse="collapse">
          <thead><!-- Cabecera de la tabla-->
            <tr>
              <th>Intervenciones Internas</th>
              <th>Observaciones del Proveedor</th>
            </tr>
          </thead>
          <tbody><!-- Cuerpo de la Tabla Impresion de datos-->
            <tr>
              <td VALIGN="TOP">
                <!-- celda de la subtabla-->
                <table class="table" VALIGN="TOP" border="0" width="100%" border-collapse="collapse"><!-- inicio de la subtabla de observaciones-->
                  <thead>
                    <tr>
                      <th>Fecha de Intervencion</th>
                      <th>Orden de Servicio</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                      //impresion de todas las observaciones de cada intervencion interna que tiene el equipo
                      $query2 = "SELECT * FROM llx_revision WHERE fk_equipment_history = $id AND status=1 AND date_send<'$fechaenvio' AND date_send>'$fecharecepcion'";
                      $result2 = mysqli_query($conn, $query2);
                      while($row2 = mysqli_fetch_assoc($result2)) {
                        $orden=$row2['orden'];
                        //Se consulta la orden de servicio enlazada a esta intervencion
                        $query2="SELECT ref FROM llx_fichinter WHERE rowid=$orden";
                        $orden = mysqli_fetch_assoc(mysqli_query($conn, $query2));
                        ?>
                        <tr>
                          <!--Se imprime fecha, responsable y observaciones-->
                          <td><?php echo $row2['date_send']; ?></td>
                          <td><?php echo $orden['ref']; ?></td>
                        </tr>           
                      <?php } ?>
                  </tbody>
                </table><!-- fin de la subtabla-->
              </td><!-- fin de la celda de la subtabla-->
              <td VALIGN="TOP"><!-- celda de la subtabla observaciones proveedor-->
                <table class="table" VALIGN="TOP" width="100%" border-collapse="collapse"><!-- inicio de la subtabla de observaciones-->
                  <thead VALIGN="TOP">
                    <tr VALIGN="TOP">
                      <th VALIGN="TOP">Fecha de Observacion</th>
                      <th VALIGN="TOP">Observacion</th>
                      <th VALIGN="TOP">Imagen</th>
                    </tr>
                  </thead>
                  <tbody VALIGN="TOP">
                    <?php
                    $idgarantia=$row['rowid'];
                    //se consultan todas las observaciones del proveedor
                    $query2 = "SELECT * FROM llx_observation_fourn WHERE fk_garantia = $idgarantia AND status=1 ORDER BY date";
                    $result2 = mysqli_query($conn, $query2);
                    //Las variables date y date2 se usan para comparar las fechas, si hay elemencos con  fecha iguales, quiere decir que se subieron juntos y se deben imprimir en la misma celda de la tabla
                    $date='';
                    $date2='';
                    //while para imprimir todas las actividades de mantenimiento
                    while($row2 = mysqli_fetch_assoc($result2)) {
                      $date=$row2['date'];
                      $rowid=$row2['rowid'];
                      //Se consulta las imagenes que tengan la misma fecha y que tengan el id diferente
                      $query="SELECT imagen FROM llx_observation_fourn WHERE date='$date' AND rowid!='$rowid' AND fk_garantia=$idgarantia";
                      $result3 = mysqli_query($conn, $query);
                      //Se evalua si se encontro algun resultado de la consulta
                      if (mysqli_num_rows($result3) >= 1){ ?>
                        <tr>
                          <!-- se imprimen la fecha, responsable y observacion -->
                          <td><?php echo $row2['date']; ?></td>
                          <td style="text-align: justify"><?php echo $row2['observation']; ?></td>
                          <td><?php
                          $cont=0;//Contador de imagenes
                          //Se imprime la imagen
                          echo '<img src="'.$row2['imagen'].'" width="130" height="130">';
                          while($row3 = mysqli_fetch_assoc($result3)) {
                              echo "<br></br>";
                              //se imprimen las demas imagenes que se encontraron con la misma fecha de subida
                              echo '<img src="'.$row3['imagen'].'" width="130" height="130">';
                              $cont=$cont+1;
                          }
                          //Se recorre el resultado de la primera consulta para que no se vuelvan a imprimir las imagenes que ya fueron impresas anteriormente
                          for($i=1;$i<=$cont;$i++) {
                              $row2 = mysqli_fetch_assoc($result2);
                          } ?>
                          </td>
                        </tr><!-- se cierra la fila -->
                        <!-- Si no se encontro otra imagen con la misma fecha, simplemente se imprime toda la informacion de esta imagen-->
                        <?php } else { ?>
                        <tr>
                          <td><?php echo $row2['date']; ?></td>
                          <td style="text-align: justify"><?php echo $row2['observation']; ?></td>
                          <td><?php echo '<img src="'.$row2['imagen'].'" width="130" height="130">';?></td>
                        </tr><!-- se cierra la fila -->
                      <?php } ?>
                    <?php }?>
                  </tbody>
                </table><!-- fin de la subtabla-->
              </td><!-- fin de la celda de la subtabla-->
            </tr><!-- se cierra la fila-->
          </tbody><!-- fin del body de la tabla-->
        </table><!-- fin de la tabla GARANTIAS-->
      <?php $fecharecepcion=$row['date_reception'];
      } ?>
        <br><br><br>
        <div>
            <!-- Espacio para las firmas-->
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
//$mipdf ->set_paper(array(0,0,792.00, 1224.00));
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
$mipdf->stream("GarantiaCompleta-$ref.pdf", array("Attachment" => false));

exit(0);
?>