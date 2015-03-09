<?php

/**
 * Controlador Preguntas
 *
 * @access public
 * @version 1.0
 */
class PreguntasController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * nom_pregunta
     *
     * @var string
     */
    public $nomPregunta;

    /**
     * publicar
     *
     * @var int
     */
    public $publicar;

    /**
     * orden
     *
     * @var int
     */
    public $orden;

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
        $columnsGrid[]=array('name'=>'Pregunta','index'=>'nom_pregunta');
        $columnsGrid[]=array('name'=>'Publicar','index'=>'publicar','width'=>'70px');
        $columnsGrid[]=array('name'=>'Orden','index'=>'orden','width'=>'60px');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Preguntas/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Preguntas
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $preguntas = $this->Preguntas->findFirst($id);
        if($preguntas) {
            Tag::displayTo('id', $preguntas->getId());
            Tag::displayTo('nom_pregunta', $preguntas->getNomPregunta());
            Tag::displayTo('publicar', $preguntas->getPublicar());
            Tag::displayTo('orden', $preguntas->getOrden());
            Tag::displayTo('creacion_at', $preguntas->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Preguntas
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $nomPregunta = $this->getPostParam("nom_pregunta", "striptags", "extraspaces");
        //$publicar = $this->getPostParam("publicar", "int");
        $publicar = $this->getPostParam("publicar");
        if ($publicar== "on")
            $publicar=1;
        else
            $publicar=0;
        $orden = $this->getPostParam("orden", "int");
        $creacionAt = $this->getPostParam("creacion_at");
        $preguntas = new Preguntas();
        $preguntas->setId($id);
        $preguntas->setNomPregunta($nomPregunta);
        $preguntas->setPublicar($publicar);
        $preguntas->setOrden($orden);
        $preguntas->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($preguntas->save()==false) {
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
     * Eliminar el Preguntas
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Preguntas->delete($ids[$i])) {
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
                    case 'nom_pregunta':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'publicar':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'orden':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Preguntas->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Preguntas->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Preguntas=$resultado[$i];
            $publicar='<font color="#01CF00"><b>Si</b></font>';
            if($Preguntas->getPublicar()==0)
                $publicar='<font color="red"><b>No</b></font>';
            $jqgrid->rows[]=array('id'=>$Preguntas->getId(),'cell'=>array($Preguntas->getNomPregunta(),"<center>".$publicar."</center>","<center>".$Preguntas->getOrden()."</center>"));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

