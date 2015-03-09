<?php

class CajeroController extends ApplicationController {

    public $id; //id de la tabla colas
    //public $id_turno;
    public $fecha_inicio_atencion;
    public $hora_inicio_atencion;
    public $carpeta;
    public $caja_id;
    public $tipo_calificacion;      //A=Teclado, B=P. Simple, C=P. Matriz, D=Ninguno
    public $usuario_id;

    public function initialize() {
        $this->setPersistance(true);

        //busco caja_id que ha iniciado la sesiÃ³n
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $idsesion = $dataUsuario->getId();  //usuario_id
        $this->usuario_id = $idsesion;

        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst("columns: carpeta");
        $this->carpeta = $buscaEmpresa->getCarpeta();

        $caja = new Caja();
        $ip = $_SERVER['REMOTE_ADDR'];
        $buscaCaja = $caja->findFirst("ip='$ip'");
        $this->caja_id = $buscaCaja->getId();
    }

    public function indexAction() {
        //busco el id del usuario que ha iniciado la sesión
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $idsesion = $dataUsuario->getId();
        $usuario = $dataUsuario->getNombre();

        /* $sql=new ActiveRecordJoin(array("entities"=>array ("Usuario","Caja","Usercaja"),
          "fields"=>array ("{#Caja}.id","{#Caja}.numero_caja","{#Caja}.descripcion"),
          "conditions"=>"{#Usercaja}.usuario_id=$idsesion"));
          foreach ($sql->getResultSet() as $result) {
          $idcaja= $result->getId();
          $numcaja1= $result->getNumeroCaja();
          } */

        $caja = new Caja();
        $ip = $_SERVER['REMOTE_ADDR'];
        $buscaCaja = $caja->findFirst("ip='$ip'");
        $idcaja = $buscaCaja->getId();
        $numcaja1 = $buscaCaja->getNumeroCaja();
        $ipBase = $buscaCaja->getIp();

        $turnoactual = 0;
        $turnoespera = 0;
        $this->tipo_calificacion = $buscaCaja->getTipoCalificacionOperador();
        Tag::displayTo('actual1', $turnoactual);
        Tag::displayTo('espera1', $turnoespera);
        Tag::displayTo('numcaja', $numcaja1);
        Tag::displayTo('idcaja', $idcaja);
        Tag::displayTo('usuario', $usuario);
        Tag::displayTo('id_usuario', $idsesion);
        Tag::displayTo('ip', $ipBase);
        Tag::displayTo('tipo_calificacion', $buscaCaja->getTipoCalificacionOperador());
    }

    public function salirAction() {
        //            $dataUsuario = SessionNamespace::get('datosUsuario');
        //            $dat = Session::unsetData();
        //            print(Session::getId());die();

        SessionNamespace::drop("datosUsuarioSMC");
        //$this->routeTo("action: index");
        $this->routeTo("controller: login");
    }

    /*
      Funcion que permite saber si la caja actual debe realizar un llamado
     */

    public function timbreAction() {
        $this->setResponse('ajax');
        $idcaja = $this->getPostParam('idcaja');

        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT timbre FROM caja WHERE id = $idcaja");
        while ($row = $db->fetchArray($result2)) {
            $timbre = $row['timbre'];
        }
        if ($timbre != 0) {
            $result2 = $db->query("UPDATE caja SET timbre = 0 WHERE id = $idcaja");
        }
        echo $timbre;
    }

    public function horaAction() {
        $this->setResponse('ajax');
        $hora_hoy = date("H:i:s");
        echo $hora_hoy;
    }

    public function inicioAtencionAction() {
        $this->setResponse("json");
        $numcaja = $this->getPostParam('caja');
        $idcaja = $this->getPostParam('idcaja');
        $id_usuario = $this->getPostParam('id_usuario');
        $hora_hoy = $this->getPostParam('hora_hoy');

        $fecha_hoy = date("Y-m-d");
        //$hora_hoy=date("H:i:s");

        $objColas = new Colas();
        $objColas->setCajaId($idcaja);
        $objColas->setPorAtender(1);
        $objColas->setAtendido(0);
        $objColas->setFechaInicioAtencion($fecha_hoy);
        $objColas->setHoraInicioAtencion($hora_hoy);
//        $objColas ->setHoraFinAtencion($hora_fin_atencion);
//        $objColas ->setHoraInicioAtencion($hora_inicio_atencion);
//        $objColas ->setDuracion($duracion);
        $objColas->setCreacionAt($fecha_hoy);
        $objColas->setIdUsername($id_usuario);
        $objColas->save();
        $this->id = $objColas->getId();

        /*
          include('coneccion.php');
          //INICIO guardar en tabla colas
          $insertSQL_cliente = "INSERT INTO colas (caja_id, por_atender, atendido, fecha_inicio_atencion, hora_inicio_atencion, fecha_fin_atencion, hora_fin_atencion, duracion, id_username )
          VALUES ('$idcaja', 1, 0, '$fecha_hoy', '$hora_hoy', '0000-00-00', '0000-00-00', '00:00:00', $id_usuario)";
          mysql_select_db($database_pki, $pki);
          mysql_query($insertSQL_cliente, $pki) or die(mysql_error());
          //FIN guardar en tabla colas
          $this->id = mysql_insert_id();
         */
        $this->hora_inicio_atencion_caja = $hora_hoy;
    }

    /**
     * Funci?n que permite guardar el turno para los cajeros y no los operadores
     */
    public $fecha_inicio_atencion_caja;
    public $hora_inicio_atencion_caja;

    public function siguientecajaAction() {
        $this->setResponse("json");
        $numcaja = $this->getPostParam('caja');
        // $idcaja= $this->getPostParam('idcaja');
        // $id_usuario= $this->getPostParam('id_usuario');
        // $fecha_hoy=date("Y-m-d");
        // $hora_hoy=date("H:i:s");
        // $fecha_fin_atencion=date("Y-m-d");
        // $hora_fin_atencion=date("H:i:s");
        //INICIO ENVIAR VALOR 1 A TIMBRE PARA CAJEROS
        $pantalla = new Pantalla();
        $condicion = "tipo_pantalla= 'Pantalla Cajero'";
        $pantalla->updateAll("timbre=1", "conditions: $condicion");
        //FIN ENVIAR VALOR 1 A TIMBRE PARA CAJEROS


        $objDisplaycajas = new Displaycajas();
        $objDisplaycajas->setCajanumero($numcaja);
        $objDisplaycajas->setUbicacion(1);
        $objDisplaycajas->setLlamo(0);
        $objDisplaycajas->save();

        /*
          include('coneccion.php');
          //_______________
          $insertSQL_cliente = "INSERT INTO displaycajas (cajanumero,ubicacion) VALUES ('" . $numcaja . "','1')";
          mysql_select_db($database_pki, $pki);
          mysql_query($insertSQL_cliente, $pki) or die(mysql_error());
         */

//_______________		
        // $this->fecha_inicio_atencion_caja=$fecha_hoy;
        // $this->hora_inicio_atencion_caja=$hora_hoy;
        //INICIO PRIMERO GUARDAR LA COLA ATENDIDA
        // /*inicio calcula la duración*/
        // $fun = new Funciones();
        // $fi=$this->fecha_inicio_atencion_caja." ".$this->hora_inicio_atencion_caja;
        // $ff=$fecha_hoy." ".$hora_hoy;
        // $duracion= $fun->difFecha($fi, $ff);
        // /*fin calcula la duración*/
        //_______________
        // $insertSQL_cliente = "UPDATE colas
        // SET atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', 
        // duracion= SEC_TO_TIME(TIME_TO_SEC('$ff') - TIME_TO_SEC(CONCAT(fecha_inicio_atencion, ' ', hora_inicio_atencion)))
        // WHERE caja_id=$idcaja AND atendido=0 AND fecha_inicio_atencion= '$fecha_hoy'";
        // mysql_select_db($database_pki, $pki );
        // mysql_query($insertSQL_cliente, $pki ) or die(mysql_error());
        //_______________			
        //_______________
        // $insertSQL_cliente = "INSERT INTO colas (caja_id, por_atender, atendido, fecha_inicio_atencion, hora_inicio_atencion, fecha_fin_atencion, hora_fin_atencion, duracion, id_username ) VALUES ('$idcaja', 1, 0, '$fecha_hoy', '$hora_hoy', '0000-00-00', '0000-00-00', '00:00:00', $id_usuario)";
        // mysql_select_db($database_pki, $pki );
        // mysql_query($insertSQL_cliente, $pki ) or die(mysql_error());
        //_______________			
        // $this->id = mysql_insert_id();
    }

    /*     * ****** FUNCIONES PARA CALIFICACION PARA CAJAS ***
      /*
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

    /*
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

    /*
     * Funciï¿½n que permite terminar el turno para los tres tipo de calificaciï¿½n
     * Teclado, pantalla, matriz
     */

    public function terminarTurno($calificacion) {
        //echo "califica".$calificacion; die();
        $fecha_fin_atencion = date("Y-m-d");
        $hora_fin_atencion = date("H:i:s");

        /* inicio calcula la duraciÃ³n */
        $fun = new Funciones();
        $fi = $this->fecha_inicio_atencion_caja . " " . $this->hora_inicio_atencion_caja;
        $ff = $fecha_fin_atencion . " " . $hora_fin_atencion;
        $duracion = $fun->difFecha($fi, $ff);
        //echo "duracion:".$this->fecha_inicio_atencion;
        /* fin calcula la duraciÃ³n */

        $colas = new Colas();
        $colas->updateAll("atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion'", "id= $this->id");

        /* include('coneccion.php');
          //_______________
          $insertSQL_cliente = "UPDATE colas
          SET atendido= 1, fecha_fin_atencion= '$fecha_fin_atencion', hora_fin_atencion= '$hora_fin_atencion', duracion= '$duracion', calificacion= '$calificacion'
          WHERE id= $this->id";
          mysql_select_db($database_pki, $pki);
          $Result1 = mysql_query($insertSQL_cliente, $pki) or die(mysql_error());

          //_______________ */
    }

    public function terminaTurnoAction() {
        $this->setResponse("json");
        $calificacion = $this->getPostParam('calificacion');
        $this->terminarTurno($calificacion);
        $datos = array("pregunta" => 1, "puntuacion" => 1, "num_preguntas" => 1, "cont_preguntas" => 1);
        return ($datos);
    }

    /**
     * Funcion que permite terminar el turno y enviar la calificacion
     * para pantalla touch con excelente, muy bueno, bueno y regural
     */
    public function terminarTurnoPantallaAction() {
        $this->setResponse("json");
        $calificacion = $this->getPostParam('calificacion');

        $res = $this->terminarTurno($calificacion);    //Terminar el turno
        $datos = array();
        $datos = array('exito' => '1');
        return($datos);

        //$this->calificador();                   //LLAMAR A CALIFICADOR
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

        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $turnospreguntas = new Colaspreguntas();
        $turnospreguntas->setPreguntasId($id_pregunta);
        $turnospreguntas->setCajaId($this->caja_id);
        $turnospreguntas->setColasId($this->id);
        $turnospreguntas->setPuntuacion($puntuacion);
        $turnospreguntas->setFecha($fecha);
        $turnospreguntas->setHora($hora);
        $turnospreguntas->save();

        $calificacion = 'Matriz';     //Envï¿½o 'Matriz' si este ha sido calificado con matriz de preguntas
        //si son iguales entonces se termina el turno
        if ($cont_preguntas == $num_preguntas) {
            $this->terminarTurno($calificacion);    //Terminar el turno
            //$this->calificador();                   //LLAMAR A CALIFICADOR
        }

        $datos = array("pregunta" => $id_pregunta, "puntuacion" => $puntuacion, "num_preguntas" => $num_preguntas, "cont_preguntas" => $cont_preguntas);
        return ($datos);
    }

    /**
     * consulta el estado del timbre para cajero
     */
    public function estadoTimbreAction() {
        $this->setResponse("json");
        $ip = $this->getPostParam('ip');
        $objCajero = $this->Caja->find("ip = '$ip'");
        $estado = 0;
        foreach ($objCajero as $result) {
            $estado = $result->getTimbre();
        }
        if ($estado == 1) {
            $this->Caja->updateAll("timbre = 0 ", "ip = '$ip'");
        }
        $datos = array("estado" => $estado);
        return ($datos);
    }

    /**
     * consulta el estado del timbre para cajero
     */
    public function estadoCalificarAction() {
        $this->setResponse("json");
        $ip = $this->getPostParam('ip');
        $objCajero = $this->Caja->find("ip = '$ip'");
        $estado = 0;
        foreach ($objCajero as $result) {
            $estado = $result->getCalificar();
        }
        if ($estado == 1) {
            $this->Caja->updateAll("calificar = 0 ", "ip = '$ip'");
        }
        $datos = array("estado" => $estado);
        return ($datos);
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

    /*
     * Guarda el regristro de pausa del operador
     */

    public function pausarAction() {
        $this->setResponse("json");
        $cajaId = $this->caja_id;
        $usuarioId = $this->usuario_id;
        $pausasId = $this->getPostParam("pausas_id");
        //$creacionAt = $this->getPostParam("creacion_at");

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
        $cajaPausas->setCreacionAt(date('Y-m-d'));
        $cajaPausas->save();
        //$cajaPausas->find("order: id DESC", "limit: 1");
        //$id_c = $cajaPausas->getId();
        /* if($cajaPausas->save()==false) {
          Flash::error('Hubo un error guardando el registro.');
          }else {
          Flash::success('Registro guardado con Ã©xito.');
          } */
        $datos = array("id_caja_pausas" => $cajaPausas->getId());
        return ($datos);
    }

    /*
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
        //estado_pausa= 0 => ya no estï¿½ en pausa
        $cajaPausas->updateAll("fecha_fin= '$fecha', hora_fin= '$hora', duracion= '$duracion', estado= 0", "id=$id");

        //$datos= array("lista_pausas"=>$html);
        //return ($datos);
    }

}

?>
