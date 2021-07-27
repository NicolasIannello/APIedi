<?php

    class servicio{

        public $Descripcion;

        public function GetNombre(){
            return $this->Descripcion;
        }
        public function __Construct(){
            $this->Descripcion;
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Servicios");
            $consulta->execute();
            $serv=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $serv;
        }
    }
?>