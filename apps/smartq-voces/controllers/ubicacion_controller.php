<?php

/**
 * Controlador Ubicacion
 *
 * @access public
 * @version 1.0
 */
class UbicacionController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nombre_ubicacion
     *
     * @var string
     */
    public $nombreUbicacion;

    /**
     * descripcion
     *
     * @var string
     */
    public $descripcion;

    /**
     * creacion_at
     *
     */
    public $creacionAt;


        /**
     * creacion_at
     *
     */
    public $estado;
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
        $columnsGrid[]=array('name'=>'Nombre UbicaciÃ³n','index'=>'nombre_ubicacion');
        $columnsGrid[]=array('name'=>'DescripciÃ³n','index'=>'descripcion');
        $columnsGrid[]=array('name'=>'Estado','index'=>'estado');

        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Ubicacion/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Ubicacion
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $ubicacion = $this->Ubicacion->findFirst($id);
        if($ubicacion) {
            Tag::displayTo('id', $ubicacion->getId());
            Tag::displayTo('nombre_ubicacion', $ubicacion->getNombreUbicacion());
            Tag::displayTo('descripcion', $ubicacion->getDescripcion());
            Tag::displayTo('creacion_at', $ubicacion->getCreacionAt());
            Tag::displayTo('estado', $ubicacion->getEstado());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Ubicacion
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $nombreUbicacion = $this->getPostParam("nombre_ubicacion", "striptags", "extraspaces");
        $descripcion = $this->getPostParam("descripcion", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");
        $estadoA = $this->getPostParam("estado", "striptags", "extraspaces");
        $ubicacion = new Ubicacion();
        $ubicacion->setId($id);
        $ubicacion->setNombreUbicacion($nombreUbicacion);
        $ubicacion->setDescripcion($descripcion);
        $ubicacion->setCreacionAt($creacionAt);

        if ($estadoA == 'on')
            $estado = 1;
        else
            $estado= 0;
        $ubicacion->setEstado($estado);

        //$db = Db::rawConnect();
        //$db->query("SELECT areas FROM empresa WHERE id=1");

        $db = DbBase::rawConnect();
          $sql="SELECT areas FROM empresa WHERE id=1";
          $result=$db->query($sql);

          $sql1= "SELECT COUNT(*) as numero FROM ubicacion WHERE estado=1";
          $result1=$db->query($sql1);


        while ($row = $db->fetchArray($result)) {
             $limite = Encrypter::decrypt($row['areas']);
        }

        while ($row = $db->fetchArray($result1)) {
             $numero = $row['numero'];
        }

        //echo("<script>console.log('PHP: ".$limite."');</script>");

        $action=($isEdit==true) ? "editar" : 'nuevo';
        

        if(is_numeric($limite)){

        if ($numero < $limite || $estado==0) {
                   if ($ubicacion->save()==false) {
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
        }else{
            Flash::error('No cuenta con los permisos para activar otra ubicacion. Cont&aacute;ctese con el proveedor.');
        }
    }else{
        Flash::error('Error de configuraci&oacute;n. Cont&aacute;ctese con el proveedor.');
    }





        $this->routeTo("action: $action","id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Ubicacion
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                //Inicio Eliminar primero los registros de servicio
                //$db = Db::rawConnect();
                //$db->query("DELETE FROM servicio WHERE ubicacion_id = $ids[$i]");
                //Fin Eliminar primero los registros de servicio
                if(!$this->Ubicacion->delete($ids[$i])) {
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
                    case 'nombre_ubicacion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'descripcion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Ubicacion->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Ubicacion->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Ubicacion=$resultado[$i];
            
            $rat = ($Ubicacion->getEstado()==1) ? $activacion="Activado" : $activacion="Desactivado" ;
            $jqgrid->rows[]=array('id'=>$Ubicacion->getId(),'cell'=>array($Ubicacion->getNombreUbicacion(),$Ubicacion->getDescripcion(),$activacion));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

