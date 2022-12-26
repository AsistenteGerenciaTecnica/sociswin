<?php

require_once ("persistencia/garantiasDAO.php");
require_once ("persistencia/conexion.php");



class Garantia {
    private $fk_soc;  # id de proveedor
    private $tms;  # tms
    private $type;
    private $reference;
    private $ref;
    private $fk_facture;
    private $fk_commande_fournisseur;  # id de pedido
    private $fk_client;
    private $fk_fournisseur;
    private $date_reception;
    private $date_operation;
    private $date_facture;
    private $verify_code;
    private $control_port;
    private $mac_address;
    private $mark;
    private $modelo;
    private $serial;
    private $fk_projet;   #id proyecto
    private $national_type;
    private $web_port;
    private $category_operation;
    private $fk_product;
    private $warranty_time;
    private $status;
    private $statut;
    private $fk_object;
    private $label;
    private $Fk_country;
    private $id_producto;
    private $qty;
    private $count;
    private $productoDAO;
    private $conexion;

    //////



    ///////
    function getProveedor(){
        return $this -> fk_soc;

    }
    //////


     function getFecha(){
        return $this -> tms;

    }
    //////

    function getNumPedido(){
        return $this -> fk_commande_fournisseur;

    }
    //////

    function getNumProyecto(){
        return $this -> fk_projet;

    }
    //////

  
    function getStatut(){
        return $this -> reference;

    }
    //////


 
      function getQty(){
        return $this -> qty;

    }

     //////

  
     function getfkObject(){
        return $this -> fk_object;

    }
    //////

    function getIdProducto(){
        return $this -> idProducto;

    }

     //////

     function getFkProducto(){
        return $this -> fk_product;

    }

     //////    / consulta 4 - consultarNacionalidad

     function getFkcountry(){
        return $this -> Fk_country;

    }
////////         consulta 4 - consultarNacionalidad

function getid_producto(){
    return $this -> id_producto;

}
////////         consulta 4 - consultarNacionalidad



function getReference(){
    return $this -> reference;

}
////////           consulta 4 - consultarNacionalidad



function getLabel(){
    return $this -> label;

}
////////   consulta 5 - 

function getCounts(){
    return $this -> count;

}
////////   consulta 4 - consultarNacionalidad


    function setIdProducto($pIdProducto){
        $this -> idProducto = $pIdProducto;
                        
    }

    function getNombre(){
        return $this -> nombre;
    }

    function setNombre($pNombre){
        $this -> nombre = $pNombre;
    }

    function getCantidad(){
        return $this -> cantidad;
    }

    function setCantidad($pCantidad){
        $this -> cantidad = $pCantidad;
    }

    function getPrecio(){
        return $this -> precio;
    }

    function setPrecio($pPrecio){
        $this -> precio = $pPrecio;
    }

    function __construct($pfk_soc = "", $ptms = "", $ptype = "", $preference = "", $pref = "", $pfk_facture = "", $pfk_commande_fournisseur = "",
     $pfk_client = "", $pfk_fournisseur = "", $pdate_reception = "", $pdate_operation = "", $pdate_facture = "", $pverify_code = "",
     $pcontrol_port = "", $pmac_address = "", $pmark = "", $pmodelo = "", $pserial = "", $pfk_projet = "",
     $pnational_type = "", $pweb_port = "", $pcategory_operation = "", $pfk_product = "", $pwarranty_time = "", $pstatus = "" , $pstatut = "", $pfk_object = "", $plabel = "",
     $pFk_country = "", $pid_producto = "" , $pqty = "", $pcount = ""){

        $this -> fk_soc = $pfk_soc;  # - 0
        $this -> tms = $ptms;   # - 1
        $this -> type = $ptype; # - 2
        $this -> reference = $preference;   # - 3
        $this -> ref = $pref;   # - 4
        $this -> fk_facture = $pfk_facture; # - 5
        $this -> fk_commande_fournisseur = $pfk_commande_fournisseur; # - 6
        $this -> fk_client = $pfk_client;   # - 7
        $this -> fk_fournisseur = $pfk_fournisseur; # - 8
        $this -> date_reception = $pdate_reception; # - 9
        $this -> date_operation = $pdate_operation; # - 10
        $this -> date_facture = $pdate_facture; # - 11
        $this -> verify_code = $pverify_code;   # - 12
        $this -> control_port = $pcontrol_port; # - 13
        $this -> mac_address = $pmac_address;   # - 14
        $this -> mark = $pmark; # - 15
        $this -> modelo = $pmodelo; # - 16
        $this -> serial = $pserial; # - 17
        $this -> fk_projet = $pfk_projet;   # - 18
        $this -> national_type = $pnational_type;   # - 19
        $this -> web_port = $pweb_port; # - 20
        $this -> category_operation = $pcategory_operation; # - 21
        $this -> fk_product = $pfk_product; # - 22
        $this -> warranty_time = $pwarranty_time;   # - 23
        $this -> status = $pstatus; # - 24
        $this -> statut = $pstatut; # - 25
        $this -> fk_object = $pfk_object; # - 26
        $this -> label = $plabel; # - 27
        $this -> Fk_country = $pFk_country; # - 28
        $this -> id_producto = $pid_producto; # - 29
        $this -> qty = $pqty; # - 30
        $this -> count = $pcount; # - 31


        $this -> garantiasDAO = new GarantiasDAO($this -> fk_soc, $this -> tms, $this -> type, $this -> reference, $this -> ref, $this -> fk_facture, $this -> fk_commande_fournisseur, 
            $this -> fk_client, $this -> fk_fournisseur, $this -> date_reception,  $this -> date_operation,  $this -> date_facture,  $this -> verify_code, 
            $this -> control_port, $this -> mac_address, $this -> mark,  $this -> modelo,  $this -> serial,  $this -> fk_projet, 
            $this -> national_type, $this -> web_port, $this -> category_operation,  $this -> fk_product,  $this -> warranty_time,  $this -> status,  $this -> statut, $this -> fk_object,
            $this -> label, $this -> Fk_country, $this -> id_producto , $this -> qty, $this -> count);
        $this -> conexion = new Conexion();
    }

    function insertar1($ref, $reference, $label2, $idproduct, $IdProveedor, $Idpedido, $fecha, $projet, $nac){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> insertar1($ref, $reference, $label2, $idproduct, $IdProveedor, $Idpedido, $fecha, $projet, $nac));
        $this -> conexion -> cerrar();

    }

    function actualizar(){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> actualizar());
        $this -> conexion -> cerrar();

    }

    function consultar(){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> consultar());
        $resultado = $this -> conexion -> extraer();
        $this -> conexion -> cerrar();
        $this -> idProducto = $resultado[0];
        $this -> nombre = $resultado[1];
        $this -> cantidad = $resultado[2];
        $this -> precio = $resultado[3];

    }

    function consultarTodo($filtro){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> consultarSinProcesar($filtro));
        $garantias = array();
        while ($resultado = $this -> conexion -> extraer()){
            array_push($garantias, new Garantia($resultado[0],$resultado[1], "" ,"" ,"" ,"" ,$resultado[2], "" ,"" ,"" ,"" , "" ,"" ,"" ,"" ,"" ,"" ,"" ,$resultado[3] ));

        }
        
        $this -> conexion -> cerrar();
        return $garantias;

    }

/////CONSULTAR PARA GARANTIA AUTOMATICA
    function consultarObjeto($filtro){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> consultar2($filtro));
        $objetos = array();
        while ($resultado = $this -> conexion -> extraer()){
            array_push($objetos, new Garantia("","", "" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,$resultado[0] ));

        }
        
        $this -> conexion -> cerrar();
        return $objetos;

    }


/////////////////////////

/////CONSULTAR PARA EXTRACCION DE PRODUCTOS DE LAS ORDENES
function consultarOrdenes($filtro){
    $this -> conexion -> abrir();
    $this -> conexion -> ejecutar($this -> garantiasDAO -> consultar3($filtro));
    $ordenes = array();
    while ($resultado = $this -> conexion -> extraer()){
        array_push($ordenes, new Garantia("","", "" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"" ,"" ,"" ,"" ,"", "" ,"" ,"" ,$resultado[0] , "" ,"" ,"" ,"","", "", "", $resultado[13],"" ));

    }
    
    $this -> conexion -> cerrar();
    return $ordenes;

}

/////CONSULTAR PARA EXTRACCION DE NACIONALIDAD
function consultarNacionalidad($filtro){
    $this -> conexion -> abrir();
    $this -> conexion -> ejecutar($this -> garantiasDAO -> consultar4($filtro));
    $nacionalidades = array();
    while ($resultado = $this -> conexion -> extraer()){
        array_push($nacionalidades, new Garantia("","", "" ,$resultado[2] ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"",$resultado[3],$resultado[0], $resultado[1] ));

    }
    
    $this -> conexion -> cerrar();
    return $nacionalidades;

}

/////CONSULTAR COUNT
function consultarCount(){
    $this -> conexion -> abrir();
    $this -> conexion -> ejecutar($this -> garantiasDAO -> consultar5());
    $counts = array();
    while ($resultado = $this -> conexion -> extraer()){
        array_push($counts, new Garantia("","", "" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"" ,"" ,"" ,"" ,"", "" ,"" ,"" ,"" , "" ,"" ,"" ,"","", "", "", "", $resultado[0] ));

    }
    
    $this -> conexion -> cerrar();
    return $counts;

}

/////////////////////////

    function consultarTodoOrden($order, $dir){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> garantiasDAO -> consultarTodoOrden($order, $dir));
        $productos = array();
        while ($resultado = $this -> conexion -> extraer()){
            array_push($productos, new Producto($resultado[0],$resultado[1], $resultado[2], $resultado[3] ));

        }
        
        $this -> conexion -> cerrar();
        return $productos;

    }

    function buscar($filtro){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> productoDAO -> buscar($filtro));
        $productos = array();
        while ($resultado = $this -> conexion -> extraer()){
            array_push($productos, new Producto($resultado[0],$resultado[1], $resultado[2], $resultado[3] ));

        }
        
        $this -> conexion -> cerrar();
        return $productos;

    }

    function eliminar(){
        $this -> conexion -> abrir();
        $this -> conexion -> ejecutar($this -> productoDAO -> eliminar());
        $this -> conexion -> cerrar();

    }




}
?>