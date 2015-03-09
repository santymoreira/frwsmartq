<?php

/**
 * Controlador Userdispensador
 *
 * @access public
 * @version 1.0
 */
class UserdispensadorController extends ApplicationController {

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
	 * dispensador_id
	 *
	 * @var int
	 */
	public $dispensadorId;

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
		$columnsGrid[]=array('name'=>'Usuario','index'=>'usuario_id');
		$columnsGrid[]=array('name'=>'Dispensador','index'=>'dispensador_id');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Userdispensador/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Userdispensador
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$userdispensador = $this->Userdispensador->findFirst($id);
		if($userdispensador){
			Tag::displayTo('id', $userdispensador->getId());
			Tag::displayTo('usuario_id', $userdispensador->getUsuarioId());
			Tag::displayTo('dispensador_id', $userdispensador->getDispensadorId());
			Tag::displayTo('creacion_at', $userdispensador->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Userdispensador
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$usuarioId = $this->getPostParam("usuario_id", "int");
		$dispensadorId = $this->getPostParam("dispensador_id", "int");
		$creacionAt = $this->getPostParam("creacion_at");
		$userdispensador = new Userdispensador();
		$userdispensador->setId($id);
		$userdispensador->setUsuarioId($usuarioId);
		$userdispensador->setDispensadorId($dispensadorId);
		$userdispensador->setCreacionAt($creacionAt);
		if($userdispensador->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Userdispensador
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Userdispensador->delete($ids[$i])){
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
					case 'usuario_id':
						$condUsuario=Utils::toSqlParamSearchGrid('nombres',$abrevoper,$strbusqueda);
						$Usuario=$this->Usuario->find($condUsuario);
						if(count($Usuario)>0){
							$arrayIdsUsuario=array();
							foreach($Usuario as $fila){
								$arrayIdsUsuario[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsUsuario);
							$abrevoper='in';
						}
					break;
					case 'dispensador_id':
						$condDispensador=Utils::toSqlParamSearchGrid('descripcion',$abrevoper,$strbusqueda);
						$Dispensador=$this->Dispensador->find($condDispensador);
						if(count($Dispensador)>0){
							$arrayIdsDispensador=array();
							foreach($Dispensador as $fila){
								$arrayIdsDispensador[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsDispensador);
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
							case 'usuario_id':
								$condUsuario=Utils::toSqlParamSearchGrid('nombres',$val['op'],$val['data']);
								$Usuario=$this->Usuario->find($condUsuario);
								if(count($Usuario)>0){
									$arrayIdsUsuario=array();
									foreach($Usuario as $fila){
										$arrayIdsUsuario[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsUsuario);
									$val['op']='in';
								}
							break;
							case 'dispensador_id':
								$condDispensador=Utils::toSqlParamSearchGrid('descripcion',$val['op'],$val['data']);
								$Dispensador=$this->Dispensador->find($condDispensador);
								if(count($Dispensador)>0){
									$arrayIdsDispensador=array();
									foreach($Dispensador as $fila){
										$arrayIdsDispensador[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsDispensador);
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
						if(count($Usuario)>0){
							$arrayIdsUsuario=array();
							foreach($Usuario as $fila){
								$arrayIdsUsuario[]=$fila->getId();
							}
							$v=join(',',$arrayIdsUsuario);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'dispensador_id':
						$condDispensador=Utils::toSqlParamSearchGrid('descripcion','bw',$v);
						$Dispensador=$this->Dispensador->find($condDispensador);
						if(count($Dispensador)>0){
							$arrayIdsDispensador=array();
							foreach($Dispensador as $fila){
								$arrayIdsDispensador[]=$fila->getId();
							}
							$v=join(',',$arrayIdsDispensador);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Userdispensador->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Userdispensador->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Userdispensador=$resultado[$i];
			$Usuario=$Userdispensador->getUsuario();
			$Dispensador=$Userdispensador->getDispensador();
			$jqgrid->rows[]=array('id'=>$Userdispensador->getId(),'cell'=>array($Usuario->getNombres(),$Dispensador->getDescripcion()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

