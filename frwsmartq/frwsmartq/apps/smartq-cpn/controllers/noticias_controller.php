<?php

/**
 * Controlador Noticias
 *
 * @access public
 * @version 1.0
 */
class NoticiasController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * titulo
     *
     * @var string
     */
    public $titulo;

    /**
     * noticia
     *
     * @var string
     */
    public $noticia;

    /**
     * publicar
     *
     * @var int
     */
    public $publicar;

    /**
     * fecha_inicio_publicacion
     *
     */
    public $fechaInicioPublicacion;

    /**
     * fecha_fin_publicacion
     *
     */
    public $fechaFinPublicacion;

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
     * AcciÃ³n por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[]=array('name'=>'Titulo','index'=>'titulo','width'=>'200px');
        $columnsGrid[]=array('name'=>'Noticia','index'=>'noticia','width'=>'400px');
        $columnsGrid[]=array('name'=>'Publicar','index'=>'publicar','width'=>'60px');
        $columnsGrid[]=array('name'=>'Fecha Inicio Publicacion','index'=>'fecha_inicio_publicacion');
        $columnsGrid[]=array('name'=>'Fecha Fin Publicacion','index'=>'fecha_fin_publicacion');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Noticias/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Noticias
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $noticias = $this->Noticias->findFirst($id);
        if($noticias) {
            Tag::displayTo('id', $noticias->getId());
            Tag::displayTo('titulo', $noticias->getTitulo());
            Tag::displayTo('noticia', $noticias->getNoticia());
            Tag::displayTo('publicar', $noticias->getPublicar());
            Tag::displayTo('fecha_inicio_publicacion', $noticias->getFechaInicioPublicacion());
            Tag::displayTo('fecha_fin_publicacion', $noticias->getFechaFinPublicacion());
            Tag::displayTo('creacion_at', $noticias->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Noticias
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $titulo = $this->getPostParam("titulo", "striptags", "extraspaces");
        $noticia = $this->getPostParam("noticia", "striptags", "extraspaces");
        //$publicar = $this->getPostParam("publicar", "int");
        $publicar = $this->getPostParam("publicar");
        if ($publicar== "on")
            $publicar=1;
        else
            $publicar=0;
        $fechaInicioPublicacion = $this->getPostParam("fecha_inicio_publicacion");
        $fechaFinPublicacion = $this->getPostParam("fecha_fin_publicacion");
        $creacionAt = $this->getPostParam("creacion_at");
        $noticias = new Noticias();
        $noticias->setId($id);
        $noticias->setTitulo($titulo);
        $noticias->setNoticia($noticia);
        $noticias->setPublicar($publicar);
        $noticias->setFechaInicioPublicacion($fechaInicioPublicacion);
        $noticias->setFechaFinPublicacion($fechaFinPublicacion);
        $noticias->setCreacionAt($creacionAt);

        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($noticias->save()==false) {
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
            Flash::success('Registro guardado con Ã©xito.');
        }
        $this->routeTo("action: $action","id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Noticias
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Noticias->delete($ids[$i])) {
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
        $limite = $this->getPostParam('rows'); // obtener el número de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de índice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la dirección de ordenado
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
                    case 'titulo':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'noticia':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'publicar':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'fecha_inicio_publicacion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'fecha_fin_publicacion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Noticias->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Noticias->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Noticias=$resultado[$i];
            $publicar='<font color="#01CF00"><b>Si</b></font>';
            if($Noticias->getPublicar()==0)
                $publicar='<font color="red"><b>No</b></font>';
            $jqgrid->rows[]=array('id'=>$Noticias->getId(),'cell'=>array($Noticias->getTitulo(),$Noticias->getNoticia(),$publicar,$Noticias->getFechaInicioPublicacion(),$Noticias->getFechaFinPublicacion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

