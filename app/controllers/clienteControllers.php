<?php
    class ClienteController{

        function cargar($request,$response,$arg){
            $datos= $request->getParsedBody();
            $turnos= cliente::obtenerTodos($datos);
            $response->getBody()->Write(json_encode($turnos));
            
            return $response;
        }

        function buscarservicios($request,$response,$arg){
            $datos= $request->getParsedBody();
            $turnos= cliente::buscarservicios($datos);
            $response->getBody()->Write(json_encode($turnos));
            
            return $response;
        }

        function diaservicio($request,$response,$arg){
            $datos= $request->getParsedBody();
            $dias= cliente::diaservicio($datos);
            $response->getBody()->Write(json_encode($dias));
            
            return $response;
        }

        function traernom($request,$response,$arg){
            $datos= $request->getParsedBody();
            $nom= cliente::traernom($datos);
            $response->getBody()->Write(json_encode($nom));
            return $response;
        }

        function horarios($request,$response,$arg){
            $datos= $request->getParsedBody();
            $res= cliente::horarios($datos);
            $response->getBody()->Write(json_encode($res));
            return $response;
        }

        function crear($request,$response,$arg){
            $datos= $request->getParsedBody();
            $res= cliente::crear($datos);
            $response->getBody()->Write(json_encode($res));
            return $response;
        }

        function eliminar($request,$response,$arg){
            $datos= $request->getParsedBody();
            $res= cliente::eliminar($datos);
            $response->getBody()->Write(json_encode($res));
            return $response;
        }
    }
    
?>