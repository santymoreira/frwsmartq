<?php

/**
 * Controlador Turnoseteo
 *
 * @access public
 * @version 1.0
 */
class TurnoseteoController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * fraseinicial
     *
     * @var string
     */
    public $fraseinicial;

    /**
     * chkfraseinicial
     *
     * @var int
     */
    public $chkfraseinicial;

    /**
     * empresa
     *
     * @var string
     */
    public $empresa;

    /**
     * chkempresa
     *
     * @var int
     */
    public $chkempresa;

    /**
     * logo
     *
     * @var string
     */
    public $logo;

    /**
     * chklogo
     *
     * @var int
     */
    public $chklogo;

    /**
     * chkinicial
     *
     * @var int
     */
    public $chkinicial;

    /**
     * chkservicio
     *
     * @var int
     */
    public $chkservicio;

    /**
     * chkservicio
     *
     * @var int
     */
    public $chkubicacion;

    /**
     * chkfecha
     *
     * @var int
     */
    public $chkfecha;

    /**
     * chktiempoespera
     *
     * @var int
     */
    public $chktiempoespera;

    /**
     * chkturnoespera
     *
     * @var int
     */
    public $chkturnoespera;

    /**
     * Inicializador del controlador/
     *
     */

    public $bienvenida;
    public $carpeta;

    public function initialize() {
        $this->setPersistance(true);
        $turno_seteo= new Turnoseteo();
        $buscaTurnos_seteo= $turno_seteo->findFirst("columns: Fraseinicial");
        $this->bienvenida= $buscaTurnos_seteo->getFraseinicial();
        $empresa= new Empresa();
        $buscaEmpresa=$empresa->findFirst("columns: carpeta");
        $this->carpeta=$buscaEmpresa->getCarpeta();
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[]=array('name'=>'Fraseinicial','index'=>'fraseinicial');
        $columnsGrid[]=array('name'=>'Imprimir frase inicial','index'=>'chkfraseinicial');
        //$columnsGrid[]=array('name'=>'Empresa','index'=>'empresa');
        //$columnsGrid[]=array('name'=>'Chkempresa','index'=>'chkempresa');
        //$columnsGrid[]=array('name'=>'Logo','index'=>'logo');
        //$columnsGrid[]=array('name'=>'Chklogo','index'=>'chklogo');
        //$columnsGrid[]=array('name'=>'Chkinicial','index'=>'chkinicial');
        $columnsGrid[]=array('name'=>'Imp. nombre y ubicación','index'=>'chkservicio');
        $columnsGrid[]=array('name'=>'Imp. fecha','index'=>'chkfecha');
        $columnsGrid[]=array('name'=>'Imp. clientes en espera','index'=>'chktiempoespera');
        $columnsGrid[]=array('name'=>'Imp. tiempo de espera','index'=>'chkturnoespera');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Turnoseteo/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Turnoseteo
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $turnoseteo = $this->Turnoseteo->findFirst($id);
        if($turnoseteo) {
            Tag::displayTo('id', $turnoseteo->getId());
            Tag::displayTo('fraseinicial', $turnoseteo->getFraseinicial());
            Tag::displayTo('chkbienvenida', $turnoseteo->getChkfraseinicial());
            Tag::displayTo('empresa', $turnoseteo->getEmpresa());
            Tag::displayTo('chkempresa', $turnoseteo->getChkempresa());
            Tag::displayTo('logo', $turnoseteo->getLogo());
            Tag::displayTo('chklogo', $turnoseteo->getChklogo());
            Tag::displayTo('chkinicial', $turnoseteo->getChkinicial());
            Tag::displayTo('chknombreservicio', $turnoseteo->getChkservicio());
            Tag::displayTo('chkfecha', $turnoseteo->getChkfecha());
            Tag::displayTo('chktiempoespera', $turnoseteo->getChktiempoespera());
            Tag::displayTo('chkturnoespera', $turnoseteo->getChkturnoespera());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Turnoseteo
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $id=1;
        $fraseinicial = $this->getPostParam("bienvenida1", "striptags", "extraspaces");

        $empresa = "";
        $chkempresa = "0";
        $logo = "logo-cooperativa.bmp";
        $chklogo = "1";

        $chkbienvenida = $this->getPostParam("chkbienvenida");
        $chknombreservicio = $this->getPostParam("chknombreservicio");
        $chkfecha = $this->getPostParam("chkfecha");
        $chkturnoespera = $this->getPostParam("chkturnoespera");
        $chktiempoespera = $this->getPostParam("chktiempoespera");
        if ($chkbienvenida== "on")
            $chkbienvenida=1;
        else
            $chkbienvenida=0;
        if ($chknombreservicio== "on")
            $chknombreservicio=1;
        else
            $chknombreservicio=0;
        if ($chkfecha== "on")
            $chkfecha=1;
        else
            $chkfecha=0;
        if ($chkturnoespera== "on")
            $chkturnoespera=1;
        else
            $chkturnoespera=0;
        if ($chktiempoespera== "on")
            $chktiempoespera=1;
        else
            $chktiempoespera=0;

        $turnoseteo = new Turnoseteo();
        $turnoseteo->setId($id);
        $turnoseteo->setFraseinicial($fraseinicial);
        $turnoseteo->setChkfraseinicial($chkbienvenida);
        $turnoseteo->setEmpresa($empresa);
        $turnoseteo->setChkempresa($chkempresa);
        $turnoseteo->setLogo($logo);
        $turnoseteo->setChklogo($chklogo);
        //$turnoseteo->setChkinicial($chkinicial);
        $turnoseteo->setChkservicio($chknombreservicio);
        $turnoseteo->setChkfecha($chkfecha);
        $turnoseteo->setChktiempoespera($chktiempoespera);
        $turnoseteo->setChkturnoespera($chkturnoespera);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($turnoseteo->save()==false) {
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
     * Eliminar el Turnoseteo
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Turnoseteo->delete($ids[$i])) {
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
                    case 'fraseinicial':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkfraseinicial':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'empresa':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkempresa':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'logo':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chklogo':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkinicial':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkservicio':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkfecha':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chktiempoespera':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'chkturnoespera':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Turnoseteo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Turnoseteo->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Turnoseteo=$resultado[$i];
            //$jqgrid->rows[]=array('id'=>$Turnoseteo->getId(),'cell'=>array($Turnoseteo->getFraseinicial(),$Turnoseteo->getChkfraseinicial(),$Turnoseteo->getEmpresa(),$Turnoseteo->getChkempresa(),$Turnoseteo->getLogo(),$Turnoseteo->getChklogo(),$Turnoseteo->getChkinicial(),$Turnoseteo->getChkservicio(),$Turnoseteo->getChkfecha(),$Turnoseteo->getChktiempoespera(),$Turnoseteo->getChkturnoespera()));
            $chkfraseinicial='<font color="#01CF00"><b>Si</b></font>';
            if($Turnoseteo->getChkfraseinicial()==0)
                $chkfraseinicial='<font color="red"><b>No</b></font>';
            $chkservicio='<font color="#01CF00"><b>Si</b></font>';
            if($Turnoseteo->getChkservicio()==0)
                $chkservicio='<font color="red"><b>No</b></font>';
            $chkfecha='<font color="#01CF00"><b>Si</b></font>';
            if($Turnoseteo->getChkfecha()==0)
                $chkfecha='<font color="red"><b>No</b></font>';
            $chktiempoespera='<font color="#01CF00"><b>Si</b></font>';
            if($Turnoseteo->getChktiempoespera()==0)
                $chktiempoespera='<font color="red"><b>No</b></font>';
            $chkturnoespera='<font color="#01CF00"><b>Si</b></font>';
            if($Turnoseteo->getChkturnoespera()==0)
                $chkturnoespera='<font color="red"><b>No</b></font>';
            $jqgrid->rows[]=array('id'=>$Turnoseteo->getId(),'cell'=>array($Turnoseteo->getFraseinicial(),$chkfraseinicial,$chkservicio,$chkfecha,$chkturnoespera,$chktiempoespera));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

