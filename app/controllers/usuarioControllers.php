<?php
    class UsuarioController{

        function obtenerTodos($request,$response,$arg){
            $usuarios= usuario::obtenerTodos();
            $band="Datos incorrectos";
            $datos= $request->getParsedBody();
            
            foreach($usuarios as $usuario){
                if($datos["user"]==$usuario->NombreUsuario && $datos["contra"]==$usuario->Contraseña ){
                    $band=$usuario->Tipo;
                }
            }

            $response->getBody()->Write(json_encode($band));
            return $response;
        }
    }
    
?>