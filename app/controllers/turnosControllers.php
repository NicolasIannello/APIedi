<?php
    class TurnoController{

        function decryptid($id){
            return openssl_decrypt ($id, "AES-128-CTR", "gdtonlineiannello", 0, '4831491486178994');
        }

        function crearTurnos($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            if(turno::Crear($datos)=="superpuesto"){
                $response->getBody()->Write(json_encode("superpuesto"));
                return $response;    
            }else{
                $turnos=turno::ObtenerTodos($datos);
                $response->getBody()->Write(json_encode($turnos));
                return $response;
            }
        }

        function cargarTurnos($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            $turnos=turno::ObtenerTodos($datos);
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function eliminarTurno($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            if(turno::Eliminar($datos)=="no encontrado"){
                $response->getBody()->Write(json_encode("no encontrado"));
                return $response;    
            }else{
                $turnos=turno::ObtenerTodos($datos);
                $response->getBody()->Write(json_encode($turnos));
                return $response;    
            }
        }

        function cargarCliente($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            $turnos=turno::clienteCargar($datos);
            if($turnos=="Usuario no encontrado"  || $turnos=="No se encontro un turno disponible" || $turnos=="Ya existe un turno vinculado a esa cuenta en dicho horario"){
                $response->getBody()->Write(json_encode($turnos));
                return $response;
            }else{
                $turnos=turno::ObtenerClientes($datos);
                $response->getBody()->Write(json_encode($turnos));
                return $response;
            }
        }

        function tablaCliente($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            $turnos=turno::ObtenerClientes($datos);
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function traernom($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
            $turnos=turno::traernom($datos);
            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }
    }
?>