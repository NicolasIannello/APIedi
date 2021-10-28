<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    use Twilio\Rest\Client; 

    class cliente{

        public static function traernom($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT US.NombreUsuario 
            FROM Usuarios AS US JOIN Clientes AS CL ON US.UsuarioID=CL.UsuarioID WHERE CL.ClienteID=:id");
            $consulta->execute(array(':id'=>$datos['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function ObtenerTodos($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TCE.tceID AS 'Codigo',TU.Dia AS 'Fecha',US.NombreUsuario AS 'Comercio',SE.Descripcion AS 'Servicio',
            TU.Horario AS 'Horario Turno',EM.Ubicacion AS 'Ubicacion' 
            FROM Turno AS TU ,TurnoClienteEmpresa AS TCE,PaqueteTurno AS PT,
            Servicios AS SE ,Empresa AS EM,Usuarios AS US 
            WHERE TCE.ClienteID=:emp && EM.UsuarioID=US.UsuarioID && EM.EmpresaID=PT.EmpresaID && PT.PaqueteID=TU.PaqueteID &&
            TU.TurnoID=TCE.TurnoID && PT.ServicioID=SE.ServicioID");
            $consulta->execute(array(':emp'=>$datos['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }
    
        public static function buscarservicios($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT distinct EM.Ubicacion,US.NombreUsuario FROM Usuarios AS US,
            Localidades AS LC JOIN Empresa AS EM ON LC.LocalidadID=EM.LocalidadID 
            LEFT JOIN PaqueteTurno AS PT ON PT.EmpresaID=EM.EmpresaID
            WHERE EM.LocalidadID=:loc && LC.LocalidadID=:loc2 && EM.UsuarioID=US.UsuarioID && PT.ServicioID=:serv GROUP BY PT.PaqueteID");
            $consulta->execute(array(':loc'=>$datos['loc'],':loc2'=>$datos['loc'],':serv'=>$datos['serv']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function diaservicio($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT distinct TU.Dia FROM Turno AS TU JOIN PaqueteTurno AS PT ON TU.PaqueteID=PT.PaqueteID 
            JOIN Empresa AS EM ON PT.EmpresaID=EM.EmpresaID JOIN Usuarios AS US ON US.UsuarioID=EM.UsuarioID 
            WHERE PT.ServicioID=:serv && US.NombreUsuario=:nom && TU.Cupos>0 ORDER by TU.Dia");
            $consulta->execute(array(':serv'=>$datos['serv'],':nom'=>$datos['nom']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function horarios($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT distinct TU.Horario, PT.DuracionMinima 
            FROM PaqueteTurno AS PT,Turno AS TU ,
            Usuarios AS US ,Empresa AS EM 
            WHERE TU.Dia=:fecha && US.NombreUsuario=:nom && US.UsuarioID=EM.UsuarioID &&
            EM.EmpresaID=PT.EmpresaID && TU.cupos>=1 && PT.PaqueteID=TU.PaqueteID ORDER by TU.Horario");
            $consulta->execute(array(':fecha'=>$datos['fecha'],':nom'=>$datos['nom']));
            $horarios=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $horarios;
        }

        public static function crear($datos){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM PaqueteTurno AS PT left JOIN Turno AS TU 
            ON TU.PaqueteID=PT.PaqueteID left JOIN Empresa AS EM ON PT.EmpresaID=EM.EmpresaID 
            JOIN Usuarios as US ON US.UsuarioID=EM.UsuarioID WHERE US.NombreUsuario=:us && TU.Dia=:dia 
            && TU.Horario=:horario && TU.cupos>=1 && PT.ServicioID=:serv");
            $consulta->execute(array(':us'=>$datos['nom'],':dia'=>$datos['fecha'],':horario'=>$datos['horario'],':serv'=>$datos['serv'] ));

            $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
            $res=count($fetcht);

            if($res==0){
                return "Se han agotado los cupos para ese horario";
            }else{
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("SELECT DISTINCT  TU.TurnoID FROM PaqueteTurno AS PT 
                left JOIN Turno AS TU ON TU.PaqueteID=PT.PaqueteID 
                left JOIN TurnoClienteEmpresa AS TCE ON TCE.TurnoID=TU.TurnoID left JOIN Clientes AS CL ON CL.ClienteID=TCE.ClienteID
                WHERE CL.ClienteID=:id && TU.Dia=:dia && TU.Horario=:horario");
                $consulta->execute(array(':id'=>$datos['ID'],':dia'=>$datos['fecha'],':horario'=>$datos['horario']));

                $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                $res=count($fetcht);
                if($res==0){
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM PaqueteTurno AS PT left JOIN Turno AS TU 
                    ON TU.PaqueteID=PT.PaqueteID left JOIN Empresa AS EM ON PT.EmpresaID=EM.EmpresaID 
                    JOIN Usuarios as US ON US.UsuarioID=EM.UsuarioID WHERE US.NombreUsuario=:us && TU.Dia=:dia 
                    && TU.Horario=:horario && TU.cupos>=1 && PT.ServicioID=:serv");
                    $consulta->execute(array(':us'=>$datos['nom'],':dia'=>$datos['fecha'],':horario'=>$datos['horario'],':serv'=>$datos['serv'] ));
                    $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                    $TurnoID=(int)$resultado[0];

                    $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO TurnoClienteEmpresa (ClienteID,TurnoID) VALUES (:clienteID,:turnoID)");
                    $consulta->execute(array(':clienteID'=>$datos['ID'],':turnoID'=>$TurnoID));

                    $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.Dia,TU.Horario,US.NombreUsuario,US.Email,EM.Ubicacion,SE.Descripcion 
                    FROM Turno AS TU, Usuarios AS US, PaqueteTurno AS PT, Empresa AS EM,Servicios as SE WHERE TU.TurnoID=:id && TU.PaqueteID=PT.PaqueteID &&
                    PT.EmpresaID=EM.EmpresaID && EM.UsuarioID=US.UsuarioID && PT.ServicioID=SE.ServicioID");
                    $consulta->execute(array(':id'=>$TurnoID));
                    $Demp=$consulta->fetchAll(PDO::FETCH_OBJ);

                    $consulta = $objAccesoDatos->prepararConsulta("SELECT US.NombreUsuario,US.Email,CL.Nombre,CL.Apellido 
                    FROM Usuarios AS US, Clientes AS CL WHERE CL.ClienteID=:id && CL.UsuarioID=US.UsuarioID");
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
            FROM TurnoClienteEmpresa AS TCE JOIN Turno AS TU ON TCE.TurnoID=TU.TurnoID JOIN PaqueteTurno AS PT ON TU.PaqueteID=PT.PaqueteID 
            JOIN Empresa AS EM oN PT.EmpresaID=EM.EmpresaID JOIN Usuarios AS US ON EM.UsuarioID=US.UsuarioID WHERE TCE.tceID=:id ');
            $consulta->execute(array(':id'=>$datos['IDtce']));
            $Dempresa=$consulta->fetchAll(PDO::FETCH_OBJ);

            $consulta=$objAccesoDatos->prepararConsulta('SELECT US.NombreUsuario,US.Email,CL.Nombre,CL.Apellido FROM TurnoClienteEmpresa AS TCE 
            JOIN Clientes AS CL ON CL.ClienteID=TCE.ClienteID JOIN Usuarios AS US ON CL.UsuarioID=US.UsuarioID WHERE TCE.tceID=:id ');
            $consulta->execute(array(':id'=>$datos['IDtce']));
            $Dcliente=$consulta->fetchAll(PDO::FETCH_OBJ);

            $consulta = $objAccesoDatos->prepararConsulta("SELECT TCE.TurnoID FROM TurnoClienteEmpresa AS TCE 
            WHERE TCE.tceID=:tce && TCE.ClienteID=:clie");
            $consulta->execute(array(':tce'=>$datos['IDtce'],':clie'=>$datos['IDclie']));
            $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
            $TurnoID=(int)$resultado[0];

            $consulta = $objAccesoDatos->prepararConsulta("DELETE TCE FROM TurnoClienteEmpresa AS TCE WHERE TCE.tceID=:tce");
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
            $mail->Body='Hola '.$clie[0]->Nombre.' <b>'.$clie[0]->NombreUsuario.'</b> '.$clie[0]->Apellido.', le informamos que su turno para el dia:<b> '.$emp[0]->Dia.'</b> en el horario de las <b> '.$emp[0]->Horario.'</b>en la ubicacion de<b>: '.$emp[0]->Ubicacion.'</b>para el comercio:<b>: '.$emp[0]->NombreUsuario.'</b> ha sido cancelado de manera exitosa.';
            $mail->AltBody='Hola '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', le informamos que su turno para el dia: '.$emp[0]->Dia.' en el horario de las  '.$emp[0]->Horario.' en la ubicacion de: '.$emp[0]->Ubicacion.'para el comercio: '.$emp[0]->NombreUsuario.' ha sido cancelado de manera exitosa.';
        
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
            $mail->Body='Hola '.$clie[0]->Nombre.' <b>'.$clie[0]->NombreUsuario.'</b> '.$clie[0]->Apellido.', le informamos que su turno para el dia:<b> '.$emp[0]->Dia.'</b> en el horario de las <b> '.$emp[0]->Horario.'</b> en:<b> '.$emp[0]->Ubicacion.'</b> para el comercio: <b>'.$emp[0]->NombreUsuario.'</b> ha sido solicitado con exito.';
            $mail->AltBody='Hola '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', le informamos que su turno para el dia: '.$emp[0]->Dia.' en el horario de las  '.$emp[0]->Horario.' en: '.$emp[0]->Ubicacion.' para el comercio:'.$emp[0]->NombreUsuario.'ha sido solicitado con exito.';
        
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
            //--------------------------------------------------------------------------------------------------
            $sid    = "ACea71c554ccddc543dc37e16e9e5b098a"; 
            $token  = "58a692f90d442fd5f62a5440e6de0fc0"; 
            $twilio = new Client($sid, $token); 
            $mensaje='Hola '.$clie[0]->Nombre.' '.$clie[0]->NombreUsuario.' '.$clie[0]->Apellido.', le informamos que su turno para el dia: '.$emp[0]->Dia.' en el horario de las  '.$emp[0]->Horario.' en: '.$emp[0]->Ubicacion.' para el comercio:'.$emp[0]->NombreUsuario.'ha sido solicitado con exito.';
            $message = $twilio->messages 
                            ->create("whatsapp:+5491161961478", // to 
                                    array( 
                                        "from" => "whatsapp:+14155238886",       
                                        "body" => $mensaje 
                                    ) 
                            ); 
            
            //print($message->sid);
        }
    }
?>