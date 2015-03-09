<?php

/**
 * Controlador Sinchistorial
 *
 * @access public
 * @version 1.0
 */
class SinchistorialController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * sucursal_id
	 *
	 * @var int
	 */
	public $sucursalId;

	/**
	 * fecha_sincronizacion
	 *
	 */
	public $fechaSincronizacion;

	/**
	 * hora_sincronizacion
	 *
	 */
	public $horaSincronizacion;

	/**
	 * registros_sincronizados
	 *
	 * @var int
	 */
	public $registrosSincronizados;

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
		$columnsGrid[]=array('name'=>'Sucursal','index'=>'sucursal_id');
		$columnsGrid[]=array('name'=>'Fecha Sincronizacion','index'=>'fecha_sincronizacion');
		$columnsGrid[]=array('name'=>'Hora Sincronizacion','index'=>'hora_sincronizacion');
		$columnsGrid[]=array('name'=>'Registros Sincronizados','index'=>'registros_sincronizados');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Sinchistorial/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Sinchistorial
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$sinchistorial = $this->Sinchistorial->findFirst($id);
		if($sinchistorial){
			Tag::displayTo('id', $sinchistorial->getId());
			Tag::displayTo('sucursal_id', $sinchistorial->getSucursalId());
			Tag::displayTo('fecha_sincronizacion', $sinchistorial->getFechaSincronizacion());
			Tag::displayTo('hora_sincronizacion', $sinchistorial->getHoraSincronizacion());
			Tag::displayTo('registros_sincronizados', $sinchistorial->getRegistrosSincronizados());
			Tag::displayTo('creacion_at', $sinchistorial->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Sinchistorial
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$sucursalId = $this->getPostParam("sucursal_id", "int");
		$fechaSincronizacion = $this->getPostParam("fecha_sincronizacion");
		$horaSincronizacion = $this->getPostParam("hora_sincronizacion");
		$registrosSincronizados = $this->getPostParam("registros_sincronizados", "int");
		$creacionAt = $this->getPostParam("creacion_at");
		$sinchistorial = new Sinchistorial();
		$sinchistorial->setId($id);
		$sinchistorial->setSucursalId($sucursalId);
		$sinchistorial->setFechaSincronizacion($fechaSincronizacion);
		$sinchistorial->setHoraSincronizacion($horaSincronizacion);
		$sinchistorial->setRegistrosSincronizados($registrosSincronizados);
		$sinchistorial->setCreacionAt($creacionAt);
		if($sinchistorial->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Sinchistorial
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Sinchistorial->delete($ids[$i])){
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
					case 'sucursal_id':
						$condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal',$abrevoper,$strbusqueda);
						$Sucursal=$this->Sucursal->find($condSucursal);
						if(count($Sucursal)>0){
							$arrayIdsSucursal=array();
							foreach($Sucursal as $fila){
								$arrayIdsSucursal[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsSucursal);
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
							case 'sucursal_id':
								$condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal',$val['op'],$val['data']);
								$Sucursal=$this->Sucursal->find($condSucursal);
								if(count($Sucursal)>0){
									$arrayIdsSucursal=array();
									foreach($Sucursal as $fila){
										$arrayIdsSucursal[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsSucursal);
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
					case 'sucursal_id':
						$condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal','bw',$v);
						$Sucursal=$this->Sucursal->find($condSucursal);
						if(count($Sucursal)>0){
							$arrayIdsSucursal=array();
							foreach($Sucursal as $fila){
								$arrayIdsSucursal[]=$fila->getId();
							}
							$v=join(',',$arrayIdsSucursal);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'fecha_sincronizacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_sincronizacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'registros_sincronizados':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Sinchistorial->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Sinchistorial->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Sinchistorial=$resultado[$i];
			$Sucursal=$Sinchistorial->getSucursal();
			$jqgrid->rows[]=array('id'=>$Sinchistorial->getId(),'cell'=>array($Sucursal->getAlias_sucursal(),$Sinchistorial->getFechaSincronizacion(),$Sinchistorial->getHoraSincronizacion(),$Sinchistorial->getRegistrosSincronizados()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

