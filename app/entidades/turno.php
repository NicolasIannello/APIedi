<?php

    class turno{

        public function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM PaqueteTurno");
            $consulta->execute();
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public function Eliminar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM PaqueteTurno WHERE PaqueteID=:id");

            $consulta->execute(array(':id'=>(int)$dat["dato"]));
        }

        public function Cargar($dat){
            
        }
    }

?>