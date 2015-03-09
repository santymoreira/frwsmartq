<?php

/**
 * Controlador Empresa
 *
 * @access public
 * @version 1.0
 */
class EmpresaController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nombrecomercial
     *
     * @var string
     */
    public $nombrecomercial;

    /**
     * alias_empresa
     *
     * @var string
     */
    public $aliasEmpresa;

    /**
     * razonsocial
     *
     * @var string
     */
    public $razonsocial;

    /**
     * matriz
     *
     * @var int
     */
    public $matriz;

    /**
     * direccion
     *
     * @var string
     */
    public $direccion;

    /**
     * modulo_operadores
     *
     * @var int
     */
    public $moduloOperadores;

    /**
     * modulo_cajas
     *
     * @var int
     */
    public $moduloCajas;

    /**
     * calif_4botones_teclado
     *
     * @var int
     */
    public $calif4botonesTeclado;

    /**
     * calif_4botones_pantalla
     *
     * @var int
     */
    public $calif4botonesPantalla;

    /**
     * calif_matriz_pantalla
     *
     * @var int
     */
    public $califMatrizPantalla;

    /**
     * dispensador_simple
     *
     * @var int
     */
    public $dispensadorSimple;

    /**
     * dispensador_touch
     *
     * @var int
     */
    public $dispensadorTouch;

    /**
     * dispensador_botonera
     *
     * @var int
     */
    public $dispensadorBotonera;

    /**
     * dispensador_touch_pequenia
     *
     * @var int
     */
    public $dispensadorTouchPequenia;

    /**
     * carpeta
     *
     * @var string
     */
    public $carpeta;

    /**
     * seleccion_operador
     *
     * @var int
     */
    public $seleccionOperador;

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
        $columnsGrid[]=array('name'=>'Id','index'=>'id','width'=>'15px');
        $columnsGrid[]=array('name'=>'Nombre Comercial','index'=>'nombrecomercial');
        $columnsGrid[]=array('name'=>'Alias Empresa','index'=>'alias_empresa');
        $columnsGrid[]=array('name'=>'Razón Social','index'=>'razonsocial');
        $columnsGrid[]=array('name'=>'Matriz','index'=>'matriz','width'=>'35px');
        $columnsGrid[]=array('name'=>'Dirección','index'=>'direccion');
        $columnsGrid[]=array('name'=>'Mod con Tickets','index'=>'modulo_operadores','width'=>'95px');
        $columnsGrid[]=array('name'=>'Mod sin Tickets','index'=>'modulo_cajas','width'=>'95px');
        $columnsGrid[]=array('name'=>'Calif 4 botones Teclado','index'=>'calif_4botones_teclado');
        $columnsGrid[]=array('name'=>'Calif 4 botones Pantalla','index'=>'calif_4botones_pantalla');
        $columnsGrid[]=array('name'=>'Calif Matriz Pantalla','index'=>'calif_matriz_pantalla');
        $columnsGrid[]=array('name'=>'Disp Simple','index'=>'dispensador_simple');
        $columnsGrid[]=array('name'=>'Disp Touch','index'=>'dispensador_touch');
        $columnsGrid[]=array('name'=>'Disp Botonera','index'=>'dispensador_botonera');
        $columnsGrid[]=array('name'=>'Disp Touch Pequeña','index'=>'dispensador_touch_pequenia');
        $columnsGrid[]=array('name'=>'Carpeta','index'=>'carpeta');
        $columnsGrid[]=array('name'=>'Selección Operador','index'=>'seleccion_operador');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Empresa/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Empresa
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $empresa = $this->Empresa->findFirst($id);
        if($empresa) {
            Tag::displayTo('id', $empresa->getId());
            Tag::displayTo('nombrecomercial', $empresa->getNombrecomercial());
            Tag::displayTo('alias_empresa', $empresa->getAliasEmpresa());
            Tag::displayTo('razonsocial', $empresa->getRazonsocial());
            Tag::displayTo('matriz', $empresa->getMatriz());
            Tag::displayTo('direccion', $empresa->getDireccion());
            Tag::displayTo('modulo_operadores', $empresa->getModuloOperadores());
            Tag::displayTo('modulo_cajas', $empresa->getModuloCajas());
            Tag::displayTo('calif_4botones_teclado', $empresa->getCalif4botonesTeclado());
            Tag::displayTo('calif_4botones_pantalla', $empresa->getCalif4botonesPantalla());
            Tag::displayTo('calif_matriz_pantalla', $empresa->getCalifMatrizPantalla());
            Tag::displayTo('dispensador_simple', $empresa->getDispensadorSimple());
            Tag::displayTo('dispensador_touch', $empresa->getDispensadorTouch());
            Tag::displayTo('dispensador_botonera', $empresa->getDispensadorBotonera());
            Tag::displayTo('dispensador_touch_pequenia', $empresa->getDispensadorTouchPequenia());
            Tag::displayTo('carpeta', $empresa->getCarpeta());
            Tag::displayTo('seleccion_operador', $empresa->getSeleccionOperador());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Empresa
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $nombrecomercial = $this->getPostParam("nombrecomercial", "striptags", "extraspaces");
        $aliasEmpresa = $this->getPostParam("alias_empresa", "striptags", "extraspaces");
        $razonsocial = $this->getPostParam("razonsocial", "striptags", "extraspaces");

        $matriz = $this->getPostParam("matriz");
        if ($matriz=='on') $matriz=1;
        $direccion = $this->getPostParam("direccion", "striptags", "extraspaces");
        $moduloOperadores = $this->getPostParam("modulo_operadores");
        if ($moduloOperadores=='on') $moduloOperadores=1;
        $moduloCajas = $this->getPostParam("modulo_cajas");
        if ($moduloCajas=='on') $moduloCajas=1;
        $calif4botonesTeclado = $this->getPostParam("calif_4botones_teclado");
        if ($calif4botonesTeclado=='on') $calif4botonesTeclado=1;
        $calif4botonesPantalla = $this->getPostParam("calif_4botones_pantalla");
        if ($calif4botonesPantalla=='on') $calif4botonesPantalla=1;
        $califMatrizPantalla = $this->getPostParam("calif_matriz_pantalla");
        if ($califMatrizPantalla=='on') $califMatrizPantalla=1;
        $dispensadorSimple = $this->getPostParam("dispensador_simple");
        if ($dispensadorSimple=='on') $dispensadorSimple=1;
        $dispensadorTouch = $this->getPostParam("dispensador_touch");
        if ($dispensadorTouch=='on') $dispensadorTouch=1;
        $dispensadorBotonera = $this->getPostParam("dispensador_botonera");
        if ($dispensadorBotonera=='on') $dispensadorBotonera=1;
        $dispensadorTouchPequenia = $this->getPostParam("dispensador_touch_pequenia");
        if ($dispensadorTouchPequenia=='on') $dispensadorTouchPequenia=1;
        $seleccionOperador = $this->getPostParam("seleccion_operador");
        if ($seleccionOperador=='on') $seleccionOperador=1;

        $carpeta = $this->getPostParam("carpeta", "striptags", "extraspaces");
        //INICIO DAR PERMISOS PARA MATRIZ PARA SUCURSALES
        /* MENU
         * 46 = Sincronizar
         * 48 = Datos suscursales
         * 55 = Turnos con ticket
         * 57 = Turnos sin ticket
         */
        if($matriz==1){
            $permiso = new Permiso();
            $permiso->updateAll("permiso=1","menu_id IN (46,48,55,57)");
        } else {
            $permiso = new Permiso();
            $permiso->updateAll("permiso=0","menu_id IN (46,48,55,57)");
        }

        $empresa = new Empresa();
        $empresa->setId($id);
        $empresa->setNombrecomercial($nombrecomercial);
        $empresa->setAliasEmpresa($aliasEmpresa);
        $empresa->setRazonsocial($razonsocial);
        $empresa->setMatriz($matriz);
        $empresa->setDireccion($direccion);
        $empresa->setModuloOperadores($moduloOperadores);
        $empresa->setModuloCajas($moduloCajas);
        $empresa->setCalif4botonesTeclado($calif4botonesTeclado);
        $empresa->setCalif4botonesPantalla($calif4botonesPantalla);
        $empresa->setCalifMatrizPantalla($califMatrizPantalla);
        $empresa->setDispensadorSimple($dispensadorSimple);
        $empresa->setDispensadorTouch($dispensadorTouch);
        $empresa->setDispensadorBotonera($dispensadorBotonera);
        $empresa->setDispensadorTouchPequenia($dispensadorTouchPequenia);
        $empresa->setCarpeta($carpeta);
        $empresa->setSeleccionOperador($seleccionOperador);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($empresa->save()==false) {
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
            Flash::success('Registro guardado con éxito.');
        }
        $this->routeTo("action: $action","id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Empresa
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Empresa->delete($ids[$i])) {
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
                    case 'id':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'nombrecomercial':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'alias_empresa':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'razonsocial':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'matriz':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'direccion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'modulo_operadores':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'modulo_cajas':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'calif_4botones_teclado':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'calif_4botones_pantalla':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'calif_matriz_pantalla':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'dispensador_simple':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'dispensador_touch':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'dispensador_botonera':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'dispensador_touch_pequenia':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'carpeta':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'seleccion_operador':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Empresa->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Empresa->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Empresa=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Empresa->getId(),'cell'=>array($Empresa->getId(),$Empresa->getNombrecomercial(),$Empresa->getAliasEmpresa(),$Empresa->getRazonsocial(),$Empresa->getMatriz(),$Empresa->getDireccion(),$Empresa->getModuloOperadores(),$Empresa->getModuloCajas(),$Empresa->getCalif4botonesTeclado(),$Empresa->getCalif4botonesPantalla(),$Empresa->getCalifMatrizPantalla(),$Empresa->getDispensadorSimple(),$Empresa->getDispensadorTouch(),$Empresa->getDispensadorBotonera(),$Empresa->getDispensadorTouchPequenia(),$Empresa->getCarpeta(),$Empresa->getSeleccionOperador()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

