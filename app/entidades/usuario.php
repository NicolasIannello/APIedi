<?php

    class usuario{

        public $NombreUsuario;
        public $Email;
        public $Contra;
        public $Tipo;

        public function GetNombre(){
            return $this->NombreUsuario;
        }
        public function GetEmail(){
            return $this->Email;
        }
        public function GetContra(){
            return $this->Contra;
        }
        public function GetTipo(){
            return $this->Tipo;
        }
        public function __Construct(){
            $this->NombreUsuario;
            $this->Email;
            $this->Contra;
            $this->Tipo;
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Usuarios");
            $consulta->execute();
            $usuarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $usuarios;
        }

    }

?>