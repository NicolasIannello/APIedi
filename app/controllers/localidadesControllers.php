<?php
    class LocalidadesController{

        function ObtenerTodos($request,$response,$arg){
            $respuesta=localidad::ObtenerTodos();
            $response->getBody()->Write(json_encode($respuesta));
            return $response;
        }
    }
?> 