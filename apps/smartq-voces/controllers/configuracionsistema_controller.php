<?php

/**
 * Controlador Configuracionsistema
 *
 * @access public
 * @version 1.0
 */
class ConfiguracionsistemaController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * activar_calificador
     *
     * @var int
     */
    public $activarCalificador;

    /**
     * activar_difusion
     *
     * @var int
     */
    public $activarDifusion;

    /**
     * publicar_noticias
     *
     * @var int
     */
    public $publicarNoticias;

    /**
     * velocidad_publicacion
     *
     * @var string
     */
    public $velocidadPublicacion;

    /**
     * pc_difusion
     *
     * @var string
     */
    public $pcDifusion;

    /**
     * puerto
     *
     * @var string
     */
    public $puerto;

    /**
     * ver_tiempo_maximo
     *
     * @var int
     */
    public $verTiempoMaximo;

    /**
     * ver_tiempo_atencion
     *
     * @var int
     */
    public $verTiempoAtencion;

    /**
     * ubicacion_impresora
     *
     * @var string
     */
    public $ubicacionImpresora;

    /**
     * nombre_impresora
     *
     * @var string
     */
    public $nombreImpresora;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

    /**
     * tono
     *
     * @var int
     */
    public $tono;

    /**
     * tiempo_tono
     *
     * @var int
     */
    public $tiempoTono;

    /**
     * tiempo_tono
     *
     * @var int
     */
    public $servidorNode;

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
        //$columnsGrid[]=array('name'=>'Activar Calificador','index'=>'activar_calificador');
        $columnsGrid[] = array('name' => 'Publicar Noticias', 'index' => 'publicar_noticias','width'=>100);
        $columnsGrid[] = array('name' => 'Velocidad Publicación', 'index' => 'velocidad_publicacion','width'=>130);
        //$columnsGrid[] = array('name' => 'Activar Difusión', 'index' => 'activar_difusion');
        //$columnsGrid[] = array('name' => 'PC Difusión', 'index' => 'pc_difusion');
        //$columnsGrid[] = array('name' => 'Puerto', 'index' => 'puerto');
        $columnsGrid[] = array('name' => 'Ver Tiempo Máximo', 'index' => 'ver_tiempo_maximo','width'=>120);
        $columnsGrid[] = array('name' => 'Ver Tiempo Atención', 'index' => 'ver_tiempo_atencion','width'=>120);
        //$columnsGrid[] = array('name' => 'Ubicacion Impresora', 'index' => 'ubicacion_impresora');
        $columnsGrid[] = array('name' => 'Nombre Impresora', 'index' => 'nombre_impresora','width'=>140);
        //$columnsGrid[] = array('name' => 'tiempo_tono', 'index' => 'tiempo_tono');
        //$columnsGrid[]=array('name'=>'tono','index'=>'tono');
        //$columnsGrid[]=array('name'=>'Fecha configuración','index'=>'creacion_at');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Configuracionsistema/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Configuracionsistema
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $configuracionsistema = $this->Configuracionsistema->findFirst($id);
        if ($configuracionsistema) {
            Tag::displayTo('id', $configuracionsistema->getId());
            Tag::displayTo('activar_calificador', $configuracionsistema->getActivarCalificador());
            Tag::displayTo('activar_difusion', $configuracionsistema->getActivarDifusion());
            Tag::displayTo('publicar_noticias', $configuracionsistema->getPublicarNoticias());
            Tag::displayTo('velocidad_publicacion', $configuracionsistema->getVelocidadPublicacion());
            Tag::displayTo('pc_difusion', $configuracionsistema->getPcDifusion());
            Tag::displayTo('puerto', $configuracionsistema->getPuerto());
            Tag::displayTo('ver_tiempo_maximo', $configuracionsistema->getVerTiempoMaximo());
            Tag::displayTo('ver_tiempo_atencion', $configuracionsistema->getVerTiempoAtencion());
            Tag::displayTo('ubicacion_impresora', $configuracionsistema->getUbicacionImpresora());
            Tag::displayTo('nombre_impresora', $configuracionsistema->getNombreImpresora());
            Tag::displayTo('creacion_at', $configuracionsistema->getCreacionAt());
            //Tag::displayTo('tiempo_tono', $configuracionsistema->getTiempoTono());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Configuracionsistema
     *
     */
    public function guardarAction($isEdit = false, $id = null) {

        //$activarCalificador = $this->getPostParam("activar_calificador", "int");
        $activarCalificador = $this->getPostParam("activar_calificador");
        if ($activarCalificador == "on")
            $activarCalificador = 1;
        else
            $activarCalificador = 1;
        $activarDifusion = $this->getPostParam("activar_difusion");
        if ($activarDifusion == "on")
            $activarDifusion = 1;
        else
            $activarDifusion = 0;
        $publicarNoticias = $this->getPostParam("publicar_noticias");
        if ($publicarNoticias == "on")
            $publicarNoticias = 1;
        else
            $publicarNoticias = 0;
        $verTiempoMaximo = $this->getPostParam("ver_tiempo_maximo");
        if ($verTiempoMaximo == "on")
            $verTiempoMaximo = 1;
        else
            $verTiempoMaximo = 0;
        $verTiempoAtencion = $this->getPostParam("ver_tiempo_atencion");
        if ($verTiempoAtencion == "on")
            $verTiempoAtencion = 1;
        else
            $verTiempoAtencion = 0;
        $velocidadPublicacion = $this->getPostParam("velocidad_publicacion", "striptags", "extraspaces");
        $pcDifusion = $this->getPostParam("pc_difusion", "striptags", "extraspaces");
        $puerto = $this->getPostParam("puerto", "striptags", "extraspaces");
        $ubicacionImpresora = $this->getPostParam("ubicacion_impresora", "striptags", "extraspaces");
        $nombreImpresora = $this->getPostParam("nombre_impresora", "striptags", "extraspaces"); 
        $creacionAt = $this->getPostParam("creacion_at");

        //$tono = $this->getPostParam("tono_prueba");
        //$tiempoTono = $this->getPostParam("tiempo_tono");
        $ventana = $this->getPostParam("ventana");
        $logo = $this->getPostParam("logo");
        $logo_sinticket = $this->getPostParam("logo_sinticket");
        $logo_conticket = $this->getPostParam("logo_conticket");
        $logo_calificador = $this->getPostParam("logo_calificador");
        $logo_ticket = $this->getPostParam("logo_ticket");
        $pantalla_ticket = $this->getPostParam("pantalla_ticket");
        $datos = SessionNamespace::get('datosUsuarioSMC');
//        $id_usuario = $datos->getId();
//        if ($id_usuario != 8) {
//            $logo = '';
//            $logo_sinticket = '';
//            $logo_conticket = '';
//            $db = DbBase::rawConnect();
//            $result2 = $db->query("SELECT 	logo, logo_sinticket, logo_conticket, logo_calificador, logo_ticket	FROM configuracionsistema ;");
//            while ($row = $db->fetchArray($result2)) {
//                $logo = $row['logo'];
//                $logo_sinticket = $row['logo_sinticket'];
//                $logo_conticket = $row['logo_conticket'];
//                $logo_calificador = $row['logo_calificador'];
//                $logo_ticket = $row['logo_ticket'];
//            }
//        }




        $configuracionsistema = new Configuracionsistema();
        $configuracionsistema->setId($id);
        $configuracionsistema->setActivarCalificador($activarCalificador);
        $configuracionsistema->setActivarDifusion($activarDifusion);
        $configuracionsistema->setPublicarNoticias($publicarNoticias);
        $configuracionsistema->setVelocidadPublicacion($velocidadPublicacion);
        $configuracionsistema->setPcDifusion($pcDifusion);
        $configuracionsistema->setPuerto($puerto);
        $configuracionsistema->setVerTiempoMaximo($verTiempoMaximo);
        $configuracionsistema->setVerTiempoAtencion($verTiempoAtencion);
        $configuracionsistema->setUbicacionImpresora($ubicacionImpresora);
        $configuracionsistema->setNombreImpresora($nombreImpresora);
        $configuracionsistema->setCreacionAt($creacionAt);
        //$configuracionsistema->setTono($tono);
        //$configuracionsistema->setTiempoTono($tiempoTono);
        $configuracionsistema->setVentana($ventana);
        $configuracionsistema->setLogo($logo);
        $configuracionsistema->setLogoSinticket($logo_sinticket);
        $configuracionsistema->setLogoConticket($logo_conticket);
        $configuracionsistema->setLogoCalificador($logo_calificador);
        $configuracionsistema->setLogoTicket($logo_ticket);
        $configuracionsistema->setPantallaTicket($pantalla_ticket);
        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if ($configuracionsistema->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
            foreach ($configuracionsistema->getMessages() as $message)
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
     * Eliminar el Configuracionsistema
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Configuracionsistema->delete($ids[$i])) {
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
                    case 'activar_calificador':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'activar_difusion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'publicar_noticias':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'velocidad_publicacion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'pc_difusion':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'puerto':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'creacion_at':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Configuracionsistema->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Configuracionsistema->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Configuracionsistema = $resultado[$i];
            //$jqgrid->rows[]=array('id'=>$Configuracionsistema->getId(),'cell'=>array($Configuracionsistema->getActivarCalificador(),$Configuracionsistema->getActivarDifusion(),$Configuracionsistema->getPublicarNoticias(),$Configuracionsistema->getVelocidadPublicacion(),$Configuracionsistema->getPcDifusion(),$Configuracionsistema->getPuerto(),$Configuracionsistema->getCreacionAt()));
            $act_calificador = '<font color="#01CF00"><b>Si</b></font>';
            if ($Configuracionsistema->getActivarCalificador() == 0)
                $act_calificador = '<font color="red"><b>No</b></font>';
            $publicar_noticias = '<font color="#01CF00"><b>Si</b></font>';
            if ($Configuracionsistema->getPublicarNoticias() == 0)
                $publicar_noticias = '<font color="red"><b>No</b></font>';
            $act_difusion = '<font color="#01CF00"><b>Si</b></font>';
            if ($Configuracionsistema->getActivarDifusion() == 0)
                $act_difusion = '<font color="red"><b>No</b></font>';
            $ver_tiempo_maximo = '<font color="#01CF00"><b>Si</b></font>';
            if ($Configuracionsistema->getVerTiempoMaximo() == 0)
                $ver_tiempo_maximo = '<font color="red"><b>No</b></font>';
            $ver_tiempo_atencion = '<font color="#01CF00"><b>Si</b></font>';
            if ($Configuracionsistema->getVerTiempoAtencion() == 0)
                $ver_tiempo_atencion = '<font color="red"><b>No</b></font>';
            
            //$jqgrid->rows[] = array('id' => $Configuracionsistema->getId(), 'cell' => array($publicar_noticias, $Configuracionsistema->getVelocidadPublicacion(), $act_difusion, $Configuracionsistema->getPcDifusion(), $Configuracionsistema->getPuerto(), $ver_tiempo_maximo, $ver_tiempo_atencion, $Configuracionsistema->getUbicacionImpresora(), $Configuracionsistema->getNombreImpresora(), $Configuracionsistema->getServidorNode()));
            $jqgrid->rows[] = array('id' => $Configuracionsistema->getId(), 'cell' => array($publicar_noticias, $Configuracionsistema->getVelocidadPublicacion(), $ver_tiempo_maximo, $ver_tiempo_atencion, $Configuracionsistema->getNombreImpresora()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
