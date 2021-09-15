<?php
    class cliente{

        public static function traernom($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT US.NombreUsuario 
            FROM usuarios AS US JOIN clientes AS CL ON US.UsuarioID=CL.UsuarioID WHERE CL.ClienteID=:id");
            $consulta->execute(array(':id'=>$datos['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function ObtenerTodos($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TCE.tceID AS 'Codigo',TU.Dia AS 'Fecha',US.NombreUsuario AS 'Comercio',
            SE.Descripcion AS 'Servicio',TU.Horario AS 'Horario Turno',EM.Ubicacion AS 'Ubicacion' 
            FROM turnoclienteempresa AS TCE,clientes AS CL, Servicios as SE,
            usuarios AS US INNER join empresa AS EM ON US.UsuarioID=EM.UsuarioID inner JOIN PaqueteTurno as PT ON EM.EmpresaID=PT.EmpresaID
            inner JOIN turno AS TU ON PT.PaqueteID=TU.PaqueteID
            WHERE CL.ClienteID=:emp && PT.ServicioID=SE.ServicioID && TCE.TurnoID=TU.TurnoID");
            $consulta->execute(array(':emp'=>$datos['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }
    
        public static function buscarservicios($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT PT.PaqueteID,EM.Ubicacion,US.NombreUsuario FROM empresa AS EM,usuarios AS US,
            paqueteturno AS PT JOIN turno AS TU ON PT.PaqueteID=TU.PaqueteID,localidades AS LC
            WHERE EM.LocalidadID=:loc && LC.LocalidadID=:loc2 && EM.UsuarioID=US.UsuarioID && PT.ServicioID=:serv GROUP BY US.NombreUsuario");
            $consulta->execute(array(':loc'=>$datos['loc'],':loc2'=>$datos['loc'],':serv'=>$datos['serv']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function diaservicio($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.Dia, TU.PaqueteID FROM turno AS TU JOIN paqueteturno AS PT ON TU.PaqueteID=PT.PaqueteID 
            JOIN empresa AS EM ON PT.EmpresaID=EM.EmpresaID JOIN usuarios AS US ON US.UsuarioID=EM.UsuarioID 
            WHERE PT.ServicioID=:serv && US.NombreUsuario=:nom GROUP BY TU.Dia");
            $consulta->execute(array(':serv'=>$datos['serv'],':nom'=>$datos['nom']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function horarios($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT distinct PT.HorarioInicio, PT.HorarioFin,PT.DuracionMinima,PT.DuracionMaxima 
            FROM paqueteturno AS PT JOIN turno AS TU ON PT.PaqueteID=TU.PaqueteID,
            usuarios AS US JOIN empresa AS EM ON US.UsuarioID=EM.UsuarioID 
            WHERE TU.Dia=:fecha && US.NombreUsuario=:nom");
            $consulta->execute(array(':fecha'=>$datos['fecha'],':nom'=>$datos['nom']));
            $horarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $horarios;
        }

        public static function crear($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM paqueteturno AS PT left JOIN turno AS TU 
            ON TU.PaqueteID=PT.PaqueteID left JOIN empresa AS EM ON PT.EmpresaID=EM.EmpresaID 
            JOIN usuarios as US ON US.UsuarioID=EM.UsuarioID WHERE US.NombreUsuario=:us && TU.Dia=:dia 
            && TU.Horario=:horario && TU.cupos>=1 && PT.ServicioID=:serv");
            $consulta->execute(array(':us'=>$datos['nom'],':dia'=>$datos['fecha'],':horario'=>$datos['horario'],':serv'=>$datos['serv'] ));

            $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
            $res=count($fetcht);

            if($res==0){
                return "Turno no encontrado";
            }else{
                $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM paqueteturno AS PT left JOIN turno AS TU 
                ON TU.PaqueteID=PT.PaqueteID left JOIN empresa AS EM ON PT.EmpresaID=EM.EmpresaID 
                JOIN usuarios as US ON US.UsuarioID=EM.UsuarioID WHERE US.NombreUsuario=:us && TU.Dia=:dia 
                && TU.Horario=:horario && TU.cupos>=1 && PT.ServicioID=:serv");
                $consulta->execute(array(':us'=>$datos['nom'],':dia'=>$datos['fecha'],':horario'=>$datos['horario'],':serv'=>$datos['serv'] ));
                $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                $TurnoID=(int)$resultado[0];

                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO TurnoClienteEmpresa (ClienteID,TurnoID) VALUES (:clienteID,:turnoID)");
                $consulta->execute(array(':clienteID'=>$datos['ID'],':turnoID'=>$TurnoID));

                return "Turno creado";
            }

        }
    }
?>