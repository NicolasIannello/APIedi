<?php

    class usuario{

        public $NombreUsuario;
        public $Email;
        public $Contra;
        public $Tipo;

        public function GetNombre(){
            return $this->NombreUsuario;
        }
        public function GetEmail(){
            return $this->Email;
        }
        public function GetContra(){
            return $this->Contra;
        }
        public function GetTipo(){
            return $this->Tipo;
        }
        public function __Construct(){
            $this->NombreUsuario;
            $this->Email;
            $this->Contra;
            $this->Tipo;
        }

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM Usuarios");
            $consulta->execute();
            $usuarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $usuarios;
        }

        public static function CrearEmpresa($dat){
            $tipo="empresa";
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Usuarios (NombreUsuario,Email,Contraseña,Tipo) VALUES (:us,:mail,:contra,:tipo)");
            $consulta->execute(array(':us'=>$dat["nomemp"],':mail'=>$dat["mailemp"],':contra'=>$dat["contraemp"],':tipo'=>$tipo));

            $consulta = $objAccesoDatos->prepararConsulta("SELECT UsuarioID FROM Usuarios WHERE NombreUsuario=:us && Email=:mail && Contraseña=:contra && Tipo=:tipo");
            $consulta->execute(array(':us'=>$dat["nomemp"],':mail'=>$dat["mailemp"],':contra'=>$dat["contraemp"],':tipo'=>$tipo));
            $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
            $UID=(int)$resultado[0];

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Empresa (UsuarioID,LocalidadID,Ubicacion) VALUES (:id,:loc,:ubi)");
            $consulta->execute(array('id'=>$UID,'loc'=>$dat['localidademp'],'ubi'=>$dat['ubicacion']));
            
            $response="Cuenta de empresa creada con exito";
            return $response;
        }

        public static function CrearCliente($dat){
            $tipo="cliente";
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Usuarios (NombreUsuario,Email,Contraseña,Tipo) VALUES (:us,:mail,:contra,:tipo)");
            $consulta->execute(array(':us'=>$dat["userclie"],':mail'=>$dat["mailclie"],':contra'=>$dat["passclie"],':tipo'=>$tipo));

            $consulta = $objAccesoDatos->prepararConsulta("SELECT UsuarioID FROM Usuarios WHERE NombreUsuario=:us && Email=:mail && Contraseña=:contra && Tipo=:tipo");
            $consulta->execute(array(':us'=>$dat["userclie"],':mail'=>$dat["mailclie"],':contra'=>$dat["passclie"],':tipo'=>$tipo));
            $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
            $UID=(int)$resultado[0];

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Clientes (UsuarioID,Nombre,Apellido) VALUES (:id,:nom,:ape)");
            $consulta->execute(array('id'=>$UID,'nom'=>$dat['nomclie'],'ape'=>$dat['apeclie']));
            
            $response="Cuenta de cliente creada con exito";
            return $response;
        }
    }

?>