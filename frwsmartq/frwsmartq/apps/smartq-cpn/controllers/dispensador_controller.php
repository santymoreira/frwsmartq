<?php

/**
 * Controlador Dispensador
 *
 * @access public
 * @version 1.0
 */
class DispensadorController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * descripcion
     *
     * @var string
     */
    public $descripcion;

    public $tipo_dispensador;

    public $dispensadorSimple;
    public $dispensadorTouch;
    public $dispensadorBotonera;
    public $dispensadorTouchPequenia;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);

        $empresa= new Empresa();
        $buscaEmpresa= $empresa->findFirst();
        $this->dispensadorSimple=$buscaEmpresa->getDispensadorSimple();
        $this->dispensadorTouch=$buscaEmpresa->getDispensadorTouch();
        $this->dispensadorBotonera=$buscaEmpresa->getDispensadorBotonera();
        $this->dispensadorTouchPequenia=$buscaEmpresa->getDispensadorTouchPequenia();
    }

    /**
     * AcciÃ³n por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        //$columnsGrid[]=array('name'=>'Id','index'=>'id');
        $columnsGrid[]=array('name'=>'DescripciÃ³n','index'=>'descripcion');
        $columnsGrid[]=array('name'=>'Tipo Dispensador','index'=>'tipo_dispensador');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Dispensador/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Dispensador
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $dispensador = $this->Dispensador->findFirst($id);
        if($dispensador) {
            Tag::displayTo('id', $dispensador->getId());
            Tag::displayTo('descripcion', $dispensador->getDescripcion());
            $tipo_dispensador= $dispensador->getTipoDispensador();
            $value="";
            if ($tipo_dispensador=="simple")
                $value="simple";
            else if ($tipo_dispensador=="touch")
                    $value="touch";
                else if ($tipo_dispensador=="botonera")
                        $value="botonera";
                    else if ($tipo_dispensador=="touch_pequenia")
                            $value="touch_pequenia";
            Tag::displayTo("radio_tipo_dispensador", $value);
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Dispensador
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $descripcion = $this->getPostParam("descripcion", "striptags", "extraspaces");
        $check_servicio= $this->getPostParam("chkservicio");
        $usuario= $this->getPostParam("usuarios");
        $tipo_dipensador= $this->getPostParam("radio_tipo_dispensador");
        $dispensador = new Dispensador();
        $dispensador->setId($id);
        $dispensador->setDescripcion($descripcion);
        $dispensador->setTipoDispensador($tipo_dipensador);

        $action=($isEdit==true) ? "editar" : 'nuevo';

        //Tengo el id del dispensador => $id
        if ($isEdit==true) {
        //Actualizar el usuario del userdispensador
            $usuario_dispensador= new Userdispensador();
            $usuario_dispensador->updateAll("usuario_id = $usuario","dispensador_id= $id");


            $dispensador_servicio = new Dispensadorservicio();
            $whereCondition="dispensador_id= $id";
            $dispensador_servicio->deleteAll($whereCondition);
            if (!empty($check_servicio)) {
                foreach($check_servicio as $servicio) {
                    $dispensador_servicio->setDispensadorId($id);
                    $dispensador_servicio->setServicioId($servicio);
                    $dispensador_servicio->save();
                }
            }
            if($dispensador->save()==false) {
                Flash::error('Hubo un error actualizando el registro Dispensador-Servicio.');
            }else {
                $this->setParamToView('save','true');
                if($this->getQueryParam("exit")!="")
                    $this->setParamToView('exit','true');
                else {
                    if(!$isEdit) {
                        $action='editar';
                    }
                }
                Flash::success('Registro actualizando con Ã©xito Dispensador-Servicio.');
            }
        } else { //Es decir nuevo dispensador
            $dispensador_servicio = new Dispensadorservicio();
            if($dispensador->save()==true) {
                $buscaMax= $dispensador->maximum("id");
                //Guardamos los servicios segÃºn el dispensador
                if (!empty($check_servicio)) {
                    foreach($check_servicio as $servicio) {
                        $dispensador_servicio->setDispensadorId($buscaMax);
                        $dispensador_servicio->setServicioId($servicio);
                        $dispensador_servicio->save();
                    }
                }
                //Guardamos el usuario_id y dispensador_id de usuariodispensador
                $usuario_dispensador = new Userdispensador();
                $usuario_dispensador->setUsuarioId($usuario);
                $usuario_dispensador->setDispensadorId($buscaMax);
                $usuario_dispensador->save();
                Flash::success('Registro guardado con Ã©xito Dispensador-Servicio.');

            }else {
                $this->setParamToView('save','true');
                if($this->getQueryParam("exit")!="")
                    $this->setParamToView('exit','true');
                else {
                    if(!$isEdit) {
                        $action='editar';
                    }
                }
                Flash::error('Hubo un error guardando el registro Dispensador-Servicio.');
            }

        }
        //$this->routeTo('action: index');
        $this->routeTo("action: $action","id: $id");
    }

    /**
     * Eliminar el Dispensador
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
            //Inicio Eliminar primero los registros de dispensadorservicio
                $db = Db::rawConnect();
                $db->query("DELETE FROM dispensadorservicio WHERE dispensador_id = $ids[$i]");
                //Inicio Eliminar primero los registros de dispensadorservicio
                //Inicio Eliminar primero la relacion userdispensador
                $db = Db::rawConnect();
                $db->query("delete from userdispensador where dispensador_id = $ids[$i]");
                //Fin Eliminar primero la relacion userdispensador
                if(!$this->Dispensador->delete($ids[$i])) {
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
                    case 'id':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'descripcion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'tipo_dispensador':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Dispensador->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Dispensador->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Dispensador=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Dispensador->getId(),'cell'=>array($Dispensador->getDescripcion(),$Dispensador->getTipoDispensador()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

