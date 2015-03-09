<?php

/**
 * Controlador Inicioturnos
 *
 * @access public
 * @version 1.0
 */
class InicioturnosController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * inicio_turno
     *
     * @var int
     */
    public $inicioTurno;

    /**
     * fin_turno
     *
     * @var int
     */
    public $finTurno;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        $inicioturnos = $this->Inicioturnos->findFirst();
        if($inicioturnos) {
            Tag::displayTo('id', $inicioturnos->getId());
            Tag::displayTo('inicio_turno', $inicioturnos->getInicioTurno());
            Tag::displayTo('fin_turno', $inicioturnos->getFinTurno());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $columnsGrid[]=array('name'=>'Inicio Turno','index'=>'inicio_turno');
        $columnsGrid[]=array('name'=>'Fin Turno','index'=>'fin_turno');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Inicioturnos/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Inicioturnos
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $inicioturnos = $this->Inicioturnos->findFirst($id);
        if($inicioturnos) {
            Tag::displayTo('id', $inicioturnos->getId());
            Tag::displayTo('inicio_turno', $inicioturnos->getInicioTurno());
            Tag::displayTo('fin_turno', $inicioturnos->getFinTurno());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Inicioturnos
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $id=1;
        $inicioTurno = $this->getPostParam("inicio_turno", "int");
        $finTurno = $this->getPostParam("fin_turno", "int");
        $inicioturnos = new Inicioturnos();
        $inicioturnos->setId($id);
        $inicioturnos->setInicioTurno($inicioTurno);
        $inicioturnos->setFinTurno($finTurno);
        if($inicioturnos->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            Flash::success('Registro guardado con éxito.');
        }

        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Inicioturnos
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Inicioturnos->delete($ids[$i])) {
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
                    case 'inicio_turno':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fin_turno':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Inicioturnos->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Inicioturnos->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Inicioturnos=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Inicioturnos->getId(),'cell'=>array($Inicioturnos->getInicioTurno(),$Inicioturnos->getFinTurno()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

