<?php

/**
 * Controlador Grupo
 *
 * @access public
 * @version 1.0
 */
class GrupoController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * modulo_id
	 *
	 * @var int
	 */
	public $moduloId;

	/**
	 * nombre_largo
	 *
	 * @var string
	 */
	public $nombreLargo;

	/**
	 * nombre_corto
	 *
	 * @var string
	 */
	public $nombreCorto;

	/**
	 * descripcion
	 *
	 */
	public $descripcion;

	/**
	 * creacion_at
	 *
	 */
	public $creacionAt;

	/**
	 * Inicializador del controlador/
	 *
	 */
	public function initialize(){
		$this->setPersistance(true);
	}

	/**
	 * AcciÃ³n por defecto del controlador/
	 *
	 */
	public function indexAction(){
		$columnsGrid[]=array('name'=>'Modulo','index'=>'modulo_id');
		$columnsGrid[]=array('name'=>'Nombre Largo','index'=>'nombre_largo');
		$columnsGrid[]=array('name'=>'Nombre Corto','index'=>'nombre_corto');
		$columnsGrid[]=array('name'=>'Descripcion','index'=>'descripcion');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Grupo/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Grupo
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$grupo = $this->Grupo->findFirst($id);
		if($grupo){
			Tag::displayTo('id', $grupo->getId());
			Tag::displayTo('modulo_id', $grupo->getModuloId());
			Tag::displayTo('nombre_largo', $grupo->getNombreLargo());
			Tag::displayTo('nombre_corto', $grupo->getNombreCorto());
			Tag::displayTo('descripcion', $grupo->getDescripcion());
			Tag::displayTo('creacion_at', $grupo->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Grupo
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$moduloId = $this->getPostParam("modulo_id", "int");
		$nombreLargo = $this->getPostParam("nombre_largo", "striptags", "extraspaces");
		$nombreCorto = $this->getPostParam("nombre_corto", "striptags", "extraspaces");
		$descripcion = $this->getPostParam("descripcion");
		$creacionAt = $this->getPostParam("creacion_at");
		$grupo = new Grupo();
		$grupo->setId($id);
		$grupo->setModuloId($moduloId);
		$grupo->setNombreLargo($nombreLargo);
		$grupo->setNombreCorto($nombreCorto);
		$grupo->setDescripcion($descripcion);
		$grupo->setCreacionAt($creacionAt);
		if($grupo->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Grupo
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Grupo->delete($ids[$i])){
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
					case 'modulo_id':
						$condModulo=Utils::toSqlParamSearchGrid('nombre',$abrevoper,$strbusqueda);
						$Modulo=$this->Modulo->find($condModulo);
						if(count($Modulo)>0){
							$arrayIdsModulo=array();
							foreach($Modulo as $fila){
								$arrayIdsModulo[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsModulo);
							$abrevoper='in';
						}
					break;
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
							case 'modulo_id':
								$condModulo=Utils::toSqlParamSearchGrid('nombre',$val['op'],$val['data']);
								$Modulo=$this->Modulo->find($condModulo);
								if(count($Modulo)>0){
									$arrayIdsModulo=array();
									foreach($Modulo as $fila){
										$arrayIdsModulo[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsModulo);
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
					case 'modulo_id':
						$condModulo=Utils::toSqlParamSearchGrid('nombre','bw',$v);
						$Modulo=$this->Modulo->find($condModulo);
						if(count($Modulo)>0){
							$arrayIdsModulo=array();
							foreach($Modulo as $fila){
								$arrayIdsModulo[]=$fila->getId();
							}
							$v=join(',',$arrayIdsModulo);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'nombre_largo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'nombre_corto':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'descripcion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Grupo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Grupo->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Grupo=$resultado[$i];
			$Modulo=$Grupo->getModulo();
			$jqgrid->rows[]=array('id'=>$Grupo->getId(),'cell'=>array($Modulo->getNombre(),$Grupo->getNombreLargo(),$Grupo->getNombreCorto(),$Grupo->getDescripcion()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

