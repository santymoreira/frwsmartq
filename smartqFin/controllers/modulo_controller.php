<?php

/**
 * Controlador Modulo
 *
 * @access public
 * @version 1.0
 */
class ModuloController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * nombre
	 *
	 * @var string
	 */
	public $nombre;

	/**
	 * estado
	 *
	 * @var int
	 */
	public $estado;

	/**
	 * ruta
	 *
	 * @var string
	 */
	public $ruta;

	/**
	 * tipo
	 *
	 * @var string
	 */
	public $tipo;

	/**
	 * Inicializador del controlador/
	 *
	 */
	public function initialize(){
            $this->setPersistance(true);            
            ApplicationController::combos();
	}
	/**
	 * AcciÃ³n por defecto del controlador/
	 *
	 */
	public function indexAction(){
                $this->setResponse('ajax');
		$columnsGrid[]=array('name'=>'Id','index'=>'id');
		$columnsGrid[]=array('name'=>'Nombre','index'=>'nombre');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
		$columnsGrid[]=array('name'=>'Ruta','index'=>'ruta');
		$columnsGrid[]=array('name'=>'Tipo','index'=>'tipo');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Modulo/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Modulo
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$modulo = $this->Modulo->findFirst($id);
		if($modulo){
			Tag::displayTo('id', $modulo->getId());
			Tag::displayTo('nombre', $modulo->getNombre());
			Tag::displayTo('estado', $modulo->getEstado());
			Tag::displayTo('ruta', $modulo->getRuta());
			Tag::displayTo('tipo', $modulo->getTipo());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Modulo
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
		$estado = $this->getPostParam("estado", "int");
		$ruta = $this->getPostParam("ruta", "striptags", "extraspaces");
		$tipo = $this->getPostParam("tipo", "striptags", "extraspaces");
		$modulo = new Modulo();
		$modulo->setId($id);
		$modulo->setNombre($nombre);
		$modulo->setEstado($estado);
		$modulo->setRuta($ruta);
		$modulo->setTipo($tipo);
		if($modulo->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Modulo
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Modulo->delete($ids[$i])){
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
					case 'id':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'nombre':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'estado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'ruta':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'tipo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Modulo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Modulo->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Modulo=$resultado[$i];
			$jqgrid->rows[]=array('id'=>$Modulo->getId(),'cell'=>array($Modulo->getId(),$Modulo->getNombre(),$Modulo->getEstado(),$Modulo->getRuta(),$Modulo->getTipo()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

