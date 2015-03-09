<?php

/**
 * Controlador Caja
 *
 * @access public
 * @version 1.0
 */
class CajaController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * numero_caja
     *
     * @var string
     */
    public $numeroCaja;

    /**
     * descripcion
     *
     * @var string
     */
    public $descripcion;

    /**
     * estado
     *
     * @var int
     */
    public $estado;

    /**
     * usuario
     *
     * @var string
     */
    public $usuario;

    /**
     * tipo_calificacion_operador
     *
     * @var string
     */
    public $tipoCalificacionOperador;

    /**
     * horario_id
     *
     * @var int
     */
    public $horarioId;

    /**
     * transferir_uno
     *
     * @var int
     */
    public $transferirUno;

    /**
     * transferir_todos
     *
     * @var int
     */
    public $transferirTodos;

    /**
     * ubicacion_id
     *
     * @var int
     */
    public $ubicacionId;

    /**
     * tiempo
     *
     * @var int
     */
    public $tiempo;

    /**
     * timbre
     *
     * @var int
     */
    public $timbre;

    /**
     * calificar
     *
     * @var int
     */
    public $calificar;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[] = array('name' => 'Módulo', 'index' => 'numero_caja', 'width' => '50px');
        $columnsGrid[] = array('name' => 'IP', 'index' => 'ip', 'width' => '100px');
        $columnsGrid[] = array('name' => 'Descripción', 'index' => 'descripcion');
        //$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
        //$columnsGrid[]=array('name'=>'Usuario','index'=>'usuario');
        //$columnsGrid[]=array('name'=>'Tipo Operador','index'=>'tipo_operador');
        $columnsGrid[] = array('name' => 'Tipo Calificación', 'index' => 'tipo_calificacion');
        $columnsGrid[] = array('name' => 'Horario', 'index' => 'horario_id');
        $columnsGrid[] = array('name' => 'Transferir Uno', 'index' => 'transferir_uno', 'width' => '80px');
        $columnsGrid[] = array('name' => 'Transferir Todos', 'index' => 'transferir_todos', 'width' => '95px');
        $columnsGrid[] = array('name' => 'Ubicación', 'index' => 'ubicacion_id', 'width' => '80px');
        $columnsGrid[] = array('name' => 'Tiempo Calificador', 'index' => 'tiempo');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Caja/
     *
     */
    public function nuevoAction() {
//        $contar = $this->Servicio->count();  //contar el numero total de registros existentes
//        //obtenemos el numero de paginas para el grid
//        if ($contar > 0) {
//            
//        }
//        $i = 0;
//        $servicio1 = $this->Servicio->find("estado='1'");
//        foreach ($servicio1 as $serv) {
//            $nombre[$i] = $serv->getNombre();
//            $nombre1 = 'nombre[' . $i . ']';
//            Tag::displayTo($nombre1, $nombre[$i]);
//            $i = $i + 1;
//        }
        $ip = $_SERVER['REMOTE_ADDR'];
        Tag::displayTo('ip', $ip);
    }

    /**
     * Editar el Caja
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $caja = $this->Caja->findFirst($id);
        if ($caja) {
            Tag::displayTo('id', $caja->getId());
            Tag::displayTo('numero', $caja->getNumeroCaja());
            Tag::displayTo('descripcion', $caja->getDescripcion());
            //Tag::displayTo('estado', $caja->getEstado());
            //Tag::displayTo('usuario', $caja->getUsuario());
            //creo un servicio caja busco los que hay de ese servicio caja y pongo en chq los servicios que son
            $servcaja = new Serviciocaja();
            $servcaja1 = $servcaja->find('caja_id=' . $id);
            foreach ($servcaja1 as $srv) {
                $array[] = $srv->getServicioId();
                $chk = "chk" . $srv->getServicioId();
                echo Tag::displayTo($chk, 'on');
            }
            //INICIO VALORES DE TIPO CALIFICACION
            $value_tipo_calificacion = $caja->getTipoCalificacionOperador();
            if (strlen($value_tipo_calificacion) > 1) {
                $value_tipo_calificacion = substr($value_tipo_calificacion, 0, 1);
            }

            Tag::displayTo("radio_tipo_calificacion", $value_tipo_calificacion);
            //FIN VALORES DE TIPO CALIFICACION
            //INICIO VALORES PARA HORARIO DE TRABAJO
            $value_horario = $caja->getHorarioId();
            Tag::displayTo("radio_horario", $value_horario);

            //Tag::displayTo('horario_id', $caja->getHorarioId());
            Tag::displayTo('transferir_uno', $caja->getTransferirUno());
            Tag::displayTo('transferir_todos', $caja->getTransferirTodos());
            Tag::displayTo('ubicacion_id', $caja->getUbicacionId());
            Tag::displayTo('ip', $caja->getIp());
            Tag::displayTo('tiempo', $caja->getTiempo());

            //FIN VALORES PARA HORARIO DE TRABAJO
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Caja
     *
     */
    public function guardarAction($isEdit = false, $id = null) {
        $numero = $this->getPostParam("numero", "striptags", "extraspaces");
        $descripcion = $this->getPostParam("descripcion", "striptags", "extraspaces");
        //$usuario                = $this->getPostParam("usuarios", "striptags", "extraspaces"); //del combo de usuarios
        $radio_tipo_calificacion = $this->getPostParam("radio_tipo_calificacion");
        //----a�adido----
        $radio_grupo_pregunta = $this->getPostParam("radio_grupo_pregunta");
        if ($radio_grupo_pregunta != NULL) {
            $radio_tipo_calificacion = $radio_tipo_calificacion . ';' . $radio_grupo_pregunta;
        }
        //----fin----
        $horario_id = $this->getPostParam("radio_horario");
        $transferirUno = $this->getPostParam("transferir_uno");
        if ($transferirUno == "on")
            $transferirUno = 1;
        else
            $transferirUno = 0;
        $transferirTodos = $this->getPostParam("transferir_todos");
        if ($transferirTodos == "on")
            $transferirTodos = 1;
        else
            $transferirTodos = 0;
        $ubicacionId = $this->getPostParam("ubicacion_id", "int");

        $check_servicio = $this->getPostParam("chkservicio");
        $check_servicio_secundario = $this->getPostParam("chkservicio_secundario");
        $ip = $this->getPostParam("ip", "striptags", "extraspaces");
        $tiempo = $this->getPostParam("tiempo", "int");

        $est = '1';
        /* if($estado=='Inactivo') {
          $est='0';
          } */
        $caja1 = new Caja();
        $idcaja = $caja1->maximum("id");
        $idcaja = $idcaja + 1;
        if ($id == null) {
            $id = $idcaja;
        }

        $caja = new Caja();
        $caja->setId($id);
        $caja->setNumeroCaja($numero);
        $caja->setDescripcion($descripcion);
        $caja->setHorarioId($horario_id);
        $caja->setEstado($est);
        $caja->setTipoCalificacionOperador($radio_tipo_calificacion);
        $caja->setTransferirUno($transferirUno);
        $caja->setTransferirTodos($transferirTodos);
        $caja->setUbicacionId($ubicacionId);
        $caja->setIp($ip);
        $caja->setTiempo($tiempo);

        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if (empty($horario_id))
            Flash::error('Seleccione el horario.');
        else {
            //busco si ya esta asignada la ip
            $buscaCaja = $caja->findFirst("ip='$ip' AND id NOT IN ($id)");
            if ($buscaCaja) {
                Flash::error("IP $ip asignada asignado previamente.");
            } else {
                if ($caja->save() == false) {
                    Flash::error('Hubo un error guardando el registro.');
                } else {
                    //INICIO SERVICIOS POR CAJA
                    if ($isEdit == true) {
                        $caja_servicio = new Serviciocaja();
                        $whereCondition = "caja_id= $id";
                        $caja_servicio->deleteAll($whereCondition);
                        if (!empty($check_servicio)) {
                            foreach ($check_servicio as $servicio) {
                                $caja_servicio->setCajaId($id);
                                $caja_servicio->setServicioId($servicio);
                                $caja_servicio->setSecundario(0);
                                $caja_servicio->setLlamar(1);
                                $caja_servicio->save();
                            }
                        }
                        if (!empty($check_servicio_secundario)) {
                            foreach ($check_servicio_secundario as $servicio2) {
                                $id_ser = $servicio2;
                                $whereCondition = "caja_id= $id AND servicio_id= $id_ser";
                                $caja_servicio->updateAll('secundario= 1, llamar=0', $whereCondition);
                            }
                        }
                    } else {
                        $buscaMax = $caja->maximum("id");
                        $caja_servicio = new Serviciocaja();
                        //$whereCondition="caja_id= $id";
                        //$caja_servicio->deleteAll($whereCondition);
                        if (!empty($check_servicio)) {
                            foreach ($check_servicio as $servicio) {
                                $caja_servicio->setCajaId($buscaMax);
                                $caja_servicio->setServicioId($servicio);
                                $caja_servicio->setSecundario(0);
                                $caja_servicio->setLlamar(1);
                                $caja_servicio->save();
                            }
                        }
                    }
                    //FIN SERVICIOS POR CAJA
                    $this->setParamToView('save', 'true');
                    if ($this->getQueryParam("exit") != "")
                        $this->setParamToView('exit', 'true');
                    else {
                        if (!$isEdit) {
                            $action = 'editar';
                        }
                    }
                    Flash::success('Registro guardado con éxito.');
                }
            }
            //}
        }
        $this->routeTo("action: $action", "id: $id");
        // $this->routeTo('action: index');
    }

    /**
     * Eliminar el Caja
     *
     */
    public function eliminarAction() {
        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {

                $conditions = "caja_id=" . $ids[$i];

                $serviciocaja = new Serviciocaja();
                $usercaja = new Usercaja();
                $turnospreguntas = new Turnospreguntas();
                $sesiones = new Sesiones();       //ligado a operador
                $caja_pausas = new CajaPausas();     //ligado a operador

                $sesiones->deleteAll($conditions);
                $caja_pausas->deleteAll($conditions);

                $serviciocaja->deleteAll($conditions);
                $usercaja->deleteAll($conditions);
                $turnospreguntas->deleteAll($conditions);



                if (!$this->Caja->delete($ids[$i])) {
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
                    case 'horario_id':
                        $condHorario = Utils::toSqlParamSearchGrid('nombre_horario', $abrevoper, $strbusqueda);
                        $Horario = $this->Horario->find($condHorario);
                        if (count($Horario) > 0) {
                            $arrayIdsHorario = array();
                            foreach ($Horario as $fila) {
                                $arrayIdsHorario[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsHorario);
                            $abrevoper = 'in';
                        }
                        break;
                    case 'ubicacion_id':
                        $condUbicacion = Utils::toSqlParamSearchGrid('nombre_ubicacion', $abrevoper, $strbusqueda);
                        $Ubicacion = $this->Ubicacion->find($condUbicacion);
                        if (count($Ubicacion) > 0) {
                            $arrayIdsUbicacion = array();
                            foreach ($Ubicacion as $fila) {
                                $arrayIdsUbicacion[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsUbicacion);
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
                            case 'horario_id':
                                $condHorario = Utils::toSqlParamSearchGrid('nombre_horario', $val['op'], $val['data']);
                                $Horario = $this->Horario->find($condHorario);
                                if (count($Horario) > 0) {
                                    $arrayIdsHorario = array();
                                    foreach ($Horario as $fila) {
                                        $arrayIdsHorario[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsHorario);
                                    $val['op'] = 'in';
                                }
                                break;
                            case 'ubicacion_id':
                                $condUbicacion = Utils::toSqlParamSearchGrid('nombre_ubicacion', $val['op'], $val['data']);
                                $Ubicacion = $this->Ubicacion->find($condUbicacion);
                                if (count($Ubicacion) > 0) {
                                    $arrayIdsUbicacion = array();
                                    foreach ($Ubicacion as $fila) {
                                        $arrayIdsUbicacion[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsUbicacion);
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
                    case 'numero':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'descripcion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'estado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'usuario':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'horario_id':
                        $condHorario = Utils::toSqlParamSearchGrid('nombre_horario', 'bw', $v);
                        $Horario = $this->Horario->find($condHorario);
                        if (count($Horario) > 0) {
                            $arrayIdsHorario = array();
                            foreach ($Horario as $fila) {
                                $arrayIdsHorario[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsHorario);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'ubicacion_id':
                        $condUbicacion = Utils::toSqlParamSearchGrid('nombre_ubicacion', 'bw', $v);
                        $Ubicacion = $this->Ubicacion->find($condUbicacion);
                        if (count($Ubicacion) > 0) {
                            $arrayIdsUbicacion = array();
                            foreach ($Ubicacion as $fila) {
                                $arrayIdsUbicacion[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsUbicacion);
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
        $contar = $this->Caja->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Caja->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;

        $array_op_calificacion = array('A' => 'Teclado de 4 botones', 'B' => 'Touch de 4 botones', 'C' => 'Touch multipreguntas', 'D' => 'Ninguno');

        for ($i = $inicio; $i < $limite; $i++) {
            $Caja = $resultado[$i];
            $caja_id = $Caja->getId();
            $condicion = "{#Caja}.id = $caja_id";
            $query = new ActiveRecordJoin(array(
                "entities" => array("Caja", "Ubicacion", "Horario"),
                "fields" => array(
                    "{#Caja}.numero_caja",
                    "{#Caja}.descripcion",
                    "{#Caja}.tipo_calificacion_operador",
                    "{#Caja}.transferir_uno",
                    "{#Caja}.transferir_todos",
                    "{#Caja}.ip",
                    "{#Caja}.tiempo",
                    "{#Horario}.nombre_horario",
                    "{#Ubicacion}.nombre_ubicacion",
                ),
                "conditions" => $condicion
            ));
            $nombre_usuario = "";
            $tipo_operador = "";
            foreach ($query->getResultSet() as $result) {
                $numero_caja = $result->getNumeroCaja();
                //$ip = $result->getIp();
                $descripcion = $result->getDescripcion();
                $tipo_calificacion = $result->getTipoCalificacionOperador();
                $horario = $result->getNombreHorario();
                $t_uno = $result->getTransferirUno();
                $t_todos = $result->getTransferirTodos();
                $ubicacion = $result->getNombreUbicacion();
                $tiempo_calificador = $result->getTiempo();
            }

            foreach ($array_op_calificacion as $key => $valor) {
                if ($key == $tipo_calificacion)
                    $tipo_calificacion = $valor;
            }

            $transferir_uno = '<font color="#01CF00"><b>Si</b></font>';
            $transferir_todos = '<font color="#01CF00"><b>Si</b></font>';
            if ($t_uno == 0)
                $transferir_uno = '<font color="red"><b>No</b></font>';
            if ($t_todos == 0)
                $transferir_todos = '<font color="red"><b>No</b></font>';

            $jqgrid->rows[] = array('id' => $Caja->getId(), 'cell' => array($Caja->getNumeroCaja(), $Caja->getIp(), $Caja->getDescripcion(), $tipo_calificacion, $horario, $transferir_uno, $transferir_todos, $ubicacion, $Caja->getTiempo()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
