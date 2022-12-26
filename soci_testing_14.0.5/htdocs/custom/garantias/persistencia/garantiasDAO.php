<?php

class GarantiasDAO {
    private $fk_soc;  # id de proveedor
    private $tms;  # tms
    private $type;
    private $reference;
    private $ref;
    private $fk_facture;
    private $fk_commande_fournisseur; # id de pedido
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
    private $fk_projet;    #id proyecto
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





    function __construct($pfk_soc = "", $ptms = "", $ptype = "", $preference = "", $pref = "", $pfk_facture = "", $pfk_commande_fournisseur = "",
     $pfk_client = "", $pfk_fournisseur = "", $pdate_reception = "", $pdate_operation = "", $pdate_facture = "", $pverify_code = "",
     $pcontrol_port = "", $pmac_address = "", $pmark = "", $pmodelo = "", $pserial = "", $pfk_projet = "",
     $pnational_type = "", $pweb_port = "", $pcategory_operation = "", $pfk_product = "", $pwarranty_time = "", $pstatus = "" , $pstatut = "", $pfk_object = "", $plabel = "",
     $Fk_country = "" , $pid_producto = "",  $pqty = "", $pcount = ""){
        
        $this -> fk_soc = $pfk_soc;
        $this -> tms = $ptms;
        $this -> type = $ptype;
        $this -> reference = $preference;
        $this -> ref = $pref;
        $this -> fk_facture = $pfk_facture;
        $this -> fk_commande_fournisseur = $pfk_commande_fournisseur;
        $this -> fk_client = $pfk_client;
        $this -> fk_fournisseur = $pfk_fournisseur;
        $this -> date_reception = $pdate_reception;
        $this -> date_operation = $pdate_operation;
        $this -> date_facture = $pdate_facture;
        $this -> verify_code = $pverify_code;
        $this -> control_port = $pcontrol_port;
        $this -> mac_address = $pmac_address;
        $this -> mark = $pmark;
        $this -> modelo = $pmodelo;
        $this -> serial = $pserial;
        $this -> fk_projet = $pfk_projet;
        $this -> national_type = $pnational_type;
        $this -> web_port = $pweb_port;
        $this -> category_operation = $pcategory_operation;
        $this -> fk_product = $pfk_product;
        $this -> warranty_time = $pwarranty_time;
        $this -> status = $pstatus;
        $this -> statut = $pstatut;
        $this -> fk_object = $pfk_object; # - 26
        $this -> label = $plabel; # - 27
        $this -> Fk_country = $pFk_country; # - 28
        $this -> id_producto = $pid_producto; # - 29
        $this -> qty = $pqty; # - 30
        $this -> count = $pcount; # - 31
        

    }


    function consultarSinProcesar($fecha_ini){
        return "SELECT t1.fk_soc, t1.tms, t1.rowid, t1.fk_projet FROM llx_commande_fournisseur t1
        LEFT JOIN llx_equipment_history t2 ON t1.rowid = t2.fk_commande_fournisseur
        WHERE (t2.fk_commande_fournisseur IS NULL) AND (t1.fk_statut=4 OR t1.fk_statut=5) AND (t1.tms>'$fecha_ini')";

    }
    //CONSULTA PARA GARANTIAS AUTOMATICAS
    function consultar2($filtro){
        /*return "select fk_object from llx_societe 
                      where (llx_societe.rowid = \"" . $this -> fk_soc . "\",
                      and llx_societe.rowid = llx_societe_extrafields.fk_object)";*/

        return "SELECT fk_object  FROM llx_societe, llx_societe_extrafields 
        WHERE  llx_societe.rowid =  '$filtro'
        AND llx_societe.rowid = llx_societe_extrafields.fk_object
        AND llx_societe_extrafields.gra = 1";
                      
    }

     //CONSULTA PARA EXTRAER LOS EQUIPOS DE LAS ORDENES DE COMPRA
     function consultar3($filtro){
        /*$query2="SELECT * FROM llx_commande_fournisseurdet WHERE fk_commande=$fk_commande_fournisseur";*/

        return "SELECT *  FROM llx_commande_fournisseurdet 
        WHERE  fk_commande =  '$filtro' ";
                      
    }

     //CONSULTA PARA EXTRAER NACIONALIDAD
       function consultar4($filtro){
        
        return "SELECT fk_country, rowid, ref, label  FROM llx_product
        WHERE  rowid=  '$filtro' ";
                      
    }

      //CONSULTA PARA EXTRAER CANTIDAD DE REGISTROS ACTUALES
      function consultar5(){
        
     
        return "SELECT COUNT(rowid)  FROM llx_equipment_history";
       
                      
    }



      //INSERTAR DATOS EN LLX_EQUIPMENT HISTORY
     // INSERT INTO llx_equipment_history (ref, reference, type, fk_product, fk_fournisseur, fk_commande_fournisseur, date_reception, fk_projet, national_type, status, statut) VALUES ('$ref', '$reference', '$label', '$idproduct', '$proveedor', '$fk_commande_fournisseur', '$fecha', '$projet', '$nacionalidad', 1, 0)";
      function insertar1($ref, $reference, $label2, $idproduct, $IdProveedor, $Idpedido, $fecha, $projet, $nac){
        return "insert into llx_equipment_history (ref, reference, type, fk_product, fk_fournisseur, fk_commande_fournisseur, date_reception, fk_projet, national_type, status, statut) 
                values ('$ref','$reference', '$label2', '$idproduct','$IdProveedor', '$Idpedido', '$fecha', '$projet', '$nac' , 1,0 )";
 
    }		

}
?>
