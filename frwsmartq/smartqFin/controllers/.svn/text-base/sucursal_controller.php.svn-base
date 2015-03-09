<?php

/**
 * Controlador Sucursal
 *
 * @access public
 * @version 1.0
 */
class SucursalController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * alias_sucursal
     *
     * @var string
     */
    public $aliasSucursal;

    /**
     * host
     *
     * @var string
     */
    public $host;

    /**
     * nombre_bd
     *
     * @var string
     */
    public $nombreBd;

    /**
     * usuario_bd
     *
     * @var string
     */
    public $usuarioBd;

    /**
     * password_bd
     *
     * @var string
     */
    public $passwordBd;

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
        $columnsGrid[]=array('name'=>'Alias Sucursal','index'=>'alias_sucursal');
        $columnsGrid[]=array('name'=>'Host','index'=>'host');
        $columnsGrid[]=array('name'=>'Nombre Base de Datos','index'=>'nombre_bd');
        $columnsGrid[]=array('name'=>'Usuario Base de Datos','index'=>'usuario_bd');
        $columnsGrid[]=array('name'=>'Password Base de Datos','index'=>'password_bd');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Sucursal/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Sucursal
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $sucursal = $this->Sucursal->findFirst($id);
        if($sucursal) {
            Tag::displayTo('id', $sucursal->getId());
            Tag::displayTo('alias_sucursal', $sucursal->getAliasSucursal());
            Tag::displayTo('host', $sucursal->getHost());
            Tag::displayTo('nombre_bd', $sucursal->getNombreBd());
            Tag::displayTo('usuario_bd', $sucursal->getUsuarioBd());
            Tag::displayTo('password_bd', $sucursal->getPasswordBd());
            Tag::displayTo('creacion_at', $sucursal->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Sucursal
     *
     */
    public function guardarAction($isEdit=false,$id=null) {
        $aliasSucursal = $this->getPostParam("alias_sucursal", "striptags", "extraspaces");
        $host = $this->getPostParam("host", "striptags", "extraspaces");
        $nombreBd = $this->getPostParam("nombre_bd", "striptags", "extraspaces");
        $usuarioBd = $this->getPostParam("usuario_bd", "striptags", "extraspaces");
        $passwordBd = $this->getPostParam("password_bd", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");
        $sucursal = new Sucursal();
        $sucursal->setId($id);
        $sucursal->setAliasSucursal($aliasSucursal);
        $sucursal->setHost($host);
        $sucursal->setNombreBd($nombreBd);
        $sucursal->setUsuarioBd($usuarioBd);
        $sucursal->setPasswordBd($passwordBd);
        $sucursal->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($sucursal->save()==false) {
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
     * Eliminar el Sucursal
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                $sinchistorial= new Sinchistorial();
                $sinchistorial->deleteAll("sucursal_id=$ids[$i]");
                $sincturnos= new Sincturnos();
                $sincturnos->deleteAll("sucursal_id=$ids[$i]");
                if(!$this->Sucursal->delete($ids[$i])) {
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
                    case 'alias_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'host':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'nombre_bd':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'usuario_bd':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'password_bd':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Sucursal->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Sucursal->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Sucursal=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Sucursal->getId(),'cell'=>array($Sucursal->getAliasSucursal(),$Sucursal->getHost(),$Sucursal->getNombreBd(),$Sucursal->getUsuarioBd(),$Sucursal->getPasswordBd()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /*
     * Funci髇 que permite probar la conexion con la sucrusal
    */
    public function probarSucursalAction() {
        // $this->setResponse("json");
        $aliasSucursal  = $this->getPostParam("alias_sucursal", "striptags", "extraspaces");
        $host           = $this->getPostParam("host", "striptags", "extraspaces");
        $nombreBd       = $this->getPostParam("nombre_bd", "striptags", "extraspaces");
        $usuarioBd      = $this->getPostParam("usuario_bd", "striptags", "extraspaces");
        $passwordBd     = $this->getPostParam("password_bd", "striptags", "extraspaces");
        try {
            if (!($link=mysql_connect($host,$usuarioBd,$passwordBd))) {
                echo "Error de conexi贸n con host.";
                //exit();
            } else {
                Flash::SUCCESS("Conexi贸n con host exitosa.");
                //$error="bien";
            }
            if (!mysql_select_db($nombreBd,$link)) {
                Flash::ERROR("La base de datos no existe. <br>");
                //exit();
            } else {
                Flash::SUCCESS("Conexi贸n con base de datos correcta");
            }
        } catch( Exception $e ) { // o el tipo de Excepcion que requieras...
            Flash::ERROR("Error de conexi贸n con host. Datos no son correctos o no est谩 disponible<br>".$e->getMessage());
            //echo $e->getMessage();
        }
    }
}

