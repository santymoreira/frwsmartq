<?php

/**
 * Controlador Grupo
 *
 * @access public
 * @version 1.0
 */
class GrupoController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nombre_largo
     *
     * @var string
     */
    public $nombreLargo;

    /**
     * nombre_corto
     *
     * @var string
     */
    public $nombreCorto;

    /**
     * unico
     *
     * @var int
     */
    public $unico;

    /**
     * descripcion
     *
     */
    public $descripcion;

    /**
     * creacion_at
     *
     */
    public $creacionAt;
    public $usuario_id;
    public $nombre;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        if (!SessionNamespace::exists("datosUsuarioSMC")) {
            Flash::addMessage("Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente", Flash::WARNING);
            $this->redirect("login");
        }
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $this->usuario_id = $dataUsuario->getId();      //id de usuario
        $this->nombre = $dataUsuario->getNombre();      //id de usuario
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[] = array('name' => 'Id', 'index' => 'id', 'width' => 40);
        $columnsGrid[] = array('name' => 'Nombre', 'index' => 'nombre_largo', 'width' => 190);
        $columnsGrid[] = array('name' => 'Unico', 'index' => 'unico', 'width' => 60);
        $columnsGrid[] = array('name' => 'Descripción', 'index' => 'descripcion', 'width' => 400);
        Tag::setColumnsToGrid($columnsGrid);
    }

    public $array_roles;

    /**
     * Luego que se loguea viene aca
     */
    public function seleccionarGrupoAction() {
        //busco roles/grupos al que pertenece
        $sql = new ActiveRecordJoin(array("entities" => array("Grupo", "Grupousuario"),
            "fields" => array(
                "{#Grupo}.id",
                "{#Grupo}.nombre_largo"),
            "conditions" => "{#Grupousuario}.usuario_id= '{$this->usuario_id}'"));
        $this->array_roles = array();
        foreach ($sql->getResultSet() as $result) {
            $this->array_roles[$result->getId()] = $result->getNombreLargo();
        }
        //pregunto si tiene un solo rol redirecciona inmediatamente
        if (count($this->array_roles) == 1) {
            foreach ($this->array_roles as $key => $value) {
                $rol_id = $key;
            }
            if(!in_array($rol_id, array(4,5,6,7,8))) {
                //-- si el rol tiene menus
                $modulo = new Modulo();
                $menu = new Menu();
                $db = DbBase::rawConnect();
                $result = $db->query("SELECT modulo.id AS modulo_id, modulo.nombre AS modulo_nombre, grupo_id
            FROM modulo, menu, permiso p
            WHERE p.menu_id=menu.id AND modulo.id=menu.modulo_id AND grupo_id = $rol_id AND modulo.estado=1 AND menu.estado=1 AND permiso=1 
            GROUP BY modulo_id");
                //echo "total:".$db->numRows($result); die();
                if ($db->numRows($result) == 0) {
                    Flash::addMessage("El rol seleccionado no tiene asignado menús. Comuníquese con el administrador del sistema.", Flash::WARNING);
                    //header("Location:" . $_SERVER['HTTP_REFERER']);
                    $this->redirect("login/salir");
                } else
                    $this->redirect("grupo/iniciar/$rol_id");
            } else
                $this->redirect("grupo/iniciar/$rol_id");
            //$this->routeTo("action: iniciar");
            //$this->routeTo("action: iniciar","rol_id: $rol_id");
        } else { //si tiene mas de un rol muestra la vista seleccionar rol
            //por defecto apacera la vista
        }
    }

    /**
     * Visualiza segun el rol
     * @param type $rol_id
     */
    public function iniciarAction($rol_id) {
        //echo "grupo:$rol_id"; die();
        //guardo en variable de sesion el rol/grupo seleccionado
        $dataUsuario = SessionNamespace::add('datosUsuarioSMC');
        $dataUsuario->setRolSeleccionado($rol_id);

        if ($rol_id == 4) { //dispensador
            $this->redirect("dispensadorservicio"); //muestra el dispensador de servicios
        } else if ($rol_id == 5) { //operador con ticket
            //Router::routeTo("controller: operador", "action: index");
            Router::routeTo("controller: operador", "action: index");   //tabler
        } else if ($rol_id == 7) { //operador sin ticket
            Router::routeTo("controller: cajero", "action: index");
        } else if ($rol_id == 6) { //pantalla
            $this->redirect("display");
        } else if ($rol_id == 8) { //pantalla
            $this->redirect("display");
        } else {
            $this->redirect("template"); //muestra el menu del usuario administrador
        }
    }

    /**
     * Crear un Grupo/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Grupo
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $grupo = $this->Grupo->findFirst($id);
        if ($grupo) {
            Tag::displayTo('id', $grupo->getId());
            Tag::displayTo('nombre_largo', $grupo->getNombreLargo());
            Tag::displayTo('nombre_corto', $grupo->getNombreCorto());
            Tag::displayTo('unico', $grupo->getUnico());
            Tag::displayTo('descripcion', $grupo->getDescripcion());
            Tag::displayTo('creacion_at', $grupo->getCreacionAt());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Grupo
     *
     */
    public function guardarAction($isEdit = false, $id = null) {
        $nombreLargo = $this->getPostParam("nombre_largo", "striptags", "extraspaces");
        $nombreCorto = $this->getPostParam("nombre_corto", "striptags", "extraspaces");
        $unico = $this->getPostParam("unico");
        if ($unico == "on")
            $unico = 1;
        else
            $unico = 0;
        $descripcion = $this->getPostParam("descripcion");
        $creacionAt = $this->getPostParam("creacion_at");
        $grupo = new Grupo();
        $grupo->setId($id);
        $grupo->setNombreLargo($nombreLargo);
        $grupo->setNombreCorto($nombreCorto);
        $grupo->setUnico($unico);
        $grupo->setDescripcion($descripcion);
        $grupo->setCreacionAt($creacionAt);
        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if ($grupo->save() == false) {
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
        $this->routeTo("action: $action", "id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Grupo
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Grupo->delete($ids[$i])) {
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
        $condicion = 'id NOT IN (1,4,5,6,7)';
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
                    case 'id':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'nombre_largo':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'unico':
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
        $contar = $this->Grupo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Grupo->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Grupo = $resultado[$i];
            $jqgrid->rows[] = array('id' => $Grupo->getId(), 'cell' => array($Grupo->getId(), $Grupo->getNombreLargo(), $Grupo->getUnico(), $Grupo->getDescripcion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
