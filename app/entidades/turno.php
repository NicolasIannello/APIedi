<?php

    class turno{

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM PaqueteTurno");
            $consulta->execute();
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function Eliminar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM PaqueteTurno WHERE PaqueteID=:id");

            $consulta->execute(array(':id'=>(int)$dat["dato"]));
        }

        public static function Cargar($dat){

        }
    }

?>