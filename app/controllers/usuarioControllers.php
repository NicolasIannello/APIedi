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

        function crearEmpresa($request,$response,$arg){
            $datos= $request->getParsedBody();
            $usuarios= usuario::obtenerTodos();
            $datos= $request->getParsedBody();
            $band="vacio";
            
            foreach($usuarios as $usuario){
                if($datos["nomemp"]==$usuario->NombreUsuario){
                    $band="El nombre de usuario ingresado ya se encuentra vinculado a una cuenta";
                }
            }

            if($band=="vacio"){
                $band=usuario::CrearEmpresa($datos);
            }

            $response->getBody()->Write(json_encode($band));
            return $response;
        }

        function crearCliente($request,$response,$arg){
            $datos= $request->getParsedBody();
            $usuarios= usuario::obtenerTodos();
            $datos= $request->getParsedBody();
            $band="vacio";
            
            foreach($usuarios as $usuario){
                if($datos["userclie"]==$usuario->NombreUsuario){
                    $band="El nombre de usuario ingresado ya se encuentra vinculado a una cuenta";
                }
            }

            if($band=="vacio"){
                $band=usuario::CrearCliente($datos);
            }

            $response->getBody()->Write(json_encode($band));
            return $response;
        }
    }
    
?>