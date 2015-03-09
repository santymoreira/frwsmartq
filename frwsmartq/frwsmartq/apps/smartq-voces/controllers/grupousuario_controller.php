<?php

/**
 * Controlador Grupousuario
 *
 * @access public
 * @version 1.0
 */
class GrupousuarioController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * grupo_id
     *
     * @var int
     */
    public $grupoId;

    /**
     * usuario_id
     *
     * @var int
     */
    public $usuarioId;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

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
        $columnsGrid[] = array('name' => 'Rol', 'index' => 'grupo_id');
        $columnsGrid[] = array('name' => 'Usuario', 'index' => 'usuario_id');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Grupousuario/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Grupousuario
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $grupousuario = $this->Grupousuario->findFirst($id);
        if ($grupousuario) {
            Tag::displayTo('id', $grupousuario->getId());
            Tag::displayTo('grupo_id', $grupousuario->getGrupoId());
            Tag::displayTo('usuario_id', $grupousuario->getUsuarioId());
            Tag::displayTo('creacion_at', $grupousuario->getCreacionAt());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Grupousuario
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $grupoId = $this->getPostParam("grupo_id", "int");
        $usuarioId = $this->getPostParam("usuario_id", "int");
        $creacionAt = $this->getPostParam("creacion_at");
        $grupousuario = new Grupousuario();
        $grupousuario->setId($id);
        $grupousuario->setGrupoId($grupoId);
        $grupousuario->setUsuarioId($usuarioId);
        $grupousuario->setCreacionAt($creacionAt);
        if ($grupousuario->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
            foreach ($grupo->getMessages() as $message)
                Flash::error($message->getMessage());
        } else {
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
        //$this->routeTo("action: $action", "id: $id");
        /* if ($grupousuario->save() == false) {
          Flash::error('Hubo un error guardando el registro.');
          } else {
          Flash::success('Registro guardado con éxito.');
          }
          $this->routeTo('action: index'); */
    }

    /**
     * Eliminar el Grupousuario
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Grupousuario->delete($ids[$i])) {
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
        $condicion = 'grupo_id NOT IN (1)';       //1=grupo configuradores del sistema
        $buscar = $this->getPostParam('_search', 'stripslashes'); //obtenemos la variable que activa la busqueda
        $strbusqueda = $this->getPostParam('searchString');  // obtenemos la cadena de busqueda
        $campoBusqueda = $this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda
        $abrevoper = $this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda
        $filtroBusqueda = $this->getPostParam('filters', 'stripslashes');
        if ($buscar == 'true') { //verificamos si la busqueda es activada
            if ($strbusqueda != '') {    // construccion de la cadena de condicion para la busqueda normal 
                switch ($campoBusqueda) {
                    case 'grupo_id':
                        $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', $abrevoper, $strbusqueda);
                        $Grupo = $this->Grupo->find($condGrupo);
                        if (count($Grupo) > 0) {
                            $arrayIdsGrupo = array();
                            foreach ($Grupo as $fila) {
                                $arrayIdsGrupo[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsGrupo);
                            $abrevoper = 'in';
                        }
                        break;
                    case 'usuario_id':
                        $condUsuario = Utils::toSqlParamSearchGrid('nombres', $abrevoper, $strbusqueda);
                        $Usuario = $this->Usuario->find($condUsuario);
                        if (count($Usuario) > 0) {
                            $arrayIdsUsuario = array();
                            foreach ($Usuario as $fila) {
                                $arrayIdsUsuario[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsUsuario);
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
                            case 'grupo_id':
                                $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', $val['op'], $val['data']);
                                $Grupo = $this->Grupo->find($condGrupo);
                                if (count($Grupo) > 0) {
                                    $arrayIdsGrupo = array();
                                    foreach ($Grupo as $fila) {
                                        $arrayIdsGrupo[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsGrupo);
                                    $val['op'] = 'in';
                                }
                                break;
                            case 'usuario_id':
                                $condUsuario = Utils::toSqlParamSearchGrid('nombres', $val['op'], $val['data']);
                                $Usuario = $this->Usuario->find($condUsuario);
                                if (count($Usuario) > 0) {
                                    $arrayIdsUsuario = array();
                                    foreach ($Usuario as $fila) {
                                        $arrayIdsUsuario[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsUsuario);
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
                    case 'grupo_id':
                        $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', 'bw', $v);
                        $Grupo = $this->Grupo->find($condGrupo);
                        if (count($Grupo) > 0) {
                            $arrayIdsGrupo = array();
                            foreach ($Grupo as $fila) {
                                $arrayIdsGrupo[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsGrupo);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'usuario_id':
                        $condUsuario = Utils::toSqlParamSearchGrid('nombres', 'bw', $v);
                        $Usuario = $this->Usuario->find($condUsuario);
                        if (count($Usuario) > 0) {
                            $arrayIdsUsuario = array();
                            foreach ($Usuario as $fila) {
                                $arrayIdsUsuario[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsUsuario);
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
        $contar = $this->Grupousuario->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Grupousuario->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Grupousuario = $resultado[$i];
            $Grupo = $Grupousuario->getGrupo();
            $Usuario = $Grupousuario->getUsuario();
            $jqgrid->rows[] = array('id' => $Grupousuario->getId(), 'cell' => array($Grupousuario->getId(), $Grupo->getNombreLargo(), $Usuario->getNombres()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
