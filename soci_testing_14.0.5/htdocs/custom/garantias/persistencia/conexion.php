<?php

class Conexion {
    private $mysqli;
    private $resultado;

    function abrir(){
        $this -> mysqli = new mysqli("localhost", "root", "", "dolibarr");
        $this -> mysqli -> set_charset("utf8");

    }
    function ejecutar($sentencia){
        $this -> resultado = $this -> mysqli -> query($sentencia);
    }

    function cerrar(){
        $this -> mysqli -> close();

    }

    function numFilas(){
        return ($this -> resultado != null) ?
                $this -> resultado -> num_rows : 0;

    }

    function extraer(){
        return $this -> resultado -> fetch_row();

    }

    function ultimoId(){
        return $this -> mysqli -> insert_id;
    }

}
?>