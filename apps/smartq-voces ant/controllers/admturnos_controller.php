<?php

/**
 * Controlador Display
 *
 * @access public
 * @version 1.0
 */
class AdmturnosController extends ApplicationController {

    /**
     * Inicializador del controlador
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $iduser = $dataUsuario->getId();      //id de usuario
    }

    /**
     * Accion por defecto del controlador/
     *
     */
    /* public function indexAction() {
      $this->setResponse('ajax');
      echo $this->listarGruposServiciosAction();
      } */

    /**
     * Inicio
     */
    public function inicioAction() {
        $this->setResponse('ajax');
        $this->render('admturnos/inicio');      //carga la vista
        //Tag::displayTo('por_atender', "Hola mundo");    //datos para la vista
    }

    public function listarGruposServiciosAction() {
        $this->setResponse('view');
        $html = "";
        $grupoServicio = new Gruposervicio();
        $buscaGrupoServicio = $grupoServicio->Find();
        $html.="<table border='0' margin='0' width='100%' cellspacing='0'>";
        foreach ($buscaGrupoServicio as $result) {
            $grupo_id = $result->getId();
            $nombre_grupo = $result->getNombreGrupoServicio();
            $html.="<tr><td class='td_grupo'><b>Grupo: </b>" . $nombre_grupo . "</td></tr>";
            $html.=$this->listarServicioPorGrupoAction($grupo_id);
        }
        $html.="</table>";
        echo $html;
    }

    /**
     * Funcion que permite listar todo los turnos por servicios
     */
    public function listarServicioPorGrupoAction($grupo) {
        //$this->setResponse("json");
        $html = "";

        $sql = new ActiveRecordJoin(array(
            "entities" => array("Servicio", "Gruposervicio", "Ubicacion"),
            "fields" => array(
                "{#Servicio}.id",
                "{#Servicio}.nombre",
                "{#Ubicacion}.nombre_ubicacion"
            ),
            //"conditions"=>"{#Caja}.estado=1 AND {#Servicio}.gruposervicio_id= $grupo")
            "conditions" => "{#Servicio}.gruposervicio_id= $grupo")
        );
        foreach ($sql->getResultSet() as $result) {
            $servicio_id = $result->getId();
            $nombre_servicio = $result->getNombre();
            $nombre_ubicacion = $result->getNombreUbicacion();

            $html.="<tr><td class='td_servicio'><b>Servicio: </b>" . $nombre_servicio . " | <b>Area o Ubicacion: </b>" . $nombre_ubicacion;
            $html.="&nbsp;&nbsp; <input type='image' src='../img/abrir.png' style='width:15px' title='Ver turnos' onclick='abrir_div_turnos($servicio_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../img/cerrar.png' style='width:15px' title='Ocultar turnos' onclick='cerrar_div_turnos($servicio_id)'>";
            $html.="&nbsp;&nbsp;|&nbsp;&nbsp;<input type='image' src='../img/check_all.gif' style='width:15px' title='Seleccionar todos los turnos' onclick='check_all($servicio_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../img/uncheck_all.gif' style='width:15px' title='Deseleccionar todos los turnos' onclick='uncheck_all($servicio_id)'> <br>";
            $html.="<div id='div_servicio_$servicio_id' name='div_servicio_$servicio_id'>";
            $html.=$this->listarTurnosTodos($servicio_id);
            $html.="</div>";
            $html.="</td><tr>";
        }

        //$datos= array("lista_turnos_todos"=>$html);
        //return ($datos);
        return $html;
    }

    /**
     * Lista todos los turnos.
     * 1era forma. Si tenemos solo una tabla que se llama turnos
     */
    public function listarTurnosTodos($servicio_id) {
        $fun = new Funciones();
        $fecha_hoy = date("Y-m-d");
        $hora_hoy = date("H:i:s");

        $html = "";

        $condition = "fecha_emision='$fecha_hoy' AND por_atender=0 AND atendido=0 AND transferido=0 AND {#Servicio}.id=$servicio_id";
        $sql = new ActiveRecordJoin(array(
            "entities" => array("Servicio", "Turnos"),
            "fields" => array(
                "{#Turnos}.id",
                "{#Servicio}.letra",
                "{#Servicio}.letra_alias",
                "{#Turnos}.numero",
                "{#Turnos}.fecha_emision",
                "{#Turnos}.hora_emision",
                "{#Turnos}.numero_alias",
                "{#Turnos}.prioridad"
            ),
            "conditions" => $condition)
        );

        /* $db = DbBase::rawConnect();
          $sql="SELECT turnos.id, letra, numero, fecha_emision, hora_emision FROM servicio, cola_".$servicio_id." turnos WHERE servicio.id=turnos.servicio_id AND fecha_emision='".$fecha_hoy."' AND por_atender=0 AND atendido=0 AND transferido=0";
          $result1=$db->query($sql); */

        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";
        $html.="<table border='1' cellspacing='0' cellpadding='0'> <tr> <th class='td_turno'>Turno</th> <th class='td_turno'>Tipo</th> <th class='td_turno'>Fecha emision</th> <th class='td_turno'>Hora emision</th> <th class='td_turno'>Tiempo esperando</th> </tr>";
        foreach ($sql->getResultSet() as $result) {
            //while($result = $db->fetchArray($result1)) {
            $turno_id = $result->getId();
            $letra_servicio = $result->getLetra();
            //$numero_turno = $fun->aumentaCerosTurno($result->getNumero());
            $fecha_emision = $result->getFechaEmision();
            $hora_emision = $result->getHoraEmision();
            //$turno = $letra_servicio . $numero_turno;

            $prioridad = $result->getPrioridad();
            $tipo = "";
            if ($prioridad == 1) {
                $tipo = "Preferencial";
                $turno = $result->getLetraAlias() . $fun->aumentaCerosTurno($result->getNumeroAlias());
                //$numero_turno = $fun->aumentaCerosTurno($result->getNumeroAlias());
            } else if ($prioridad == 0) {
                $tipo = "Normal";
                $turno = $result->getLetra() . $fun->aumentaCerosTurno($result->getNumero());
                //$numero_turno = $fun->aumentaCerosTurno($result->getNumero());
            }
            /* $turno_id       =$result['id'];
              $letra_servicio =$result['letra'];
              $numero_turno   =$fun->aumentaCerosTurno($result['numero']);
              $fecha_emision  =$result['fecha_emision'];
              $hora_emision   =$result['hora_emision']; */

            //$turno = $letra_servicio . $numero_turno;
            $fi = $fecha_emision . " " . $hora_emision;
            $ff = $fecha_hoy . " " . $hora_hoy;
            $duracion = $fun->difFecha($fi, $ff);
            $html.="<tr>";
            $html.="<td class='td_turno'><input type='checkbox' id='chk_turno' name='chk_turno' value='$turno_id' class='c_$servicio_id'/>Turno $turno </td>";
            $html.="<td class='td_turno'>$tipo</td>";
            $html.="<td class='td_turno'>$fecha_emision</td>";
            $html.="<td class='td_turno'>$hora_emision</td>";
            $html.="<td class='td_turno'>$duracion</td>";
            $html.="</tr>";
        }
        $html.="</fieldset>";
        $html.="</table>";
        return ($html);
    }

    /*
     * Funcion primero las ubicaciones de los Usuario para luego mostrar los modulos
     */

    public function listaOperadoresTodosAction() {
        $html = "";
        $this->setResponse("json");
        //$modulo_id= $this->getPostParam("modulo_id");

        $db = DbBase::rawConnect();
        //-- consulto la ubicacion para agrupar por ubicacion
        $result = $db->query("SELECT u.id as id, nombre_ubicacion FROM ubicacion u, caja c, servicio s, serviciocaja sc WHERE u.id=c.ubicacion_id AND s.id=sc.servicio_id AND c.id=sc.caja_id GROUP BY nombre_ubicacion;");
        $html.="<table border='0' margin='0' width='100%' cellspacing='0'>";
        while ($row = $db->fetchArray($result)) {
            $ubicacion_id = $row['id'];

            $html.="<tr><td class='td_servicio'><b>Area o Ubicacion: </b>" . $row['nombre_ubicacion'];
            $html.="&nbsp;&nbsp; <input type='image' src='../img/abrir.png' style='width:15px' title='Ver mÃ³dulos' onclick='abrir_div_operadores($ubicacion_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../img/cerrar.png' style='width:15px' title='Ocultar mÃ³dulos' onclick='cerrar_div_operadores($ubicacion_id)'>";
            $html.="&nbsp;&nbsp;|&nbsp;&nbsp;<input type='image' src='../img/check_all.gif' style='width:15px' title='Seleccionar todos los operadores' onclick='check_all_modulos($ubicacion_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../img/uncheck_all.gif' style='width:15px' title='Deseleccionar todos los operadores' onclick='uncheck_all_modulos($ubicacion_id)'> <br>";
            $html.="</td></tr>";
            $html.="<tr><td>";
            $html.="<div id='div_ubicacion_$ubicacion_id' name='div_ubicacion_$ubicacion_id'>";
            $html.=$this->listaOperadoresTodos($ubicacion_id);
            $html.="</div>";
            $html.="</td></tr>";
        }
        $html.="</table>";
        $datos = array("mensaje" => $html);
        return ($datos);
    }

    /*
     * Funcion que permite mostrar la lista de operadores que seleccionaria
     * para la tranferencia los turnos seleccionados
     * Retorna: lista de operadores
     */

    public function listaOperadoresTodos($ubicacion_id) {
        $html = "";
        $this->setResponse("json");

        $db = DbBase::rawConnect();
        $query = $db->query("SELECT c.id as caja_id, c.ubicacion_id, c.descripcion, c.numero_caja, nombre_ubicacion, u.nombres, u.login
            FROM caja c
            JOIN ubicacion ub ON ub.id=c.ubicacion_id
            LEFT JOIN usuario u ON u.id=c.usuario_actual
            LEFT JOIN grupousuario gu ON gu.usuario_id=u.id
            WHERE grupo_id=5 and ub.id=$ubicacion_id");

        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";
        $html.="<table border='1' cellspacing='0' cellpadding='0'>
            <tr>
            <th class='td_turno'>N Modulo</th>
            <th class='td_turno'>Descripacion modulo</th>
            <th class='td_turno'>Usuario</th>
            <th class='td_turno'>Ubicación</th>
            <th class='td_turno'>Estado login</th></tr>";
        //<th class='td_turno'>C.I. usuario</th> </tr>";
        while ($result = $db->fetchArray($query)) {
            $caja_id = $result['caja_id'];
            $numero_caja = $result['numero_caja'];
            $login = $result['login'];
            if ($login == 1)
                $login= '<font color="#01CF00"><b>Activo</b>';
            else
                $login= '<font color="red"><b>Inactivo</b>';
            $html.="<tr>";
            $html.="<td class='td_turno'><input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' class='c_modulos_$ubicacion_id' onclick='seleccionar($ubicacion_id)' />$numero_caja</td>";
            $html.="<td class='td_turno'>{$result['descripcion']}</td>";
            $html.="<td class='td_turno'>{$result['nombres']}</td>";
            $html.="<td class='td_turno'>{$result['nombre_ubicacion']}</td>";
            $html.="<td class='td_turno'>$login</td>";
            //$html.="<td class='td_turno'>{$result->getCi()}</td>";
            //$dato="<input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' class='c_modulos_$ubicacion_id' onclick='seleccionar($ubicacion_id)' />Módulo $numero_caja<br>";
            //$array_lista_operadores_aux[]=array('dato'=>$dato,'numero'=>$numero_caja);
            $html.="</tr>";
        }
        /* $fun= new Funciones();
          $array_lista_operadores= array();
          $array_lista_operadores= $fun->ordenarArrayMultidimensional($array_lista_operadores_aux,'numero',false);
          foreach($array_lista_operadores as $result) {
          $html.=$result['dato'];
          } */

        $html.="</table>";

        $html.="</fieldset>";

        return $html;
    }

    /*
     * Funcion que hace la transferencia de los turnos seleccionados
     */

    public function transferirTodosAction($id = null) {
        $this->setResponse("json");

        $fech = date("Y-m-d");
        $hora = date("H:i:s");

        $remitente = 0;
        $permisoCajas = $this->getPostParam("modulos_id");   //ids de los modulos a los que se les pasa
        $turnosId = $this->getPostParam("turnos_id");        //ids de los turnos que se les pasa
        $ubicacionesId = $this->getPostParam("ubicaciones_id");        //ids de los turnos que se les pasa

        $cont = strlen($turnosId);
        $turnosId = substr($turnosId, 0, strlen($turnosId) - 1);

        $a = "";
        $turnos = new Turnos();
        $condition = "{#Turnos}.id IN ($turnosId)";
        //$buscaTurno= $turnos->find("conditions: $condition");

        $sql = new ActiveRecordJoin(array(
            "entities" => array("Turnos", "Servicio"),
            "fields" => array(
                "{#Turnos}.id",
                "{#Turnos}.servicio_id",
                "{#Turnos}.numero",
                "{#Turnos}.numero_alias",
                "{#Turnos}.fecha_emision",
                "{#Turnos}.hora_emision",
                "{#Turnos}.prioridad",
                "{#Servicio}.letra",
                "{#Servicio}.letra_alias",
            ),
            "conditions" => "$condition")
        );
        foreach ($sql->getResultSet() as $result) {

            //foreach ($buscaTurno as $result) {
            $turnoId = $result->getId();
            $servicioId = $result->getServicioId();
            //$numero = $result->getNumero();
            $fechaEmision = $result->getFechaEmision();
            $horaEmision = $result->getHoraEmision();
            //$letra = $result->getLetra();
            $prioridad = $result->getPrioridad();

            if ($prioridad == 1) {
                $numero = $result->getNumeroAlias();
                $letra = $result->getLetraAlias();
            } else if ($prioridad == 0) {
                $numero = $result->getNumero();
                $letra = $result->getLetra();
            }

            //INICIO ACTUALIZO EL TURNO A TRANSFERIDO=1
            $turnos->updateAll("transferido= 1", "id= $turnoId");
            //FIN ACTUALIZO EL TURNO A TRANSFERIDO=1

            $turnosTransferidos = new TurnosTransferidos();
            $id = null;
            $turnosTransferidos->setId($id);
            $turnosTransferidos->setServicioId($servicioId);
            $turnosTransferidos->setNumero($numero);
            $turnosTransferidos->setFechaEmision($fechaEmision);
            $turnosTransferidos->setHoraEmision($horaEmision);
            $turnosTransferidos->setHoraTransferido($hora);
            $turnosTransferidos->setPorAtender(0);  //no
            $turnosTransferidos->setRechazado(0);   //no
            $turnosTransferidos->setAtendido(0);    //no
            $turnosTransferidos->setPermisoCajas($permisoCajas);
            $turnosTransferidos->setLetra($letra);
            $turnosTransferidos->setRemitente($remitente);
            $turnosTransferidos->setUbicacionId($ubicacionesId);
            $turnosTransferidos->setAdmRevisado(0);
            $turnosTransferidos->save();
        }
    }

    /**
     *
     */
    public function contarTurnosMalaCalificacionAction() {
        $fecha = date('Y-m-d');
        $this->setResponse("ajax");
        $turnos = new Turnos();
        $buscaTurnos = $turnos->find("calificacion='Regular' AND adm_revisado=0 AND fecha_emision='$fecha'");
        $cont_registros = 0;
        foreach ($buscaTurnos as $result) {
            $cont_registros+=1;
        }
//        if ($cont_registros > 0)
//            $cont_registros = $cont_registros . " Turnos con calificacion Regular";

        $turnos = new TurnosTransferidos();
        $buscaTurnosTransferidos = $turnos->find("calificacion='Regular' AND adm_revisado=0 AND fecha_emision='$fecha'");
        $cont_registros_t = 0;
        foreach ($buscaTurnosTransferidos as $result) {
            $cont_registros_t+=1;
        }
//        if ($cont_registros > 0)
//            $cont_registros = $cont_registros . " Turnos con calificacion Regular";
        if (($cont_registros + $cont_registros_t) > 0)
            $cont_registros = $cont_registros + $cont_registros_t . " Turnos con calificacion Regular";
        echo $cont_registros;
    }

    /**
     * Ve los Turnos con mala calificacion
     */
    public function verTurnoAction() {
        $this->setResponse("ajax");
        $calificacion = $this->getPostParam("calificacion");
        date_default_timezone_set('America/Guayaquil');
        $fecha_hoy = date('Y-m-d');

        $html = "<table border='1' cellspacing='0'>";
        $html.="<tr>";
        $html.="<th>Revisado</th><th>Id</th><th>Servicio</th><th>Letra</th><th>Turno</th><th class='td_turno'>Tipo</th><th>F. Emision</th><th>H. Emision</th><th>F. ini. Atencion</th><th>H. ini. Atencion</th>
            <th>F. fin Atencion</th><th>H. fin. Atencion</th><th>Duracion</th><th>Grupo Servicio</th>
            <th>Ubicacion</th><th>Modulo</th><th>Usuario</th>"; //<th>Tipo Calificacion</th>
        $html.="</tr>";

        $db = DbBase::rawConnect();
        //-- consulto la ubicacion para agrupar por ubicacion
        $result = $db->query("SELECT t.id AS idTurno, numero, numero_alias,fecha_emision,hora_emision,
                                fecha_inicio_atencion,hora_inicio_atencion,fecha_fin_atencion,
                                hora_fin_atencion,duracion,prioridad,s.id AS idServicio,nombre,letra,letra_alias,
                                numero_caja,nombres, nombre_ubicacion,nombre_grupo_servicio
                                FROM turnos t,servicio s,caja ca, usuario u,ubicacion ub,gruposervicio gs
                                WHERE s.id = t.servicio_id
                                AND gs.id = s.gruposervicio_id
                                AND ub.id = s.ubicacion_id
                                AND t.id_username = u.id
                                AND t.caja_id = ca.id
                                AND t.calificacion='$calificacion' AND t.fecha_emision= '$fecha_hoy' AND adm_revisado = 0"
        );

        while ($row = $db->fetchArray($result)) {
            $html.="<tr>";
            $html.="<td><input type='checkbox' id='chk_turno_{$row['idTurno']}' name='chk_turno_{$row['idTurno']}' value='{$row['idTurno']}' onclick='actualizar_turno({$row['idTurno']})' /></td>";
            $html.="<td>{$row['idTurno']}</td>";
            $html.="<td>{$row['nombre']}</td>";
            $prioridad = $row['prioridad'];
            $tipo = "";
            $letra = "";
            if ($prioridad == 1) {
                $tipo = "Preferencial";
                $letra = $row['letra_alias'];
                $numero = $row['numero_alias'];
            } else if ($prioridad == 0) {
                $tipo = "Normal";
                $letra = $row['letra'];
                $numero = $row['numero'];
            }
            $html.="<td>$letra</td>";
            $html.="<td>$numero</td>";
            $html.="<td>$tipo</td>";
            $html.="<td>{$row['fecha_emision']}</td>";
            $html.="<td>{$row['hora_emision']}</td>";
            $html.="<td>{$row['fecha_inicio_atencion']}</td>";
            $html.="<td>{$row['hora_inicio_atencion']}</td>";
            $html.="<td>{$row['fecha_fin_atencion']}</td>";
            $html.="<td>{$row['hora_fin_atencion']}</td>";
            $html.="<td>{$row['duracion']}</td>";
            $html.="<td>{$row['nombre_grupo_servicio']}</td>";
            $html.="<td>{$row['nombre_ubicacion']}</td>";
            $html.="<td>{$row['numero_caja']}</td>";
            $html.="<td>{$row['nombres']}</td>";
            $html.="</tr>";
        }
        $html.="</table>";
        echo $html;
    }

    public function actualizarTurnoAction() {
        $this->setResponse("ajax");
        //INICIO ACTUALIZO EL TURNO A TRANSFERIDO=1
        $turno_id = $this->getPostParam("turno_id");
        $turnos = new Turnos();
        $turnos->updateAll("adm_revisado= 1", "id= $turno_id");
        //FIN ACTUALIZO EL TURNO A TRANSFERIDO=1
    }

    public function actualizarTurnoTransferidoAction() {
        $this->setResponse("ajax");
        //INICIO ACTUALIZO EL TURNO A TRANSFERIDO=1
        $turno_id = $this->getPostParam("turno_id");
        $turnos = new TurnosTransferidos();
        $turnos->updateAll("adm_revisado= 1", "id= $turno_id");
        //FIN ACTUALIZO EL TURNO A TRANSFERIDO=1
    }

}
