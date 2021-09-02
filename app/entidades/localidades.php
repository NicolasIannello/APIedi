<?php

    class localidad{

        public $Descripcion;

        public function GetNombre(){
            return $this->Descripcion;
        }
        public function __Construct(){
            $this->Descripcion;
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Localidades");
            $consulta->execute();
            $loc=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $loc;
        }
    }
?>