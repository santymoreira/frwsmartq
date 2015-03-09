<?php

/**
 * Controlador Caja
 *
 * @access public
 * @version 1.0
 */
class ListacajaController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * numero
     *
     * @var int
     */
    public $numero;

    /**
     * descripcion
     *
     * @var string
     */
    public $descripcion;

    /**
     * estado
     *
     * @var int
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
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
    }

    /**
     * Crear un Caja/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Caja
     *
     */
    public function editarAction($id=null) {


    }

    /**
     * Guardar el Caja
     *
     */
    public function guardarAction($isEdit=false,$id=null) {


    }

    /**
     * Eliminar el Caja
     *
     */
    public function activarcajaAction() {
        $this->setResponse("json");
        $idcaja=$this->getPostParam('id');
        $filter = new Filter();
        $id = $filter->applyFilter($idcaja,"int");
        $caja = $this->Caja->findFirst($idcaja);
        $cnumero=$caja->getNumeroCaja();
        $cdescripcion=$caja->getDescripcion();

        $datosUsuario=SessionNamespace::get("datosUsuarioSMC");
        $username=$datosUsuario->getUsername();
        $caja  = new Caja();
        $caja->setId($idcaja);
        $caja->setNumero($cnumero);
        $caja->setDescripcion($cdescripcion);
        $caja->setEstado(1);
        $caja->setUsuario($username);
        $caja->save();
        $this->routeTo('action: index');
    }

       public function desactivarcajaAction() {
        $this->setResponse("json");
        $numerocaja=$this->getPostParam('num');
        $condicion="numero=".$numerocaja;
        $caja = $this->Caja->findFirst($condicion);
        
//        $filter = new Filter();
//        $id = $filter->applyFilter($idcaja,"int");
        $cid=$caja->getId();
        $cdescripcion=$caja->getDescripcion();
//        Flash::notice($cid);
  //      echo Tag::displayTo('numcaja', "2");
        $caja  = new Caja();
        $caja->setId($cid);
        $caja->setNumero($numerocaja);
        $caja->setDescripcion($cdescripcion);
        $caja->setEstado(0);
        //$caja->setUsuario($username);
        $caja->save();
        $this->routeTo('action: index');
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
                    case 'numero':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'descripcion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'estado':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Caja->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Caja->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Caja=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Caja->getId(),'cell'=>array($Caja->getNumeroCaja(),$Caja->getDescripcion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

