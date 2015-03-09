<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class ReportesSucursales extends ActiveRecord {
    /*
     * Función que permite obtener la duracion de cada registro por dia de turnos
    */
    public function duracionAtencion($dia, $condicion) {
        $fun= new funciones();
        $db = DbBase::rawConnect();
        $cont_reg=0;
        $total_segundos=0;
        $result = $db->query("SELECT duracion FROM sincturnos WHERE DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $cont_reg+=1;
            $total_segundos+=$fun->totalSegundos($row['duracion']); //retorna la duracion en segundos
        }
        return array($cont_reg,$total_segundos);
    }
    /*
     * Función que permite obtener el promedio de llamada por cada día
    */
    public function promedioLlamada($dia, $condicion) {
        $fun= new funciones();
        $db = DbBase::rawConnect();
        $cont_reg=0;
        $total_segundos=0;
        //$duracion_promedio=0;
        $result = $db->query("SELECT hora_emision, hora_inicio_atencion FROM sincturnos WHERE DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $cont_reg+=1;
            $total_segundos_ini_atencion=$fun->totalSegundos($row['hora_inicio_atencion']); //retorna la duracion en segundos
            $total_segundos_emision=$fun->totalSegundos($row['hora_emision']); //retorna la duracion en segundos
            if (($total_segundos_ini_atencion-$total_segundos_emision)>=0)
                $total_segundos+=$total_segundos_ini_atencion-$total_segundos_emision;
        }
        //$duracion_promedio=$fun->tiempo(round($duracion/$cont_reg));
        return array($cont_reg,$total_segundos);
    }
    /*
     * Función que permite obtener la duracion de cada registro por dia de colas
    */
    public function duracionAtencionColas($dia, $condicion) {
        $fun= new funciones();
        $db = DbBase::rawConnect();
        $cont_reg=0;
        $total_segundos=0;
        $result = $db->query("SELECT duracion FROM caja c, colas co, usercaja uc, usuario u WHERE c.id=co.caja_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_inicio_atencion)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $cont_reg+=1;
            $total_segundos+=$fun->totalSegundos($row['duracion']); //retorna la duracion en segundos
        }
        return array($cont_reg,$total_segundos);
    }

    /*
     * Funcion que permite obtener el total de turnos atendidos por módulo y servicio
    */
    public function totalTurnosAtendidosPorDia($dia, $condicion) {
        $tot_t_atendidos_x_dia=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_dia FROM sincturnos WHERE DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_atendidos_x_dia= $row['total_dia'];
        }
        return $tot_t_atendidos_x_dia;
    }
    /*
     * Funcion que permite obtener el total de turnos anulados por módulo y servicio
    */
    public function totalTurnosAnuladosPorDia($dia, $condicion) {
        $tot_t_anulados_x_dia=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_dia FROM sincturnos WHERE DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_anulados_x_dia= $row['total_dia'];
        }
        return $tot_t_anulados_x_dia;
    }

    /*
     * Funcion que permite obtener el total de turnos no atendidos, es decir = NULL
    */
    /*public function totalTurnosNoAtendidosPorDia($dia, $condicion) {
        $tot_t_no_atendidos_x_dia=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_anulados_x_dia= $row['total_dia'];
        }
        return $tot_t_anulados_x_dia;
    }*/

    /*
     * Función que permite obtener la duracion de cada registro por dia y por hora
    */
    public function duracionAtencionHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin, $id_servicio, $forma_duracion) {
        //        if ($id_servicio==0) //todos los servicios
        //            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0";
        //        else
        $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0 AND s.id IN ($id_servicio) AND duracion>= '$forma_duracion'" ;

        $fun= new funciones();
        $db = DbBase::rawConnect();
        $cont_reg=0;
        $total_segundos=0;
        $result = $db->query("SELECT duracion FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $cont_reg+=1;
            $total_segundos+=$fun->totalSegundos($row['duracion']); //retorna la duracion en segundos
        }
        return array($cont_reg,$total_segundos);
    }
    /*
     * Función que permite obtener el total de turnos atendidos
    */
    public function totalTurnosAtendidosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin, $id_servicio, $forma_duracion) {
        //SELECT * FROM caja c, turnos t, servicio s, usercaja uc, usuario u
        //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
        //AND DAYNAME(fecha_emision)='Monday' AND fecha_inicio_atencion BETWEEN '2010-11-01' AND '2010-11-24'
        //AND hora_inicio_atencion BETWEEN '15:00:00' AND '15:59:59' AND c.id= 66 AND atendido=1 AND rechazado=0 ;

        //        if ($id_servicio==0) //todos los servicios
        //            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0";
        //        else
        $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0 AND s.id IN ($id_servicio) AND duracion>= '$forma_duracion'" ;

        $tot_t_atendidos_x_hora=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_hora FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_atendidos_x_hora= $row['total_hora'];
        }
        return $tot_t_atendidos_x_hora;
    }
    /*
     * Función que permite obtener el total de turnos anulados
    */
    public function totalTurnosAnuladosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin, $id_servicio, $forma_duracion) {
        //        if ($id_servicio==0) //todos los servicios
        //            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=1";
        //        else
        $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=1 AND s.id IN ($id_servicio) AND duracion>= '$forma_duracion'" ;
        $tot_t_anulados_x_hora=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_hora FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_anulados_x_hora= $row['total_hora'];
        }
        return $tot_t_anulados_x_hora;
    }
    /*
     * Funcion que permite obtener el total de colas atendidos por caja
    */
    public function totalColasAtendidosPorDia($dia, $condicion) {
        $tot_t_atendidos_x_dia=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, colas co, usercaja uc, usuario u WHERE c.id=co.caja_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_inicio_atencion)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_atendidos_x_dia= $row['total_dia'];
        }
        return $tot_t_atendidos_x_dia;
    }
    /*
     * Función que permite obtener el total de colas atendidos por los cajeros
    */
    public function totalColasAtendidosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini, $hora_fin) {
        //SELECT * FROM caja c, turnos t, servicio s, usercaja uc, usuario u
        //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
        //AND DAYNAME(fecha_emision)='Monday' AND fecha_inicio_atencion BETWEEN '2010-11-01' AND '2010-11-24'
        //AND hora_inicio_atencion BETWEEN '15:00:00' AND '15:59:59' AND c.id= 66 AND atendido=1 AND rechazado=0 ;
        //if ($id_servicio==0) //todos los servicios
        $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1";
        //else
        //$condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0 AND s.id= $id_servicio" ;

        $tot_t_atendidos_x_hora=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_hora FROM caja c, colas co, usercaja uc, usuario u WHERE c.id=co.caja_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_inicio_atencion)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $tot_t_atendidos_x_hora= $row['total_hora'];
        }
        return $tot_t_atendidos_x_hora;
    }
    /*
     * Función que permite obtener la duracion de cada registro por dia y por hora de colas
    */
    public function duracionAtencionColasHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin) {
        //if ($id_servicio==0) //todos los servicios
        $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1";
        //else
        //$condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_modulo AND hora_inicio_atencion BETWEEN '$hora_ini' AND '$hora_fin' AND atendido=1 AND rechazado=0 AND s.id= $id_servicio" ;

        $fun= new funciones();
        $db = DbBase::rawConnect();
        $cont_reg=0;
        $total_segundos=0;
        $result = $db->query("SELECT duracion FROM caja c, colas co, usercaja uc, usuario u WHERE c.id=co.caja_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_inicio_atencion)='$dia' AND $condicion");
        while($row = $db->fetchArray($result)) {
            $cont_reg+=1;
            $total_segundos+=$fun->totalSegundos($row['duracion']); //retorna la duracion en segundos
        }
        return array($cont_reg,$total_segundos);
    }
    /*
     * Funcion que permite obtener el primer turno según la fecha recibida
    */
    public function primerTurno($fecha) {
        $primer_turno=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT hora_emision FROM turnos WHERE fecha_emision='$fecha' ORDER BY hora_emision ASC LIMIT 1");
        while($row = $db->fetchArray($result)) {
            $primer_turno= $row['hora_emision'];
        }
        return $primer_turno;
    }
    /*
     * Funcion que permite obtener el ultimo turno según la fecha recibida
    */
    public function ultimoTurno($fecha) {
        $ultimo_turno=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT hora_emision FROM turnos WHERE fecha_emision='$fecha' ORDER BY hora_emision DESC LIMIT 1");
        while($row = $db->fetchArray($result)) {
            $ultimo_turno= $row['hora_emision'];
        }
        return $ultimo_turno;
    }
    /*
     * Funcion que permite obtener el total de turnos emitidos por el turnero
     * Parámetros: id_servicio y fecha
    */
    public function turnosEmitidos($id_servicio, $fecha) {
        $total_turnos=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) as total FROM turnos WHERE fecha_emision='$fecha' AND servicio_id=$id_servicio");
        while($row = $db->fetchArray($result)) {
            $total_turnos= $row['total'];
        }
        return $total_turnos;
    }
    /*
     * Funcion que permite obtener el total de turnos atendidos por el turnero
     * Parámetros: id_servicio y fecha
    */
    public function turnosAtendidos($id_servicio, $fecha) {
        $total_turnos=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) as total FROM turnos WHERE fecha_emision='$fecha' AND servicio_id=$id_servicio AND atendido=1 AND rechazado=0");
        while($row = $db->fetchArray($result)) {
            $total_turnos= $row['total'];
        }
        return $total_turnos;
    }
    /*
     * Funcion que permite obtener el total de turnos atendidos por el turnero
     * Parámetros: id_servicio y fecha
    */
    public function turnosAnulados($id_servicio, $fecha) {
        $total_turnos=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) as total FROM turnos WHERE fecha_emision='$fecha' AND servicio_id=$id_servicio AND (rechazado=1 OR atendido=0) AND caja_id IS NOT NULL");
        while($row = $db->fetchArray($result)) {
            $total_turnos= $row['total'];
        }
        return $total_turnos;
    }

    /*
     * Funcion que permite obtener el total de turnos no timbrados
     * Parámetros: id_servicio y fecha
     * Módulo: Reporte turnero
    */
    public function turnosNoTimbrados($id_servicio, $fecha) {
        $total_turnos=0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) as total FROM turnos WHERE fecha_emision='$fecha' AND servicio_id=$id_servicio AND caja_id IS NULL");
        while($row = $db->fetchArray($result)) {
            $total_turnos= $row['total'];
        }
        return $total_turnos;
    }

    /*
     * Funcion que permite obtener el promedio de calificacion de la matriz
     * Parámetros:
     * Menú: Estadísticas de calificaciones
    */
    public function promedioCalificacion($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion) {
        $total_turnos=0;
        $db = DbBase::rawConnect();
        $condicion="t.caja_id=$modulo_id AND servicio_id IN ($forma_condicion_servicios) AND preguntas_id=$pregunta_id
                        AND fecha BETWEEN '$desde' AND '$hasta' AND hora BETWEEN '$hora_i' AND '$hora_f'
                        AND duracion>='$forma_duracion'";
        $result = $db->query("SELECT AVG(puntuacion) as prom_puntos FROM turnos t, preguntas p, turnospreguntas tp
                        WHERE t.id=tp.turnos_id AND p.id=tp.preguntas_id AND $condicion");
        while($row = $db->fetchArray($result)) {
            $prom_puntos=$row['prom_puntos'];

        }
        return $prom_puntos;
    }

    /*
     * Funcion que permite obtener el numero de turnos calificados
     * Parámetros:
     * Menú: Estadísticas de calificaciones
    */
    public function turnosCalificados($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion) {
        $turnos_calificados=0;
        $db = DbBase::rawConnect();
        $condicion="t.caja_id=$modulo_id AND servicio_id IN ($forma_condicion_servicios) AND preguntas_id=$pregunta_id
                        AND fecha BETWEEN '$desde' AND '$hasta' AND hora BETWEEN '$hora_i' AND '$hora_f'
                        AND duracion>='$forma_duracion'";
        $result = $db->query("SELECT COUNT(*) as turnos_calificados FROM turnos t, preguntas p, turnospreguntas tp
                        WHERE t.id=tp.turnos_id AND p.id=tp.preguntas_id AND $condicion");
        while($row = $db->fetchArray($result)) {
            $turnos_calificados=$row['turnos_calificados'];

        }
        return $turnos_calificados;
    }

    /*
     * Función que permite retornar el nombre de la calificación según el promedio recibido
     * Recibir: array_calificacion, promedio
     * Retorno: nom_calificacion =>ejemplo Excelente, Muy Bueno
    */
    public function nombreCalificacion($array_calificacion, $promedio) {
        $nom_calificacion=0;
        foreach ($array_calificacion as $key=>$nombre) {
            if ($key==$promedio)
                $nom_calificacion=$nombre;
        }
        return $nom_calificacion;
    }
    /* encontrar los nombres de los modulos selecionadaos */
    public function nombremodulos($id_sucursal,$modulos_id) {
        $encontro=false;
        $nombre="";
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT usuario,numero_modulo,modulo_id_sucursal FROM sincturnos WHERE sucursal_id='$id_sucursal' AND modulo_id_sucursal='$modulos_id'");
        while($row = $db->fetchArray($result)) {
            if ($encontro==false)
                $nombre="M-".$row['usuario'];
            break;
        }
        return $nombre;
    }
    public function nombresucursales($id_sucursal) {
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT alias_sucursal FROM sucursal WHERE id=$id_sucursal");
        while($row = $db->fetchArray($result)) {
            $alias_sucursal =$row["alias_sucursal"];
        }
        return $alias_sucursal;
    }

    public function totalTunosModulos($querySucursal,$id_sucursal,$id_modulo) {
        $total=0;
        $id=trim($id_sucursal);
        foreach($querySucursal as $valor) {
            $modulo=trim($valor['Modulo_id_Sucursal']);
            if($id==trim($valor['sucursal_id'])&&trim($id_modulo)==$modulo)$total++;
        }
        return($total);
    }
}
?>
