<?php

    class turno{

        public static function ObtenerTodos(){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT TU.PaqueteID as 'Codigo agrupador',TU.TurnoID as 'ID Turno particular',TU.Dia as 'Fecha',SE.Descripcion as 'Servicio',TU.Horario as 'Horario Turno',TU.Cupos as 'Cupos disponibles' FROM Turno as TU,PaqueteTurno as PT,Servicios as SE WHERE TU.PaqueteID=PT.PaqueteID && PT.ServicioID=SE.ServicioID");
            $consulta->execute();
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);
            return $turnos;
        }

        public static function Eliminar($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            switch($dat["tipo"]){
                case 'PaqueteID':
                    $consulta=$objAccesoDatos->prepararConsulta("DELETE PT,TU FROM PaqueteTurno as PT JOIN Turno as TU ON PT.PaqueteID=TU.PaqueteID WHERE PT.PaqueteID=:dato");
                    $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    break;
                case 'TurnoID':
                    $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM Turno WHERE TurnoID=:dato");
                    $consulta->execute(array(':dato'=>(int)$dat["dato"]));
                    break;
                case 'Dia':
                    $consulta=$objAccesoDatos->prepararConsulta("DELETE FROM Turno WHERE Dia=:dato");
                    $consulta->execute(array(':dato'=>$dat["dato"]));
                    break;
            }
        }

        public static function Crear($dat){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO PaqueteTurno (EmpresaID,ServicioID,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,DiaFinalizacion,DuracionMinima,DuracionMaxima,Capacidad,HorarioInicio,HorarioFin) VALUES (:Emp,:serv,:M,:Tu,:W,:Th,:F,:Sa,:Su,:DF,:DMi,:DMa,:C,:HI,:HF)");
            $emp=1;
            $consulta->execute(array(':Emp'=>$emp,':serv'=>$dat["servicio"],':M'=>$dat["Monday"],':Tu'=>$dat["Tuesday"],':W'=>$dat["Wednesday"],':Th'=>$dat["Thursday"],':F'=>$dat["Friday"],':Sa'=>$dat["Saturday"],':Su'=>$dat["Sunday"],':DF'=>$dat["FechaFin"],':DMi'=>$dat["DuracionMin"],':DMa'=>$dat["DuracionMax"],':C'=>$dat["Capacidad"],':HI'=>$dat["HoraInicio"],':HF'=>$dat["HoraFin"]));
            //-------------------------------------------
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
        }
    }

?>