<?php

/**
 * Controlador Servicio
 *
 * @access public
 * @version 1.0
 */
class ServicioController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nombre
     *
     * @var string
     */
    public $nombre;

    /**
     * letra
     *
     * @var string
     */
    public $letra;

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
     * inicio
     *
     * @var int
     */
    public $inicio;

    /**
     * fin
     *
     * @var int
     */
    public $fin;

    /**
     * actual
     *
     * @var int
     */
    public $actual;
    public $ubicacion_id;
    public $estilo_letra;
    public $letra_alias;

    /**
     * tiempo_maximo
     *
     */
    public $tiempoMaximo;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

    /**
     * Inicializador del controlador/
     *
     */
    /* variable que sirve para seleccionar la letra sin repetirse */
    public $letra_asignada;

    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[] = array('name' => 'Nombre', 'index' => 'nombre');
        $columnsGrid[] = array('name' => 'Letra', 'index' => 'letra', 'width' => 40);
        $columnsGrid[] = array('name' => 'Descripción', 'index' => 'descripcion', 'width' => 250);
        $columnsGrid[] = array('name' => 'Ubicación', 'index' => 'descripcion', 'width' => 70);
        $columnsGrid[] = array('name' => 'Grupo Servicio', 'index' => 'gruposervicio', 'width' => 140);
        $columnsGrid[] = array('name' => 'Estado', 'index' => 'ubicacion', 'width' => 50);
        $columnsGrid[] = array('name' => 'Tiempo Máximo', 'index' => 'tiempo_maximo', 'width' => 100);
        $columnsGrid[] = array('name' => 'Atención Preferencial', 'index' => 'letra_alias', 'width' => 110);
        $columnsGrid[] = array('name' => 'Letra A. Preferencial', 'index' => 'letra_alias', 'width' => 110);
        //$columnsGrid[]=array('name'=>'Inicio','index'=>'inicio','width'=> 70);
        //$columnsGrid[]=array('name'=>'Fin','index'=>'fin','width'=> 70);
        //$columnsGrid[]=array('name'=>'Turno Actual','index'=>'actual','width'=> 70);
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Servicio/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * buscarletra
     *
     */
    public function buscarletraAction($identificador = NULL) {
        $this->setResponse('ajax');
        $letra = $this->getPostParam('letra');
        $conteo = 0;
        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT  letra FROM servicio where id NOT IN ($identificador) AND letra = '$letra' ");
        while ($row = $db->fetchArray($result2)) {
            $conteo++;
        }
        $result2 = $db->query("SELECT  letra_alias FROM servicio WHERE letra_alias = '$letra'");
        while ($row = $db->fetchArray($result2)) {
            $conteo++;
        }
        echo $conteo;
    }

    /**
     * buscarletra
     *
     */
    public function buscarletraaliasAction($identificador = NULL) {
        $this->setResponse('ajax');
        $letra = $this->getPostParam('letra');
        $conteo = 0;
        $db = DbBase::rawConnect();
        $result2 = $db->query("SELECT  letra FROM servicio where letra = '$letra' ");
        while ($row = $db->fetchArray($result2)) {
            $conteo++;
        }
        $result2 = $db->query("SELECT  letra_alias FROM servicio WHERE id NOT IN ($identificador) AND  letra_alias = '$letra'");
        while ($row = $db->fetchArray($result2)) {
            $conteo++;
        }
        echo $conteo;
    }

    /**
     * Editar el Servicio
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $servicio = $this->Servicio->findFirst($id);
        if ($servicio) {
            $fun = new funciones();
            $total_segundos = 0;
            Tag::displayTo('id', $servicio->getId());
            Tag::displayTo('nombre', $servicio->getNombre());
            Tag::displayTo('letra', $servicio->getLetra());   //Bloquear si letra es con combo
            Tag::displayTo('descripcion', $servicio->getDescripcion());
            Tag::displayTo('estado', $servicio->getEstado());
            Tag::displayTo('actual', $servicio->getActual());
            Tag::displayTo('color', $servicio->getEstiloLetra());
            Tag::displayTo('tiempo_maximo', $servicio->getTiempoMaximo());
            Tag::displayTo('letra_alias', $servicio->getLetraAlias());
            Tag::displayTo('atencion_preferencial', $servicio->getAtencionPreferencial());
            if($servicio->getAtencionPreferencial()==2){
                Tag::displayTo('mostrar_atencion_normal', 1);
            }else{
                Tag::displayTo('mostrar_atencion_normal', 0);
            }
            Tag::displayTo('creacion_at', $servicio->getCreacionAt());

            $fecha_hoy = date("Y-m-d");
            //INICIO Calcular un tiempo maximo de atencion de la fecha actual a 15 días antes
            $fecha_anterior = date("Y-m-d", strtotime("$fecha_hoy - 15 day"));   //a la fecha de hoy le resto 15 días
            //Consultar el tiempo según el servicio
            $cont = 0;
            $condicion = "{#Servicio}.id=$id AND fecha_emision>='$fecha_anterior' AND duracion>'00:00:00'";
            $query = new ActiveRecordJoin(array(
                        "entities" => array("Servicio", "Turnos"),
                        "fields" => array(
                            "{#Turnos}.duracion"),
                        "conditions" => $condicion
                    ));
            foreach ($query->getResultSet() as $result) {
                $cont+=1;
                $duracion = $result->getDuracion();
                $total_segundos+=$fun->totalSegundos($duracion); //retorna la duracion en segundos
            }
            //echo " cont".$cont."  ".round($total_segundos/$cont);
            if ($cont == 0)
                $tiempoMaximoSugerido = "00:00:00";
            else
                $tiempoMaximoSugerido = $fun->tiempo2(round($total_segundos / $cont));
            Tag::displayTo('tiempo_maximo_sugerido', $tiempoMaximoSugerido);

            //Usar si letra es con combo
            //$this->letra_asignada= $servicio->getLetra();
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Servicio
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        $nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
        //$letra = $this->getPostParam("letra", "onechar");   //desbloquer para letra con combo
        $letra = $this->getPostParam("letra", "striptags", "extraspaces");   //bloquear para letra con combo
        $descripcion = $this->getPostParam("descripcion", "striptags", "extraspaces");
        $estado = $this->getPostParam("estado");
        if ($estado == "on")
            $estado1 = 1;
        else
            $estado1 = 0;
        $inicio = $this->getPostParam("inicio", "int");
        $fin = $this->getPostParam("fin", "int");
        $actual = $this->getPostParam("actual", "int");

        $gruposervicio_id = $this->getPostParam("gruposervicio", "striptags", "extraspaces"); //del combo de grupo se servicio
        $ubicacion = $this->getPostParam("ubicacion", "striptags", "extraspaces"); //del combo de la ubicacion
        $estilo_letra = $this->getPostParam("color", "striptags", "extraspaces");
        $tiempoMaximo = $this->getPostParam("tiempo_maximo");
        $letraAlias = $this->getPostParam("letra_alias");
        $atencion_preferencial = $this->getPostParam("atencion_preferencial");
        if ($atencion_preferencial == "on")
            $atencion_preferencial = 1;
        else
            $atencion_preferencial = 0;
        $mostrar_atencion_normal = $this->getPostParam("mostrar_atencion_normal");
        if ($mostrar_atencion_normal == "on"){
            $atencion_preferencial = 2;
        }
        
        $creacionAt = $this->getPostParam("creacion_at");
        $servicio = new Servicio();
        $servicio->setId($id);
        $servicio->setNombre($nombre);
        $servicio->setLetra($letra);
        $servicio->setDescripcion($descripcion);
        //$servicio->setEstado($estado);
        $servicio->setEstado($estado1);
        $servicio->setInicio($inicio);
        $servicio->setFin($fin);
        $servicio->setActual($actual);
        $servicio->setGruposervicioId($gruposervicio_id);
        $servicio->setUbicacionId($ubicacion);
        $servicio->setEstiloLetra($estilo_letra);
        $servicio->setTiempoMaximo($tiempoMaximo);
        //__________________
        if ($letraAlias == '')
            $servicio->setLetraAlias(' ');
        else
            $servicio->setLetraAlias($letraAlias);
        //__________________
        $servicio->setAtencionPreferencial($atencion_preferencial);
        $servicio->setCreacionAt($creacionAt);

        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if ($servicio->save() == false) {
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
            Flash::success('Registro guardado con éxito.');
        }
        $this->routeTo("action: $action", "id: $id");
        // $this->routeTo('action: index');
    }

    /**
     * Eliminar el Servicio
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                //Inicio Eliminar primero los registros de serviciocaja
                $db = Db::rawConnect();
                $db->query("DELETE FROM serviciocaja WHERE servicio_id = $ids[$i]");
                //Fin Eliminar primero los registros de serviciocaja
                ////Inicio Eliminar primero los registros de dispensadorservicio
                $db = Db::rawConnect();
                $db->query("DELETE FROM dispensadorservicio WHERE servicio_id = $ids[$i]");
                //Fin Eliminar primero los registros de dispensadorservicio
                //Inicio Eliminar primero los registros de turnos
                $db = Db::rawConnect();
                $db->query("delete from turnos where servicio_id = $ids[$i]");
                //Fin Eliminar primero los registros de turnos

                if (!$this->Servicio->delete($ids[$i])) {
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
                    case 'nombre':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'letra':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'descripcion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'estado':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'inicio':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'fin':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'actual':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Servicio->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Servicio->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Servicio = $resultado[$i];
            $estado = '<font color="#01CF00"><b>Activo</b></font>';
            if ($Servicio->getEstado() == 0)
                $estado = '<font color="red"><b>Inactivo</b></font>';
            //INICIO PONER UBICACION DEL SERVICIO EN GRID
            $id_servicio = $Servicio->getId();
            $condicion = "{#Servicio}.id = $id_servicio";
            $query = new ActiveRecordJoin(array(
                        "entities" => array("Ubicacion", "Servicio"),
                        "fields" => array(
                            "{#Ubicacion}.id",
                            "{#Ubicacion}.nombre_ubicacion"),
                        "conditions" => $condicion
                    ));
            $nombre_ubicacion = "";
            foreach ($query->getResultSet() as $result) {
                $nombre_ubicacion = $result->getNombreUbicacion();
            }
            //FIN PONER UBICACION DEL SERVICIO EN GRID
            //INICIO PONER GRUPO DE SERVICIO EN GRID
            $condicion = "{#Servicio}.id = $id_servicio";
            $query = new ActiveRecordJoin(array(
                        "entities" => array("Gruposervicio", "Servicio"),
                        "fields" => array(
                            "{#Gruposervicio}.nombre_grupo_servicio"),
                        "conditions" => $condicion
                    ));
            $nombre_gruposervicio = "";
            $nombre_gruposervicio = $query->getResultSet()->getFirst()->getNombreGrupoServicio();
            //FIN PONER GRUPO DE SERVICIO EN GRID
            //-- PReferencial
            $preferencial = '<font color="#01CF00"><b>SI</b></font>';
            if ($Servicio->getAtencionPreferencial() == 0)
                $preferencial = '<font color="red"><b>NO</b></font>';

            $jqgrid->rows[] = array('id' => $Servicio->getId(), 'cell' => array($Servicio->getNombre(), $Servicio->getLetra(), $Servicio->getDescripcion(), $nombre_ubicacion, $nombre_gruposervicio, $estado, $Servicio->getTiempoMaximo(), $preferencial, $Servicio->getLetraAlias()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    public function inicializarAction() {
        echo Tag::displayTo('actual', "0");
    }

}

