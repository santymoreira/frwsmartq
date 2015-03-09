<?php

/**
 * Controlador Video
 *
 * @access public
 * @version 1.0
 */
class VideoController extends ApplicationController {

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
     * ubicacion
     *
     * @var string
     */
    public $ubicacion;

    /**
     * duracion
     *
     * @var int
     */
    public $duracion;

    /**
     * activo
     *
     * @var int
     */
    public $activo;

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
        $columnsGrid[]=array('name'=>'Nombre','index'=>'nombre','width'=>'300');
        $columnsGrid[]=array('name'=>'Ubicación','index'=>'ubicacion','width'=>'200');
        $columnsGrid[]=array('name'=>'Duración en segundos','index'=>'duracion');
        $columnsGrid[]=array('name'=>'Estado','index'=>'activo');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Video/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Video
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $video = $this->Video->findFirst($id);
        if($video) {
            Tag::displayTo('id', $video->getId());
            Tag::displayTo('nombre', $video->getNombre());
            Tag::displayTo('ubicacion', $video->getUbicacion());
            Tag::displayTo('duracion', $video->getDuracion());
            Tag::displayTo('activo', $video->getActivo());
            Tag::displayTo('creacion_at', $video->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Video
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
        $ubicacion = $this->getPostParam("ubicacion", "striptags", "extraspaces");
        $duracion = $this->getPostParam("duracion", "int");
        $activo = $this->getPostParam("activo");
        if ($activo== "on")
            $estado1=1;
        else
            $estado1=0;
        $creacionAt = $this->getPostParam("creacion_at");
        $video = new Video();
        $video->setId($id);
        $video->setNombre($nombre);
        $video->setUbicacion($ubicacion);
        $video->setDuracion($duracion);
        $video->setActivo($estado1);
        $video->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($video->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            $this->setParamToView('save','true');
            if($this->getQueryParam("exit")!="")
                $this->setParamToView('exit','true');
            else {
                if(!$isEdit) {
                    $action='editar';
                }
            }
            Flash::success('Registro guardado con éxito.');
        }
        $this->routeTo("action: $action","id: $id");
    //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Video
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
            //Inicio Eliminar primero los videos asignados en las pantallas
                $db = Db::rawConnect();
                $db->query("DELETE FROM pantallavideos WHERE video_id = $ids[$i]");
                //Fin Eliminar primero los videos asignados en las pantallas
                if(!$this->Video->delete($ids[$i])) {
                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
                }else {
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
        if(!$col_orden) $col_orden =1;
        //construccion de condicion de consulta
        $condicion='1';
        $buscar=$this->getPostParam('_search','stripslashes'); //obtenemos la variable que activa la busqueda
        $strbusqueda=$this->getPostParam('searchString');  // obtenemos la cadena de busqueda
        $campoBusqueda=$this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda
        $abrevoper=$this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda
        $filtroBusqueda=$this->getPostParam('filters','stripslashes');
        if($buscar=='true') { //verificamos si la busqueda es activada
            if($strbusqueda!='') {    // construccion de la cadena de condicion para la busqueda normal
                switch($campoBusqueda) {
                }
                $condicion.=' AND '.Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
            }elseif($filtroBusqueda) {
                $jsona = json_decode($filtroBusqueda,true);
                if(is_array($jsona)) {
                    $gopr = $jsona['groupOp'];
                    $rules = $jsona['rules'];
                    $i =0;
                    foreach($rules as $key=>$val) {
                        $field = $val['field'];
                        switch($field) {
                        }
                        $op = $val['op'];
                        $v = $val['data'];
                        if($v && $op) {
                            $i++;
                            $v=Utils::toSqlParamSearchGrid($field, $op, $v);
                            if ($i == 1)
                                $condicion.=' AND ';
                            else
                                $condicion.= " " .$gopr." ";
                            $condicion.= $v;
                        }
                    }
                }
            }
            //construimos la condicion por barra de busqueda del grid
            $sarr = $_POST;
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'nombre':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'ubicacion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'duracion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'activo':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Video->count("conditions: $condicion");  //contar el numero total de registros existentes
        //obtenemos el numero de paginas para el grid
        if( $contar >0 ) {
            $total_pags = ceil($contar/$limite);
        } else {
            $total_pags = 0;
        }
        if ($pagina > $total_pags) $pagina=$total_pags;
        $inicio = $limite*$pagina - $limite; // no poner $limite*($pagina - 1)
        if ($inicio<0) $inicio = 0;
        $limite=$inicio+$limite;  // igualamos el limite al total de registros que se obtendra hasta la pagina actual
        $resultado=$this->Video->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Video=$resultado[$i];
            $activo='<font color="#01CF00"><b>Activo</b></font>';
            if($Video->getNombre()!='z-inicio.mpg') {
                if($Video->getActivo()==0)
                    $activo='<font color="red"><b>Inactivo</b></font>';
                //$jqgrid->rows[]=array('id'=>$Video->getId(),'cell'=>array($Video->getNombre(),$Video->getUbicacion(),$Video->getDuracion(),$Video->getActivo()));
                $jqgrid->rows[]=array('id'=>$Video->getId(),'cell'=>array($Video->getNombre(),$Video->getUbicacion(),$Video->getDuracion(),/*$Video->getActivo()*/ $activo));
            }
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

