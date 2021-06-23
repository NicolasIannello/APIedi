<?php
    class TurnoController{

        function crearTurnos($request,$response,$arg){

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $datos= $request->getParsedBody();

            $consulta=$objAccesoDatos->prepararConsulta("SELECT PaqueteID FROM PaqueteTurno WHERE EmpresaID=:Emp && ServicioID=:serv && Monday=:M && Tuesday=:Tu && Wednesday=:W && Thursday=:Th && Friday=:F && Saturday=:Sa && Sunday=:Su && DiaFinalizacion=:DF && DuracionMinima=:DMi && DuracionMaxima=:DMa && Capacidad=:C && HorarioInicio=:HI && HorarioFin=:HF");
            $emp=1;
            $consulta->execute(array(':Emp'=>$emp,':serv'=>$datos["servicio"],':M'=>$datos["Monday"],':Tu'=>$datos["Tuesday"],':W'=>$datos["Wednesday"],':Th'=>$datos["Thursday"],':F'=>$datos["Friday"],':Sa'=>$datos["Saturday"],':Su'=>$datos["Sunday"],':DF'=>$datos["FechaFin"],':DMi'=>$datos["DuracionMin"],':DMa'=>$datos["DuracionMax"],':C'=>$datos["Capacidad"],':HI'=>$datos["HoraInicio"],':HF'=>$datos["HoraFin"]));
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);

            $band="No encontrados";
            /*
            foreach($turnos as $turno){
                if($datos["HoraInicio"]==$turno->HorarioInicio && $datos["HoraFin"]==$turno->HorarioFin){
                    $band="Turno ya existente";
                }
            }*/

            if($band=="No encontrados"){
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO PaqueteTurno (EmpresaID,ServicioID,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday,DiaFinalizacion,DuracionMinima,DuracionMaxima,Capacidad,HorarioInicio,HorarioFin) VALUES (:Emp,:serv,:M,:Tu,:W,:Th,:F,:Sa,:Su,:DF,:DMi,:DMa,:C,:HI,:HF)");
                $emp=1;
                $consulta->execute(array(':Emp'=>$emp,':serv'=>$datos["servicio"],':M'=>$datos["Monday"],':Tu'=>$datos["Tuesday"],':W'=>$datos["Wednesday"],':Th'=>$datos["Thursday"],':F'=>$datos["Friday"],':Sa'=>$datos["Saturday"],':Su'=>$datos["Sunday"],':DF'=>$datos["FechaFin"],':DMi'=>$datos["DuracionMin"],':DMa'=>$datos["DuracionMax"],':C'=>$datos["Capacidad"],':HI'=>$datos["HoraInicio"],':HF'=>$datos["HoraFin"]));
            }else{
                $response->getBody()->Write(json_encode($band));
                return $response;
            }
            
            $consulta=$objAccesoDatos->prepararConsulta("SELECT * FROM PaqueteTurno");
            $consulta->execute();
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);

            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }

        function cargarTurnos($request,$response,$arg){
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta=$objAccesoDatos->prepararConsulta("SELECT * FROM PaqueteTurno");
            $consulta->execute();
            $turnos=$consulta->fetchAll(PDO::FETCH_OBJ);

            $response->getBody()->Write(json_encode($turnos));
            return $response;
        }
    }
?>