<?php

/**
 * Controlador Sesiones
 *
 * @access public
 * @version 1.0
 */
class SesionesController extends ApplicationController {

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
     * ubicacion_id
     *
     * @var int
     */
    public $ubicacionId;

    /**
     * estado
     *
     * @var string
     */
    public $estado;

    /**
     * ip
     *
     * @var string
     */
    public $ip;

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
     * Acci贸n por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[]=array('name'=>'Usuario','index'=>'usuario_id');
        $columnsGrid[]=array('name'=>'M贸dulo','index'=>'caja_id','width'=>'60px');
        $columnsGrid[]=array('name'=>'Ubicaci贸n','index'=>'ubicacion_id','width'=>'75px');
        $columnsGrid[]=array('name'=>'Estado','index'=>'estado','width'=>'60px');
        $columnsGrid[]=array('name'=>'Ip','index'=>'ip','width'=>'80px');
        $columnsGrid[]=array('name'=>'Fecha Inicio','index'=>'fecha_inicio','width'=>'80px');
        $columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio','width'=>'80px');
        $columnsGrid[]=array('name'=>'Fecha Fin','index'=>'fecha_fin','width'=>'80px');
        $columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin','width'=>'80px');
        $columnsGrid[]=array('name'=>'Duraci贸n','index'=>'duracion','width'=>'80px');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Sesiones/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Sesiones
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $sesiones = $this->Sesiones->findFirst($id);
        if($sesiones) {
            Tag::displayTo('id', $sesiones->getId());
            Tag::displayTo('usuario_id', $sesiones->getUsuarioId());
            Tag::displayTo('caja_id', $sesiones->getCajaId());
            Tag::displayTo('ubicacion_id', $sesiones->getUbicacionId());
            Tag::displayTo('estado', $sesiones->getEstado());
            Tag::displayTo('ip', $sesiones->getIp());
            Tag::displayTo('fecha_inicio', $sesiones->getFechaInicio());
            Tag::displayTo('hora_inicio', $sesiones->getHoraInicio());
            Tag::displayTo('fecha_fin', $sesiones->getFechaFin());
            Tag::displayTo('hora_fin', $sesiones->getHoraFin());
            Tag::displayTo('duracion', $sesiones->getDuracion());
            Tag::displayTo('creacion_at', $sesiones->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Sesiones
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $usuarioId = $this->getPostParam("usuario_id", "int");
        $cajaId = $this->getPostParam("caja_id", "int");
        $ubicacionId = $this->getPostParam("ubicacion_id", "int");
        $estado = $this->getPostParam("estado", "striptags", "extraspaces");
        $ip = $this->getPostParam("ip", "striptags", "extraspaces");
        $fechaInicio = $this->getPostParam("fecha_inicio");
        $horaInicio = $this->getPostParam("hora_inicio");
        $fechaFin = $this->getPostParam("fecha_fin");
        $horaFin = $this->getPostParam("hora_fin");
        $duracion = $this->getPostParam("duracion");
        $creacionAt = $this->getPostParam("creacion_at");
        $sesiones = new Sesiones();
        $sesiones->setId($id);
        $sesiones->setUsuarioId($usuarioId);
        $sesiones->setCajaId($cajaId);
        $sesiones->setUbicacionId($ubicacionId);
        $sesiones->setEstado($estado);
        $sesiones->setIp($ip);
        $sesiones->setFechaInicio($fechaInicio);
        $sesiones->setHoraInicio($horaInicio);
        $sesiones->setFechaFin($fechaFin);
        $sesiones->setHoraFin($horaFin);
        $sesiones->setDuracion($duracion);
        $sesiones->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($sesiones->save()==false) {
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
            Flash::success('Registro guardado con 茅xito.');
        }
        $this->routeTo("action: $action","id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Sesiones
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Sesiones->delete($ids[$i])) {
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
        $limite = $this->getPostParam('rows'); // obtener el n鷐ero de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de 韓dice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la direcci髇 de ordenado
        if(!$col_orden) $col_orden =1;
        //construccion de condicion de consulta
        date_default_timezone_set('America/Guayaquil');
        $hoy = date("Y-m-d");
        
        $condicion='fecha_inicio = \''.$hoy.'\' ';
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
                    case 'ubicacion_id':
                        $condUbicacion=Utils::toSqlParamSearchGrid('nombre_ubicacion',$abrevoper,$strbusqueda);
                        $Ubicacion=$this->Ubicacion->find($condUbicacion);
                        if(count($Ubicacion)>0) {
                            $arrayIdsUbicacion=array();
                            foreach($Ubicacion as $fila) {
                                $arrayIdsUbicacion[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsUbicacion);
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
                            case 'ubicacion_id':
                                $condUbicacion=Utils::toSqlParamSearchGrid('nombre_ubicacion',$val['op'],$val['data']);
                                $Ubicacion=$this->Ubicacion->find($condUbicacion);
                                if(count($Ubicacion)>0) {
                                    $arrayIdsUbicacion=array();
                                    foreach($Ubicacion as $fila) {
                                        $arrayIdsUbicacion[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsUbicacion);
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
                    case 'ubicacion_id':
                        $condUbicacion=Utils::toSqlParamSearchGrid('nombre_ubicacion','bw',$v);
                        $Ubicacion=$this->Ubicacion->find($condUbicacion);
                        if(count($Ubicacion)>0) {
                            $arrayIdsUbicacion=array();
                            foreach($Ubicacion as $fila) {
                                $arrayIdsUbicacion[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsUbicacion);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'estado':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'ip':
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
        $contar =$this->Sesiones->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Sesiones->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Sesiones=$resultado[$i];
            $Usuario=$Sesiones->getUsuario();
            $Caja=$Sesiones->getCaja();
            $Ubicacion=$Sesiones->getUbicacion();

            $estado='<font color="#01CF00"><b>Activo</b></font>';
            if($Sesiones->getEstado()=="Inactivo")
                $estado='<font color="red"><b>Inactivo</b></font>';

            $jqgrid->rows[]=array('id'=>$Sesiones->getId(),'cell'=>array($Usuario->getNombres(),$Caja->getNumeroCaja(),$Ubicacion->getNombreUbicacion(),$estado,$Sesiones->getIp(),$Sesiones->getFechaInicio(),$Sesiones->getHoraInicio(),$Sesiones->getFechaFin(),$Sesiones->getHoraFin(),$Sesiones->getDuracion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

