<?php

/**
 * Controlador Permiso
 *
 * @access public
 * @version 1.0
 */
class PermisoController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * menu_id
     *
     * @var int
     */
    public $menuId;

    /**
     * grupo_id
     *
     * @var int
     */
    public $grupoId;

    /**
     * hora_inicio
     *
     */
    public $horaInicio;

    /**
     * hora_fin
     *
     */
    public $horaFin;

    /**
     * permiso
     *
     * @var int
     */
    public $permiso;

    /**
     * creacion_at
     *
     * @var string
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
        $columnsGrid[]=array('name'=>'Menu','index'=>'menu_id');
        $columnsGrid[]=array('name'=>'Grupo','index'=>'grupo_id');
        //$columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio');
        //$columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin');
        $columnsGrid[]=array('name'=>'Permiso','index'=>'permiso');
        //$columnsGrid[]=array('name'=>'Creacion At','index'=>'creacion_at');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Permiso/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Permiso
     *
     */
    public function editarAction($id=null,$menu_id=null,$grupo_id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $menuId = $filter->applyFilter($menu_id,"int");
        $grupoId = $filter->applyFilter($grupo_id,"int");
        $permiso = $this->Permiso->findFirst($id,$menuId,$grupoId);
        if($permiso) {
            Tag::displayTo('id', $permiso->getId());
            Tag::displayTo('menu_id', $permiso->getMenuId());
            Tag::displayTo('grupo_id', $permiso->getGrupoId());
            Tag::displayTo('hora_inicio', $permiso->getHoraInicio());
            Tag::displayTo('hora_fin', $permiso->getHoraFin());
            Tag::displayTo('permiso', $permiso->getPermiso());
            Tag::displayTo('creacion_at', $permiso->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Permiso
     *
     */
    //public function guardarAction($isEdit=false,$id=null,$menu_id=null,$grupo_id=null) {
    public function guardarAction($isEdit=false,$id=null,$menu_id=null,$grupo_id=null) {
        $menuId     = $this->getPostParam("menu_id");
        $grupoId    = $this->getPostParam("grupo_id");
        $horaInicio = $this->getPostParam("hora_inicio"); 
        $horaFin = $this->getPostParam("hora_fin");
        //$permiso = $this->getPostParam("permiso", "int");
        $permiso = $this->getPostParam("permiso");
        if ($permiso=='on') $permiso_v=1; else $permiso_v=0;
        $creacionAt = $this->getPostParam("creacion_at", "striptags", "extraspaces");
        $permiso = new Permiso();
        $permiso->setId($id);
        $permiso->setMenuId($menuId);
        $permiso->setGrupoId($grupoId);
        $permiso->setHoraInicio($horaInicio);
        $permiso->setHoraFin($horaFin);
        $permiso->setPermiso($permiso_v);
        $permiso->setCreacionAt($creacionAt);

        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($permiso->save()==false) {
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
     * Eliminar el Permiso
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Permiso->delete($ids[$i])) {
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
                    case 'menu_id':
                        $condMenu=Utils::toSqlParamSearchGrid('nombre',$abrevoper,$strbusqueda);
                        $Menu=$this->Menu->find($condMenu);
                        if(count($Menu)>0) {
                            $arrayIdsMenu=array();
                            foreach($Menu as $fila) {
                                $arrayIdsMenu[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsMenu);
                            $abrevoper='in';
                        }
                        break;
                    case 'grupo_id':
                        $condGrupo=Utils::toSqlParamSearchGrid('nombre_largo',$abrevoper,$strbusqueda);
                        $Grupo=$this->Grupo->find($condGrupo);
                        if(count($Grupo)>0) {
                            $arrayIdsGrupo=array();
                            foreach($Grupo as $fila) {
                                $arrayIdsGrupo[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsGrupo);
                            $abrevoper='in';
                        }
                        break;
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
                            case 'menu_id':
                                $condMenu=Utils::toSqlParamSearchGrid('nombre',$val['op'],$val['data']);
                                $Menu=$this->Menu->find($condMenu);
                                if(count($Menu)>0) {
                                    $arrayIdsMenu=array();
                                    foreach($Menu as $fila) {
                                        $arrayIdsMenu[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsMenu);
                                    $val['op']='in';
                                }
                                break;
                            case 'grupo_id':
                                $condGrupo=Utils::toSqlParamSearchGrid('nombre_largo',$val['op'],$val['data']);
                                $Grupo=$this->Grupo->find($condGrupo);
                                if(count($Grupo)>0) {
                                    $arrayIdsGrupo=array();
                                    foreach($Grupo as $fila) {
                                        $arrayIdsGrupo[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsGrupo);
                                    $val['op']='in';
                                }
                                break;
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
                    case 'menu_id':
                        $condMenu=Utils::toSqlParamSearchGrid('nombre','bw',$v);
                        $Menu=$this->Menu->find($condMenu);
                        if(count($Menu)>0) {
                            $arrayIdsMenu=array();
                            foreach($Menu as $fila) {
                                $arrayIdsMenu[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsMenu);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'grupo_id':
                        $condGrupo=Utils::toSqlParamSearchGrid('nombre_largo','bw',$v);
                        $Grupo=$this->Grupo->find($condGrupo);
                        if(count($Grupo)>0) {
                            $arrayIdsGrupo=array();
                            foreach($Grupo as $fila) {
                                $arrayIdsGrupo[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsGrupo);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'hora_inicio':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_fin':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'permiso':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'creacion_at':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Permiso->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Permiso->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Permiso=$resultado[$i];
            $Menu=$Permiso->getMenu();
            $Grupo=$Permiso->getGrupo();
            //$jqgrid->rows[]=array('id'=>$Permiso->getId(),'cell'=>array($Menu->getNombre(),$Grupo->getNombreLargo(),$Permiso->getHoraInicio(),$Permiso->getHoraFin(),$Permiso->getPermiso(),$Permiso->getCreacionAt()));
            $jqgrid->rows[]=array('id'=>$Permiso->getId(),'cell'=>array($Menu->getNombre(),$Grupo->getNombreLargo(),$Permiso->getPermiso()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

