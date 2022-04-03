<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class turno{

        public static function ObtenerTodos($dat){
            $Fhoy = new DateTime(date("Y-n-j"));
            $Fhoy=$Fhoy->format("Y-n-j");
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.PaqueteID as 'Codigo agrupador',TU.TurnoID as 'ID Turno particular',TU.Dia as 'Fecha',SE.Descripcion as 'Servicio',TU.Horario as 'Horario Turno',TU.Cupos as 'Cupos disponibles' FROM Turno as TU,PaqueteTurno as PT,Servicios as SE WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=SE.ServicioID && PT.EmpresaID=:emp && TU.Dia>=:hoy ORDER BY TU.Dia");
            $consulta->execute(array(':emp'=>$dat["ID"],':hoy'=>$Fhoy));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function Eliminar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            switch($dat["tipo"]){
                case 'Codigo agrupador':
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM Turno WHERE PaqueteID=:dato GROUP BY PaqueteID ORDER BY PaqueteID");
                    $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                    $cant=count($fetcht);
                    if ($cant == 1) { 
                        //--------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta('SELECT US.Email, US.NombreUsuario, CL.Nombre, CL.Apellido, TU.Dia, TU.Horario FROM Usuarios as US, Clientes as CL, Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.PaqueteID=:dato && US.Tipo="cliente" && US.UsuarioID=CL.UsuarioID && CL.ClienteID=TCE.ClienteID');
                        $consulta->execute(array(':dato'=>$dat["dato"]));
                        $afectados=$consulta->fetchAll(PDO::FETCH_OBJ);
                        //---------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU,TCE FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID JOIN TurnoClienteEmpresa as TCE on TU.TurnoID=TCE.TurnoID WHERE PT.PaqueteID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE PT.PaqueteID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }else{
                        return "no encontrado";
                    }
                    break;
                case 'ID Turno particular':
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM Turno WHERE TurnoID=:dato");
                    $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        
                    $paquete=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                    if(count($paquete)>0){
                        $PID=(int)$paquete[0];
                    }else{
                        $PID=0;
                    }
                    
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT * FROM Turno WHERE PaqueteID=:dato");
                    $consulta->execute(array(':dato'=>$PID));

                    $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                    $cant=count($fetcht);
                    if ($cant == 1) { 
                        //--------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta('SELECT US.Email, US.NombreUsuario, CL.Nombre, CL.Apellido, TU.Dia, TU.Horario FROM Usuarios as US, Clientes as CL, Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.TurnoID=:dato && US.Tipo="cliente" && US.UsuarioID=CL.UsuarioID && CL.ClienteID=TCE.ClienteID');
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        $afectados=$consulta->fetchAll(PDO::FETCH_OBJ);
                        //---------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU,TCE FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE TU.TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }else if($cant==0){
                        return "no encontrado";
                    }else{
                        //--------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta('SELECT US.Email, US.NombreUsuario, CL.Nombre, CL.Apellido, TU.Dia, TU.Horario FROM Usuarios as US, Clientes as CL, Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.TurnoID=:dato && US.Tipo="cliente" && US.UsuarioID=CL.UsuarioID && CL.ClienteID=TCE.ClienteID');
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        $afectados=$consulta->fetchAll(PDO::FETCH_OBJ);
                        //---------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE TU,TCE FROM Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE TU FROM Turno as TU WHERE TU.TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }
                    break;
                case 'Fecha':
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT TU.PaqueteID FROM Turno as TU join PaqueteTurno as PT on TU.PaqueteID=PT.PaqueteID WHERE TU.Dia=:dato && PT.EmpresaID=:id");
                    $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$dat["ID"]));
                    
                    $paquete=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                    if(count($paquete)>0){
                        $PID=(int)$paquete[0];
                    }else{
                        $PID=0;
                    }
                    
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT Dia FROM Turno WHERE PaqueteID=:dato GROUP BY Dia ORDER BY Dia");
                    $consulta->execute(array(':dato'=>$PID));

                    $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                    $cant=count($fetcht);
                    if ($cant == 1) { 
                        //--------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta('SELECT US.Email, US.NombreUsuario, CL.Nombre, CL.Apellido, TU.Dia, TU.Horario FROM Usuarios as US, Clientes as CL, Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.Dia=:dato && US.Tipo="cliente" && TU.PaqueteID=:id && US.UsuarioID=CL.UsuarioID && CL.ClienteID=TCE.ClienteID');
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                        $afectados=$consulta->fetchAll(PDO::FETCH_OBJ);
                        //---------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU,TCE FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID JOIN TurnoClienteEmpresa as TCE on TU.TurnoID=TCE.TurnoID WHERE TU.Dia=:dato && PT.PaqueteID=:id");
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE TU.Dia=:dato && PT.PaqueteID=:id");
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                    }else if($cant==0){
                        return "no encontrado";
                    }else{
                        //--------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta('SELECT US.Email, US.NombreUsuario, CL.Nombre, CL.Apellido, TU.Dia, TU.Horario FROM Usuarios as US, Clientes as CL, Turno as TU JOIN TurnoClienteEmpresa as TCE ON TU.TurnoID=TCE.TurnoID WHERE TU.Dia=:dato && US.Tipo="cliente" && TU.PaqueteID=:id && US.UsuarioID=CL.UsuarioID && CL.ClienteID=TCE.ClienteID');
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                        $afectados=$consulta->fetchAll(PDO::FETCH_OBJ);
                        //---------------------------------
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE TU,TCE FROM Turno as TU JOIN TurnoClienteEmpresa as TCE on TU.TurnoID=TCE.TurnoID WHERE Dia=:dato && TU.PaqueteID=:id");
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE TU FROM Turno as TU WHERE Dia=:dato && TU.PaqueteID=:id");
                        $consulta->execute(array(':dato'=>$dat["dato"],':id'=>$PID));
                    }
                    break;
            }
            turno::ReportarEliminacion($afectados);
            //var_dump($afectados);
        }

        public static function Crear($dat){
            $emp=$dat['ID'];
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
    
            $selectsql="SELECT * FROM PaqueteTurno WHERE EmpresaID=:Emp && ServicioID=:serv && ( ";
            $arraysql=array(':Emp'=>$emp,':serv'=>$dat["servicio"]);

            $ant=false;
            if($dat["Monday"]=="true"){
                $selectsql.="Monday=:M ";
                $arraysql[':M']=$dat["Monday"];
                $ant=true;
            }
            if($dat["Tuesday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $ant=true;
                $selectsql.="Tuesday=:Tu ";
                $arraysql[':Tu']=$dat["Tuesday"];
            }
            if($dat["Wednesday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $ant=true;
                $selectsql.="Wednesday=:W ";
                $arraysql[':W']=$dat["Wednesday"];
            }
            if($dat["Thursday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $ant=true;
                $selectsql.="Thursday=:Th ";
                $arraysql[':Th']=$dat["Thursday"];
            }
            if($dat["Friday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $ant=true;
                $selectsql.="Friday=:F ";
                $arraysql[':F']=$dat["Friday"];
            }
            if($dat["Saturday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $ant=true;
                $selectsql.="Saturday=:Sa ";
                $arraysql[':Sa']=$dat["Saturday"];
            }
            if($dat["Sunday"]=="true"){
                if($ant==true){
                    $selectsql.="|| ";
                }
                $selectsql.="Sunday=:Su ";
                $arraysql[':Su']=$dat["Sunday"];
            }
            $selectsql.=") && ( ( HorarioInicio<=:HI && HorarioFin>=:HI2 ) || ( HorarioInicio<=:HF && HorarioFin>=:HF2 ) || ( HorarioInicio>=:HI3 && HorarioFin<=:HF3 ) )";
            $arraysql[':HI']=$dat["HoraInicio"];
            $arraysql[':HI2']=$dat["HoraInicio"];
            $arraysql[':HI3']=$dat["HoraInicio"];
            $arraysql[':HF']=$dat["HoraFin"];
            $arraysql[':HF2']=$dat["HoraFin"];
            $arraysql[':HF3']=$dat["HoraFin"];
           
            $consulta = $objAccesoDatos->prepararConsulta($selectsql);
            $consulta->execute($arraysql);
            $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
         
            $res=count($fetcht);
            if ($res == 0) { 
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                //$consulta = $objAccesoDatos->prepararConsulta("INSERT INTO PaqueteTurno (EmpresaID,ServicioID,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,DiaFinalizacion,DuracionMinima,DuracionMaxima,Capacidad,HorarioInicio,HorarioFin) VALUES (:Emp,:serv,:M,:Tu,:W,:Th,:F,:Sa,:Su,:DF,:DMi,:DMa,:C,:HI,:HF)");
                //$consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':DMa'=>$dat["DuracionMax"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO PaqueteTurno (EmpresaID,ServicioID,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,DiaFinalizacion,DuracionMinima,Capacidad,HorarioInicio,HorarioFin) VALUES (:Emp,:serv,:M,:Tu,:W,:Th,:F,:Sa,:Su,:DF,:DMi,:C,:HI,:HF)");
                $consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                $consulta = $objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM PaqueteTurno WHERE EmpresaID=:Emp && ServicioID=:serv && Monday=:M && Tuesday=:Tu && Wednesday=:W && Thursday=:Th && Friday=:F && Saturday=:Sa && Sunday=:Su && DiaFinalizacion=:DF && DuracionMinima=:DMi && Capacidad=:C && HorarioInicio=:HI && HorarioFin=:HF");
                $consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
                $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                $PID=(int)$resultado[0];

                $Fhoy = new DateTime(date("Y-n-j"));
                $Ffin = new DateTime($dat["FechaFin"]);
                $tiempo=(int)$dat["DuracionMin"]*60;
                $Hini=strtotime($dat["HoraInicio"]);
                $Hfin=strtotime($dat["HoraFin"]);
            
                for($i=$Fhoy; $i<=$Ffin; $i->modify('+1 day')){
                    if($i->format("l")=="Monday" && $dat["Monday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Tuesday" && $dat["Tuesday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Wednesday" && $dat["Wednesday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Thursday" && $dat["Thursday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Friday" && $dat["Friday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Saturday" && $dat["Saturday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                    if($i->format("l")=="Sunday" && $dat["Sunday"]=="true"){
                        $dia=$i->format("Y-n-j");
                        for( $j=$Hini; $j<=$Hfin; $j+=$tiempo) {
                            $horario=date("H:i",$j);
                            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO Turno (PaqueteID,Dia,Horario,Cupos) VALUES (:PID,:Dia,:Horario,:Cupos)");
                            $consulta->execute(array(':PID'=>$PID,':Dia'=>$dia,':Horario'=>$horario,':Cupos'=>$dat["Capacidad"]));
                        }
                    }
                }
            }else{ 
                return "superpuesto";
            }
        }

        public static function ReportarEliminacion($turnos){
            //$cant=count($turnos);
            foreach ($turnos as $cliente) {
                $mail = new PHPMailer(true);
                //http://www.google.com/accounts/DisplayUnlockCaptcha
                //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
                $mail->isSMTP();                                            
                $mail->Host='smtp.gmail.com';                       
                $mail->SMTPAuth=true;                                   
                //$mail->Username=getenv('mail'); 
                $mail->Username='GestorDeTurnosOnline@gmail.com';       
                //$mail->Password=getenv('mailpass');
                $mail->Password='gestordeturnoss';                       
                $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
                $mail->Port=465;                                    
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                    
                $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');

                $mail->addAddress($cliente->Email);
                $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
                $mail->isHTML(true);
                $mail->Subject='Se ha eliminado un turno';
                $mail->Body='<!DOCTYPE html><html lang="en"><head>
                <meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                <style>.Aspan{font-family: Arial, Helvetica, sans-serif;margin-left: 5%;}i{margin-left: 1%;}.header{background-color: rgb(52, 73, 94);color: rgb(236, 236, 236);font-size: 3rem;align-items: center;}#A{height: 4rem;}.container{border: solid;border-color: rgb(52, 73, 94);}.Bspan{margin-left: 3%;margin-right: 10%;margin-top: 1.5%;margin-bottom: 1.5%;}</style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
                </head>
                <body>
                <div class="container">
                    <div id="A" class="row header">
                        <i class="fas fa-map-marker-alt"><span class="Aspan">Gestor de Turnos</span></i>
                    </div>
                    <div id="B" class="row">
                        <span class="Bspan">
                        Hola '.$cliente->Nombre.' <b>'.$cliente->NombreUsuario.'</b> '.$cliente->Apellido.', le informamos que su turno para el 
                        dia:<b> '.$cliente->Dia.'</b> en el horario de las <b> '.$cliente->Horario.'</b> ha sido cancelado.
                        </span>
                    </div>
                </div>
                </body></html>';
                $mail->AltBody='Hola '.$cliente->Nombre.' '.$cliente->NombreUsuario.' '.$cliente->Apellido.', le informamos que su turno para el dia: '.$cliente->Dia.' en el horario de las  '.$cliente->Horario.' ha sido cancelado.';

                $mail->send();
            }
            /*for ($i=0; $i < $cant; $i++) { 
                $mail = new PHPMailer(true);
                //http://www.google.com/accounts/DisplayUnlockCaptcha
                //$mail->SMTPDebug  = SMTP::DEBUG_SERVER;                   
                $mail->isSMTP();                                            
                $mail->Host='smtp.gmail.com';                       
                $mail->SMTPAuth=true;                                   
                //$mail->Username=getenv('mail'); 
                $mail->Username='GestorDeTurnosOnline@gmail.com';       
                //$mail->Password=getenv('mailpass');
                $mail->Password='gestordeturnoss';                       
                $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;            
                $mail->Port=465;                                    
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                    
                $mail->setFrom('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');

                $mail->addAddress($turnos[$i]->Email);
                $mail->addReplyTo('GestorDeTurnosOnline@gmail.com', 'Gestor de Turnos');
                    
                $mail->isHTML(true);
                $mail->Subject='Se ha eliminado un turno';
                //$mail->Body='Hola '.$turnos[$i]->Nombre.' <b>'.$turnos[$i]->NombreUsuario.'</b> '.$turnos[$i]->Apellido.', le informamos que su turno para el dia:<b> '.$turnos[$i]->Dia.'</b> en el horario de las <b> '.$turnos[$i]->Horario.'</b> ha sido cancelado.';
                $mail->Body='<!DOCTYPE html><html lang="en"><head>
                <meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
                <style>.Aspan{font-family: Arial, Helvetica, sans-serif;margin-left: 5%;}i{margin-left: 1%;}.header{background-color: rgb(52, 73, 94);color: rgb(236, 236, 236);font-size: 3rem;align-items: center;}#A{height: 4rem;}.container{border: solid;border-color: rgb(52, 73, 94);}.Bspan{margin-left: 3%;margin-right: 10%;margin-top: 1.5%;margin-bottom: 1.5%;}</style>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
                </head>
                <body>
                <div class="container">
                    <div id="A" class="row header">
                        <i class="fas fa-map-marker-alt"><span class="Aspan">Gestor de Turnos</span></i>
                    </div>
                    <div id="B" class="row">
                        <span class="Bspan">
                        Hola '.$turnos[$i]->Nombre.' <b>'.$turnos[$i]->NombreUsuario.'</b> '.$turnos[$i]->Apellido.', le informamos que su turno para el 
                        dia:<b> '.$turnos[$i]->Dia.'</b> en el horario de las <b> '.$turnos[$i]->Horario.'</b> ha sido cancelado.
                        </span>
                    </div>
                </div>
                </body></html>';
                $mail->AltBody='Hola '.$turnos[$i]->Nombre.' '.$turnos[$i]->NombreUsuario.' '.$turnos[$i]->Apellido.', le informamos que su turno para el dia: '.$turnos[$i]->Dia.' en el horario de las  '.$turnos[$i]->Horario.' ha sido cancelado.';
        
                $mail->send();    
            }*/
        }

        public static function clienteCargar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta=$objAccesoDatos->prepararConsulta("SELECT CL.ClienteID FROM Usuarios as US, Clientes as CL WHERE US.NombreUsuario=:user && US.Tipo='cliente'");
            $consulta->execute(array(':user'=>$dat["cliente"]));

            $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
            $res=count($fetcht);

            if($res==0){
                return "Usuario no encontrado";
            }else{
                $consulta=$objAccesoDatos->prepararConsulta("SELECT CL.ClienteID FROM Usuarios as US, Clientes as CL WHERE US.NombreUsuario=:user && US.Tipo='cliente' && US.UsuarioID=CL.UsuarioID");
                $consulta->execute(array(':user'=>$dat["cliente"]));
                $resultado=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                $clienteID=(int)$resultado[0];
                

                $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM Turno as TU, PaqueteTurno as PT WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=:serv && TU.Dia=:dia && TU.Horario=:horario && TU.Cupos>=1 && PT.EmpresaID=:id");
                $consulta->execute(array(':serv'=>(int)$dat["servicio"],':dia'=>$dat["fecha"],':horario'=>$dat["time"],':id'=>$dat["ID"]));
                
                $resultado2=$consulta->fetchAll(PDO::FETCH_COLUMN, 0);
                if(count($resultado2)>0){
                    $turnoID=(int)$resultado2[0];
                }else{
                    $turnoID=0;
                }
                
                               
                $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.TurnoID FROM Turno as TU, PaqueteTurno as PT WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=:serv && TU.Dia=:dia && TU.Horario=:horario && TU.Cupos>=1 && PT.EmpresaID=:id");
                $consulta->execute(array(':serv'=>(int)$dat["servicio"],':dia'=>$dat["fecha"],':horario'=>$dat["time"],':id'=>$dat["ID"]));
                $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                $res2=count($fetcht);
                
                if($res2==0){
                    return "No se encontro un turno disponible";
                }else{
                    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM TurnoClienteEmpresa WHERE ClienteID=:clie && TurnoID=:turn");
                    $consulta->execute(array(':clie'=>$clienteID,':turn'=>$turnoID));
                    $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                    $res=count($fetcht);
                    if($res>0){
                        return "Ya existe un turno vinculado a esa cuenta en dicho horario";
                    }else{
                        $consulta = $objAccesoDatos->prepararConsulta("UPDATE `Turno` as TU, PaqueteTurno as PT SET TU.Cupos=TU.Cupos-1 WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=:serv && TU.Dia=:dia && TU.Horario=:horario && TU.Cupos>=1 && PT.EmpresaID=:id");
                        $consulta->execute(array(':serv'=>(int)$dat["servicio"],':dia'=>$dat["fecha"],':horario'=>$dat["time"],':id'=>$dat["ID"]));

                        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO TurnoClienteEmpresa (ClienteID,TurnoID) VALUES (:clienteID,:turnoID)");
                        $consulta->execute(array(':clienteID'=>$clienteID,':turnoID'=>$turnoID));
                    }
                }
            }
        }

        public static function ObtenerClientes($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
            SELECT DISTINCT  TU.Dia as 'Fecha', SE.Descripcion as 'Servicio', TU.Horario as 'Horario turno',
            US.NombreUsuario as 'Cliente' FROM Usuarios AS US, Empresa AS EM JOIN 
            PaqueteTurno AS PT ON EM.EmpresaID=PT.EmpresaID join 
            Turno AS TU ON TU.PaqueteID=PT.PaqueteID,Servicios AS SE,TurnoClienteEmpresa AS TCE, Clientes as CL
             WHERE TU.TurnoID=TCE.TurnoID && US.Tipo='cliente' && SE.ServicioID=PT.ServicioID && PT.EmpresaID=:emp
             && TCE.ClienteID=CL.ClienteID && CL.UsuarioID=US.UsuarioID");
            $consulta->execute(array(':emp'=>$dat['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }
        
        public static function traernom($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
            SELECT US.NombreUsuario FROM Usuarios AS US JOIN Empresa AS EM ON US.UsuarioID=EM.UsuarioID WHERE EM.EmpresaID=:id");
            $consulta->execute(array(':id'=>$dat['ID']));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }
    }

?>