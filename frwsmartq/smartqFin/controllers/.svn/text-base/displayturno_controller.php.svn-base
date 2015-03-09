<?php

/**
 * Controlador Displayturno
 *
 * @access public
 * @version 1.0
 */
class DisplayturnoController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * displayId
	 *
	 * @var int
	 */
	public $displayId;

	/**
	 * numeroturno
	 *
	 * @var int
	 */
	public $numeroturno;

	/**
	 * valor
	 *
	 * @var int
	 */
	public $valor;

	/**
	 * cajanumero
	 *
	 * @var int
	 */
	public $cajanumero;
        public $turno;

	/**
	 * Inicializador del controlador/
	 *
	 */
	public function initialize(){
		$this->setPersistance(true);
	}

	/**
	 * Acción por defecto del controlador/
	 *
	 */
	public function indexAction(){
		$columnsGrid[]=array('name'=>'DisplayId','index'=>'displayId');
		$columnsGrid[]=array('name'=>'Numeroturno','index'=>'numeroturno');
		$columnsGrid[]=array('name'=>'Valor','index'=>'valor');
		$columnsGrid[]=array('name'=>'Cajanumero','index'=>'cajanumero');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Displayturno/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Displayturno
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$displayturno = $this->Displayturno->findFirst($id);
		if($displayturno){
			Tag::displayTo('id', $displayturno->getId());
			Tag::displayTo('displayId', $displayturno->getDisplayId());
			Tag::displayTo('numeroturno', $displayturno->getNumeroturno());
			Tag::displayTo('valor', $displayturno->getValor());
			Tag::displayTo('cajanumero', $displayturno->getCajanumero());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Displayturno
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$displayId = $this->getPostParam("displayId", "int");
		$numeroturno = $this->getPostParam("numeroturno", "int");
		$valor = $this->getPostParam("valor", "int");
		$cajanumero = $this->getPostParam("cajanumero", "int");
		$displayturno = new Displayturno();
		$displayturno->setId($id);
		$displayturno->setDisplayId($displayId);
		$displayturno->setNumeroturno($numeroturno);
		$displayturno->setValor($valor);
		$displayturno->setCajanumero($cajanumero);
		if($displayturno->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Displayturno
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Displayturno->delete($ids[$i])){
					$msg.="El registro $ids[$i] no pudo ser eliminado.";
				}else {
				$msg.="El registro $ids[$i] fue eliminado correctamente.";
				}
			}
		}
		echo $msg;
	}
	public function obtenerDatosGridAction(){
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
		 if($buscar=='true'){ //verificamos si la busqueda es activada
			if($strbusqueda!=''){    // construccion de la cadena de condicion para la busqueda normal 
				switch($campoBusqueda){
				}
				$condicion.=' AND '.Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
			}elseif($filtroBusqueda){
				$jsona = json_decode($filtroBusqueda,true);
				if(is_array($jsona)){
					$gopr = $jsona['groupOp'];
					$rules = $jsona['rules'];
					$i =0;
					foreach($rules as $key=>$val) {
						$field = $val['field'];
						switch($field){
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
					case 'displayId':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'numeroturno':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'valor':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'cajanumero':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Displayturno->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Displayturno->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Displayturno=$resultado[$i];
			$jqgrid->rows[]=array('id'=>$Displayturno->getId(),'cell'=>array($Displayturno->getDisplayId(),$Displayturno->getNumeroturno(),$Displayturno->getValor(),$Displayturno->getCajanumero()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

