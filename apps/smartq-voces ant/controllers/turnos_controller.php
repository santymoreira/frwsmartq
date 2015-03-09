<?php

/**
 * Controlador Turnos
 *
 * @access public
 * @version 1.0
 */
class TurnosController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * servicio_id
     *
     * @var int
     */
    public $servicioId;

    /**
     * numero
     *
     * @var int
     */
    public $numero;

    /**
     * fecha_emision
     *
     */
    public $fechaEmision;

    /**
     * hora_emision
     *
     */
    public $horaEmision;

    /**
     * estado
     *
     * @var int
     */
    public $estado;

    /**
     * caja_id
     *
     * @var int
     */
    public $cajaId;

    /**
     * por_atender
     *
     * @var int
     */
    public $porAtender;

    /**
     * atendido
     *
     * @var int
     */
    public $atendido;

    /**
     * fecha_inicio_atencion
     *
     */
    public $fechaInicioAtencion;

    /**
     * hora_inicio_atencion
     *
     */
    public $horaInicioAtencion;

    /**
     * fecha_fin_atencion
     *
     */
    public $fechaFinAtencion;

    /**
     * hora_fin_atencion
     *
     */
    public $horaFinAtencion;

    /**
     * duracion
     *
     */
    public $duracion;

    /**
     * rechazado
     *
     * @var int
     */
    public $rechazado;

    /**
     * calificacion
     *
     * @var string
     */
    public $calificacion;

    /**
     * transferido
     *
     * @var int
     */
    public $transferido;

    /**
     * username
     *
     * @var string
     */
    public $username;

    /**
     * nombre_usuario
     *
     * @var string
     */
    public $nombreUsuario;

    /**
     * adm_revisado
     *
     * @var int
     */
    public $admRevisado;

    /**
     * numero_alias
     *
     * @var int
     */
    public $numeroAlias;

    /**
     * prioridad
     *
     * @var int
     */
    public $prioridad;

    /**
     * id_username
     *
     * @var int
     */
    public $idUsername;

    /**
     * valor_calificacion
     *
     * @var int
     */
    public $valorCalificacion;

    /**
     * foto
     *
     */
    public $foto;

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
        $columnsGrid[] = array('name' => 'Id', 'index' => 'id');
        $columnsGrid[] = array('name' => 'Servicio', 'index' => 'servicio_id');
        $columnsGrid[] = array('name' => 'Numero', 'index' => 'numero');
        $columnsGrid[] = array('name' => 'Fecha Emision', 'index' => 'fecha_emision');
        $columnsGrid[] = array('name' => 'Hora Emision', 'index' => 'hora_emision');
        //$columnsGrid[] = array('name' => 'Estado', 'index' => 'estado');
        //$columnsGrid[] = array('name' => 'Caja', 'index' => 'caja_id');
        $columnsGrid[] = array('name' => 'Por Atender', 'index' => 'por_atender');
        $columnsGrid[] = array('name' => 'Atendido', 'index' => 'atendido');
        $columnsGrid[] = array('name' => 'Fecha Inicio Atencion', 'index' => 'fecha_inicio_atencion');
        $columnsGrid[] = array('name' => 'Hora Inicio Atencion', 'index' => 'hora_inicio_atencion');
        $columnsGrid[] = array('name' => 'Fecha Fin Atencion', 'index' => 'fecha_fin_atencion');
        $columnsGrid[] = array('name' => 'Hora Fin Atencion', 'index' => 'hora_fin_atencion');
        $columnsGrid[] = array('name' => 'Duracion', 'index' => 'duracion');
        $columnsGrid[] = array('name' => 'Rechazado', 'index' => 'rechazado');
        $columnsGrid[] = array('name' => 'Calificacion', 'index' => 'calificacion');
        $columnsGrid[] = array('name' => 'Transferido', 'index' => 'transferido');
        $columnsGrid[] = array('name' => 'Adm Revisado', 'index' => 'adm_revisado');
        $columnsGrid[] = array('name' => 'Numero Alias', 'index' => 'numero_alias');
        $columnsGrid[] = array('name' => 'Prioridad', 'index' => 'prioridad');
        $columnsGrid[] = array('name' => 'Id Usuario', 'index' => 'id_username');
        $columnsGrid[] = array('name' => 'Nombre Usuario', 'index' => 'usuario');
        $columnsGrid[] = array('name' => 'Valor Calificacion', 'index' => 'valor_calificacion');
        $columnsGrid[] = array('name' => 'Foto', 'index' => 'foto');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Turnos/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Turnos
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $turnos = $this->Turnos->findFirst($id);
        if ($turnos) {
            Tag::displayTo('id', $turnos->getId());
            Tag::displayTo('servicio_id', $turnos->getServicioId());
            Tag::displayTo('numero', $turnos->getNumero());
            Tag::displayTo('fecha_emision', $turnos->getFechaEmision());
            Tag::displayTo('hora_emision', $turnos->getHoraEmision());
            Tag::displayTo('estado', $turnos->getEstado());
            Tag::displayTo('caja_id', $turnos->getCajaId());
            Tag::displayTo('por_atender', $turnos->getPorAtender());
            Tag::displayTo('atendido', $turnos->getAtendido());
            Tag::displayTo('fecha_inicio_atencion', $turnos->getFechaInicioAtencion());
            Tag::displayTo('hora_inicio_atencion', $turnos->getHoraInicioAtencion());
            Tag::displayTo('fecha_fin_atencion', $turnos->getFechaFinAtencion());
            Tag::displayTo('hora_fin_atencion', $turnos->getHoraFinAtencion());
            Tag::displayTo('duracion', $turnos->getDuracion());
            Tag::displayTo('rechazado', $turnos->getRechazado());
            Tag::displayTo('calificacion', $turnos->getCalificacion());
            Tag::displayTo('transferido', $turnos->getTransferido());
            Tag::displayTo('username', $turnos->getUsername());
            Tag::displayTo('nombre_usuario', $turnos->getNombreUsuario());
            Tag::displayTo('adm_revisado', $turnos->getAdmRevisado());
            Tag::displayTo('numero_alias', $turnos->getNumeroAlias());
            Tag::displayTo('prioridad', $turnos->getPrioridad());
            Tag::displayTo('id_username', $turnos->getIdUsername());
            Tag::displayTo('valor_calificacion', $turnos->getValorCalificacion());
            Tag::displayTo('foto', $turnos->getFoto());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Turnos
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $servicioId = $this->getPostParam("servicio_id", "int");
        $numero = $this->getPostParam("numero", "int");
        $fechaEmision = $this->getPostParam("fecha_emision");
        $horaEmision = $this->getPostParam("hora_emision");
        $estado = $this->getPostParam("estado", "int");
        $cajaId = $this->getPostParam("caja_id", "int");
        $porAtender = $this->getPostParam("por_atender", "int");
        $atendido = $this->getPostParam("atendido", "int");
        $fechaInicioAtencion = $this->getPostParam("fecha_inicio_atencion");
        $horaInicioAtencion = $this->getPostParam("hora_inicio_atencion");
        $fechaFinAtencion = $this->getPostParam("fecha_fin_atencion");
        $horaFinAtencion = $this->getPostParam("hora_fin_atencion");
        $duracion = $this->getPostParam("duracion");
        $rechazado = $this->getPostParam("rechazado", "int");
        $calificacion = $this->getPostParam("calificacion", "striptags", "extraspaces");
        $transferido = $this->getPostParam("transferido", "int");
        $username = $this->getPostParam("username", "striptags", "extraspaces");
        $nombreUsuario = $this->getPostParam("nombre_usuario", "striptags", "extraspaces");
        $admRevisado = $this->getPostParam("adm_revisado", "int");
        $numeroAlias = $this->getPostParam("numero_alias", "int");
        $prioridad = $this->getPostParam("prioridad", "int");
        $idUsername = $this->getPostParam("id_username", "int");
        $valorCalificacion = $this->getPostParam("valor_calificacion", "int");
        $foto = $this->getPostParam("foto");
        $turnos = new Turnos();
        $turnos->setId($id);
        $turnos->setServicioId($servicioId);
        $turnos->setNumero($numero);
        $turnos->setFechaEmision($fechaEmision);
        $turnos->setHoraEmision($horaEmision);
        $turnos->setEstado($estado);
        $turnos->setCajaId($cajaId);
        $turnos->setPorAtender($porAtender);
        $turnos->setAtendido($atendido);
        $turnos->setFechaInicioAtencion($fechaInicioAtencion);
        $turnos->setHoraInicioAtencion($horaInicioAtencion);
        $turnos->setFechaFinAtencion($fechaFinAtencion);
        $turnos->setHoraFinAtencion($horaFinAtencion);
        $turnos->setDuracion($duracion);
        $turnos->setRechazado($rechazado);
        $turnos->setCalificacion($calificacion);
        $turnos->setTransferido($transferido);
        $turnos->setUsername($username);
        $turnos->setNombreUsuario($nombreUsuario);
        $turnos->setAdmRevisado($admRevisado);
        $turnos->setNumeroAlias($numeroAlias);
        $turnos->setPrioridad($prioridad);
        $turnos->setIdUsername($idUsername);
        $turnos->setValorCalificacion($valorCalificacion);
        $turnos->setFoto($foto);
        if ($turnos->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
        } else {
            Flash::success('Registro guardado con éxito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Turnos
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Turnos->delete($ids[$i])) {
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
                    case 'caja_id':
                        $condCaja = Utils::toSqlParamSearchGrid('id', $abrevoper, $strbusqueda);
                        $Caja = $this->Caja->find($condCaja);
                        if (count($Caja) > 0) {
                            $arrayIdsCaja = array();
                            foreach ($Caja as $fila) {
                                $arrayIdsCaja[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsCaja);
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
                            case 'caja_id':
                                $condCaja = Utils::toSqlParamSearchGrid('id', $val['op'], $val['data']);
                                $Caja = $this->Caja->find($condCaja);
                                if (count($Caja) > 0) {
                                    $arrayIdsCaja = array();
                                    foreach ($Caja as $fila) {
                                        $arrayIdsCaja[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsCaja);
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
                    case 'id':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
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
                    case 'numero':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'fecha_emision':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'hora_emision':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'estado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'caja_id':
                        $condCaja = Utils::toSqlParamSearchGrid('id', 'bw', $v);
                        $Caja = $this->Caja->find($condCaja);
                        if (count($Caja) > 0) {
                            $arrayIdsCaja = array();
                            foreach ($Caja as $fila) {
                                $arrayIdsCaja[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsCaja);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'por_atender':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'atendido':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'fecha_inicio_atencion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'hora_inicio_atencion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'fecha_fin_atencion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'hora_fin_atencion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'duracion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'rechazado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'calificacion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'transferido':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'adm_revisado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'numero_alias':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'prioridad':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'id_username':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'valor_calificacion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'foto':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Turnos->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Turnos->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Turnos = $resultado[$i];
            $Servicio = $Turnos->getServicio();

            $Caja = $Turnos->getCaja();
            //$Caja->getId()
            //$Usuario = $Turnos->getIdUsername();
            $id_usuario = $Turnos->getIdUsername();
            $nombre_usuario = "";
            if ($id_usuario > 0) {
                $usuario = new Usuario();
                $buscaUsuario = $usuario->findFirst("id='$id_usuario'");
                $nombre_usuario = $buscaUsuario->getNombres();
            }
            $foto = "<img id='' src='{$Turnos->getFoto()}'/>";
            $jqgrid->rows[] = array('id' => $Turnos->getId(), 'cell' => array($Turnos->getId(), $Servicio->getNombre(), $Turnos->getNumero(), $Turnos->getFechaEmision(), $Turnos->getHoraEmision(), $Turnos->getPorAtender(), $Turnos->getAtendido(), $Turnos->getFechaInicioAtencion(), $Turnos->getHoraInicioAtencion(), $Turnos->getFechaFinAtencion(), $Turnos->getHoraFinAtencion(), $Turnos->getDuracion(), $Turnos->getRechazado(), $Turnos->getCalificacion(), $Turnos->getTransferido(), $Turnos->getAdmRevisado(), $Turnos->getNumeroAlias(), $Turnos->getPrioridad(), $Turnos->getIdUsername(), $nombre_usuario, $Turnos->getValorCalificacion(), $foto));
            //$jqgrid->rows[] = array('id' => $Turnos->getId(), 'cell' => array($Turnos->getId(), $Servicio->getNombre(), $Turnos->getNumero(), $Turnos->getFechaEmision(), $Turnos->getHoraEmision(), $Turnos->getPorAtender(), $Turnos->getAtendido(), $Turnos->getFechaInicioAtencion(), $Turnos->getHoraInicioAtencion(), $Turnos->getRechazado(), $Turnos->getEstado()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    public function listarturnosAction() {
        $db1 = DbBase::rawConnect();
        $result = $db1->query("SELECT * FROM turnos where atendido = 1 ORDER BY id DESC");
        $tabla = "<table>";
        while ($row = $db1->fetchArray($result)) {
            //$foto = $row['foto'];
            $foto = "<img id='' src='{$row['foto']}'/>";
            $tabla.= "<tr><td>{$row['id']}</td><td>$foto</td></tr>";
        }
        $tabla.= "</table>";
        echo $tabla;
    }

}
