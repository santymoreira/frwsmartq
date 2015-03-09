<?php

class Operador2Controller extends ApplicationController {

    public $id_turno;
    public $fecha_inicio_atencion;
    public $hora_inicio_atencion;
    public $fecha_fin_atencion;
    public $hora_fin_atencion;
    public $ubi_id;
    public $tipo_calificacion;      //A=Teclado, B=P. Simple, C=P. Matriz, D=Ninguno
    public $caja_id;
    public $en_pausa;
    public $usuario_id;
    public $ver_tiempo_maximo;
    public $ver_tiempo_atencion;
    public $anulado;        //bandera: si es igual a 1 tonces ya ha sido anulado (para tipo D)
    public $terminado;      //bandera: si es igual a 1 tonces ya ha sido terminado (para tipo D)
    //dentro del modelo caja
    public $carpeta;
    public $servidor_node;
    public $numero_modulo;
    public $foto;
    public $turno;
    public $nombre_operador;
    public $id_grupopregunta;
    public $num_preguntas;
    public $ip_calificador;

    public function initialize() {
        $this->setPersistance(true);

        if (!SessionNamespace::exists("datosUsuarioSMC")) {
            Flash::addMessage("Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente", Flash::WARNING);
            $this->redirect("login");
        }

        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $this->usuario_id = $dataUsuario->getId();
    }

    public function indexAction($valor = 2) {
        $ip = $_SERVER['REMOTE_ADDR'];
        //busco la ip de la caja
        $caja = new Caja();
        $buscaCaja = $caja->findFirst("ip='$ip'");
        //--pregunto si la ip esta en la tabla caja
        if (!$buscaCaja) {  //si no existe la ip en la tabla caja
            Flash::addMessage("La ip $ip no está asignado a un módulo. Por favor comuníquese con el administrador del sistema.", Flash::WARNING);
            //header("Location:" . $_SERVER['HTTP_REFERER']);
            $this->redirect("login/salir");
        } else {
            //-- busco los datos del usuario
            $usuario = new Usuario();
            $buscaUsuario = $usuario->findFirst("id=$this->usuario_id");
            $this->foto = $buscaUsuario->getFoto();
            $this->nombre_operador = $buscaUsuario->getNombres();

            $this->caja_id = $buscaCaja->getId();
            $this->tipo_calificacion = $buscaCaja->getTipoCalificacionOperador();
            $this->numero_modulo = $buscaCaja->getNumeroCaja();
            $this->ip_calificador = $buscaCaja->getIpCalificador();
            Tag::displayTo('tipo_calificacion', $buscaCaja->getTipoCalificacionOperador());
            Tag::displayTo('txt_transferir_uno', $buscaCaja->getTransferirUno());
            Tag::displayTo('txt_transferir_todos', $buscaCaja->getTransferirTodos());

            $cajaPausas = new CajaPausas();
            $buscaCajaPausas = $cajaPausas->findFirst("usuario_id=$this->usuario_id AND estado=1");
            if ($buscaCajaPausas)
                $this->en_pausa = $buscaCajaPausas->getId();
            else
                $this->en_pausa = "no";
            Tag::displayTo('esta_pausado', $this->en_pausa);

            //INICIO VERIFICAR CALIFICAR
            $configuracion = new Configuracionsistema();
            $buscaConfiguracion = $configuracion->findFirst();
            Tag::displayTo('calificador', $buscaConfiguracion->getActivarCalificador());
            Tag::displayTo('ver_tiempo_maximo', $buscaConfiguracion->getVerTiempoMaximo());
            Tag::displayTo('ver_tiempo_atencion', $buscaConfiguracion->getVerTiempoAtencion());
            $this->ver_tiempo_maximo = $buscaConfiguracion->getVerTiempoMaximo();
            $this->ver_tiempo_atencion = $buscaConfiguracion->getVerTiempoAtencion();
            //FIN VERIFICAR CALIFICAR

            $empresa = new Empresa();
            $buscaEmpresa = $empresa->findFirst();
            $this->carpeta = $buscaEmpresa->getCarpeta();
            $this->servidor_node = $buscaEmpresa->getServidorNode();

            //INICIO GUARDAR LA SESI�N
            $sesion_id = null;
            $estado = "Activo";
            $fechaInicio = date("Y-m-d");
            $horaInicio = date("H:i:s");


            $ubicacionId = $buscaCaja->getUbicacionId();
            //$fechaFin = $this->getPostParam("fecha_fin");
            //$horaFin = $this->getPostParam("hora_fin");
            $creacionAt = $this->getPostParam("creacion_at");
            $sesiones = new Sesiones();
            $sesiones->setId($sesion_id);
            $sesiones->setUsuarioId($this->usuario_id);
            $sesiones->setCajaId($buscaCaja->getId());
            $sesiones->setUbicacionId($ubicacionId);
            $sesiones->setEstado($estado);
            $sesiones->setIp($ip);
            $sesiones->setFechaInicio($fechaInicio);
            $sesiones->setHoraInicio($horaInicio);
            $sesiones->setFechaFin("0000-00-00");
            $sesiones->setHoraFin("00:00:00");
            $sesiones->setDuracion("00:00:00");
            $sesiones->setCreacionAt($creacionAt);
            $sesiones->save();

            $dataUsuario = SessionNamespace::add('datosUsuarioSMC');
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT id FROM sesiones WHERE usuario_id=$this->usuario_id AND caja_id={$buscaCaja->getId()} ORDER BY id DESC LIMIT 1;");
            while ($row = $db->fetchArray($result)) {
                echo $dataUsuario->setSesionId($row['id']);
            }
            $dataUsuario->setEsOperador(1);

            //busco el id del usuario que ha iniciado la sesiÃ³n
            $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
            $idsesion = $dataUsuario->getId();
            $usuario = $dataUsuario->getNombre();

            //actualizo caja.usuario_actual
            $caja1 = new Caja();
            $condicion = "ip = '$ip'";
            $caja1->updateAll("usuario_actual=$idsesion", "conditions: $condicion");
            $turnoactual = 0;
            $turnoespera = 0;
            Tag::displayTo('actual1', $turnoactual);
            Tag::displayTo('espera1', $turnoespera);
            Tag::displayTo('numcaja', $buscaCaja->getNumeroCaja());
            Tag::displayTo('idcaja', $buscaCaja->getId());
            Tag::displayTo('usuario', $usuario);
            Tag::displayTo('ip', $ip);
            Tag::displayTo('id_usuario', $idsesion);
        }
    }

    public function salirAction() {
        SessionNamespace::drop("datosUsuarioSMC");
        $this->routeTo("controller: login");
    }

    /**
     * Funcion del boton siguiente reliza: cambia en vista operador del turno actual al siguinet
     * grabo los turnos en displayturno moviendo un puesto
     * grabo turno actual en detalle turno cerrando fecha y actualizo el estado
     * @return <type>
     */
    public function siguienteAction() {
        $this->setResponse("json");
        $numcaja = $this->getPostParam('caja');
        $idcaja = $this->getPostParam('idcaja');
        $tipo_d = $this->getPostParam('num');    //si tipo calificacion es ninguno me sirve

        $fecha_hoy = date("Y-m-d");

        $this->normal = $this->getPostParam('normal');   //resive 1 porque es atencion normal
        //Primero termina el turno anterior si es que existe, para esto debe cumplir
        //* que exista dato anterior en num
        //* que antes no haya sido anulado (en anular ya termina el turno)
        //* que antes no haya sido terminado (en terminarTurnoAction ya termina el turno)
        if (($this->tipo_calificacion == 'D') & ($tipo_d != "") & ($this->anulado == 0) & ($this->terminado == 0)) {  //calificacion= ninguna
            $this->terminarTurno('NO');    //Terminar el turno
            //echo "nelson";
        }
        $this->anulado = 0;
        $this->terminado = 0;

        //INICIO BUSCAR PRIMER REGISTRO DE TURNO
        $id_servicio = "";
        $actual = "";
        $letra = "";
        $ubicacion_id = "";
        $hora_emision = "";
        $tiempo_maximo = "";
        //--selecciona el primer registro de turno segun la caja que llama y la fecha actual
        $db = DbBase::rawConnect();
//        $result = $db->query(""
//                . "SELECT t.id, tiempo_maximo, numero, letra, ubicacion_id, hora_emision, letra_alias, numero_alias, prioridad "
//                . "FROM turnos t, servicio s WHERE s.id=t.servicio_id AND  t.estado=0 AND por_atender=0 AND atendido=0 AND rechazado=0 "
//                . "AND (caja_id=$idcaja OR caja_id IS NULL) AND fecha_emision= '$fecha_hoy' AND "
//                . "servicio_id IN (SELECT servicio_id FROM serviciocaja WHERE caja_id=$idcaja AND llamar=1) ORDER BY prioridad DESC, id ASC");
        $result = $db->query(""
                . "SELECT t.id, tiempo_maximo, numero, letra, ubicacion_id, hora_emision, letra_alias, numero_alias, prioridad, "
                . "(select preferencia_servicio from serviciocaja sc where sc.caja_id=$idcaja and t.servicio_id=sc.servicio_id) as preferencia_servicio "
                . "FROM turnos t, servicio s "
                . "WHERE s.id=t.servicio_id "
                . "AND t.estado=0 AND por_atender=0 AND atendido=0 AND rechazado=0 AND fecha_emision= '$fecha_hoy' "
                . "AND t.servicio_id IN (SELECT servicio_id FROM serviciocaja WHERE caja_id=$idcaja AND llamar=1) "
                . "ORDER BY prioridad DESC, preferencia_servicio DESC, id ASC");
        //echo count($db->fetchArray($result)); die();
        $prioridad = 0;
        $preferencia_servicio = 0;
        while ($row = $db->fetchArray($result)) {
            $prioridad = $row['prioridad'];
            $preferencia_servicio = $row['preferencia_servicio'];
            $id_turno = $row['id'];
            $tiempo_maximo = $row['tiempo_maximo'];
            $ubicacion_id = $row['ubicacion_id'];
            $hora_emision = $row['hora_emision'];
            $this->id_turno = $id_turno;
            $this->ubi_id = $ubicacion_id;
            if ($prioridad == 1) {  //--si existe prioridad llama
                $actual = $row['numero_alias'];
                $letra = $row['letra_alias'];
                break;
            } if ($preferencia_servicio == 1) {  //--si existe preferencia en servicio
                $actual = $row['numero'];
                $letra = $row['letra'];
                break;
            } else {
                $actual = $row['numero'];
                $letra = $row['letra'];
                break;
            }
        }

        $turno = new Turnos();
        $datos = SessionNamespace::get('datosUsuarioSMC');
        $id_usuario = $datos->getId();
        $turno->updateAll("por_atender= 1, caja_id= $idcaja, id_username= $id_usuario", "id= $this->id_turno");

        $this->turno = $letra . $actual;

        $datosturno = array("actual1" => $actual, "servicio" => $letra,
            "hora_emision" => $hora_emision, "tiempo_maximo" => $tiempo_maximo,
            "tipo_d" => $tipo_d, "anulado" => $this->anulado, "terminado" => $this->terminado, "ubicacion_id" => $this->ubi_id, "turno_id" => $this->id_turno);
        return ($datosturno);
    }

    /**
     * Funciï¿½n que permite mostrar los turnos en espera en cada cajero segï¿½n los servicios q debe atender
     * @return <type>
     */
    public function contarTurnosAction() {
        $this->setResponse("json");
        list($espera1, $espera2) = $this->contarTurnosEnEspera();

        //--contar turnos en espera del serivio preferencial
        $contps = $this->contarPreferenciaServicio();

        $datos = array("espera1" => $espera1, "espera2" => $espera2, "cont_preferencia_servicio" => $contps);
        return ($datos);
    }

    public function contarPreferenciaServicio() {
        $idcaja = $this->getPostParam('idcaja');
        $fecha_hoy = date("Y-m-d");
        $db = DbBase::rawConnect();
        $result = $db->query(""
                . "SELECT t.id, tiempo_maximo, numero, letra, ubicacion_id, hora_emision, letra_alias, numero_alias, prioridad, "
                . "(select preferencia_servicio from serviciocaja sc where sc.caja_id=$idcaja and t.servicio_id=sc.servicio_id) as preferencia_servicio "
                . "FROM turnos t, servicio s "
                . "WHERE s.id=t.servicio_id "
                . "AND t.estado=0 AND por_atender=0 AND atendido=0 AND rechazado=0 AND fecha_emision= '$fecha_hoy' "
                . "AND t.servicio_id IN (SELECT servicio_id FROM serviciocaja WHERE caja_id=$idcaja AND llamar=1) "
                . "ORDER BY prioridad DESC, preferencia_servicio DESC, id ASC");
        //echo count($db->fetchArray($result)); die();
        $cont = 0;
        while ($row = $db->fetchArray($result)) {
            if ($row['preferencia_servicio'] == 1)
                $cont = $cont + 1;
        }
        return $cont;
    }

    /**
     *
     */
    public function contarTurnosEnEspera() {
        //INICIO VER LOS TURNOS EN ESPERA
        $ip = $_SERVER['REMOTE_ADDR'];
        $fecha_hoy = date("Y-m-d");
        $turno = new Turnos();
        $espera1 = $turno->count("atendido= 0 AND por_atender=0 AND transferido=0 AND fecha_emision= '$fecha_hoy'"); //Total turnos en espera
        $db = DbBase::rawConnect();
        //contar turnos en espera del aperador mas los turnos nuevos
        //$result = $db->query("select count(*) from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha_hoy' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id=$this->caja_id AND sc.llamar=1) AND (caja_id=$this->caja_id OR caja_id IS NULL)");
        $result = $db->query("select count(*) from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha_hoy' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.ip='$ip' AND sc.llamar=1)");
        while ($row = $db->fetchArray($result)) {
            $espera2 = $row[0];
        }
        return array($espera1, $espera2);
        //FIN VER LOS TURNOS EN ESPERA
    }

    /**
     * Funcion que se activa cuando el operador pulsa rellamar
     * Esta funcion no se usa porque esta con sockets
     */
    public function timbreAction() {
        $this->setResponse("json");
        $servicio = $this->getPostParam('servicio');
        $turno = $this->getPostParam('turno');
        $numcaja = $this->getPostParam('num_caja');
        //$normal=$this->getPostParam('normal');
        //$ubicacion_id=$this->getPostParam('ubicacion_id'); //para turno transferido seleccionado

        $fecha_hoy = date("Y-m-d");

        //INICIO ENVIAR VALOR 1 A TIMBRE
        $pantalla = new Pantalla();
        if ($this->normal == 1)
            $condicion = "ubicacion_id= $this->ubi_id";
        else
            $condicion = "ubicacion_id= $this->ubicacion_t_t";
        $pantalla->updateAll("timbre=1", "conditions: $condicion");
        //FIN ENVIAR VALOR 1 A TIMBRE
        //INICIO ENVIAR A DISPLAYTURNO
        //Sirve para ver en la pantalla los turnos
        //INICIO AUMENTAR CEROS EN TURNO
        $turno_displayturno = $turno;
        $contturno = strlen($turno_displayturno);
        if ($contturno == 1)
            $turno = "00" . $turno_displayturno;
        else if ($contturno == 2)
            $turno = "0" . $turno_displayturno;
        else
            $turno = $turno_displayturno;
        //FIN AUMENTAR CEROS EN TURNO

        $displayturno = new Displayturno();
        $displayturno->setCajanumero($numcaja);
        $displayturno->setNumeroturno($servicio . $turno);
        $displayturno->setFecha($fecha_hoy);
        $displayturno->setTurno(0);
        $displayturno->setUbicacion($this->ubi_id);
        $displayturno->save();
        //FIN ENVIAR A DISPLAYTURNO
    }

    /**
     * Funcion que se activa cuando el operador pulsa atender
     * parametros que recibe para turnos_transferidos:
     * normal 1=normal, 0=turnos transferido a ser atendido
     */
    public function atenderAction() {
        //INICIO ACTUALIZAR EL TURNO CON FECHAS DE INICIO DE ATENCION
        //Al dar clic sobre atender debe iniciar la duraciÃ³n de la atenciÃ³n
        //Termina al dar clic en siguiente turno
        $fecha_inicio_atencion = date("Y-m-d");
        $hora_inicio_atencion = date("H:i:s");
        $this->fecha_inicio_atencion = $fecha_inicio_atencion;
        $this->hora_inicio_atencion = $hora_inicio_atencion;

        $datos = SessionNamespace::get('datosUsuarioSMC');
        $id_usuario = $datos->getId();
        if ($this->normal == 1) {
            $turno = new Turnos();
            $turno->updateAll("fecha_inicio_atencion= '$fecha_inicio_atencion', hora_inicio_atencion= '$hora_inicio_atencion'", "id= $this->id_turno");
        } else {
            $turnosTrasferidos = new TurnosTransferidos();
            $turnosTrasferidos->updateAll("fecha_inicio_atencion= '$fecha_inicio_atencion', hora_inicio_atencion= '$hora_inicio_atencion'", "id= $this->id_t_t_atender");
        }
        //FIN ACTUALIZAR EL TURNO CON FECHAS DE INICIO DE ATENCIï¿½?N
    }

    /**
     * Funcion que se activa cuando el operador pulsa anular
     */
    public function anularAction() {
        $this->setResponse("json");
        $id_usuario = $this->getPostParam('id_usuario');
        $hora = date("H:i:s");
        if ($this->normal == 1) {
            $turno = new Turnos();
            //$turno->updateAll("rechazado= 1, atendido=0,hora_inicio_atencion='$hora', id_username=$id_usuario", "id= $this->id_turno");
            $turno->updateAll("rechazado= 1, atendido=0, id_username=$id_usuario", "id= $this->id_turno");
        } else {
            $turnosTransferidos = new TurnosTransferidos();
            $turno->updateAll("rechazado= 1, atendido=1 ", "id= $this->id_t_t_atender");
        }
        $this->anulado = 1;
        return ($this->anulado);
    }

    public function recargarAction() {
        $this->setResponse("json");
        $idcaja = $this->getPostParam('caja');
        $turno1 = new Turno();
        $espera = $turno1->count("estado=0 and servicio_id IN (select servicio_id from serviciocaja where caja_id= $idcaja)");
        return ($espera);
    }

    /**
     * Funcion que se activa cuando ha calificado por teclado de 4 botones
     */
    public function terminarTurnoTecladoAction() {
        $this->setResponse("json");
        $calificacion_teclado = strtolower($this->getPostParam('calificacion_teclado'));  //convierte a minï¿½scula la letra

        if ($calificacion_teclado == "h" || $calificacion_teclado == "d")
            $calificacion_teclado = "Excelente";
        else if ($calificacion_teclado == "g" || $calificacion_teclado == "c")
            $calificacion_teclado = "Muy Bueno";
        else if ($calificacion_teclado == "f" || $calificacion_teclado == "b")
            $calificacion_teclado = "Bueno";
        else if ($calificacion_teclado == "e" || $calificacion_teclado == "a")
            $calificacion_teclado = "Regular";
        else
            $calificacion_teclado = "error al calificar";

        $this->terminarTurno($calificacion_teclado);    //Terminar el turno
    }

    /**
     * Funcion que permite terminar el turno y enviar la calificacion
     * para pantalla touch con excelente, muy bueno, bueno y regular
     */
    public function terminarTurnoPantallaAction() {

        $this->setResponse("json");
        $calificacion = $this->getPostParam('calificacion');

        $this->terminarTurno($calificacion);    //Terminar el turno
//_______________
        $datos = array('exito' => '1');
        return($datos);
//_______________	
        $this->calificador();                   //LLAMAR A CALIFICADOR
        //$datosturno= array("calificacion"=>$calificacion, "mensaje"=>$mensaje);
        //return ($datosturno);
    }

    /**
     * Funcion que permite terminar el turno si es de tipo D= ninguno
     */
    public function terminarTurnoAction() {
        $this->setResponse("json");
        $tipo_d = $this->getPostParam('num');    //si tipo calificacion es ninguno me sirve
        //if (($this->tipo_calificacion=='D')&($tipo_d!="") & ($this->anulado!=1)) {  //calificacion= ninguna
        $this->terminarTurno('NO');    //Terminar el turno, NO=> calificacion=NO
        //$this->anulado=1;       //el nuevo valor es 1, para no volver a anular en siguienteAction
        $this->terminado = 1;
        //}
        return ($this->terminado);
    }

    /**
     * Funcion que califica cada pregunta de la pantalla de multipregunta
     */
    public function calificarMatrizAction() {
        /* Cojo los servicios asignados a la caja que solicita el turno */
        $this->setResponse("json");
        $id_pregunta = $this->getPostParam('id_pregunta');       //Id de la pregunta
        $puntuacion = $this->getPostParam('puntuacion');         //Puntos de la pregunta
        $num_preguntas = $this->getPostParam('num_preguntas');   //Total de preguntas
        $cont_preguntas = $this->getPostParam('cont_preguntas'); //Contador de preguntas de las que califica
        $id_caja = $this->getPostParam('idcaja'); //Id de la Caja
        //echo "PREGUntas".$num_preguntas;

        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $turnospreguntas = new Turnospreguntas();
        $turnospreguntas->setPreguntasId($id_pregunta);
        $turnospreguntas->setCajaId($id_caja);
        //$turnospreguntas->setUsuarioId($this->id_username);
        $turnospreguntas->setTurnosId($this->id_turno);
        $turnospreguntas->setPuntuacion($puntuacion);
        $turnospreguntas->setFecha($fecha);
        $turnospreguntas->setHora($hora);
        $turnospreguntas->save();

        $calificacion = 'Matriz';     //Envï¿½o 'Matriz' si este ha sido calificado con matriz de preguntas
        //si son iguales entonces se termina el turno
        if ($cont_preguntas == $num_preguntas) {
            $this->terminarTurno($calificacion);    //Terminar el turno
            $this->calificador();                   //LLAMAR A CALIFICADOR
        }

        $datos = array("pregunta" => $id_pregunta, "puntuacion" => $puntuacion, "num_preguntas" => $num_preguntas, "cont_preguntas" => $cont_preguntas);
        return ($datos);
    }

    /**
     * Funcion que permite terminar el turno para los tres tipo de calificacion
     * Teclado, pantalla, matriz
     */
    public function terminarTurno($calificacion) {
        $fecha_fin_atencion = date("Y-m-d");
        $hora_fin_atencion = date("H:i:s");
        $this->fecha_fin_atencion = $fecha_fin_atencion;
        $this->hora_fin_atencion = $hora_fin_atencion;

        switch ($calificacion) {
            case 'Excelente':
                $valor_calificacion = 4;
                break;
            case 'Muy Bueno':
                $valor_calificacion = 3;
                break;
            case 'Bueno':
                $valor_calificacion = 2;
                break;
            case 'Regular':
                $valor_calificacion = 1;
                break;
            default :
                $valor_calificacion = 0;
                break;
        }

        /* inicio calcula la duraciÃ³n */
        $fun = new Funciones();
        $fi = $this->fecha_inicio_atencion . " " . $this->hora_inicio_atencion;
        $ff = $this->fecha_fin_atencion . " " . $this->hora_fin_atencion;
        $duracion = $fun->difFecha($fi, $ff);

        /* fin calcula la duraciÃ³n */

        $datos = SessionNamespace::get('datosUsuarioSMC');
        $id_usuario = $datos->getId();

        if ($this->normal == 1) {
            $turno = new Turnos();
            //$turno->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion'","id= $this->id_turno");
            $turno->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion', valor_calificacion= $valor_calificacion", "id= $this->id_turno");
        } else {
            $turno_transferido = new TurnosTransferidos();
            //$turno_transferido->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion', valor_calificacion= $valor_calificacion", "id= $this->id_t_t_atender");
            $turno_transferido->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion'", "id= $this->id_t_t_atender");
        }
    }

    /*
     * Funciï¿½n para llamar al programa calificador en c#
     */

    public $mensaje;

    public function calificador() {
        //INICIO ENVIAR VALOR PARA EL PROGRAMA C#
        $nombre_archivo = 'C:/SmartQ.txt';
        $contenido = "1";
        $mensaje = "";
        // Asegurarse primero de que el archivo existe y puede escribirse sobre el.
        if (is_writable($nombre_archivo)) {
            // En nuestro ejemplo estamos abriendo $nombre_archivo en modo de adicion.
            // El apuntador de archivo se encuentra al final del archivo, asi que
            // alli es donde ira $contenido cuando llamemos fwrite().
            if (!$gestor = fopen($nombre_archivo, 'w')) {
                $mensaje = "No se puede abrir el archivo ($nombre_archivo)";
                //exit;
            }
            // Escribir $contenido a nuestro arcivo abierto.
            if (fwrite($gestor, $contenido) === FALSE) {
                $mensaje = "No se puede escribir al archivo ($nombre_archivo)";
                //exit;
            }
            fclose($gestor);
        } else {
            $mensaje = "No existe el archivo $nombre_archivo o no estÃ¡ en ejecuciÃ³n el programa 'MousePointer'";
        }

        $this->mensaje = $mensaje;

        /* $archivo="C:/SmartQ.txt";
          $cuerpo="1";
          $fp=fopen($archivo,'w');        // Abrir el archivo para anexar al final
          fwrite($fp,$cuerpo);            // Escribir en el archivo
          fclose($fp); */
        //FIN ENVIAR VALOR PARA EL PROGRAMA C#
    }

    public function terminaTurnoAction() {
        $this->setResponse("json");
        $calificacion = $this->getPostParam('calificacion');
        $this->terminarTurno($calificacion);
        $datos = array("pregunta" => 1, "puntuacion" => 1, "num_preguntas" => 1, "cont_preguntas" => 1);
        return ($datos);
    }

    /*
     * Funcion que permite mostrar la lista de operadores que seleccionar
     * para la tranferencia de turnos
     * Recibe: servicio_id
     * Retorna: lista de operadores
     */

    public function listaOperadores1Action() {
        $html = "";
        $this->setResponse("json");
        //$modulo_id= $this->getPostParam("modulo_id");
        $servicio_id = $this->getPostParam("servicio_id");

        $sql = new ActiveRecordJoin(array(
            "entities" => array("Servicio", "Serviciocaja", "Caja"),
            "fields" => array(
                "{#Caja}.id",
                "{#Caja}.numero_caja",
                "{#Servicio}.nombre",
            ),
            "conditions" => "{#Servicio}.id=$servicio_id")
        );
        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";
        $html.="<input type='image' src='../../img/check_all.gif' style='width:15px' title='Seleccionar todos los mÃ³dulos' onclick='check_all_modulos_turno_actual()'> &nbsp;&nbsp;";
        $html.="<input type='image' src='../../img/uncheck_all.gif' style='width:15px' title='Deseleccionar todos los mÃ³dulos' onclick='uncheck_all_modulos_turno_actual()'> <br>";
        foreach ($sql->getResultSet() as $result) {
            $caja_id = $result->getId();
            $numero_caja = $result->getNumeroCaja();
            if ($caja_id == $this->caja_id)
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' onclick='seleccionar_turno_actual()' disabled='disabled' />MÃ³dulo $numero_caja<br>";
            else
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' class='c_modulos_turno_actual' onclick='seleccionar_turno_actual()' />MÃ³dulo $numero_caja<br>";
            $nombre_servicio = $result->getNombre();
        }
        $html.="</fieldset>";

        $datos = array("mensaje" => $html, "nombre_servicio" => $nombre_servicio);
        return ($datos);
    }

    /*
     * Funcion que permite mostrar la lista de operadores que estan el linea y con pausas
     */

    public function listaOperadoresAction() {
        $html = "";
        $this->setResponse("json");
        $servicio_id = $this->getPostParam('servicio_id');

        $fecha = date("Y-m-d");
        $html.="<table border='1'>
            <tr>
                <th align='center'>Mod.</th>
                <th align='center'>Operador</th>
                <th align='center'>Estado</th>
                <th align='center'>Clientes<br>en espera</th>
            </tr>";

        $db = DbBase::rawConnect();
        $forma_cajas_ids = "";

        //ver los que están en pausas
        $condicion = "{#Servicio}.id=$servicio_id AND {#CajaPausas}.fecha_inicio='$fecha' AND {#CajaPausas}.estado=1";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Caja", "Usuario", "Ubicacion", "Pausas", "CajaPausas", "Servicio", "Serviciocaja"),
            "fields" => array(
                "{#Caja}.id",
                "{#Caja}.numero_caja",
                "{#Usuario}.nombres",
                "{#Ubicacion}.nombre_ubicacion",
                "{#Pausas}.nombre_pausa"
            ),
            "conditions" => $condicion,
            "order" => "{#Caja}.numero_caja"
        ));
        foreach ($query->getResultSet() as $result) {
            $caja_id = $result->getId();
            $forma_cajas_ids.=$result->getId() . ",";
            $html.= "<tr>";
            $html.= "<td style='padding:0px' align='center'>";
            if ($caja_id == $this->caja_id)
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' onclick='seleccionar_turno_actual()' disabled='disabled' />  {$result->getNumeroCaja()}<br>";
            else
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' class='c_modulos_turno_actual' onclick='seleccionar_turno_actual()' />  {$result->getNumeroCaja()}<br>";
            $html.= "</td>";
            $html.= "<td style='padding:0px'> {$result->getNombres()} </td>";
            //$html.= "<td> {$result->getNombreUbicacion()} </td>";
            $html.= "<td style='padding:0px'> <font color='#3366FF'><b>{$result->getNombrePausa()}</b></font>  </td>";
            //contar turnos en espera del aperador mas los turnos nuevos
            $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
            while ($row = $db->fetchArray($result2)) {
                $turnos_en_espera = $row['total'];
            }
            $html.= "<td style='padding:0px' align='center'>$turnos_en_espera</td>";
            $html.= "</tr>";
        }

        //ver los que estan atendiendo
        /* $condicion="{#Turnos}.fecha_emision='$fecha' AND {#Turnos}.por_atender=1 AND {#Turnos}.atendido=0" ;
          $query = new ActiveRecordJoin(array(
          "entities" => array("Caja", "Usuario", "Usercaja","Ubicacion","Turnos"),
          "fields" => array(
          "{#Caja}.id",
          "{#Caja}.numero_caja",
          "{#Usuario}.nombres",
          "{#Ubicacion}.nombre_ubicacion"
          ),
          "conditions" => $condicion,
          "order"=>"{#Caja}.numero_caja"
          ));
          foreach($query->getResultSet() as $result) {
          $caja_id=$result->getId();
          $forma_cajas_ids.=$result->getId().",";
          $html.= "<tr>";
          $html.= "<td style='padding:0px' align='center'>";
          if ($caja_id==$this->caja_id)
          $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' onclick='seleccionar_turno_actual()' disabled='disabled' />  {$result->getNumeroCaja()}<br>";
          else
          $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' class='c_modulos_turno_actual' onclick='seleccionar_turno_actual()' />  {$result->getNumeroCaja()}<br>";
          $html.= "</td>";
          $html.= "<td style='padding:0px'> {$result->getNombres()} </td>";
          //$html.= "<td> {$result->getNombreUbicacion()} </td>";
          $html.= "<td style='padding:0px'> <font color='#009900'><b>Atendiendo</b></font> </td>";
          //contar turnos en espera del aperador mas los turnos nuevos
          $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
          while($row = $db->fetchArray($result2)) {
          $turnos_en_espera=$row['total'];
          }
          $html.= "<td style='padding:0px' align='center'>$turnos_en_espera</td>";
          $html.= "</tr>";
          } */


        $forma_cajas_ids_aux = $forma_cajas_ids;
        $forma_cajas_ids = substr($forma_cajas_ids, 0, strlen($forma_cajas_ids) - 1);

        //poner los demas como activo si ha inciado la sesion a la fecha actual
        if ($forma_cajas_ids != "")
            $condicion = "{#Servicio}.id=$servicio_id AND {#Sesiones}.estado='Activo' AND {#Sesiones}.fecha_inicio='$fecha' AND {#Sesiones}.id IN (SELECT MAX(id) FROM sesiones WHERE caja_id NOT IN ($forma_cajas_ids) GROUP BY caja_id)";
        else
            $condicion = "{#Servicio}.id=$servicio_id AND {#Sesiones}.estado='Activo' AND {#Sesiones}.fecha_inicio='$fecha' AND {#Sesiones}.id IN (SELECT MAX(id) FROM sesiones GROUP BY caja_id)";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Caja", "Usuario", "Ubicacion", "Sesiones", "Servicio", "Serviciocaja"),
            "fields" => array(
                "{#Caja}.id",
                "{#Caja}.numero_caja",
                "{#Usuario}.nombres",
                "{#Ubicacion}.nombre_ubicacion",
                "{#Sesiones}.estado"
            ),
            "conditions" => $condicion,
            "order" => "{#Caja}.numero_caja"
        ));

        foreach ($query->getResultSet() as $result) {
            $forma_cajas_ids_aux.=$result->getId() . ",";
            $caja_id = $result->getId();
            $html.= "<tr>";
            $html.= "<td style='padding:0px' align='center'>";
            if ($caja_id == $this->caja_id)
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' onclick='seleccionar_turno_actual()' disabled='disabled' />  {$result->getNumeroCaja()}<br>";
            else
                $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' class='c_modulos_turno_actual' onclick='seleccionar_turno_actual()' />  {$result->getNumeroCaja()}<br>";
            $html.= "</td>";
            $html.= "<td style='padding:0px'> {$result->getNombres()} </td>";
            $html.= "<td style='padding:0px'> <font color='#336600'><b>{$result->getEstado()}</b></font></td>";
            //contar turnos en espera del aperador mas los turnos nuevos
            $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
            while ($row = $db->fetchArray($result2)) {
                $turnos_en_espera = $row['total'];
            }
            $html.= "<td style='padding:0px' align='center'>$turnos_en_espera</td>";
            $html.= "</tr>";
        }

        //poner al resto como inactivos
        //Se comento esta seccion de codigo puesto que no se debe mostrar los usuarios que no estan 

        /*
          $forma_cajas_ids_aux= substr($forma_cajas_ids_aux,0, strlen($forma_cajas_ids_aux)-1);
          if ($forma_cajas_ids_aux!="")
          $condicion="{#Servicio}.id=$servicio_id AND {#Usercaja}.caja_id NOT IN ($forma_cajas_ids_aux)" ;
          else
          $condicion="{#Servicio}.id=$servicio_id" ;
          $query = new ActiveRecordJoin(array(
          "entities" => array("Caja", "Usuario", "Ubicacion","Servicio","Serviciocaja"),
          "fields" => array(
          "{#Caja}.id",
          "{#Caja}.numero_caja",
          "{#Usuario}.nombres",
          "{#Ubicacion}.nombre_ubicacion"
          ),
          "conditions" =>$condicion,
          "order"=>"{#Caja}.numero_caja"
          ));

          foreach($query->getResultSet() as $result) {
          $caja_id=$result->getId();
          $html.= "<tr>";
          $html.= "<td style='padding:0px' align='center'>";
          if ($caja_id==$this->caja_id)
          $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' onclick='seleccionar_turno_actual()' disabled='disabled' />  {$result->getNumeroCaja()}<br>";
          else
          $html.="<input type='checkbox' id='chk_modulos_turno_actual_$caja_id' name='chk_modulos_turno_actual_$caja_id' value='$caja_id' class='c_modulos_turno_actual' onclick='seleccionar_turno_actual()' />  {$result->getNumeroCaja()}<br>";
          $html.= "</td>";
          $html.= "<td style='padding:0px'> {$result->getNombres()} </td>";
          $html.= "<td style='padding:5px'> <font color='red'><b>Inactivo</b></font></td>";
          //contar turnos en espera del aperador mas los turnos nuevos
          $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
          while($row = $db->fetchArray($result2)) {
          $turnos_en_espera=$row['total'];
          }
          $html.= "<td style='padding:5px' align='center'>$turnos_en_espera</td>";
          $html.= "</tr>";
          }
         */
        $html.="</table>";

        $datos = array("mensaje" => $html);
        return ($datos);
    }

    /*
     * Funcion primero las ubicaciones de los Usuario para luego mostrar los modulos
     */

    public function listaOperadoresTodosAction() {
        $html = "";
        $this->setResponse("json");
        //$modulo_id= $this->getPostParam("modulo_id");

        $db = DbBase::rawConnect();
        $result = $db->query("SELECT u.id as id, nombre_ubicacion FROM ubicacion u, caja c, servicio s, serviciocaja sc WHERE u.id=c.ubicacion_id AND s.id=sc.servicio_id AND c.id=sc.caja_id GROUP BY nombre_ubicacion;");
        while ($row = $db->fetchArray($result)) {
            $ubicacion_id = $row['id'];
            $nombre_ubicacion = $row['nombre_ubicacion'];

            $html.="<b>" . $nombre_ubicacion . "</b>";

            $html.="&nbsp;&nbsp; <input type='image' src='../../img/abrir.png' style='width:15px' title='Ver modulos' onclick='abrir_div_operadores($ubicacion_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../../img/cerrar.png' style='width:15px' title='Ocultar modulos' onclick='cerrar_div_operadores($ubicacion_id)'>";
            $html.="&nbsp;&nbsp;|&nbsp;&nbsp;<input type='image' src='../../img/check_all.gif' style='width:15px' title='Seleccionar todos los operadores' onclick='check_all_modulos($ubicacion_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../../img/uncheck_all.gif' style='width:15px' title='Deseleccionar todos los operadores' onclick='uncheck_all_modulos($ubicacion_id)'> <br>";
            $html.="<div id='div_ubicacion_$ubicacion_id' name='div_ubicacion_$ubicacion_id'>";
            $html.=$this->listaOperadoresTodos($ubicacion_id);
            $html.="</div>";
        }
        $datos = array("mensaje" => $html);
        return ($datos);
    }

    /*
     * Funciï¿½n que permite mostrar la lista de operadores que seleccionarï¿½
     * para la tranferencia los turnos seleccionados
     * Retorna: lista de operadores
     */

    public function listaOperadoresTodos1($ubicacion_id) {
        $html = "";
        $this->setResponse("json");
        //$modulo_id= $this->getPostParam("modulo_id");

        $caja = new Caja();
        $buscaCaja = $caja->find("ubicacion_id=$ubicacion_id");

        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";

        foreach ($buscaCaja as $result) {
            $caja_id = $result->getId();
            $numero_caja = $result->getNumeroCaja();
            if ($caja_id == $this->caja_id)
                $html.="<input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' onclick='seleccionar($ubicacion_id)' disabled='disabled' />Modulo $numero_caja<br>";
            else
                $html.="<input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' class='c_modulos_$ubicacion_id' onclick='seleccionar($ubicacion_id)' />Modulo $numero_caja<br>";
        }

        $html.="</fieldset>";

        //$datos= array("mensaje"=>$html);
        return $html;
    }

    /*
     * Funciï¿½n que permite mostrar la lista de operadores que seleccionarï¿½
     * para la tranferencia los turnos seleccionados
     * Retorna: lista de operadores
     */

    public function listaOperadoresTodos($ubicacion_id) {
        $html = "";
        $this->setResponse("json");
        //$modulo_id= $this->getPostParam("modulo_id");

        $caja = new Caja();
        $buscaCaja = $caja->find("ubicacion_id=$ubicacion_id");

        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";

        foreach ($buscaCaja as $result) {
            $caja_id = $result->getId();
            $numero_caja = $result->getNumeroCaja();
            if ($caja_id == $this->caja_id)
                $html.="<input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' onclick='seleccionar($ubicacion_id)' disabled='disabled' />Modulo $numero_caja<br>";
            else
                $html.="<input type='checkbox' id='chk_modulos_todos_$caja_id' name='chk_modulos_todos_$caja_id' value='$caja_id' class='c_modulos_$ubicacion_id' onclick='seleccionar($ubicacion_id)' />Modulo $numero_caja<br>";
        }

        $html.="</fieldset>";

        //$datos= array("mensaje"=>$html);
        return $html;
    }

    /*
     * Funciï¿½n que hace la transferencia del turno
     */

    public function transferirAction($id = null) {
        $this->setResponse("json");

        $fech = date("Y-m-d");
        $hora = date("H:i:s");

        //Inicio guardar el turno transferido
        $remitente = $this->getPostParam("remitente");
        $servicioId = $this->getPostParam("servicio_id", "int");
        $permisoCajas = $this->getPostParam("modulos_id");
        $letra = $this->getPostParam("letra");
        $numero = $this->getPostParam("numero", "int");
        $ubicacionId = $this->getPostParam("ubicacion_id");
        $hora_emision = $this->getPostParam("hora_emision"); //para transferencia
        $tipo = $this->getPostParam("tipo_transferencia");
        $fechaAtender = $this->getPostParam("fecha_atender");
        $horaAtender = $this->getPostParam("hora_atender");
        $id_usuario = $this->getPostParam("id_usuario");

        $turnosTransferidos = new TurnosTransferidos();
        $turnosTransferidos->setId($id);
        $turnosTransferidos->setServicioId($servicioId);
        $turnosTransferidos->setNumero($numero);
        $turnosTransferidos->setFechaEmision($fech);
        $turnosTransferidos->setHoraEmision($hora_emision);
        $turnosTransferidos->setHoraTransferido($hora);
        $turnosTransferidos->setPorAtender(0);  //no
        $turnosTransferidos->setRechazado(0);   //no
        $turnosTransferidos->setAtendido(0);    //no
        $turnosTransferidos->setPermisoCajas($permisoCajas);
        $turnosTransferidos->setLetra($letra);
        $turnosTransferidos->setRemitente($remitente);
        $turnosTransferidos->setUbicacionId($ubicacionId);
        $turnosTransferidos->setTipo($tipo);
        $turnosTransferidos->setIdUserTransfiere($id_usuario);
        if ($tipo == "posponer") {
            $turnosTransferidos->setFechaAtender($fechaAtender);
            $turnosTransferidos->setHoraAtender($horaAtender);
        }
        $turnosTransferidos->save();
        //Fin guardar el turno transferido
        //Inicio actualizar el turno normal o transferido
        if ($this->normal == 1) {
            $turno = new Turnos();
            $turno->updateAll("transferido= 1", "id= $this->id_turno");
        } else {
            $turnosTransferidos = new TurnosTransferidos();
            //$turno->updateAll("transferido= 1","id= $this->id_turno");
        }
        //Inicio actualizar el turno normal o transferido

        $datos = array("mensaje" => $servicioId);
        return ($datos);
    }

    /*
     * Funciï¿½n que hace la transferencia de los turnos seleccionados
     */

    public function transferirTodosAction($id = null) {
        $this->setResponse("json");

        $fech = date("Y-m-d");
        $hora = date("H:i:s");

        $remitente = $this->getPostParam("remitente");
        $permisoCajas = $this->getPostParam("modulos_id");   //ids de los modulos a los que se les pasa
        $turnosId = $this->getPostParam("turnos_id");        //ids de los turnos que se les pasa
        $ubicacionesId = $this->getPostParam("ubicaciones_id");        //ids de los turnos que se les pasa
        $id_usuario = $this->getPostParam("id_usuario");        //id de usuario que transfiere

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
                "{#Turnos}.fecha_emision",
                "{#Turnos}.hora_emision",
                "{#Servicio}.letra",
            ),
            "conditions" => "$condition")
        );
        foreach ($sql->getResultSet() as $result) {

            //foreach ($buscaTurno as $result) {
            $turnoId = $result->getId();
            $servicioId = $result->getServicioId();
            $numero = $result->getNumero();
            $fechaEmision = $result->getFechaEmision();
            $horaEmision = $result->getHoraEmision();
            $letra = $result->getLetra();

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
            $turnosTransferidos->setIdUserTransfiere($id_usuario);
            $turnosTransferidos->save();
        }
        list($espera1, $espera2) = $this->contarTurnosEnEspera();

        $datos = array("todos" => $espera1, "sus_turnos" => $espera2);
        return ($datos);
    }

    /*
     * Funciï¿½n que permite ver si el operador tiene turnos transferidos
     * tabla: turnos_transferidos
     */

    public function contarTurnosTransferidosAction() {
        $this->setResponse("json");
        $fecha = date("Y-m-d");
        $cont_registros = 0;
        $turnosTransferidos = new TurnosTransferidos();
        $buscaTurnosTransferidos = $turnosTransferidos->find("por_atender=0 AND fecha_emision='$fecha'");
        foreach ($buscaTurnosTransferidos as $result) {
            $permisoCajas = $result->getPermisoCajas();
            $separa = explode(',', $permisoCajas);
            foreach ($separa as $id) {
                if ($this->caja_id == $id)
                    $cont_registros+=1;
            }
        }
        $datos = array("num_turnos_transferidos" => $cont_registros);
        return ($datos);
    }

    /*
     * Funciï¿½n que permite listar los turnos transferidos
     */

    public function listaTurnosTransferidosAction() {
        $this->setResponse("json");
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        //$cont_registros=0;
        $html = "";
        $html.="<table border='1'> <tr> <th>Turno</th><th>Remitente</th><th>T. espera</th><th></th> </tr>";
        $turnosTransferidos = new TurnosTransferidos();
        $buscaTurnosTransferidos = $turnosTransferidos->find("por_atender=0 AND fecha_emision='$fecha'", "order: fecha_emision");
        $fun = new Funciones();
        foreach ($buscaTurnosTransferidos as $result) {
            $permisoCajas = $result->getPermisoCajas();
            $separa = explode(',', $permisoCajas);
            foreach ($separa as $id) {
                if ($this->caja_id == $id) {
                    //$cont_registros+=1;
                    $id = $result->getId();
                    $letra = $result->getLetra();
                    $numero = $result->getNumero();
                    //$remitente=$result->getRemitente();
                    $ubicacion_id = $result->getUbicacionId();
                    //$hora_emision=$result->getHoraEmision();
                    $turno = $letra . $numero;
                    $fecha_inicio = $result->getFechaEmision() . " " . $result->getHoraTransferido();
                    $fecha_fin = $fecha . " " . $hora;
                    $tiempo_esperando = $fun->difFecha($fecha_inicio, $fecha_fin);
                    if ($result->getRemitente() == 0)
                        $remitente = "Aministrador";
                    else
                        $remitente = "MODULO " . $result->getRemitente();
                    $html.="<tr> <td>$turno</td><td>$remitente</td><td>$tiempo_esperando</td><td><input type='button' value='llamar' onclick=" . " \"llamar_t_transferido($id,'$letra',$numero,$ubicacion_id)\" " . "/></td> </tr>";
                }
            }
        }
        $html.="</table>";
        $datos = array("lista_turnos_transferidos" => $html);
        return ($datos);
    }

    /*
     * Funcion que permite llamar al turno actualizando la tabla turnos_transferidos
     * el campo por_atender=1 para que otro operador no llame a este.
     */

    public $id_t_t_atender;
    public $normal;
    public $ubicacion_t_t;

    public function llamarTurnoTransferidoAction() {
        $this->setResponse("json");
        $this->id_t_t_atender = $this->getPostParam("id_t_t_atender");
        $this->normal = $this->getPostParam("normal");
        $this->ubicacion_t_t = $this->getPostParam("ubicacion_t_t");
        $servicio = $this->getPostParam('servicio');
        $turno = $this->getPostParam('turno');
        $numcaja = $this->getPostParam('num_caja');

        $fecha_hoy = date("Y-m-d");

        //----------a�adido-------
        //obtener id del usuario que esta atendiendo en caja

        $db = DbBase::rawConnect();
        $result = $db->query("SELECT usuario_id
                                FROM sesiones
                                WHERE caja_id = $this->caja_id");
        $id_usuario = 0;
        while ($row = $db->fetchArray($result)) {
            $id_usuario = $row['usuario_id'];
        }

        //----------Fin----
        $turnosTransferidos = new TurnosTransferidos();
        /* $buscaTurnosTransferidos= $turnosTransferidos->findFirst("id=$id_t_t_atender");
          $letra=$buscaTurnosTransferidos->getLetra();
          $numero=$buscaTurnosTransferidos->getNumero();
          $ubicacion_id=$buscaTurnosTransferidos->getUbicacionId(); */

        $turnosTransferidos->updateAll("por_atender= 1, id_user_atiende=$id_usuario, caja_id= $this->caja_id", "id= $this->id_t_t_atender");
    }

    /*
     * Funciï¿½n que permite listar los servicios al que se desea transferir el turno
     */

    public function listarServiciosAction() {
        $this->setResponse("json");
        $html = "";
        $servicio = new Servicio();
        $buscaServicios = $servicio->find("estado=1");
        foreach ($buscaServicios as $result) {
            $servicio_id = $result->getId();
            $nombre_servicio = $result->getNombre();
            $ubicacion_id = $result->getUbicacionId();
            $html.="<input type='radio' id='radio_servicio' name='radio_servicio' value='$servicio_id' onclick='obtener_operadores($servicio_id, $ubicacion_id)'/>$nombre_servicio<br>";
        }
        $datos = array("lista_servicios" => $html);
        return ($datos);
    }

    /*
     * Funciï¿½n que permite listar todo los turnos por servicios
     */

    public function listarTurnosTodosAction() {
        $this->setResponse("json");
        $html = "";

        $sql = new ActiveRecordJoin(array(
            "entities" => array("Servicio", "Serviciocaja", "Caja"),
            "fields" => array(
                "{#Servicio}.id",
                "{#Servicio}.nombre"
            ),
            "conditions" => "{#Caja}.id=$this->caja_id")
        );
        foreach ($sql->getResultSet() as $result) {
            $servicio_id = $result->getId();
            $nombre_servicio = $result->getNombre();

            $html.="<b>" . $nombre_servicio . "</b>";

            $html.="&nbsp;&nbsp; <input type='image' src='../../img/abrir.png' style='width:15px' title='Ver turnos' onclick='abrir_div_turnos($servicio_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../../img/cerrar.png' style='width:15px' title='Ocultar turnos' onclick='cerrar_div_turnos($servicio_id)'>";
            $html.="&nbsp;&nbsp;|&nbsp;&nbsp;<input type='image' src='../../img/check_all.gif' style='width:15px' title='Seleccionar todos los turnos' onclick='check_all($servicio_id)'> &nbsp;&nbsp;";
            $html.="<input type='image' src='../../img/uncheck_all.gif' style='width:15px' title='Deseleccionar todos los turnos' onclick='uncheck_all($servicio_id)'> <br>";
            $html.="<div id='div_servicio_$servicio_id' name='div_servicio_$servicio_id'>";
            $html.=$this->listarTurnosTodos($servicio_id);
            $html.="</div>";
        }

        $datos = array("lista_turnos_todos" => $html);
        return ($datos);
    }

    /*
     * Funciï¿½n que permite listar los turnos dependiendo del servicio que antes seleccionï¿½
     */

    public function listarTurnosTodos($servicio_id) {
        $fecha_hoy = date("Y-m-d");
        $hora_hoy = date("H:i:s");

        $html = "";

        $condition = "fecha_emision='$fecha_hoy' AND por_atender=0 AND atendido=0 AND transferido=0 AND {#Servicio}.id=$servicio_id";
        $sql = new ActiveRecordJoin(array(
            "entities" => array("Servicio", "Turnos"),
            "fields" => array(
                "{#Turnos}.id",
                "{#Servicio}.letra",
                "{#Turnos}.numero",
                "{#Turnos}.fecha_emision",
                "{#Turnos}.hora_emision",
            ),
            "conditions" => $condition)
        );

        $html.="<fieldset class='ui-corner-all ui-widget-content'>
                    <legend></legend>";
        $html.="<table border='0'> <tr> <th>Turno</th> <th>Tiempo esperando</th> </tr>";
        foreach ($sql->getResultSet() as $result) {
            $turno_id = $result->getId();
            $letra_servicio = $result->getLetra();
            $numero_turno = $result->getNumero();
            $fecha_emision = $result->getFechaEmision();
            $hora_emision = $result->getHoraEmision();
            $turno = $letra_servicio . $numero_turno;
            $fun = new Funciones();
            $fi = $fecha_emision . " " . $hora_emision;
            $ff = $fecha_hoy . " " . $hora_hoy;
            $duracion = $fun->difFecha($fi, $ff);

            $html.="<tr>";
            $html.="<td style='padding-left:8px; padding-right:8px'><input type='checkbox' id='chk_turno' name='chk_turno' value='$turno_id' class='c_$servicio_id'/>Turno $turno </td>";
            $html.="<td style='padding-left:8px; padding-right:8px'>$duracion</td>";
            $html.="</tr>";
        }
        $html.="</fieldset>";
        $html.="</table>";
        return ($html);
    }

    /*
     * Ver menï¿½ de pausas
     */

    public function verPausasAction() {
        $this->setResponse("json");
        $html = "";
        $pausas = new Pausas();
        //$buscarPausas= $pausas->find("estado=1");
        $buscarPausas = $pausas->find();
        foreach ($buscarPausas as $result) {
            $pausaId = $result->getId();
            $nombrePausa = $result->getNombrePausa();
            $maximoPermitido = $result->getMaximoPermitido();
            $html.="<input type='radio' id='radio_pausas' name='radio_pausas' value='$pausaId' onclick='caja_pausas_id($pausaId)'/>$nombrePausa<br>";
        }
        $datos = array("lista_pausas" => $html);
        return ($datos);
    }

    /**
     * Guarda el regristro de pausa del operador
     */
    public function pausarAction() {
        $this->setResponse("json");
        $cajaId = $this->caja_id;
        $usuarioId = $this->usuario_id;
        $pausasId = $this->getPostParam("pausas_id");
        $creacionAt = $this->getPostParam("creacion_at");

        $id = null;

        $cajaPausas = new CajaPausas();
        $cajaPausas->setId($id);
        $cajaPausas->setUsuarioId($usuarioId);
        $cajaPausas->setCajaId($cajaId);
        $cajaPausas->setPausasId($pausasId);
        $cajaPausas->setFechaInicio(date('Y-m-d'));
        $cajaPausas->setHoraInicio(date('H:i:s'));
        $cajaPausas->setEstado(1);
        $cajaPausas->setDuracion('00:00:00');
        $cajaPausas->setCreacionAt($creacionAt);
        $cajaPausas->save();
        $cajaPausas->find("order: id DESC", "limit: 1");
        $id_c = $cajaPausas->getId();
        /* if($cajaPausas->save()==false) {
          Flash::error('Hubo un error guardando el registro.');
          }else {
          Flash::success('Registro guardado con Ã©xito.');
          } */
        $datos = array("id_caja_pausas" => $id_c);
        return ($datos);
    }

    /**
     * Ver menu de preferencia de servicio
     */
    public function verServiciosAction() {
        $this->setResponse("json");
        $fecha_hoy = date("Y-m-d");
        $html = "";
        $condicion = "{#Caja}.id=$this->caja_id AND {#Servicio}.estado=1";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Caja", "Servicio", "Serviciocaja"),
            "fields" => array(
                "{#Servicio}.id",
                "{#Servicio}.nombre",
                "{#Servicio}.letra",
                "{#Serviciocaja}.preferencia_servicio"
            ),
            "conditions" => $condicion,
            "order" => "{#Servicio}.nombre"
        ));
        $html.="<table><tr><th>Servicio</th><th>T. en espera</th><th>Mis T. atendidos</th><th>T. atendidos</th></tr>";
        foreach ($query->getResultSet() as $result) {
            //--contar los turnos segun el servicio
            $db = DbBase::rawConnect();
            $result_2 = $db->query("SELECT COUNT(*) AS turnos_espera
                FROM turnos t
                WHERE fecha_emision ='$fecha_hoy' AND por_atender=0 AND servicio_id=$result->id");
            $turnos_espera = 0;
            while ($row = $db->fetchArray($result_2)) {
                $turnos_espera = $row['turnos_espera'];
            }

            //--contar mis turnos atendidos
            $mis_turnos_atendidos = 0;
            $result_2 = $db->query("SELECT COUNT(*) AS turnos_atendidos FROM turnos
            WHERE fecha_emision = '$fecha_hoy' AND atendido=1 AND id_username = $this->usuario_id AND servicio_id=$result->id");
            while ($row = $db->fetchArray($result_2)) {
                $mis_turnos_atendidos = $row['turnos_atendidos'];
            }
            
            //--contar los turnos atendidos
            $turnos_atendidos = 0;
            $result_2 = $db->query("SELECT COUNT(*) AS turnos_atendidos FROM turnos
            WHERE fecha_emision = '$fecha_hoy' AND atendido=1 AND servicio_id=$result->id");
            while ($row = $db->fetchArray($result_2)) {
                $turnos_atendidos = $row['turnos_atendidos'];
            }

            $html.="<tr><td>";
            if ($result->preferencia_servicio == 1)
                $html.="<input type='radio' id='radio_preferencia_servicio' name='radio_preferencia_servicio' value='$result->id' checked='checked' class='fakeRadio'/>$result->nombre<br>";
            else
                $html.="<input type='radio' id='radio_preferencia_servicio' name='radio_preferencia_servicio' value='$result->id' class='fakeRadio'/>$result->nombre<br>";
            $html.="</td>";
            $html.="<td>$turnos_espera</td>";
            $html.="<td>$mis_turnos_atendidos</td>";
            $html.="<td>$turnos_atendidos</td>";
            $html.="</tr>";
        }
        $html.="</table>";
        $datos = array("lista_servicios" => $html);
        return ($datos);
    }

    /**
     * Actualizar en 
     */
    public function actualizarPreferenciaServicioAction() {
        $this->setResponse("json");
        $servicio_id = $this->getPostParam('servicio_id');
        $servicioCaja = new Serviciocaja();
        $servicioCaja->updateAll("preferencia_servicio=0", "caja_id=$this->caja_id");
        if ($servicio_id != 'undefined')
            $servicioCaja->updateAll("preferencia_servicio=1", "servicio_id=$servicio_id AND caja_id=$this->caja_id");
        $datos = array("mensaje" => 'ok');
        return ($datos);
    }

    /**
     * Guarda el regristro de pausa del operador
     */
    public function actualizarPausaAction() {

        $id = $this->getPostParam('cajapausa_id');

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $cajaPausas = new CajaPausas();
        $buscaCajaPausas = $cajaPausas->findFirst("id=$id");
        $fechaInicio = $buscaCajaPausas->getFechaInicio();
        $horaInicio = $buscaCajaPausas->getHoraInicio();
        $fun = new Funciones();
        $duracion = $fun->difFecha($fechaInicio . " " . $horaInicio, $fecha . " " . $hora);
        //estado_pausa= 0 => ya no esta en pausa
        $cajaPausas->updateAll("fecha_fin= '$fecha', hora_fin= '$hora', duracion= '$duracion', estado= 0", "id=$id");

        //$datos= array("lista_pausas"=>$html);
        //return ($datos);
    }

    /**
     * Allamo al iniciar
     */
    public function calificadorAction() {
        $nombreip = $_SERVER['REMOTE_ADDR'];
        $caja = new Caja();
        $buscaCaja = $caja->findFirst("ip='$nombreip'");
        $idcaja = $buscaCaja->getId();
        $numcaja1 = $buscaCaja->getNumeroCaja();
        $tipo_calificacion_operador = $buscaCaja->getTipoCalificacionOperador();
        $t = explode(';', $tipo_calificacion_operador);
        if (count($t) >= 2) {
            $this->id_grupopregunta = $t[1];
            $preguntas = new Preguntas();
            $this->num_preguntas = $preguntas->count("conditions: publicar=1 AND id_grupopregunta=$this->id_grupopregunta", "order: orden");
            if ($this->num_preguntas > 8)
                $this->num_preguntas = 8;
        }
    }

}

?>
