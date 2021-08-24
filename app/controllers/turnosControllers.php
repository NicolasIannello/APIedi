<?php
    class TurnoController{

        function crearTurnos($request,$response,$arg){
            $datos= $request->getParsedBody();
            if(turno::Crear($datos)=="superpuesto"){
                $response->getBody()->Write(json_encode("superpuesto"));
                return $response;    
            }else{
                $turnos=turno::ObtenerTodos();
                $response->getBody()->Write(json_encode($turnos));
                return $response;
            }
        }

        function cargarTurnos($request,$response,$arg){
            $turnos=turno::ObtenerTodos();
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function eliminarTurno($request,$response,$arg){
            $datos= $request->getParsedBody();
            if(turno::Eliminar($datos)=="no encontrado"){
                $response->getBody()->Write(json_encode("no encontrado"));
                return $response;    
            }else{
                $turnos=turno::ObtenerTodos();
                $response->getBody()->Write(json_encode($turnos));
                return $response;    
            }
        }

        function cargarCliente($request,$response,$arg){
            $datos= $request->getParsedBody();
            $res=turno::clienteCargar($datos);
            if($res==null){

            }else{
                $response->getBody()->Write(json_encode($res));
                return $response;
            }
        }

        function tablaCliente($request,$response,$arg){
            $turnos=turno::ObtenerClientes();
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }
    }
?>