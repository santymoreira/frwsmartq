<?php

/**
 * Controlador Pantalla
 *
 * @access public
 * @version 1.0
 */
class PantallaController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * numero
     *
     * @var int
     */
    public $numero;

    /**
     * descripcion
     *
     * @var string
     */
    public $descripcion;

    /**
     * ubicacion_id
     *
     * @var int
     */
    public $ubicacionId;

    /**
     * timbre
     *
     * @var int
     */
    public $timbre;

    /**
     * ip_equipo
     *
     * @var string
     */
    public $ipEquipo;

    /**
     * tipo_pantalla
     *
     * @var string
     */
    public $tipoPantalla;

    /**
     * con_ticket
     *
     * @var int
     */
    public $conTicket;

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

    /**
     * creacion_at
     *
     */
    public $creacionAt;

    /**
     * Inicializador del controlador/
     *
     */
    public $modulo_atencion_con_ticket;
    public $modulo_atencion_sin_ticket;

    public function initialize() {
        $this->setPersistance(true);

        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        $this->modulo_atencion_con_ticket = $buscaEmpresa->getModuloOperadores();
        $this->modulo_atencion_sin_ticket = $buscaEmpresa->getModuloCajas();
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        //$columnsGrid[]=array('name'=>'Numero','index'=>'numero');
        /* $columnsGrid[]=array('name'=>'Descripción','index'=>'descripcion');
          $columnsGrid[]=array('name'=>'Dirección IP','index'=>'ip_equipo','width'=>'80');
          $columnsGrid[]=array('name'=>'Ubicación','index'=>'ubicacion','width'=>'80'); */

        //$columnsGrid[]=array('name'=>'Id','index'=>'id');
        $columnsGrid[] = array('name' => 'Número', 'index' => 'numero', 'width' => '50');
        $columnsGrid[] = array('name' => 'Tipo pantalla', 'index' => 'tipo_pantalla', 'width' => '110');
        $columnsGrid[] = array('name' => 'Descripcion', 'index' => 'descripcion', 'width' => '180');
        $columnsGrid[] = array('name' => 'Ip Equipo', 'index' => 'ip_equipo', 'width' => '90');
        $columnsGrid[] = array('name' => 'Timbre/Voz', 'index' => 'tono', 'width' => '75');
        $columnsGrid[] = array('name' => 'Formato Voz', 'index' => 'tono', 'width' => '120');
        $columnsGrid[] = array('name' => 'Tiempo tono', 'index' => 'tiempo_tono', 'width' => '80');
        $columnsGrid[] = array('name' => 'Ubicación', 'index' => 'nombre_ubicacion', 'width' => '110');
        $columnsGrid[] = array('name' => 'Descripción Ubicación', 'index' => 'ubicacion_descripcion', 'width' => '180');
        $columnsGrid[] = array('name' => 'Usuario', 'index' => 'usuario_nombre', 'width' => '110');
        $columnsGrid[] = array('name' => 'Login', 'index' => 'usuario_nombre', 'width' => '80');
        $columnsGrid[] = array('name' => 'Estado usuario', 'index' => 'usuario_nombre', 'width' => '90');
        $columnsGrid[] = array('name' => 'Videos asignados', 'index' => 'videos_asignados', 'width' => '105');
        $columnsGrid[] = array('name' => 'Plantilla', 'index' => 'plantilla', 'width' => '110');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Pantalla/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Pantalla
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $pantalla = $this->Pantalla->findFirst($id);
        if ($pantalla) {
            Tag::displayTo('id', $pantalla->getId());
            Tag::displayTo('numero', $pantalla->getNumero());
            Tag::displayTo('descripcion', $pantalla->getDescripcion());
            Tag::displayTo('ip_equipo', $pantalla->getIpEquipo());
            Tag::displayTo('creacion_at', $pantalla->getCreacionAt());
            $tipo_pantalla = $pantalla->getTipoPantalla();
            //Tag::displayTo("radio_modelos", $value);
            $value2 = "";
            if ($tipo_pantalla == "Pantalla Operador")
                $value2 = "A";
            else if ($tipo_pantalla == "Pantalla Cajero")
                $value2 = "B";
            Tag::displayTo("radio_tipo_pantalla", $value2);

            $horariopublicidad = new Horariopublicidad();
            $total = $horariopublicidad->count("pantalla_id=$id");
            Tag::displayTo('txt1', $total);

            Tag::displayTo('color_turnos', $pantalla->getColorTurnos());
            Tag::displayTo('color_noticias', $pantalla->getColorNoticias());
            Tag::displayTo('color_reloj', $pantalla->getColorReloj());
            Tag::displayTo('efecto_turno_superior', $pantalla->getEfectoTurnoSuperior());
            Tag::displayTo('tono', $pantalla->getTono());
            Tag::displayTo('tiempo_tono', $pantalla->getTiempoTono());
            Tag::displayTo('formato_voz', $pantalla->getFormatoVoz());
            Tag::displayTo('plantilla', $pantalla->getPlantilla());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar en Pantalla
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $check_servicio = $this->getPostParam("chkvideo");
        $check_publicidad = $this->getPostParam("chkpublicidad");      //no eliminar
        //$radio_publicidad=$this->getPostParam("radio_publicidad");

        $usuario = $this->getPostParam("usuarios");
        $numero = $this->getPostParam("numero", "int");
        $descripcion = $this->getPostParam("descripcion", "striptags", "extraspaces");
        $ip_equipo = $this->getPostParam("ip_equipo", "striptags", "extraspaces");
        $colorTurnos = $this->getPostParam("color_turnos", "striptags", "extraspaces");
        $colorNoticias = $this->getPostParam("color_noticias", "striptags", "extraspaces");
        $colorReloj = $this->getPostParam("color_reloj", "striptags", "extraspaces");
        $efectoTurnoSuperior = $this->getPostParam("efecto_turno_superior");
        if ($efectoTurnoSuperior == 'on')
            $efectoTurnoSuperior = 1;
        else
            $efectoTurnoSuperior = 0;

        $ubicacion = $this->getPostParam("ubicacion", "striptags", "extraspaces"); //del combo de la ubicacion

        $radio_modelos = $this->getPostParam("radio_modelos");
        $radio_tipo_pantalla = $this->getPostParam("radio_tipo_pantalla");
        $formato_voz = $this->getPostParam("formato_voz");
        //$radio_tipo_pantalla="A";

        $creacionAt = $this->getPostParam("creacion_at");
        $tono = $this->getPostParam("tono");
        $tiempo_tono = $this->getPostParam("tiempo_tono");
        $ventana = $this->getPostParam("ventana");

        $pantalla = new Pantalla();
        $pantalla->setId($id);
        $pantalla->setNumero($numero);
        $pantalla->setDescripcion($descripcion);
        $pantalla->setIpEquipo($ip_equipo);
        $pantalla->setUbicacionId($ubicacion);
        if ($radio_tipo_pantalla == "A")
            $pantalla->setTipoPantalla("Pantalla Operador");
        else if ($radio_tipo_pantalla == "B")
            $pantalla->setTipoPantalla("Pantalla Cajero");
        $pantalla->setColorTurnos($colorTurnos);
        $pantalla->setColorNoticias($colorNoticias);
        $pantalla->setColorReloj($colorReloj);
        $pantalla->setEfectoTurnoSuperior($efectoTurnoSuperior);
        $pantalla->setCreacionAt($creacionAt);
        $pantalla->setTono($tono);
        $pantalla->setTiempoTono($tiempo_tono);
        $pantalla->setVentana($ventana);
        $pantalla->setFormatoVoz($formato_voz);
        $pantalla->setPlantilla(trim($this->getPostParam("plantilla")));


        $action = ($isEdit == true) ? "editar" : 'nuevo';

        /* Valores para ultimo video */
        $video = new Video();
        $buscaVideo = $video->findFirst("nombre='z-inicio.mpg'");
        $video_id1 = $buscaVideo->getId();

        //Tengo el id de la pantalla => $id
        if ($isEdit == true) {
            //Actualizar el usuario del userpantalla
            $usuario_pantalla = new Userpantalla();
            $usuario_pantalla->updateAll("usuario_id = $usuario", "pantalla_id= $id");

            //INICIO GUARDAR PANTALLAVIDEOS
            $pantallavideo_id = null;
            $pantalla_videos = new Pantallavideos();
            $whereCondition = "pantalla_id= $id";
            $pantalla_videos->deleteAll($whereCondition);
            if (!empty($check_servicio)) {
                $orden = 0;
                foreach ($check_servicio as $video) {
                    $orden = $orden + 1;
                    $pantalla_videos->setId($pantallavideo_id);
                    $pantalla_videos->setPantallaId($id);
                    $pantalla_videos->setVideoId($video);
                    $pantalla_videos->setActivo(1);
                    $pantalla_videos->setOrden($orden);
                    if ($orden == 1) {
                        $pantalla_videos->setReproducir(1);
                    } else {
                        $pantalla_videos->setReproducir(0);
                    }
                    $pantalla_videos->save();
                }

                //Guardar luego de todos los videos el ultimo
                $pantalla_videos->setId($pantallavideo_id);
                $pantalla_videos->setPantallaId($id);
                $pantalla_videos->setVideoId($video_id1);
                $pantalla_videos->setActivo(1);
                $pantalla_videos->setOrden($orden + 1);
                $pantalla_videos->setReproducir(0);
                $pantalla_videos->save();
            }
            //FIN GUARDAR PANTALLAVIDEOS
            //INICIO GUARDAR PANTALLAPUBLICIDAD
            $pantallapublicidad_id = null;
            $pantallapublicidad = new PantallaPublicidad();
            $whereCondition = "pantalla_id= $id";
            $pantallapublicidad->deleteAll($whereCondition);
            if (!empty($check_publicidad)) {          //no eliminar
                foreach ($check_publicidad as $publicidad_id) {
                    $pantallapublicidad->setId($pantallapublicidad_id);
                    $pantallapublicidad->setPantallaId($id);
                    $pantallapublicidad->setPublicidadId($publicidad_id);
                    $pantallapublicidad->save();
                }
            }
            /* $pantallapublicidad->setId($pantallapublicidad_id);
              $pantallapublicidad->setPantallaId($id);
              $pantallapublicidad->setPublicidadId($radio_publicidad);
              $pantallapublicidad->save(); */
            //FIN GUARDAR PANTALLAPUBLICIDAD
            //INICIO GUARDAR HORARIOPUBLICIDAD
            $numeroItems = $this->getPostParam("txt1");
            $horariopublicidad = new Horariopublicidad();
            $whereCondition = "pantalla_id= $id";
            $horariopublicidad->deleteAll($whereCondition);
            for ($i = 1; $i <= $numeroItems; $i++) {
                $horaInicio = $this->getPostParam("hora_i_" . $i);
                $horaFin = $this->getPostParam("hora_f_" . $i);
                $tipo = $this->getPostParam("tipo_" . $i);
                //echo $horaInicio." ".$horaFin." ".$tipo."<br>";
                if (!empty($horaInicio) && !empty($tipo)) {
                    $horariopublicidad_id = null;
                    $horariopublicidad->setId($horariopublicidad_id);
                    $horariopublicidad->setPantallaId($id);
                    $horariopublicidad->setHoraInicio($horaInicio);
                    $horariopublicidad->setHoraFin($horaFin);
                    $horariopublicidad->setTipo($tipo);
                    $horariopublicidad->setCreacionAt($creacionAt);

                    if ($horariopublicidad->save() == false) {
                        Flash::error('Hubo un error guardando el registro.');
                    } else {
                        Flash::success('Registro guardado con éxito.');
                    }
                }
            }
            //FIN GUARDAR HORARIOPUBLICIDAD
            //INICIO GUARDAR PANTALLAFEED
            $pantallafeed = new Pantallafeed();
            $whereCondition = "pantalla_id= $id";
            $pantallafeed->deleteAll($whereCondition);

            $categoriafeeds = new Categoriafeeds();
            $buscaCategoriafeeds = $categoriafeeds->Find();
            foreach ($buscaCategoriafeeds as $result) {
                $categoriafeedsId = $result->getId();
                $feedIds = $this->getPostParam("seleccionados_" . $categoriafeedsId);
                //$limiteItems    = $this->getPostParam("limite_".$categoriafeedsId);
                $array_feedIds = explode(',', $feedIds);
                for ($i = 0; $i < (count($array_feedIds)); $i++) {
                    //echo $array_feedIds[$i];
                    //$feedId = $this->getPostParam("feed_id", "int");
                    //$categoriafeedsId = $categoriafeed_id;
                    //$publicarIcono = $this->getPostParam("publicar_icono", "int");
                    //$publicarTitulo = $this->getPostParam("publicar_titulo", "int");
                    //$publicarFecha = $this->getPostParam("publicar_fecha", "int");
                    //$publicarHora = $this->getPostParam("publicar_hora", "int");
                    //$publicarContenido = $this->getPostParam("publicar_contenido", "int");
                    //$limiteItems = $this->getPostParam("limite_items", "int");
                    //$fechaInicio = $this->getPostParam("fecha_inicio");
                    //$fechaFin = $this->getPostParam("fecha_fin");
                    $limiteItems = $this->getPostParam("limite_" . $array_feedIds[$i]);
                    $creacionAt = $this->getPostParam("creacion_at");
                    $pantallafeed = new Pantallafeed();
                    $pantallafeed->setId(null);
                    $pantallafeed->setPantallaId($id);      //id de la pantalla
                    $pantallafeed->setFeedId($array_feedIds[$i]);
                    $pantallafeed->setCategoriafeedsId($categoriafeedsId);
                    /* $pantallafeed->setPublicarIcono($publicarIcono);
                      $pantallafeed->setPublicarTitulo($publicarTitulo);
                      $pantallafeed->setPublicarFecha($publicarFecha);
                      $pantallafeed->setPublicarHora($publicarHora);
                      $pantallafeed->setPublicarContenido($publicarContenido); */
                    $pantallafeed->setLimiteItems($limiteItems);
                    /* $pantallafeed->setFechaInicio($fechaInicio);
                      $pantallafeed->setFechaFin($fechaFin); */
                    $pantallafeed->setCreacionAt($creacionAt);
                    $pantallafeed->save();
                }
            }
            //die();
            //FIN GUARDAR PANTALLAFEED

            if ($pantalla->save() == false) {
                Flash::error('Hubo un error actualizando el registro Pantalla - Videos.');
            } else {
                $this->setParamToView('save', 'true');
                if ($this->getQueryParam("exit") != "")
                    $this->setParamToView('exit', 'true');
                else {
                    if (!$isEdit) {
                        $action = 'editar';
                    }
                }
                Flash::success('Registro actualizando con éxito Pantalla - Videos.');
            }
        } else { //Nueva pantalla
            $pantalla_videos = new Pantallavideos();
            if ($pantalla->save() == false) {
                Flash::error('Hubo un error guardando el registro.');
            } else {
                $buscaId = $pantalla->maximum("id");
                $pantallavideo_id = null;
                if (!empty($check_servicio)) {
                    $orden = 0;
                    foreach ($check_servicio as $video) {
                        $orden = $orden + 1;
                        $pantalla_videos->setId($pantallavideo_id);
                        $pantalla_videos->setPantallaId($buscaId);
                        $pantalla_videos->setVideoId($video);
                        $pantalla_videos->setActivo(1);
                        $pantalla_videos->setOrden($orden);
                        if ($orden == 1) {
                            $pantalla_videos->setReproducir(1);
                        } else {
                            $pantalla_videos->setReproducir(0);
                        }
                        $pantalla_videos->save();
                    }
//Guardar luego de todos los videos el ultimo
                    $pantalla_videos->setId($pantallavideo_id);
                    $pantalla_videos->setPantallaId($buscaId);
                    $pantalla_videos->setVideoId($video_id1);
                    $pantalla_videos->setActivo(1);
                    $pantalla_videos->setOrden($orden + 1);
                    $pantalla_videos->setReproducir(0);
                    $pantalla_videos->save();
                }
//Guardamos el usuario_id y pantalla_id de userpantalla
                $usuario_pantalla = new Userpantalla();
                $usuario_pantalla->setUsuarioId($usuario);
                $usuario_pantalla->setPantallaId($buscaId);
                $usuario_pantalla->save();

//INICIO GUARDAR PANTALLAPUBLICIDAD
                $pantallapublicidad_id = null;
                $pantallapublicidad = new PantallaPublicidad();
                if (!empty($check_publicidad)) {          //no eliminar
                    foreach ($check_publicidad as $publicidad_id) {
                        $pantallapublicidad->setId($pantallapublicidad_id);
                        $pantallapublicidad->setPantallaId($id);
                        $pantallapublicidad->setPublicidadId($publicidad_id);
                        $pantallapublicidad->save();
                    }
                }
                /* $pantallapublicidad->setId($pantallapublicidad_id);
                  $pantallapublicidad->setPantallaId($buscaId);
                  $pantallapublicidad->setPublicidadId($radio_publicidad);
                  $pantallapublicidad->save(); */
//FIN GUARDAR PANTALLAPUBLICIDAD
//INICIO GUARDAR HORARIOPUBLICIDAD
                $numeroItems = $this->getPostParam("txt1");
                $horariopublicidad = new Horariopublicidad();
                for ($i = 1; $i <= $numeroItems; $i++) {
                    $horaInicio = $this->getPostParam("hora_i_" . $i);
                    $horaFin = $this->getPostParam("hora_f_" . $i);
                    $tipo = $this->getPostParam("tipo_" . $i);
//echo $horaInicio." ".$horaFin." ".$tipo."<br>";
                    if (!empty($horaInicio) && !empty($tipo)) {
                        $horariopublicidad_id = null;
                        $horariopublicidad->setId($horariopublicidad_id);
                        $horariopublicidad->setPantallaId($buscaId);
                        $horariopublicidad->setHoraInicio($horaInicio);
                        $horariopublicidad->setHoraFin($horaFin);
                        $horariopublicidad->setTipo($tipo);
                        $horariopublicidad->setCreacionAt($creacionAt);

                        if ($horariopublicidad->save() == false) {
                            Flash::error('Hubo un error guardando el registro.');
                        } else {
                            Flash::success('Registro guardado con éxito.');
                        }
                    }
                }
//FIN GUARDAR HORARIOPUBLICIDAD

                $this->setParamToView('save', 'true');
                if ($this->getQueryParam("exit") != "")
                    $this->setParamToView('exit', 'true');
                else {
                    if (!$isEdit) {
                        $action = 'editar';
                    }
                }
//Flash::error('Hubo un error guardando el registro Dispensador-Servicio.');
            }
        }
//$this->routeTo('action: index');
        $this->routeTo("action: $action", "id: $id");
    }

    /**
     * Eliminar el Pantalla
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
//Inicio Eliminar primero los registros de pantallavideos
                $db = Db::rawConnect();
                $db->query("DELETE FROM pantallavideos WHERE pantalla_id = $ids[$i]");
//Fin Eliminar primero los registros de pantallavideos
//Inicio Eliminar primero la relacion userpantalla
                $db = Db::rawConnect();
                $db->query("delete from userpantalla where pantalla_id = $ids[$i]");
//Inicio Eliminar primero la relacion userpantalla
                if (!$this->Pantalla->delete($ids[$i])) {
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
                    case 'numero':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'descripcion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Pantalla->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        //$resultado=$this->Pantalla->find("conditions: $condicion","order: $orden","limit: $limite");
        $db = DbBase::rawConnect();
        $resultado = $db->query("select p.id, numero, p.descripcion, ip_equipo, tipo_pantalla, tiempo_tono, plantilla, nombre_ubicacion, u.descripcion as ubicacion_descripcion, nombres as usuario_nombre, username, estado as usuario_estado, us.descripcion as usuario_descripcion
            ,CASE 
                WHEN tono=1 THEN 'VOZ' 
                ELSE 'TIMBRE' 
                END AS timbre_tono
            ,formato_voz, (select count(*) from pantallavideos where pantalla_id=p.id) as videos_asignados
            from pantalla p, ubicacion u, userpantalla up, usuario us
            where p.ubicacion_id = u.id
            and up.usuario_id=us.id and up.pantalla_id=p.id limit $inicio, $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        //for($i=$inicio; $i<$limite; $i++) {
        while ($row = $db->fetchArray($resultado)) {
            $jqgrid->rows[] = array('id' => $row['id'], 'cell' => array($row['numero'], $row['tipo_pantalla'], $row['descripcion'], $row['ip_equipo'], $row['timbre_tono'], $row['formato_voz'], $row['tiempo_tono']
                    , $row['nombre_ubicacion'], $row['ubicacion_descripcion'], $row['usuario_nombre'], $row['username'], $row['usuario_estado'], $row['videos_asignados'], $row['plantilla']));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
