<?php

/**
 * Controlador Menu
 *
 * @access public
 * @version 1.0
 */
class MenuController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * modulo_id
     *
     * @var int
     */
    public $moduloId;

    /**
     * nombre
     *
     * @var string
     */
    public $nombre;

    /**
     * ruta
     *
     * @var string
     */
    public $ruta;

    /**
     * idreferencia
     *
     * @var int
     */
    public $idreferencia;

    /**
     * estado
     *
     * @var int
     */
    public $estado;

    /**
     * orden
     *
     * @var int
     */
    public $orden;

    /**
     * principal
     *
     * @var int
     */
    public $principal;

    /**
     * posicion
     *
     * @var string
     */
    public $posicion;

    /**
     * tipo_ventana
     *
     * @var string
     */
    public $tipoVentana;

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
        $columnsGrid[] = array('name' => 'Modulo', 'index' => 'modulo_id');
        $columnsGrid[] = array('name' => 'Nombre', 'index' => 'nombre');
        $columnsGrid[] = array('name' => 'Ruta', 'index' => 'ruta');
        $columnsGrid[] = array('name' => 'Idreferencia', 'index' => 'idreferencia');
        $columnsGrid[] = array('name' => 'Estado', 'index' => 'estado');
        $columnsGrid[] = array('name' => 'Orden', 'index' => 'orden');
        $columnsGrid[] = array('name' => 'Principal', 'index' => 'principal');
        $columnsGrid[] = array('name' => 'Posicion', 'index' => 'posicion');
        $columnsGrid[] = array('name' => 'Tipo Ventana', 'index' => 'tipo_ventana');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Menu/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Menu
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $menu = $this->Menu->findFirst($id);
        if ($menu) {
            Tag::displayTo('id', $menu->getId());
            Tag::displayTo('modulo_id', $menu->getModuloId());
            Tag::displayTo('nombre', $menu->getNombre());
            Tag::displayTo('ruta', $menu->getRuta());
            Tag::displayTo('idreferencia', $menu->getIdreferencia());
            Tag::displayTo('estado', $menu->getEstado());
            Tag::displayTo('orden', $menu->getOrden());
            Tag::displayTo('principal', $menu->getPrincipal());
            Tag::displayTo('posicion', $menu->getPosicion());
            Tag::displayTo('tipo_ventana', $menu->getTipoVentana());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Menu
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $moduloId = $this->getPostParam("modulo_id", "int");
        $nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
        $ruta = $this->getPostParam("ruta", "striptags", "extraspaces");
        $idreferencia = $this->getPostParam("idreferencia", "int");
        $estado = $this->getPostParam("estado", "int");
        $orden = $this->getPostParam("orden", "int");
        $principal = $this->getPostParam("principal", "int");
        $posicion = $this->getPostParam("posicion", "striptags", "extraspaces");
        $tipoVentana = $this->getPostParam("tipo_ventana", "striptags", "extraspaces");
        $menu = new Menu();
        $menu->setId($id);
        $menu->setModuloId($moduloId);
        $menu->setNombre($nombre);
        $menu->setRuta($ruta);
        $menu->setIdreferencia($idreferencia);
        $menu->setEstado($estado);
        $menu->setOrden($orden);
        $menu->setPrincipal($principal);
        $menu->setPosicion($posicion);
        $menu->setTipoVentana($tipoVentana);
        if ($menu->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
        } else {
            Flash::success('Registro guardado con éxito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Menu
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Menu->delete($ids[$i])) {
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
                    case 'modulo_id':
                        $condModulo = Utils::toSqlParamSearchGrid('id', $abrevoper, $strbusqueda);
                        $Modulo = $this->Modulo->find($condModulo);
                        if (count($Modulo) > 0) {
                            $arrayIdsModulo = array();
                            foreach ($Modulo as $fila) {
                                $arrayIdsModulo[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsModulo);
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
                            case 'modulo_id':
                                $condModulo = Utils::toSqlParamSearchGrid('id', $val['op'], $val['data']);
                                $Modulo = $this->Modulo->find($condModulo);
                                if (count($Modulo) > 0) {
                                    $arrayIdsModulo = array();
                                    foreach ($Modulo as $fila) {
                                        $arrayIdsModulo[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsModulo);
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
                    case 'modulo_id':
                        $condModulo = Utils::toSqlParamSearchGrid('id', 'bw', $v);
                        $Modulo = $this->Modulo->find($condModulo);
                        if (count($Modulo) > 0) {
                            $arrayIdsModulo = array();
                            foreach ($Modulo as $fila) {
                                $arrayIdsModulo[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsModulo);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'nombre':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'ruta':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'idreferencia':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'estado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'orden':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'principal':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'posicion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'tipo_ventana':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Menu->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Menu->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Menu = $resultado[$i];
            $Modulo = $Menu->getModulo();
            $jqgrid->rows[] = array('id' => $Menu->getId(), 'cell' => array($Menu->getId(), $Modulo->getNombre(), $Menu->getNombre(), $Menu->getRuta(), $Menu->getIdreferencia(), $Menu->getEstado(), $Menu->getOrden(), $Menu->getPrincipal(), $Menu->getPosicion(), $Menu->getTipoVentana()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
