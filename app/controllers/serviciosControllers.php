<?php
    class ServicioController{

        function ObtenerTodos($request,$response,$arg){
            $respuesta=servicio::ObtenerTodos();
            $response->getBody()->Write(json_encode($respuesta));
            return $response;
        }
    }
?> 