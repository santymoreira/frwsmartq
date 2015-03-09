<?php

/**
 * Controlador Grupopregunta
 *
 * @access public
 * @version 1.0
 */
class GrupopreguntaController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nom_grupo
     *
     * @var string
     */
    public $nomGrupo;

    /**
     * cracion_at
     *
     */
    public $cracionAt;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * AcciÃ³n por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
//		$columnsGrid[]=array('name'=>'Id','index'=>'id');
        $columnsGrid[] = array('name' => 'Nombre', 'index' => 'nom_grupo');
//		$columnsGrid[]=array('name'=>'Cracion At','index'=>'cracion_at');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Grupopregunta/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Grupopregunta
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $grupopregunta = $this->Grupopregunta->findFirst($id);
        if ($grupopregunta) {
            Tag::displayTo('id', $grupopregunta->getId());
            Tag::displayTo('nom_grupo', $grupopregunta->getNomGrupo());
            Tag::displayTo('cracion_at', $grupopregunta->getCracionAt());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Grupopregunta
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $nomGrupo = $this->getPostParam("nom_grupo", "striptags", "extraspaces");
        $cracionAt = $this->getPostParam("cracion_at");
        if ($isEdit) {
            $cracionAt = date('Y-m-d');
        }
        $grupopregunta = new Grupopregunta();
        $grupopregunta->setId($id);
        $grupopregunta->setNomGrupo($nomGrupo);
        $grupopregunta->setCracionAt($cracionAt);
        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if ($grupopregunta->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
        } else {
            $this->setParamToView('save', 'true');
            if ($this->getQueryParam("exit") != "")
                $this->setParamToView('exit', 'true');
            else {
                if (!$isEdit) {
                    $action = 'editar';
                }
            }
            Flash::success('Registro guardado con Ã©xito.');
        }

        $this->routeTo("action: $action", "id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Grupopregunta
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Grupopregunta->delete($ids[$i])) {
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
        $limite = $this->getPostParam('rows'); // obtener el número de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de índice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la dirección de ordenado
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
                    case 'id':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'nom_grupo':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'cracion_at':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Grupopregunta->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Grupopregunta->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Grupopregunta = $resultado[$i];
            $jqgrid->rows[] = array('id' => $Grupopregunta->getId(), 'cell' => array($Grupopregunta->getNomGrupo()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}

