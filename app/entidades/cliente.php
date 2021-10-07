<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TCE.tceID AS 'Codigo',TU.Dia AS 'Fecha',US.NombreUsuario AS 'Comercio',SE.Descripcion AS 'Servicio',
            TU.Horario AS 'Horario Turno',EM.Ubicacion AS 'Ubicacion' 
            FROM turno AS TU ,turnoclienteempresa AS TCE,paqueteturno AS PT,
            servicios AS SE ,empresa AS EM,usuarios AS US 
            WHERE TCE.ClienteID=:emp && EM.UsuarioID=US.UsuarioID && EM.EmpresaID=PT.EmpresaID && PT.PaqueteID=TU.PaqueteID &&
            TU.TurnoID=TCE.TurnoID && PT.ServicioID=SE.ServicioID");
            $consulta->execute(array(':emp'=>$datos['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }
    
        public static function buscarservicios($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT PT.PaqueteID,EM.Ubicacion,US.NombreUsuario FROM usuarios AS US,
            localidades AS LC JOIN empresa AS EM ON LC.LocalidadID=EM.LocalidadID 
            LEFT JOIN paqueteturno AS PT ON PT.EmpresaID=EM.EmpresaID
            WHERE EM.LocalidadID=:loc && LC.LocalidadID=:loc2 && EM.UsuarioID=US.UsuarioID && PT.ServicioID=:serv GROUP BY PT.PaqueteID");
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT distinct TU.Horario, PT.DuracionMinima 
            FROM paqueteturno AS PT,turno AS TU ,
            usuarios AS US ,empresa AS EM 
            WHERE TU.Dia=:fecha && US.NombreUsuario=:nom && US.UsuarioID=EM.UsuarioID &&
            EM.EmpresaID=PT.EmpresaID && TU.cupos>=1 && PT.PaqueteID=TU.PaqueteID");
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
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("SELECT DISTINCT  TU.TurnoID FROM paqueteturno AS PT 
                left JOIN turno AS TU ON TU.PaqueteID=PT.PaqueteID 
                left JOIN turnoclienteempresa AS TCE ON TCE.TurnoID=TU.TurnoID left JOIN clientes AS CL ON CL.ClienteID=TCE.ClienteID
                WHERE CL.ClienteID=:id && TU.Dia=:dia && TU.Horario=:horario");
                $consulta->execute(array(':id'=>$datos['ID'],':dia'=>$datos['fecha'],':horario'=>$datos['horario']));

                $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                $res=count($fetcht);
                if($res==0){
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM paqueteturno AS PT left JOIN turno AS TU 
                    ON TU.PaqueteID=PT.PaqueteID left JOIN empresa AS EM ON PT.EmpresaID=EM.EmpresaID 
                    JOIN usuarios as US ON US.UsuarioID=EM.UsuarioID WHERE US.NombreUsuario=:us && TU.Dia=:dia 
                    && TU.Horario=:horario && TU.cupos>=1 && PT.ServicioID=:serv");
                    $consulta->execute(array(':us'=>$datos['nom'],':dia'=>$datos['fecha'],':horario'=>$datos['horario'],':serv'=>$datos['serv'] ));
                    $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                    $TurnoID=(int)$resultado[0];

                    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO TurnoClienteEmpresa (ClienteID,TurnoID) VALUES (:clienteID,:turnoID)");
                    $consulta->execute(array(':clienteID'=>$datos['ID'],':turnoID'=>$TurnoID));

                    $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.Dia,TU.Horario,US.NombreUsuario,US.Email,EM.Ubicacion,SE.Descripcion 
                    FROM turno AS TU, usuarios AS US, paqueteturno AS PT, empresa AS EM,servicios as SE WHERE TU.TurnoID=:id && TU.PaqueteID=PT.PaqueteID &&
                    PT.EmpresaID=EM.EmpresaID && EM.UsuarioID=US.UsuarioID && PT.ServicioID=SE.ServicioID");
                    $consulta->execute(array(':id'=>$TurnoID));
                    $Demp=$consulta->fetchAll(PDO::FETCH_OBJ);

                    $consulta = $objAccesoDatos->prepararConsulta("SELECT US.NombreUsuario,US.Email,CL.Nombre,CL.Apellido 
                    FROM usuarios AS US, clientes AS CL WHERE CL.ClienteID=:id && CL.UsuarioID=US.UsuarioID");
                    $consulta->execute(array(':id'=>$datos['ID']));
                    $Dclie=$consulta->fetchAll(PDO::FETCH_OBJ);

                    $consulta = $objAccesoDatos->prepararConsulta("UPDATE `Turno` as TU SET TU.Cupos=TU.Cupos-1 
                    WHERE TU.TurnoID=:id");
                    $consulta->execute(array(':id'=>$TurnoID));

                    cliente::informarturno($Dclie,$Demp);
                    return "Turno creado";
                }else{
                    return "Ya tienes un turno en ese horario";
                }
            }
        }
        public static function eliminar($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta=$objAccesoDatos->prepararConsulta('SELECT TU.Dia,TU.Horario,EM.Ubicacion,US.NombreUsuario,US.Email 
            FROM turnoclienteempresa AS TCE JOIN turno AS TU ON TCE.TurnoID=TU.TurnoID JOIN paqueteturno AS PT ON TU.PaqueteID=PT.PaqueteID 
            JOIN empresa AS EM oN PT.EmpresaID=EM.EmpresaID JOIN usuarios AS US ON EM.UsuarioID=US.UsuarioID WHERE TCE.tceID=:id ');
            $consulta->execute(array(':id'=>$datos['IDtce']));
            $Dempresa=$consulta->fetchAll(PDO::FETCH_OBJ);

            $consulta=$objAccesoDatos->prepararConsulta('SELECT US.NombreUsuario,US.Email,CL.Nombre,CL.Apellido FROM turnoclienteempresa AS TCE 
            JOIN clientes AS CL ON CL.ClienteID=TCE.ClienteID JOIN usuarios AS US ON CL.UsuarioID=US.UsuarioID WHERE TCE.tceID=:id ');
            $consulta->execute(array(':id'=>$datos['IDtce']));
            $Dcliente=$consulta->fetchAll(PDO::FETCH_OBJ);

            $consulta = $objAccesoDatos->prepararConsulta("SELECT TCE.TurnoID FROM turnoclienteempresa AS TCE 
            WHERE TCE.tceID=:tce && TCE.ClienteID=:clie");
            $consulta->execute(array(':tce'=>$datos['IDtce'],':clie'=>$datos['IDclie']));
            $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
            $TurnoID=(int)$resultado[0];

            $consulta = $objAccesoDatos->prepararConsulta("DELETE TCE FROM turnoclienteempresa AS TCE WHERE TCE.tceID=:tce");
            $consulta->execute(array(':tce'=>$datos['IDtce']));

            $consulta = $objAccesoDatos->prepararConsulta("UPDATE `Turno` as TU SET TU.Cupos=TU.Cupos+1 
            WHERE TU.TurnoID=:id");
            $consulta->execute(array(':id'=>$TurnoID));

            cliente::ReportarEliminacion($Dcliente,$Dempresa);
            return "Turno eliminado";
        }

        public static function ReportarEliminacion($clie,$emp){

            $mail = new PHPMailer(true);
            //http://www.google.com/accounts/DisplayUnlockCaptcha
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
            $mail->isSMTP();                                            
            $mail->Host='smtp.gmail.com';                       
            $mail->SMTPAuth=true;                                   
            //$mail->Username=getenv('mail'); 
            $mail->Username='GestorDeTurnosOnline@gmail.com';       
            //$mail->Password=getenv('mailpass');
            $mail->Password='gestordeturnos';                       
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port=465;                                    
                    
            $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
            
            $mail->addAddress($clie[0]->Email);
            $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
            $mail->isHTML(true);
            $mail->Subject='Se ha eliminado un turno';
            $mail->Body='Hola '.$clie[0]->Nombre.' <b>'.$clie[0]->NombreUsuario.'</b> '.$clie[0]->Apellido.', le informamos que su turno para el dia:<b> '.$emp[0]->Dia.'</b> en el horario de las <b> '.$emp[0]->Horario.'</b> ha sido cancelado de manera exitosa.';
            $mail->AltBody='Hola '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', le informamos que su turno para el dia: '.$emp[0]->Dia.' en el horario de las  '.$emp[0]->Horario.' ha sido cancelado de manera exitosa.';
        
            $mail->send();    
            //--------------------------------------------------------------------------------------------//--------------------------------------------------------------------------------------------
            $mail = new PHPMailer(true);
            //http://www.google.com/accounts/DisplayUnlockCaptcha
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
            $mail->isSMTP();                                            
            $mail->Host='smtp.gmail.com';                       
            $mail->SMTPAuth=true;                                   
            //$mail->Username=getenv('mail'); 
            $mail->Username='GestorDeTurnosOnline@gmail.com';       
            //$mail->Password=getenv('mailpass');
            $mail->Password='gestordeturnos';                       
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port=465;                                    
                    
            $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');

            $mail->addAddress($emp[0]->Email);
            $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
            $mail->isHTML(true);
            $mail->Subject='Se ha eliminado un turno';
            $mail->Body='Hola <b>'.$emp[0]->NombreUsuario.'</b> le informamos que el usuario: '.$clie[0]->Nombre.' <b>'.$clie[0]->NombreUsuario.'</b> '.$clie[0]->Apellido.', ha eliminado su turno para el dia:<b> '.$emp[0]->Dia.'</b> en el horario de las <b> '.$emp[0]->Horario.'</b>.';
            $mail->AltBody='Hola '.$emp[0]->NombreUsuario.'le informamos que el usuario: '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', ha eliminado su turno para el dia: '.$emp[0]->Dia.' en el horario de las '.$emp[0]->Horario.'.';
        
            $mail->send(); 
        }

        
        public static function informarturno($clie,$emp){

            $mail = new PHPMailer(true);
            //http://www.google.com/accounts/DisplayUnlockCaptcha
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
            $mail->isSMTP();                                            
            $mail->Host='smtp.gmail.com';                       
            $mail->SMTPAuth=true;                                   
            //$mail->Username=getenv('mail'); 
            $mail->Username='GestorDeTurnosOnline@gmail.com';       
            //$mail->Password=getenv('mailpass');
            $mail->Password='gestordeturnos';                       
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port=465;                                    
                    
            $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
            
            $mail->addAddress($clie[0]->Email);
            $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
            $mail->isHTML(true);
            $mail->Subject='Se ha solicitado un turno';
            $mail->Body='Hola '+$clie[0]->Nombre+' <b>'+$clie[0]->NombreUsuario+'</b> '+$clie[0]->Apellido+', le informamos que su turno para el dia:<b> '+$emp[0]->Dia+'</b> en el horario de las <b> '+$emp[0]->Horario+'</b> en:<b> '+$emp[0]->Ubicacion+'</b> para el comercio: <b>'+$emp[0]->NombreUsuario+'</b> ha sido solicitado con exito+';
            $mail->AltBody='Hola '+$clie[0]->Nombre+' '+$clie[0]->NombreUsuario+' '+$clie[0]->Apellido+', le informamos que su turno para el dia: '+$emp[0]->Dia+' en el horario de las  '+$emp[0]->Horario+' en: '+$emp[0]->Ubicacion+' para el comercio:'+$emp[0]->NombreUsuario+'ha sido solicitado con exito.';
        
            $mail->send();    
            //--------------------------------------------------------------------------------------------//--------------------------------------------------------------------------------------------
            $mail = new PHPMailer(true);
            //http://www.google.com/accounts/DisplayUnlockCaptcha
            //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
            $mail->isSMTP();                                            
            $mail->Host='smtp.gmail.com';                       
            $mail->SMTPAuth=true;                                   
            //$mail->Username=getenv('mail'); 
            $mail->Username='GestorDeTurnosOnline@gmail.com';       
            //$mail->Password=getenv('mailpass');
            $mail->Password='gestordeturnos';                       
            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port=465;                                    
                    
            $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');

            $mail->addAddress($emp[0]->Email);
            $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
            $mail->isHTML(true);
            $mail->Subject='Se ha solicitado un turno';
            $mail->Body='Hola <b>'.$emp[0]->NombreUsuario.'</b> le informamos que el usuario: '.$clie[0]->Nombre.' <b>'.$clie[0]->NombreUsuario.'</b> '.$clie[0]->Apellido.', ha solicitado un turno para el dia:<b> '.$emp[0]->Dia.'</b> en el horario de las <b> '.$emp[0]->Horario.'</b> para el servicio: <b>'.$emp[0]->Descripcion.'</b>.';
            $mail->AltBody='Hola '.$emp[0]->NombreUsuario.'le informamos que el usuario: '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', ha solicitado un turno para el dia: '.$emp[0]->Dia.' en el horario de las '.$emp[0]->Horario.' para el servicio: '.$emp[0]->Descripcion.'.';
        
            $mail->send(); 
        }
    }
?>