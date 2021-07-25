<?php
    class TurnoController{

        function crearTurnos($request,$response,$arg){
            $datos= $request->getParsedBody();
            turno::Crear($datos);
            $turnos=turno::ObtenerTodos();
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function cargarTurnos($request,$response,$arg){
            $turnos=turno::ObtenerTodos();
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function eliminarTurno($request,$response,$arg){
            $datos= $request->getParsedBody();
            turno::Eliminar($datos);
            $turnos=turno::ObtenerTodos();
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }
    }
?>