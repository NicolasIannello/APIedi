<?php

    class servicio{

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Servicios");
            $consulta->execute();
            $serv=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $serv;
        }

    }

?>