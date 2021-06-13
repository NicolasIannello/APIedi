<?php
    class UsuarioController{

        function obtenerTodos($request,$response,$arg){

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
            $consulta->execute();
            $usuarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            $band=false;

            foreach($usuarios as $usuario){
                if($arg["user"]==$usuario->NombreUsuario && $arg["contra"]==$usuario->Contraseña ){
                    $band=true;
                }
            }

            //echo $arg["user"]."<br>".$arg["contra"]."<br>";
            $response->getBody()->Write(json_encode($band));
            return $response;
        }
    }
    
?>