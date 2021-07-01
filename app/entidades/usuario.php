<?php

    class usuario{

        public function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Usuarios");
            $consulta->execute();
            $usuarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $usuarios;
        }

    }

?>