<?php

/**
 * Controlador Display
 *
 * @access public
 * @version 1.0
 */
class DisplayController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * formato
     *
     * @var int
     */
    public $formato;

    /**
     * turnos
     *
     * @var string
     */
    public $turnos;

    /**
     * chkbanner
     *
     * @var int
     */
    public $chkbanner;

    /**
     * numvideos
     *
     * @var int
     */
    public $numvideos;

    /**
     * horainicio
     *
     */
    public $horainicio;

    /**
     * horafin
     *
     */
    public $horafin;

    /**
     * color_turnos
     *
     * @var string
     */
    public $colorTurnos;

    /**
     * color_noticias
     *
     * @var string
     */
    public $colorNoticias;

    /**
     * color_reloj
     *
     * @var string
     */
    public $colorReloj;

    /**
     * efecto_turno_superior
     *
     * @var int
     */
    public $efectoTurnoSuperior;
    public $logo_superior;
    public $carpeta;

    //public $nombre_empresa;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $iduser = $dataUsuario->getId();      //id de usuario
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        $this->carpeta = $buscaEmpresa->getCarpeta();
        //Tag::displayTo("nombre_empresa", $buscaEmpresa->getNombrecomercial());

        $sql = new ActiveRecordJoin(array(
                    "entities" => array("Usuario", "Pantalla", "Userpantalla"),
                    "fields" => array(
                        "{#Pantalla}.id",
                        "{#Pantalla}.color_turnos",
                        "{#Pantalla}.color_noticias",
                        "{#Pantalla}.color_reloj",
                        "{#Pantalla}.efecto_turno_superior"),
                    "conditions" => "{#Userpantalla}.usuario_id=$iduser"));
        $this->id = $sql->getResultSet()->getFirst()->getId();
        $this->colorTurnos = $sql->getResultSet()->getFirst()->getColorTurnos();
        $this->colorNoticias = $sql->getResultSet()->getFirst()->getColorNoticias();
        $this->colorReloj = $sql->getResultSet()->getFirst()->getColorReloj();
        $this->efectoTurnoSuperior = $sql->getResultSet()->getFirst()->getEfectoTurnoSuperior();
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $columnsGrid[] = array('name' => 'Formato', 'index' => 'formato');
        $columnsGrid[] = array('name' => 'Turnos', 'index' => 'turnos');
        $columnsGrid[] = array('name' => 'Chkbanner', 'index' => 'chkbanner');
        $columnsGrid[] = array('name' => 'Numvideos', 'index' => 'numvideos');
        $columnsGrid[] = array('name' => 'Horainicio', 'index' => 'horainicio');
        $columnsGrid[] = array('name' => 'Horafin', 'index' => 'horafin');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Display/
     *
     */
    public function nuevoAction() {


        $this->setPersistance(true);

        $columnsGrid[] = array('name' => 'Video', 'index' => 'video');
        $columnsGrid[] = array('name' => 'Serie', 'index' => 'serie');
        $columnsGrid[] = array('name' => 'Prioridad', 'index' => 'prioridad');
        $columnsGrid[] = array('name' => 'Display', 'index' => 'display_id');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Editar el Display
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $display = $this->Display->findFirst($id);
        if ($display) {
            Tag::displayTo('id', $display->getId());
            Tag::displayTo('formato', $display->getFormato());
            Tag::displayTo('turnos', $display->getTurnos());
            Tag::displayTo('chkbanner', $display->getChkbanner());
            Tag::displayTo('numvideos', $display->getNumvideos());
            Tag::displayTo('horainicio', $display->getHorainicio());
            Tag::displayTo('horafin', $display->getHorafin());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Display
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $formato = $this->getPostParam("formato", "int");
        $turnos = $this->getPostParam("turnos", "striptags", "extraspaces");
        $chkbanner = $this->getPostParam("chkbanner", "int");
        $numvideos = $this->getPostParam("numvideos", "int");
        $horainicio = $this->getPostParam("horainicio");
        $horafin = $this->getPostParam("horafin");
        $display = new Display();
        $display->setId($id);
        $display->setFormato($formato);
        $display->setTurnos($turnos);
        $display->setChkbanner($chkbanner);
        $display->setNumvideos($numvideos);
        $display->setHorainicio($horainicio);
        $display->setHorafin($horafin);
        if ($display->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
        } else {
            Flash::success('Registro guardado con éxito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Display
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Display->delete($ids[$i])) {
                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
                } else {
                    $msg.="El registro $ids[$i] fue eliminado correctamente.";
                }
            }
        }
        echo $msg;
    }

    public function obtenerDatosGridAction() {
        $this->setResponse('ajax');  // asignamos el tipo de respuesta para esta accion
        $pagina = $this->getPostParam('page'); // obtener el numero de pagina
        $limite = $this->getPostParam('rows'); // obtener el n�mero de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de �ndice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la direcci�n de ordenado
        if (!$col_orden)
            $col_orden = 1;
        //construccion de condicion de consulta
        $condicion = '1';
        $buscar = $this->getPostParam('_search', 'stripslashes'); //obtenemos la variable que activa la busqueda
        $strbusqueda = $this->getPostParam('searchString');  // obtenemos la cadena de busqueda
        $campoBusqueda = $this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda
        $abrevoper = $this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda
        $filtroBusqueda = $this->getPostParam('filters', 'stripslashes');
        if ($buscar == 'true') { //verificamos si la busqueda es activada
            if ($strbusqueda != '') {    // construccion de la cadena de condicion para la busqueda normal
                switch ($campoBusqueda) {
                    
                }
                $condicion.=' AND ' . Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
            } elseif ($filtroBusqueda) {
                $jsona = json_decode($filtroBusqueda, true);
                if (is_array($jsona)) {
                    $gopr = $jsona['groupOp'];
                    $rules = $jsona['rules'];
                    $i = 0;
                    foreach ($rules as $key => $val) {
                        $field = $val['field'];
                        switch ($field) {
                            
                        }
                        $op = $val['op'];
                        $v = $val['data'];
                        if ($v && $op) {
                            $i++;
                            $v = Utils::toSqlParamSearchGrid($field, $op, $v);
                            if ($i == 1)
                                $condicion.=' AND ';
                            else
                                $condicion.= " " . $gopr . " ";
                            $condicion.= $v;
                        }
                    }
                }
            }
            //construimos la condicion por barra de busqueda del grid
            $sarr = $_POST;
            foreach ($sarr as $k => $v) {
                switch ($k) {
                    case 'formato':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'turnos':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'chkbanner':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'numvideos':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'horainicio':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'horafin':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Display->count("conditions: $condicion");  //contar el numero total de registros existentes
        //obtenemos el numero de paginas para el grid
        if ($contar > 0) {
            $total_pags = ceil($contar / $limite);
        } else {
            $total_pags = 0;
        }
        if ($pagina > $total_pags)
            $pagina = $total_pags;
        $inicio = $limite * $pagina - $limite; // no poner $limite*($pagina - 1)
        if ($inicio < 0)
            $inicio = 0;
        $limite = $inicio + $limite;  // igualamos el limite al total de registros que se obtendra hasta la pagina actual
        $resultado = $this->Display->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Display = $resultado[$i];
            $jqgrid->rows[] = array('id' => $Display->getId(), 'cell' => array($Display->getFormato(), $Display->getTurnos(), $Display->getChkbanner(), $Display->getNumvideos(), $Display->getHorainicio(), $Display->getHorafin()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /*
     * Función que muestra los turnos en la pantalla
     */

    public function verturnosAction() {
        $this->setResponse("json");

        if (!empty($this->id)) {
            $fecha_hoy = date("Y-m-d");
            $sql2 = new ActiveRecordJoin(array("entities" => array("Pantalla", "Displayturno"),
                        "fields" => array("{#Displayturno}.numeroturno",
                            "{#Displayturno}.cajanumero"),
                        "conditions" => "{#Displayturno}.ubicacion={#Pantalla}.ubicacion_id AND {#Pantalla}.id=$this->id AND {#Displayturno}.fecha='$fecha_hoy'",
                        "order" => "{#Displayturno}.id DESC limit 12"));
            $turno = "";
            $array_turnos = array();
            $array_turnos_aux = array();
            $array_modulos_aux = array();
            foreach ($sql2->getResultSet() as $result) {
                $ver = $turno . $result->getNumeroturno();
                $vera = $result->getCajanumero();
                $array_turnos[] = $ver;
                $array_turnos_aux[] = $ver;
                $array_modulos[] = $vera;
                $array_modulos_aux[] = $vera;
            }
            $cont = count($array_turnos);
            for ($i = 0; $i < $cont - 1; $i++) {
                for ($j = $i + 1; $j < $cont; $j++) {
                    if ($array_turnos[$i] == $array_turnos[$j]) {
                        unset($array_turnos_aux[$j]);
                        unset($array_modulos_aux[$j]);
                    }
                }
            }

            $mensajeturno1 = "";
            $mensajeturno2 = "";
            $mensajeturno3 = "";
            $mensajeturno4 = "";
            $mensajeturno5 = "";
            $mensajeturno6 = "";
            $mensajeturnoa1 = "";
            $mensajeturnoa2 = "";
            $mensajeturnoa3 = "";
            $mensajeturnoa4 = "";
            $mensajeturnoa5 = "";
            $mensajeturnoa6 = "";
            $array_turnos_aux1 = array();
            $array_modulos_aux1 = array();
            foreach ($array_turnos_aux as $valor)
                $array_turnos_aux1[] = $valor;
            foreach ($array_modulos_aux as $valor)
                $array_modulos_aux1[] = $valor;

            $cont_array = count($array_turnos_aux);
            $cont = 1;
            for ($i = 0; $i < $cont_array; $i++) {
                if ($cont == 1) {
                    $mensajeturno1 = $array_turnos_aux1[$i];
                    $mensajeturnoa1 = $array_modulos_aux1[$i];
                }
                if ($cont == 2) {
                    $mensajeturno2 = $array_turnos_aux1[$i];
                    $mensajeturnoa2 = $array_modulos_aux1[$i];
                }
                if ($cont == 3) {
                    $mensajeturno3 = $array_turnos_aux1[$i];
                    $mensajeturnoa3 = $array_modulos_aux1[$i];
                }
                if ($cont == 4) {
                    $mensajeturno4 = $array_turnos_aux1[$i];
                    $mensajeturnoa4 = $array_modulos_aux1[$i];
                }
                if ($cont == 5) {
                    $mensajeturno5 = $array_turnos_aux1[$i];
                    $mensajeturnoa5 = $array_modulos_aux1[$i];
                }
                if ($cont == 6) {
                    $mensajeturno6 = $array_turnos_aux1[$i];
                    $mensajeturnoa6 = $array_modulos_aux1[$i];
                }
                $cont = $cont + 1;
            }
            $respuesta = array("t1" => $mensajeturno1, "t2" => $mensajeturno2, "t3" => $mensajeturno3,
                "t4" => $mensajeturno4, "t5" => $mensajeturno5, "t6" => $mensajeturno6,
                "ta1" => $mensajeturnoa1, "ta2" => $mensajeturnoa2, "ta3" => $mensajeturnoa3,
                "ta4" => $mensajeturnoa4, "ta5" => $mensajeturnoa5, "ta6" => $mensajeturnoa6);
            return ($respuesta);
        }
    }

    /* Funci�n que permite ver la llamada del cajero
     */

    public function vercajaAction() {
        $this->setResponse("json");

        //obtener el id de la sesión actual;
        $datosUsuario = SessionNamespace::get("datosUsuarioSMC");
        $iduser = $datosUsuario->getId();

        $sql = new ActiveRecordJoin(array("entities" => array("Usuario", "Pantalla", "Userpantalla"),
                    "fields" => array("{#Pantalla}.id",
                        "{#Pantalla}.timbre"),
                    "conditions" => "{#Userpantalla}.usuario_id=$iduser"));
        $id_pantalla = $sql->getResultSet()->getFirst()->getId();
        $timbre = $sql->getResultSet()->getFirst()->getTimbre();

        $ver = "";
        $iddn = 0;
        $existe = 0;
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT id,cajanumero FROM displaycajas WHERE llamo = 0 ORDER BY id ASC LIMIT 1");
//        $result = $db->query("SELECT cajanumero FROM displaycajas ORDER BY id ASC LIMIT 1");
        while ($row = $db->fetchArray($result)) {
            $iddn = $row['id'];
            $ver = $row['cajanumero'];
        }

        if ($iddn != NULL) {
            $existe = 1;
            $db->query("UPDATE displaycajas SET llamo = 1 WHERE id = " . $iddn);
        }

        //INICIO ENVIAR VALOR 0 A TIMBRE
        $pantalla = new Pantalla();
        $condicion = "id= $id_pantalla";
        $pantalla->updateAll("timbre=0", "conditions: $condicion");
        //FIN ENVIAR VALOR 0 A TIMBRE

        $mensajeturno1 = "Pase a la caja " . $ver;

        //INICIO cambiar el mensaje en la pantalla
        $pantalla_ticket = '';
        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT 	pantalla_ticket FROM configuracionsistema ;");
        while ($row = $db->fetchArray($result2)) {
            $pantalla_ticket = $row['pantalla_ticket'];
        }
        // if($pantalla_ticket==1) $mensajeturno1="Pase a la :C ".$ver;
        if ($pantalla_ticket == 1)
            $mensajeturno1 = $ver;
        //FIN cambiar el mensaje en la pantalla		

        $respuesta = array("t1" => $mensajeturno1, "v" => $timbre, "numcaja" => $ver, "existe" => $existe);
        //$respuesta= array("t1"=>"Pase a la caja 1","v"=>"1","numcaja"=>"1");
        return ($respuesta);
    }

    public function vervideoAction() {
        //$this->setPersistance(true);
        $orientacion = "";
        $ubicacion = "";
        $nombre = "";
        $t = "";

        //obtener el id de la sesión actual();
        $datosUsuario = SessionNamespace::get("datosUsuarioSMC");
        $iduser = $datosUsuario->getId();

        $this->setResponse("json");

        //instanciamos las clases Pantalla, Video y Pantallavideos
        $vid = new Video();
        $pantalla = new Pantalla();
        $pantalla_videos = new Pantallavideos();
        $user_pantalla = new Userpantalla();

        //obtener el id de pantalla según el usuario del SessionNamespace
        $buscaUsuarioPantalla = $user_pantalla->find("usuario_id= $iduser");
        foreach ($buscaUsuarioPantalla as $res) {
            $idpantalla = $res->getPantallaid();
        }

        //Contar el total de videos asignados a la Pantalla
        $contarVideo = $pantalla_videos->find("pantalla_id= $idpantalla");
        $total_registros = count($contarVideo);

        //Encontrar los videos asignados según la Pantalla
        $condicion = "{#Pantallavideos}.pantalla_id = $idpantalla and {#Pantallavideos}.reproducir = 1";
        $query = new ActiveRecordJoin(array(
                    "entities" => array("Pantalla", "Video", "Pantallavideos"),
                    "fields" => array(
                        "{#Video}.nombre",
                        "{#Video}.ubicacion",
                        "{#Video}.duracion",
                        "{#Pantallavideos}.id",
                        "{#Pantallavideos}.orden"),
                    "conditions" => $condicion
                ));

        foreach ($query->getResultSet() as $result) {
            $nombre = $result->getNombre();
            $ubicacion = $result->getUbicacion();
            $id_pantallavideo = $result->getId();
            $orden = $result->getOrden();
            if ($orden == 1) {
                $or = $total_registros;
                $condicion2 = "{#Pantallavideos}.pantalla_id = $idpantalla and {#Pantallavideos}.orden = $or";
            } else {
                $orden_1 = $orden - 1;
                $condicion2 = "{#Pantallavideos}.pantalla_id = $idpantalla and {#Pantallavideos}.orden = $orden_1";
            }
            $query1 = new ActiveRecordJoin(array(
                        "entities" => array("Pantalla", "Video", "Pantallavideos"),
                        "fields" => array(
                            "{#Video}.duracion"
                        ),
                        "conditions" => $condicion2
                    ));

            foreach ($query1->getResultSet() as $result2) {
                $t = $result2->getDuracion();
            }

            if ($orden == $total_registros) {
                $cond2 = "orden=1 and pantalla_id=" . $idpantalla;
                $pantalla_videos->updateAll("reproducir=0", "id= $id_pantallavideo");
                $pantalla_videos->updateAll("reproducir=1", "conditions: $cond2");
                
            } else {
                $orden1 = $orden + 1;
                $cond1 = "orden=" . $orden1 . " and pantalla_id=" . $idpantalla;
                $pantalla_videos->updateAll("reproducir=0", "id= $id_pantallavideo");
                $pantalla_videos->updateAll("reproducir=1", "conditions: $cond1");
            }
        }
        $n = $ubicacion . '/' . $nombre;
        $respuesta = array("tiempo" => $t, "nombre" => $n);
        return ($respuesta);
    }

    /* Funci�n que emite el sonido de llamado de turno
     */

    public function timbrarTurnoAction() {
        $this->setResponse("json");

        $val = 0;
        $existe = 0;
        $datosUsuario = SessionNamespace::get("datosUsuarioSMC");
        $iduser = $datosUsuario->getId();
        
        //---agregado
        $fecha_hoy = date("Y-m-d");
        $sql2 = new ActiveRecordJoin(array("entities" => array("Pantalla", "Displayturno"),
                    "fields" => array("{#Displayturno}.numeroturno","{#Displayturno}.id","{#Displayturno}.turno",
                        "{#Displayturno}.cajanumero"),
                    "conditions" => "{#Displayturno}.ubicacion={#Pantalla}.ubicacion_id AND {#Pantalla}.id=$this->id AND {#Displayturno}.fecha='$fecha_hoy' AND {#Displayturno}.turno='0'",
                    "order" => "{#Displayturno}.id DESC limit 1"));
        $id_display_turno = 0;
        $numero_turno = "";
        $caja_numero = 0;
        $turno = 0;
        foreach ($sql2->getResultSet() as $result) {
            $id_display_turno = $result->getId();
            $numero_turno = $result->getNumeroturno();
            $caja_numero = $result->getCajanumero();
            $turno = $result->getTurno();
        }

        if ($id_display_turno != NULL) {
            $existe = 1;
            $displayturno = new Displayturno();
            $condicion = "id= $id_display_turno";
            $displayturno->updateAll("turno='1'", "conditions: $condicion");
        }
        //---fin
        
        $sql = new ActiveRecordJoin(array("entities" => array("Pantalla", "Userpantalla"),
                    "fields" => array("{#Pantalla}.id",
                        "{#Pantalla}.timbre"
                    ),
                    "conditions" => "{#Userpantalla}.usuario_id=$iduser"
                ));
        $val = $sql->getResultSet()->getFirst()->getTimbre();
        $idpantalla = $sql->getResultSet()->getFirst()->getId();


        //INICIO ENVIAR VALOR 0 A TIMBRE

        $pantalla = new Pantalla();
        $condicion = "id= $idpantalla";
//        $pantalla->find($condicion);
//        $timbre = $pantalla->getTimbre();
//        if ($timbre == 1) {
//            $existe = 1;
//        }
        $pantalla->updateAll("timbre=0", "conditions: $condicion");
        //FIN ENVIAR VALOR 0 A TIMBRE
        //$respuesta= array("timbre"=>$n,"efecto"=>$efecto);;
        
        $respuesta = array("reproducir" => $val, "existe" => $existe, "numero_turno" => $numero_turno, "caja_numero" => $caja_numero);
        return ($respuesta);
    }

    public $difundir;
    public $fondo_pantalla;
    public $flash_pantalla;
    public $velocidad_publicacion;
    public $publicar_noticias;
    public $pc_difusion;
    public $puerto;

    public function pantalla_con_ticketAction() {
        $this->setPersistance(true);
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        Tag::displayTo("nombre_empresa", $buscaEmpresa->getNombrecomercial());
        $this->fondo_pantalla = $buscaEmpresa->getFondopantalla();
        $this->flash_pantalla = $buscaEmpresa->getFlashpantalla();
        $configuracion = new Configuracionsistema();
        $buscaConfiguracion = $configuracion->findFirst();
        $this->velocidad_publicacion = $buscaConfiguracion->getVelocidadPublicacion();
        $this->publicar_noticias = $buscaConfiguracion->getPublicarNoticias();
        $this->pc_difusion = $buscaConfiguracion->getPcDifusion();
        $this->puerto = $buscaConfiguracion->getPuerto();

        //INICIO PRIMERO CONSULTO SI ES PANTALLA CON DIFUSION
        $configuracion = new Configuracionsistema();
        $buscaConfiguracion = $configuracion->findFirst();
        $this->difundir = $buscaConfiguracion->getActivarDifusion();
        //FIN PRIMERO CONSULTO SI ES PANTALLA CON DIFUSION
    }

    /* Esta funcion se aument� para las pantalla de cajeros y no operadores
     */

    public function pantalla_sin_ticketAction() {
        $displaycajas = new Displaycajas();
        $displaycajas->sql("TRUNCATE displaycajas");
        $this->setPersistance(true);
    }

    public function pantallaAction() {
        $this->setPersistance(true);
        $empresa = new Empresa();
        $configuracion = new Configuracionsistema();
        $buscaConfiguracion = $configuracion->findFirst();
        $this->velocidad_publicacion = $buscaConfiguracion->getVelocidadPublicacion();
        $this->publicar_noticias = $buscaConfiguracion->getPublicarNoticias();
        $this->pc_difusion = $buscaConfiguracion->getPcDifusion();
        $this->puerto = $buscaConfiguracion->getPuerto();
    }

    /*
     * Funcion que permite consultar el horario de la publicacion
     */

    public $tipo;
    public $h_i;
    public $h_f;

    //public $archivo_publicidad;
    public function verHorarioAction() {
        $this->setResponse('json');
        $archivo_publicidad = "";
        $hora_actual = strtotime(date("H:i:s"));
        $horariopublicidad = new Horariopublicidad();
        $buscaHorariopublicidad = $horariopublicidad->find("pantalla_id=$this->id");
        $r = "P";
        foreach ($buscaHorariopublicidad as $result) {
            $hora_inicio = strtotime($result->getHoraInicio());
            $hora_fin = strtotime($result->getHoraFin());
            $tipo = $result->getTipo();
            if (($hora_actual > $hora_inicio) & ($hora_actual < $hora_fin)) {
                if ($tipo != '@')
                    $r = $tipo;
                else
                    $r = 'P';
                break;
            }
        }
        if ($r == 'P') {
            $archivo_publicidad = $this->carpeta . "/publicidad/publicidad.swf";
            $pantallapublicidad = new Pantallapublicidad();
            $query = new ActiveRecordJoin(array("entities" => array("Publicidad", "Pantallapublicidad"),
                        "fields" => array(
                            "{#Publicidad}.archivo_publicidad"
                        ),
                        "conditions" => "{#Pantallapublicidad}.pantalla_id=$this->id",
                        "order" => "{#Publicidad}.archivo_publicidad")
            );
            if (!empty($query)) {
                //$archivo_publicidad=$query->getResultSet()->getFirst()->getArchivoPublicidad();
                foreach ($query->getResultSet() as $result) {
                    //echo $result->getArchivoPublicidad();
                    $archivo_publicidad = $result->getArchivoPublicidad();
                }
            }
            else
                $archivo_publicidad = "publicidad.swf";
        }
        $datos = array('tipo' => $r, 'archivo' => $archivo_publicidad);
        //return($r);
        return($datos);
    }

    /*
     * Permite las noticias RSS seg�n la pantalla
     */

    public function verNoticiasRssAction() {
        include 'lastRSS.php';
        $array_noticias = array();
        $this->setResponse('ajax');
        $marquee_direccion = $this->getPostParam('marquee_direccion');
        $marquee_ancho = $this->getPostParam('marquee_ancho');
        $marquee_alto = $this->getPostParam('marquee_alto');
        $marquee_font_size = $this->getPostParam('marquee_font_size') . "px";
        $marquee_alineacion = $this->getPostParam('marquee_alineacion');

        $html = "";
        // Options de base
        //$url_flux_rss = 'http://www.eluniverso.com/rss/deportes.xml';
        //$url_flux_rss= $this->getPostParam('url_feed');
        //$limite       = 1; // nombre d'actus � afficher
        // on cr�e un objet lastRSS
        $rss = new lastRSS;

        // options lastRSS
        $rss->cache_dir = './cache'; // dossier pour le cache
        $rss->cache_time = 3600;      // fr�quence de mise � jour du cache (en secondes)
        //$rss->date_format = 'Y-m-d H:i:s';     // format de la date (voir fonction date() pour syntaxe)
        $rss->date_format = 'j M Y g:i a';     // format de la date (voir fonction date() pour syntaxe)

        $rss->CDATA = 'content'; // on retire les tags CDATA en conservant leur contenu
        // lecture du flux
        $query = new ActiveRecordJoin(array("entities" => array("Feed", "Pantallafeed"),
                    "fields" => array(
                        "{#Feed}.url_feed",
                        "{#Pantallafeed}.limite_items"
                    ),
                    "conditions" => "{#Pantallafeed}.pantalla_id=$this->id",
                    "order" => "{#Feed}.url_feed")
        );
        $rss_seleccionadas = '';
        if (!empty($query)) {
            foreach ($query->getResultSet() as $result) {
                $url_flux_rss = $result->getUrlFeed();
                $limite = $result->getLimiteItems();
                if ($rs = $rss->get($url_flux_rss)) {
                    for ($i = 0; $i < $limite; $i++) {
                        $fecha = $rs['items'][$i]['pubDate'];
                        $titulo = $rs['items'][$i]['title'];
                        $descripcion = $rs['items'][$i]['description'];
                        //$array_noticias[]="<img src='../../img/$this->carpeta/sistema/separador.png' width='20px' height='20px' alt='guion'/> <strong>$fecha</strong> &middot; $titulo   ";
                        $rss_seleccionadas = $rss_seleccionadas . "<img src='../../img/$this->carpeta/sistema/separador.png' width='20px' height='20px' alt='guion'/> <strong>$fecha</strong> &middot; $titulo   ";
                    }
                }
            }
        }

        $fecha_hoy = date("Y-m-d");
        $noticias = new Noticias();
        $buscaNoticia = $noticias->find("publicar=1 AND '$fecha_hoy' BETWEEN fecha_inicio_publicacion AND fecha_fin_publicacion");
        $titulo_ynoticia = '';
        foreach ($buscaNoticia as $result) {
            $titulo_ynoticia = $titulo_ynoticia . "<img src='../../img/$this->carpeta/sistema/separador.png' width='20px' height='20px' alt='guion'/> <strong>{$result->getTitulo()}</strong> &middot; {$result->getNoticia()}   ";
            ;
            //$array_noticias[]="<img src='../../img/$this->carpeta/sistema/separador.png' width='20px' height='20px' alt='guion'/> <strong>{$result->getTitulo()}</strong> &middot; {$result->getNoticia()}   ";
        }

        /*
          shuffle($array_noticias);
          $html.="<marquee direction='$marquee_direccion' align='$marquee_alineacion' scrollamount='".$this->velocidad_publicacion."' width='$marquee_ancho' height='$marquee_alto' id='Marquesina' bgcolor='transparent'>
          <font style='color:#$this->colorNoticias; font-size:$marquee_font_size';>.";
          foreach($array_noticias as $valor) {
          $html.=$valor;
          }
          $html.="</font>
          </marquee>"; */
        /* else {
          die ('Flux RSS non trouv�');
          } */
        $texo_amostrar = $titulo_ynoticia . $rss_seleccionadas;
        $html = '<marquee height="'.$marquee_alto.'px" scrollamount="' . $this->velocidad_publicacion . '" direction="' . $marquee_direccion . '" style="font-size: 40px; color:#' . $this->colorNoticias . '; background:transparent">' . $texo_amostrar . '</marquee>';
        echo $html;

        //$html.='<strong>'.$rs['items'][$i]['pubDate'].'</strong> &middot; <a href="'.$rs['items'][$i]['link'].'">'.$rs['items'][$i]['description'].'</a><br />';
    }

    public function actualizarOrdenPantallaAction() {
        //INICIO ACTULIZACION DE ORDEN EN PANTALLA
        $pantallavideos = new Pantallavideos();
        $condicion1 = "pantalla_id= $this->id";
        $condicion2 = "pantalla_id= $this->id AND orden=1";
        $pantallavideos->updateAll("reproducir=0", "conditions: $condicion1");
        $pantallavideos->updateAll("reproducir=1", "conditions: $condicion2");
        //FIN ACTULIZACION DE ORDEN EN PANTALLA
    }

}

