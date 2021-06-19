<?php
    class UsuarioController{

        function obtenerTodos($request,$response,$arg){

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Usuarios");
            $consulta->execute();
            $usuarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            $band=false;
            $datos= $request->getParsedBody();
            
            foreach($usuarios as $usuario){
                if($datos["user"]==$usuario->NombreUsuario && $datos["contra"]==$usuario->Contraseña ){
                    $band=true;
                }
            }

            //echo $datos["user"]."<br>".$datos["contra"]."<br>sadadsa";
            $response->getBody()->Write(json_encode($band));
            return $response;
        }
    }
    
?>