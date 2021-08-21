<?php

    class turno{

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.PaqueteID as 'Codigo agrupador',TU.TurnoID as 'ID Turno particular',TU.Dia as 'Fecha',SE.Descripcion as 'Servicio',TU.Horario as 'Horario Turno',TU.Cupos as 'Cupos disponibles' FROM Turno as TU,PaqueteTurno as PT,Servicios as SE WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=SE.ServicioID && PT.EmpresaID=:emp");
            $consulta->execute(array(':emp'=>1));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function Eliminar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            switch($dat["tipo"]){
                case 'PaqueteID':
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM Turno WHERE PaqueteID=:dato GROUP BY PaqueteID ORDER BY PaqueteID");
                    $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    $fetcht=$consulta->fetchAll(PDO::FETCH_OBJ);
                    $cant=count($fetcht);
                    if ($cant == 1) { 
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE PT.PaqueteID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }else{
                        return "no encontrado";
                    }
                    break;
                case 'TurnoID':
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
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE TU.TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }else if($cant==0){
                        return "no encontrado";
                    }else{
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM Turno WHERE TurnoID=:dato");
                        $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    }
                    break;
                case 'Dia':
                    $consulta=$objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM Turno WHERE Dia=:dato");
                    $consulta->execute(array(':dato'=>$dat["dato"]));
                    
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
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE TU.Dia=:dato");
                        $consulta->execute(array(':dato'=>$dat["dato"]));
                    }else if($cant==0){
                        return "no encontrado";
                    }else{
                        $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM Turno WHERE Dia=:dato");
                        $consulta->execute(array(':dato'=>$dat["dato"]));
                    }
                    break;
            }
        }

        public static function Crear($dat){
            $emp=1;
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
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO PaqueteTurno (EmpresaID,ServicioID,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,DiaFinalizacion,DuracionMinima,DuracionMaxima,Capacidad,HorarioInicio,HorarioFin) VALUES (:Emp,:serv,:M,:Tu,:W,:Th,:F,:Sa,:Su,:DF,:DMi,:DMa,:C,:HI,:HF)");
                $consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':DMa'=>$dat["DuracionMax"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                $consulta = $objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM PaqueteTurno WHERE EmpresaID=:Emp && ServicioID=:serv && Monday=:M && Tuesday=:Tu && Wednesday=:W && Thursday=:Th && Friday=:F && Saturday=:Sa && Sunday=:Su && DiaFinalizacion=:DF && DuracionMinima=:DMi && DuracionMaxima=:DMa && Capacidad=:C && HorarioInicio=:HI && HorarioFin=:HF");
                $consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':DMa'=>$dat["DuracionMax"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
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

        public static function ReportarEliminacion(){

        }
    }

?>