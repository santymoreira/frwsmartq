
<?php

/**
 * Controlador Dispensadorservicio
 *
 * @access public
 * @version 1.0
 */
class DispensadorservicioController extends ApplicationController {

    /**
     * servicio_id
     *
     * @var int
     */
    public $servicioId;

    /**
     * dispensador_id
     *
     * @var int
     */
    public $dispensadorId;

    /**
     * Inicializador del controlador/
     *
     */
    /* variable para mostrar los servivios en dispensar.phtml */
    public $dispensador_servicios;

    /* varibles para el ancho y el alto de los botones del dispensador */
    public $id_dispensador;
    public $descripcion_dispensador;

    /* variables para obtener el tipo de dispensador */
    public $tipoDispensador;
    public $total_servicios;
    public $ubicacion_impresora;
    public $nombre_impresora;
    public $carpeta;
    public $seleccion_operador; //1=> si escoge el operador
    public $fraseinicial;
    public $chk_fi;
    public $empresa;
    public $chk_e;
    public $logo;
    public $chk_l;
    public $chk_li;
    public $chk_s;
    public $chk_u;
    public $chk_f;
    public $chk_tiempo_e;
    public $chk_turno_e;
    public $idsesion;
    public $logo_ticket;

    public function initialize() {
        $this->setPersistance(true);

        if (!SessionNamespace::exists("datosUsuarioSMC")) {
            Flash::addMessage("Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente", Flash::WARNING);
            $this->redirect("login");
        }

        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $this->idsesion = $dataUsuario->getId();
    }

    /**
     * Acción por defecto del controlador
     */
    public function indexAction() {
        //--busco el 
        $sql = new ActiveRecordJoin(array("entities" => array("Usuario", "Dispensador"),
            "fields" => array(
                "{#Dispensador}.id",
                "{#Dispensador}.descripcion",
                "{#Dispensador}.tipo_dispensador"
            ),
            "conditions" => "{#Dispensador}.usuario_id=$this->idsesion"));
        foreach ($sql->getResultSet() as $result) {
            $this->id_dispensador = $result->getId();
            $this->descripcion_dispensador = $result->getDescripcion();
            $this->tipoDispensador = $result->getTipoDispensador();
        }

        //obtener el total de servicios por dispensador
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT COUNT(*) AS total_servicios FROM servicio, dispensador, dispensadorservicio, ubicacion
        WHERE ubicacion.id = servicio.ubicacion_id AND servicio.id = dispensadorservicio.servicio_id AND dispensador.id = dispensadorservicio.dispensador_id
        AND dispensadorservicio.dispensador_id= $this->id_dispensador;");
        while ($row = $db->fetchArray($result)) {
            $this->total_servicios = $row[0];
            Tag::displayTo('total_servicios', $row[0]);
        }

        $configuracionsistema = new Configuracionsistema();
        $buscaConfiguracionsistema = $configuracionsistema->findFirst('id=1');
        $this->ubicacion_impresora = $buscaConfiguracionsistema->getUbicacionImpresora();
        $this->nombre_impresora = $buscaConfiguracionsistema->getNombreImpresora();
        $this->logo_ticket = $buscaConfiguracionsistema->getLogoTicket();

        //ver datos de la configuracion del diseño del ticket
        $turnos = new Turnoseteo();
        $turnoseteo = $turnos->findFirst();
        $this->fraseinicial = $turnoseteo->getFraseinicial();
        $this->chk_fi = $turnoseteo->getChkfraseinicial();
        $this->empresa = $turnoseteo->getEmpresa();
        $this->chk_e = $turnoseteo->getChkempresa();
        $this->logo = $turnoseteo->getLogo();
        $this->chk_l = $turnoseteo->getChklogo();
        $this->chk_li = $turnoseteo->getChkinicial();
        $this->chk_s = $turnoseteo->getChkservicio();
        $this->chk_u = $turnoseteo->getChkubicacion();
        $this->chk_f = $turnoseteo->getChkfecha();
        $this->chk_tiempo_e = $turnoseteo->getChktiempoespera();
        $this->chk_turno_e = $turnoseteo->getChkturnoespera();
        
        $this->routeTo('action: dispensar');    //abre la vista del sispensador
    }

    /**
     * Editar el Dispensadorservicio
     *
     */
    public function editarAction($servicio_id = null, $dispensador_id = null) {
        $filter = new Filter();
        $servicioId = $filter->applyFilter($servicioId, "int");
        $dispensadorId = $filter->applyFilter($dispensadorId, "int");
        $dispensadorservicio = $this->Dispensadorservicio->findFirst($servicio_id, $dispensador_id);
        if ($dispensadorservicio) {
            Tag::displayTo('servicio_id', $dispensadorservicio->getServicioId());
            Tag::displayTo('dispensador_id', $dispensadorservicio->getDispensadorId());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Dispensadorservicio
     *
     */
    public function guardarAction($isEdit = false, $servicio_id = null, $dispensador_id = null) {

        $dispensadorservicio = new Dispensadorservicio();
        $dispensadorservicio->setServicioId($servicioId);
        $dispensadorservicio->setDispensadorId($dispensadorId);
        if ($dispensadorservicio->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
        } else {
            Flash::success('Registro guardado con éxito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Dispensadorservicio
     *
     */
    public function eliminarAction() {
        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Dispensadorservicio->delete($ids[$i])) {
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
                    case 'servicio_id':
                        $condServicio = Utils::toSqlParamSearchGrid('nombre', $abrevoper, $strbusqueda);
                        $Servicio = $this->Servicio->find($condServicio);
                        if (count($Servicio) > 0) {
                            $arrayIdsServicio = array();
                            foreach ($Servicio as $fila) {
                                $arrayIdsServicio[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsServicio);
                            $abrevoper = 'in';
                        }
                        break;
                    case 'dispensador_id':
                        $condDispensador = Utils::toSqlParamSearchGrid('id', $abrevoper, $strbusqueda);
                        $Dispensador = $this->Dispensador->find($condDispensador);
                        if (count($Dispensador) > 0) {
                            $arrayIdsDispensador = array();
                            foreach ($Dispensador as $fila) {
                                $arrayIdsDispensador[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsDispensador);
                            $abrevoper = 'in';
                        }
                        break;
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
                            case 'servicio_id':
                                $condServicio = Utils::toSqlParamSearchGrid('nombre', $val['op'], $val['data']);
                                $Servicio = $this->Servicio->find($condServicio);
                                if (count($Servicio) > 0) {
                                    $arrayIdsServicio = array();
                                    foreach ($Servicio as $fila) {
                                        $arrayIdsServicio[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsServicio);
                                    $val['op'] = 'in';
                                }
                                break;
                            case 'dispensador_id':
                                $condDispensador = Utils::toSqlParamSearchGrid('id', $val['op'], $val['data']);
                                $Dispensador = $this->Dispensador->find($condDispensador);
                                if (count($Dispensador) > 0) {
                                    $arrayIdsDispensador = array();
                                    foreach ($Dispensador as $fila) {
                                        $arrayIdsDispensador[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsDispensador);
                                    $val['op'] = 'in';
                                }
                                break;
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
                    case 'servicio_id':
                        $condServicio = Utils::toSqlParamSearchGrid('nombre', 'bw', $v);
                        $Servicio = $this->Servicio->find($condServicio);
                        if (count($Servicio) > 0) {
                            $arrayIdsServicio = array();
                            foreach ($Servicio as $fila) {
                                $arrayIdsServicio[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsServicio);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'dispensador_id':
                        $condDispensador = Utils::toSqlParamSearchGrid('id', 'bw', $v);
                        $Dispensador = $this->Dispensador->find($condDispensador);
                        if (count($Dispensador) > 0) {
                            $arrayIdsDispensador = array();
                            foreach ($Dispensador as $fila) {
                                $arrayIdsDispensador[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsDispensador);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Dispensadorservicio->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Dispensadorservicio->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Dispensadorservicio = $resultado[$i];
            $Servicio = $Dispensadorservicio->getServicio();
            $Dispensador = $Dispensadorservicio->getDispensador();
            $jqgrid->rows[] = array('id' => $Dispensadorservicio->getServicio_id(), 'cell' => array($Servicio->getNombre(), $Dispensador->getId()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /**
     * Inicio abrir dispensador de servicio
     * Esta funcion es llamada cuando se loguea como dispensador
     */
    public function dispensarAction() {
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst('id=1');
        $this->carpeta = $buscaEmpresa->getCarpeta();
        $this->seleccion_operador = $buscaEmpresa->getSeleccionOperador();
        Tag::displayTo('nombre', $buscaEmpresa->getNombrecomercial());
        Tag::displayTo('seleccion_operador', $buscaEmpresa->getSeleccionOperador()); //1=>si puede seleccionar el operador
    }

//    public function cambiarTildes($nombre_servicio) {
//        //INICIO IMPRIMIR TILDES
//        $cont = strlen($nombre_servicio);
//        $palabra = "";
//        for ($i = 0; $i <= $cont; $i++) {
//            $l1 = substr("$nombre_servicio", $i, 1);
//            $l2 = substr("$nombre_servicio", $i + 1, 1);
//            $l3 = $l1 . $l2;
//            if ($l3 == "á") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else if ($l3 == "é") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else if ($l3 == "í") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else if ($l3 == "ó") {
//                $s = "Ã³";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else if ($l3 == "ú") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else if ($l3 == "ñ") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } /* else if ($l3 == "�?"){
//              $s= "�";
//              $palabra=$palabra.$s;
//              $i=$i+1;
//              } */ else if ($l3 == "Ñ") {
//                $s = "�";
//                $palabra = $palabra . $s;
//                $i = $i + 1;
//            } else {
//                $palabra = $palabra . $l1;
//            }
//        }
//        return $palabra;
//    }

    /**
     * Con php_printer
     */
    public function emitirturnoAction() {
        //echo("<script>console.log('PHP: si');</script>");
        $this->setResponse("json");
        $serv = $this->getPostParam('servicio_id'); //id del servicio
        $id_gruposervicio = $this->getPostParam('gruposervicio_id');
        $letra = $this->getPostParam('letra');
        $nombre_servicio = $this->getPostParam('nombre_servicio');
        $nombre_ubicacion = utf8_decode($this->getPostParam('nombre_ubicacion'));

         $dispensador = $this->getPostParam('dispensador');

         //echo("<script>console.log('PHP: ".$dispensador."');</script>");

        /*
        $db = DbBase::rawConnect();
        $res = $db->query("SELECT letra_alias FROM servicio WHERE id=$serv");
        while ($ro = $db->fetchArray($res)) {
            $let=$ro['letra_alias'];
        }        */

        //$prioridad = $this->getPostParam('prioridad');
        $letra_alias = $this->getPostParam('letra_alias');

        $prioridad=1;

        switch ($letra_alias) {
        case "A":
            $prioridad=1;
        break;
        case "B":
            $prioridad=2;
        break;
        case "C":
            $prioridad=3;
        break;
}


        //$prioridad = $this->getPostParam('prioridad');
        

        //$palabra = $this->cambiarTildes($nombre_servicio);
        $palabra= utf8_decode($nombre_servicio);

        //encontrar el mayor numero de turno para servicio y asignar a variable $numturno
        //los turnos varian dependiendo del servicio es decir pueden tener los mismos numero pero distintos servicios
        $maxturno = 0;
        $fech = date("Y-m-d");
        $hora = date("H:i:s");
        $turno = new Turnos();


        if ($prioridad == 1)
            $buscaMaxturno = $turno->maximum("numero_alias", "conditions: fecha_emision= '$fech' AND servicio_id= $serv AND prioridad= 1");
        else
            $buscaMaxturno = $turno->maximum("numero", "conditions: servicio_id= $serv and fecha_emision= '$fech'");
        $maxturno= $buscaMaxturno;
        

        //guardar el turno
        $maxturno +=1;
        $turno = new Turnos();
        $turno->setServicioId($serv);
        $turno->setFechaEmision($fech);
        $turno->setHoraEmision($hora);
        $turno->setEstado(0);
        $turno->setPorAtender(0);
        $turno->setAtendido(0);
        $turno->setRechazado(0);
        $turno->setTransferido(0);
        $turno->setAdmRevisado(0);
        $turno->setPrioridad($prioridad);
        $turno->setCalificacion('NO CALIFICADO');


        if ($prioridad == 1) {
            $turno->setNumero(0);
            $turno->setNumeroAlias($maxturno);
        } else {
            $turno->setNumeroAlias(0);
            $turno->setNumero($maxturno);
        }
        $turno->save();

        //INICIO CALCULAR TURNOS EN ESPERA, PROMEDIO
        $total_turnos_esperando = 0;
        $total_turnos_atendidos = 0;
        $tiempo_espera = 0;
        $total_segundos = 0;
        $fecha_hoy = date("Y-m-d");
        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT por_atender, atendido, duracion AS total FROM turnos t, servicio s, gruposervicio gs WHERE gs.id=s.gruposervicio_id AND s.id=t.servicio_id AND fecha_emision='$fecha_hoy' AND gs.id=$id_gruposervicio");
        while ($row2 = $db->fetchArray($result2)) {
            if ($row2['por_atender'] == 0)
                $total_turnos_esperando+=1;
            if ($row2['atendido'] == 1)
                $total_turnos_atendidos+=1;
            //$duracion= $result->getDuracion();
            $duracion = $row2[2];
            $segundos = substr($duracion, 6, 2);
            $minutos = substr($duracion, 3, 2) * 60;
            $horas = substr($duracion, 0, 2) * 3600;
            $total_segundos = $total_segundos + $segundos + $minutos + $horas;
        }
        if ($total_segundos <> 0) {
            $promedio_segundos = $total_segundos / $total_turnos_atendidos;
            $tiempo_segundo = $total_turnos_esperando * $promedio_segundos;
            $fun = new Funciones();
            $tiempo_espera = $fun->tiempo($tiempo_segundo);
        }

        $fecha = date("Y-m-d H:i:s");


        /* */

        //$handle = printer_open($this->nombre_impresora);
        //printer_start_doc($handle, "My Document");
        //printer_start_page($handle);

        /*
       
       
        $y = 0;
        if ($this->chk_l == 1) {
            printer_draw_bmp($handle, "C:/logo_ticket1.bmp", 160, -5, 200, 100); //X,Y,W,H
            $y = $y + 100;
        }
        if ($this->chk_fi == 1) {
            $font = printer_create_font("Arial", 20, 15, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, utf8_decode($this->fraseinicial), 190, $y);
            $y = $y + 15;
        }
        if ($this->chk_e == 1) {
            $font2 = printer_create_font("Arial", 25, 25, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font2);
            printer_draw_text($handle, utf8_decode($this->empresa), 150, $y);
            $y = $y + 50;
        }
       
       */
        //INICIO AUMENTAR CEROS EN TURNO
        $contturno = strlen($maxturno);
        //echo $contturno; die();
        if ($contturno == 1)
            $turno = "00" . $maxturno;
        else if ($contturno == 2)
            $turno = "0" . $maxturno;
        else
            $turno = $maxturno;
        //FIN AUMENTAR CEROS EN TURNO
       
       /*
        if (strlen($letra) == 1) {
            $font = printer_create_font("Arial", 90, 80, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, $letra . $turno, 42, $y);
        } else {
            $font = printer_create_font("Arial", 90, 65, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, $letra . $turno, 42, $y);
        }
        $y = $y + 85;

        if ($this->chk_s == 1) {
            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, $palabra, 20, $y);
            $y = $y + 20;

            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, "=> " . $nombre_ubicacion, 300, $y - 22);
        }
        if ($this->chk_f == 1) {
            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, $fecha, 135, $y);
            $y = $y + 20;
        }
        if ($this->chk_turno_e == 1) {
            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, "Clientes en espera: " . $total_turnos_esperando, 20, $y);
            $y = $y + 20;
        }
        if ($this->chk_tiempo_e == 1) {
            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, "Tiempo de espera (h/m/s): " . $tiempo_espera, 20, $y);
            $y = $y + 126;
        }

        //______________
        if ($this->chk_tiempo_e == 1) {
            $font = printer_create_font("Arial", 20, 12, 400, false, false, false, 0); //alto y ancho
            printer_select_font($handle, $font);
            printer_draw_text($handle, " .", 20, $y);
            //$y=$y+16;
        }
        //______________		

        //if ($this->logo_ticket == 1) {
            printer_draw_bmp($handle, "C:/SmartQ_monocromatico_vertical.bmp", 0, 50, 30, 120); //X,Y,W,H
       // }
    $font = printer_create_font("Arial", 15, 8, 400, false, false, false, 0); //alto y ancho
          printer_select_font($handle, $font);
          printer_draw_text($handle, "www.peopleweb.com.ec", 280, $y); //x,y
          printer_select_font($handle, $font);
          printer_draw_text($handle, "(593-2) 242-5428", 310, $y+14); //x,y 
       

        printer_draw_bmp($handle, "C:/telefono_peopleweb.bmp", 540, 25, 35, 225); //X,Y,W,H
        printer_end_page($handle);
        printer_end_doc($handle);
        printer_close($handle);
        //FIN IMPRIMIR TURNO
    */

        //Load::lib('fpdf');
         //$data="hola";
        //echo("<script>console.log('PHP: ".$data."');</script>")
         //require_ones 'Library/Fpdf';

        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT carpeta FROM empresa");
        while ($row2 = $db->fetchArray($result2)) {
            $carpeta=$row2['carpeta'];
        }

        



        $direccionLogo='public/img/'.$carpeta.'/sistema/logo_ticket.png';


         $db1 = DbBase::rawConnect();
        $result1 = $db1->query("SELECT fraseinicial FROM turnoseteo");
        while ($row1 = $db1->fetchArray($result1)) {
            $frase=$row1['fraseinicial'];
        }

        $impresionD=1;
         $db2 = DbBase::rawConnect();
        $result2 = $db2->query("SELECT impresion FROM dispensador WHERE id=$dispensador");
        while ($row2 = $db2->fetchArray($result2)) {
            $impresionD=$row2['impresion'];
        }

        //echo("<script>console.log('PHP: ".$impresionD."');</script>");
        //SELECT impresion FROM dispensador WHERE id=1
        
        //echo("<script>console.log('PHP: ".$direccionLogo."');</script>");
         require_once 'Library/Fpdf/impresion.php';
        $pdf=new PDF_AutoPrint();
        $pdf->AddPage();

        $pdf->Image($direccionLogo,30,0,70,27,'png');
        $pdf->SetFont('Arial','',20);
        $pdf->Text(40, 35, $frase);
        $pdf->SetFont('Arial','',60);
        $pdf->Image('public/SmartQ_monocromatico_vertical.png',15,50,25,45);
        $pdf->Image('public/telefono_peopleweb.png',180,35,12,75);
        $pdf->Text(75,55,$letra . $turno);
        $pdf->SetFont('Arial','',20);
        $pdf->Text(40,70,$palabra." => ".$nombre_ubicacion);
        $pdf->Text(65,80,$fecha);
        $pdf->Text(65,90,"Clientes en espera: " . $total_turnos_esperando);
        $pdf->Text(50,100,"Tiempo de espera (h/m/s): " . $tiempo_espera);

        //src='../images/SmartQ_monocromatico_vertical.bmp
        // $pdf->Image('SmartQ_monocromatico_vertical.bmp',10,8,22);
        //$pdf->Image('SmartQ_monocromatico_vertical.bmp' , 80 ,22, 35 , 38,'JPG', '../images/');
        //Open the print dialog
        $pdf->AutoPrint(false);
        //$pdf->Output();
        if ($impresionD==1) $pdf->Output('public/filename.pdf','F');
        else if ($impresionD==2)
        {
            $pdf->Output('public/filename.pdf','F');
            $pdf->Output('public/filename2.pdf','F');
        }
        return  array("esperando" => $total_turnos_esperando, "turnos" => $turno, "tiempo" => $tiempo_espera, "fecha" => $fecha,"dispensador" => $impresionD);
        //return (json_encode(array("esperando" => $total_turnos_esperando, "atendidos" => $total_turnos_atendidos, "tiempo" => $tiempo_espera)));
    }

    /**
     * Con scriptx
     */
    public function emitirturno1Action() {
        $this->setResponse("json");
        $serv = $this->getPostParam('servicio_id'); //id del servicio
        $id_gruposervicio = $this->getPostParam('gruposervicio_id');
        $letra = $this->getPostParam('letra');
        $nombre_servicio = $this->getPostParam('nombre_servicio');
        $nombre_ubicacion = $this->getPostParam('nombre_ubicacion');

        //INICIO IMPRIMIR TILDES
        $cont = strlen($nombre_servicio);
        $palabra = "";
        for ($i = 0; $i <= $cont; $i++) {
            $l1 = substr("$nombre_servicio", $i, 1);
            $l2 = substr("$nombre_servicio", $i + 1, 1);
            $l3 = $l1 . $l2;
            if ($l3 == "á") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else if ($l3 == "é") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else if ($l3 == "é") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else if ($l3 == "�*") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else if ($l3 == "ó") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else if ($l3 == "ú") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } /* else if ($l3 == "�?"){
              $s= "�";
              $palabra=$palabra.$s;
              $i=$i+1;
              } */ else if ($l3 == "ñ") {
                $s = "�";
                $palabra = $palabra . $s;
                $i = $i + 1;
            } else {
                $palabra = $palabra . $l1;
            }
        }
        //FIN IMPRIMIR TILDES
        //encontrar el mayor numero de turno para servicio y asignar a variable $numturno
        //los turnos varian dependiendo del servicio es decir pueden tener los mismos numero pero distintos servicios
        $maxturno = 0;
        $fech = date("Y-m-d");
        $hora = date("H:i:s");
        $turno = new Turnos();
        $buscaMaxturno = $turno->maximum("numero", "conditions: servicio_id= $serv and fecha_emision= '$fech'");
        if ($buscaMaxturno) {
            $maxturno = $buscaMaxturno;
        }
        $maxturno +=1;
        $turno->setServicioId($serv);
        $turno->setNumero($maxturno);
        $turno->setFechaEmision($fech);
        $turno->setHoraEmision($hora);
        $turno->setEstado(0);
        $turno->setPorAtender(0);
        $turno->setAtendido(0);
        $turno->setRechazado(0);
        $turno->setTransferido(0);
        $turno->save();

        //INICIO CALCULAR TURNOS EN ESPERA, PROMEDIO
        $total_turnos_esperando = 0;
        $total_turnos_atendidos = 0;
        $tiempo_espera = 0;
        $total_segundos = 0;
        $fecha_hoy = date("Y-m-d");
        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT por_atender, atendido, duracion AS total FROM turnos t, servicio s, gruposervicio gs WHERE gs.id=s.gruposervicio_id AND s.id=t.servicio_id AND gs.id=$id_gruposervicio AND fecha_emision='$fecha_hoy'");
        while ($row2 = $db->fetchArray($result2)) {
            if ($row2['por_atender'] == 0)
                $total_turnos_esperando+=1;
            if ($row2['atendido'] == 1)
                $total_turnos_atendidos+=1;
            //$duracion= $result->getDuracion();
            $duracion = $row2[2];
            $segundos = substr($duracion, 6, 2);
            $minutos = substr($duracion, 3, 2) * 60;
            $horas = substr($duracion, 0, 2) * 3600;
            $total_segundos = $total_segundos + $segundos + $minutos + $horas;
        }



        if ($total_segundos <> 0) {
            $promedio_segundos = $total_segundos / $total_turnos_atendidos;
            $tiempo_segundo = $total_turnos_esperando * $promedio_segundos;
            $fun = new Funciones();
            $tiempo_espera = $fun->tiempo($tiempo_segundo);
        }
        //FIN CALCULAR TURNOS EN ESPERA, PROMEDIO
        //INICIO IMPRIMIR TURNO
        //seteo de turno para imprimir
        $fecha = date("Y-m-d H:i:s");
        $turnos = new Turnoseteo();
        $turnoseteo = $turnos->findFirst();
        $fraseinicial = $turnoseteo->getFraseinicial();
        $chk_fi = $turnoseteo->getChkfraseinicial();
        $empresa = $turnoseteo->getEmpresa();
        $chk_e = $turnoseteo->getChkempresa();
        $logo = $turnoseteo->getLogo();
        $chk_l = $turnoseteo->getChklogo();
        $chk_li = $turnoseteo->getChkinicial();
        $chk_s = $turnoseteo->getChkservicio();
        $chk_u = $turnoseteo->getChkubicacion();
        $chk_f = $turnoseteo->getChkfecha();
        $chk_tiempo_e = $turnoseteo->getChktiempoespera();
        $chk_turno_e = $turnoseteo->getChkturnoespera();

        //inicio obtener logo para ticket de empresa
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        $logoticket = $buscaEmpresa->getLogoTicket();
        //fin obtener logo para ticket de empresa
        //INICIO AUMENTAR CEROS EN TURNO
        $contturno = strlen($maxturno);
        //echo $contturno; die();
        if ($contturno == 1)
            $turno = "00" . $maxturno;
        else if ($contturno == 2)
            $turno = "0" . $maxturno;
        else
            $turno = $maxturno;
        //FIN AUMENTAR CEROS EN TURNO

        $html = "";
        $html.="
<div id='div_logo'><center><img src='../img/defensoria/logo.png' alt='logo' width='90' height='70'/></center></div>
<div id='div_logo_smartq'><img src='../img/defensoria/SmartQ_monocromatico_vertical.bmp' alt='logo_smartq' width='21' height='77'/></div>
<div id='div_fono_pw'><img src='../img/defensoria/telefono_peopleweb.bmp' alt='fono_pw' width='20' height='127'/></div>
<div id='div_texto'>
<center><span class='nombre'> DEFENSORÍA PÚBLICA </span></center>
<center><span class='frase'> $fraseinicial </span></center>
<center><span class='ticket'> $letra $turno </span></center>
<center>$palabra    => $nombre_ubicacion </center>
<center> $fecha </center>
Clientes en espera: $total_turnos_esperando<br />
Tiempo de espera: $tiempo_espera
</div>
                ";

        //FIN IMPRIMIR TURNO
        $datos = array("esperando" => $total_turnos_esperando, "atendidos" => $total_turnos_atendidos, "tiempo" => $tiempo_espera, "html" => $html);
        return ($datos);
    }

    /**
      Funcion que permite recargar la hora en el dispensador
     */
    public function recargaPantallaAction() {
        $this->setResponse("json");
        //$aux=date("Y-m-d H:i:s");
        $datos = array("aux" => " ");
        return ($datos);
    }

    /**
     * Funcion que permite mostrar la lista de operadores que estan el linea y con pausas
     * Usado para la Defensoria publica
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
            "entities" => array("Caja", "Usuario", "Usercaja", "Ubicacion", "Pausas", "CajaPausas", "Servicio", "Serviciocaja"),
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
            $forma_cajas_ids.=$result->getId() . ",";
            $html.= "<tr>";
            $html.= "<td style='padding:5px' align='center'> {$result->getNumeroCaja()} </td>";
            $html.= "<td style='padding:5px'> <input name='btn_operador' type='button' value='{$result->getNombres()}' class='class_boton_operador' onclick='emitir_turno_operador({$result->getId()},{$result->getNumeroCaja()})'> </td>";
            //$html.= "<td> {$result->getNombreUbicacion()} </td>";
            $html.= "<td style='padding:5px'> <font color='#3366FF'><b>{$result->getNombrePausa()}</b></font>  </td>";
            //contar turnos en espera del aperador mas los turnos nuevos
            $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
            while ($row = $db->fetchArray($result2)) {
                $turnos_en_espera = $row['total'];
            }
            $html.= "<td style='padding:5px' align='center'>$turnos_en_espera</td>";
            $html.= "</tr>";
        }

        //ver los que están atendiendo
        $condicion = "{#Turnos}.fecha_emision='$fecha' AND {#Turnos}.por_atender=1 AND {#Turnos}.atendido=0";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Caja", "Usuario", "Usercaja", "Ubicacion", "Turnos"),
            "fields" => array(
                "{#Caja}.id",
                "{#Caja}.numero_caja",
                "{#Usuario}.nombres",
                "{#Ubicacion}.nombre_ubicacion"
            ),
            "conditions" => $condicion,
            "order" => "{#Caja}.numero_caja"
        ));
        foreach ($query->getResultSet() as $result) {
            $forma_cajas_ids.=$result->getId() . ",";
            $html.= "<tr>";
            $html.= "<td style='padding:5px' align='center'> {$result->getNumeroCaja()} </td>";
            //$html.= "<td> {$result->getNombres()} </td>";
            $html.= "<td style='padding:5px'> <input name='btn_operador' type='button' value='{$result->getNombres()}' class='class_boton_operador' onclick='emitir_turno_operador({$result->getId()},{$result->getNumeroCaja()})'> </td>";
            //$html.= "<td> {$result->getNombreUbicacion()} </td>";
            $html.= "<td style='padding:5px'> <font color='#009900'><b>Atendiendo</b></font> </td>";
            //contar turnos en espera del aperador mas los turnos nuevos
            $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
            while ($row = $db->fetchArray($result2)) {
                $turnos_en_espera = $row['total'];
            }
            $html.= "<td style='padding:5px' align='center'>$turnos_en_espera</td>";
            $html.= "</tr>";
        }

        $forma_cajas_ids = substr($forma_cajas_ids, 0, strlen($forma_cajas_ids) - 1);

        //poner los demas como activo si ha inciado la sesion a la fecha actual
        if ($forma_cajas_ids != "")
            $condicion = "{#Servicio}.id=$servicio_id AND {#Sesiones}.estado='activo' AND {#Sesiones}.fecha_inicio='$fecha' AND {#Sesiones}.id IN (SELECT MAX(id) FROM sesiones WHERE caja_id NOT IN ($forma_cajas_ids) GROUP BY caja_id)";
        else
            $condicion = "{#Servicio}.id=$servicio_id AND {#Sesiones}.estado='activo' AND {#Sesiones}.fecha_inicio='$fecha' AND {#Sesiones}.id IN (SELECT MAX(id) FROM sesiones GROUP BY caja_id)";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Caja", "Usuario", "Usercaja", "Ubicacion", "Sesiones", "Servicio", "Serviciocaja"),
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
            $html.= "<tr>";
            $html.= "<td style='padding:5px' align='center'> {$result->getNumeroCaja()} </td>";
            $html.= "<td style='padding:5px'> <input name='btn_operador' type='button' value='{$result->getNombres()}' class='class_boton_operador' onclick='emitir_turno_operador({$result->getId()},{$result->getNumeroCaja()})'> </td>";
            $html.= "<td style='padding:5px'> <font color='red'><b>{$result->getEstado()}</b></font></td>";
            //contar turnos en espera del aperador mas los turnos nuevos
            $result2 = $db->query("select count(*) as total from turnos where por_atender=0 AND transferido=0 and fecha_emision='$fecha' and servicio_id in (SELECT sc.servicio_id FROM servicio s, caja c, serviciocaja sc WHERE s.id= sc.servicio_id AND c.id=sc.caja_id AND c.id={$result->getId()} AND sc.llamar=1) AND (caja_id={$result->getId()} OR caja_id IS NULL)");
            while ($row = $db->fetchArray($result2)) {
                $turnos_en_espera = $row['total'];
            }
            $html.= "<td style='padding:5px' align='center'>$turnos_en_espera</td>";
            $html.= "</tr>";
        }


        $html.= "<tr> <td colspan='4' style='padding:5px' align='center'> <input name='btn_operador' type='button' value='Nuevo' class='class_boton_operador_nuevo' onclick='emitir_turno_nuevo()'> </td> </tr>";
        $html.="</table>";

        $datos = array("mensaje" => $html);
        return ($datos);
    }

}
