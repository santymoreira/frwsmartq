<?php

/**
 * Controlador Caja_pausas
 *
 * @access public
 * @version 1.0
 */
class Caja_pausasController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * usuario_id
     *
     * @var int
     */
    public $usuarioId;

    /**
     * caja_id
     *
     * @var int
     */
    public $cajaId;

    /**
     * pausas_id
     *
     * @var int
     */
    public $pausasId;

    /**
     * estado
     *
     * @var string
     */
    public $estado;

    /**
     * fecha_inicio
     *
     */
    public $fechaInicio;

    /**
     * hora_inicio
     *
     */
    public $horaInicio;

    /**
     * fecha_fin
     *
     */
    public $fechaFin;

    /**
     * hora_fin
     *
     */
    public $horaFin;

    /**
     * duracion
     *
     */
    public $duracion;

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
        $columnsGrid[]=array('name'=>'Usuario','index'=>'usuario_id');
        $columnsGrid[]=array('name'=>'MÃ³dulo','index'=>'caja_id','width'=>'60px');
        $columnsGrid[]=array('name'=>'Mombre Pausa','index'=>'pausas_id','width'=>'110px');
        $columnsGrid[]=array('name'=>'Estado','index'=>'estado','width'=>'100px');
        $columnsGrid[]=array('name'=>'Fecha Inicio','index'=>'fecha_inicio','width'=>'80px');
        $columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio','width'=>'80px');
        $columnsGrid[]=array('name'=>'Fecha Fin','index'=>'fecha_fin','width'=>'80px');
        $columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin','width'=>'80px');
        $columnsGrid[]=array('name'=>'DuraciÃ³n','index'=>'duracion','width'=>'80px');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un CajaPausas/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el CajaPausas
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $cajaPausas = $this->CajaPausas->findFirst($id);
        if($cajaPausas) {
            Tag::displayTo('id', $cajaPausas->getId());
            Tag::displayTo('usuario_id', $cajaPausas->getUsuarioId());
            Tag::displayTo('caja_id', $cajaPausas->getCajaId());
            Tag::displayTo('pausas_id', $cajaPausas->getPausasId());
            Tag::displayTo('estado', $cajaPausas->getEstado());
            Tag::displayTo('fecha_inicio', $cajaPausas->getFechaInicio());
            Tag::displayTo('hora_inicio', $cajaPausas->getHoraInicio());
            Tag::displayTo('fecha_fin', $cajaPausas->getFechaFin());
            Tag::displayTo('hora_fin', $cajaPausas->getHoraFin());
            Tag::displayTo('duracion', $cajaPausas->getDuracion());
            Tag::displayTo('creacion_at', $cajaPausas->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el CajaPausas
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $usuarioId = $this->getPostParam("usuario_id", "int");
        $cajaId = $this->getPostParam("caja_id", "int");
        $pausasId = $this->getPostParam("pausas_id", "int");
        $estado = $this->getPostParam("estado", "striptags", "extraspaces");
        $fechaInicio = $this->getPostParam("fecha_inicio");
        $horaInicio = $this->getPostParam("hora_inicio");
        $fechaFin = $this->getPostParam("fecha_fin");
        $horaFin = $this->getPostParam("hora_fin");
        $duracion = $this->getPostParam("duracion");
        $creacionAt = $this->getPostParam("creacion_at");
        $cajaPausas = new CajaPausas();
        $cajaPausas->setId($id);
        $cajaPausas->setUsuarioId($usuarioId);
        $cajaPausas->setCajaId($cajaId);
        $cajaPausas->setPausasId($pausasId);
        $cajaPausas->setEstado($estado);
        $cajaPausas->setFechaInicio($fechaInicio);
        $cajaPausas->setHoraInicio($horaInicio);
        $cajaPausas->setFechaFin($fechaFin);
        $cajaPausas->setHoraFin($horaFin);
        $cajaPausas->setDuracion($duracion);
        $cajaPausas->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($cajaPausas->save()==false) {
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
     * Eliminar el CajaPausas
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Caja_pausas->delete($ids[$i])) {
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
                    case 'usuario_id':
                        $condUsuario=Utils::toSqlParamSearchGrid('nombres',$abrevoper,$strbusqueda);
                        $Usuario=$this->Usuario->find($condUsuario);
                        if(count($Usuario)>0) {
                            $arrayIdsUsuario=array();
                            foreach($Usuario as $fila) {
                                $arrayIdsUsuario[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsUsuario);
                            $abrevoper='in';
                        }
                        break;
                    case 'caja_id':
                        $condCaja=Utils::toSqlParamSearchGrid('numero_caja',$abrevoper,$strbusqueda);
                        $Caja=$this->Caja->find($condCaja);
                        if(count($Caja)>0) {
                            $arrayIdsCaja=array();
                            foreach($Caja as $fila) {
                                $arrayIdsCaja[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsCaja);
                            $abrevoper='in';
                        }
                        break;
                    case 'pausas_id':
                        $condPausas=Utils::toSqlParamSearchGrid('nombre_pausa',$abrevoper,$strbusqueda);
                        $Pausas=$this->Pausas->find($condPausas);
                        if(count($Pausas)>0) {
                            $arrayIdsPausas=array();
                            foreach($Pausas as $fila) {
                                $arrayIdsPausas[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsPausas);
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
                            case 'usuario_id':
                                $condUsuario=Utils::toSqlParamSearchGrid('nombres',$val['op'],$val['data']);
                                $Usuario=$this->Usuario->find($condUsuario);
                                if(count($Usuario)>0) {
                                    $arrayIdsUsuario=array();
                                    foreach($Usuario as $fila) {
                                        $arrayIdsUsuario[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsUsuario);
                                    $val['op']='in';
                                }
                                break;
                            case 'caja_id':
                                $condCaja=Utils::toSqlParamSearchGrid('numero_caja',$val['op'],$val['data']);
                                $Caja=$this->Caja->find($condCaja);
                                if(count($Caja)>0) {
                                    $arrayIdsCaja=array();
                                    foreach($Caja as $fila) {
                                        $arrayIdsCaja[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsCaja);
                                    $val['op']='in';
                                }
                                break;
                            case 'pausas_id':
                                $condPausas=Utils::toSqlParamSearchGrid('nombre_pausa',$val['op'],$val['data']);
                                $Pausas=$this->Pausas->find($condPausas);
                                if(count($Pausas)>0) {
                                    $arrayIdsPausas=array();
                                    foreach($Pausas as $fila) {
                                        $arrayIdsPausas[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsPausas);
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
                    case 'usuario_id':
                        $condUsuario=Utils::toSqlParamSearchGrid('nombres','bw',$v);
                        $Usuario=$this->Usuario->find($condUsuario);
                        if(count($Usuario)>0) {
                            $arrayIdsUsuario=array();
                            foreach($Usuario as $fila) {
                                $arrayIdsUsuario[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsUsuario);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'caja_id':
                        $condCaja=Utils::toSqlParamSearchGrid('numero_caja','bw',$v);
                        $Caja=$this->Caja->find($condCaja);
                        if(count($Caja)>0) {
                            $arrayIdsCaja=array();
                            foreach($Caja as $fila) {
                                $arrayIdsCaja[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsCaja);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'pausas_id':
                        $condPausas=Utils::toSqlParamSearchGrid('nombre_pausa','bw',$v);
                        $Pausas=$this->Pausas->find($condPausas);
                        if(count($Pausas)>0) {
                            $arrayIdsPausas=array();
                            foreach($Pausas as $fila) {
                                $arrayIdsPausas[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsPausas);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'estado':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fecha_inicio':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_inicio':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fecha_fin':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_fin':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'duracion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->CajaPausas->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->CajaPausas->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $CajaPausas=$resultado[$i];
            $Usuario=$CajaPausas->getUsuario();
            $Caja=$CajaPausas->getCaja();
            $Pausas=$CajaPausas->getPausas();

            $estado='<font color="#01CF00"><b>Pausa finalizada</b></font>';
            if($CajaPausas->getEstado()=="1")
                $estado='<font color="red"><b>En pausa</b></font>';
            
            $jqgrid->rows[]=array('id'=>$CajaPausas->getId(),'cell'=>array($Usuario->getNombres(),$Caja->getNumeroCaja(),$Pausas->getNombrePausa(),$estado,$CajaPausas->getFechaInicio(),$CajaPausas->getHoraInicio(),$CajaPausas->getFechaFin(),$CajaPausas->getHoraFin(),$CajaPausas->getDuracion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

