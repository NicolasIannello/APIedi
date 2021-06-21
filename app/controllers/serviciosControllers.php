<?php
    class ServicioController{

        function ObtenerTodos($request,$response,$arg){
            
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Servicios");
            $consulta->execute();
            
            $respuesta=$consulta->fetchAll(PDO::FETCH_OBJ);
            $response->getBody()->Write(json_encode($respuesta));
            return $response;
        }
    }
?> 