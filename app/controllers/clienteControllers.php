<?php
    class ClienteController{

        function decryptid($id){
            return openssl_decrypt ($id, "AES-128-CTR", "gdtonlineiannello", 0, '4831491486178994');
        }

        function cargar($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['ID']=$this->decryptid($datos['ID']);
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
            $datos['ID']=$this->decryptid($datos['ID']);
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
            $datos['ID']=$this->decryptid($datos['ID']);
            $res= cliente::crear($datos);
            $response->getBody()->Write(json_encode($res));
            return $response;
        }

        function eliminar($request,$response,$arg){
            $datos= $request->getParsedBody();
            $datos['IDclie']=$this->decryptid($datos['IDclie']);
            $res= cliente::eliminar($datos);
            $response->getBody()->Write(json_encode($res));
            return $response;
        }
    }

?>