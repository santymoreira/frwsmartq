<?php

/**
 * Controlador Serviciocaja
 *
 * @access public
 * @version 1.0
 */
class ServiciocajaController extends ApplicationController {

	/**
	 * servicio_id
	 *
	 * @var int
	 */
	public $servicioId;

	/**
	 * caja_id
	 *
	 * @var int
	 */
	public $cajaId;

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
	public function initialize(){
		$this->setPersistance(true);
	}

	/**
	 * AcciÃ³n por defecto del controlador/
	 *
	 */
	public function indexAction(){
		$columnsGrid[]=array('name'=>'Servicio','index'=>'servicio_id');
		$columnsGrid[]=array('name'=>'Caja','index'=>'caja_id');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Serviciocaja/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Serviciocaja
	 *
	 */
	public function editarAction($servicio_id=null,$caja_id=null){

		$filter = new Filter();
		$servicioId = $filter->applyFilter($servicioId,"int");
		$cajaId = $filter->applyFilter($cajaId,"int");
		$serviciocaja = $this->Serviciocaja->findFirst($servicio_id,$caja_id);
		if($serviciocaja){
			Tag::displayTo('servicio_id', $serviciocaja->getServicioId());
			Tag::displayTo('caja_id', $serviciocaja->getCajaId());
			Tag::displayTo('estado', $serviciocaja->getEstado());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Serviciocaja
	 *
	 */
	public function guardarAction($isEdit=false,$servicio_id=null,$caja_id=null){

		$estado = $this->getPostParam("estado", "int");
		$serviciocaja = new Serviciocaja();
		$serviciocaja->setServicioId($servicioId);
		$serviciocaja->setCajaId($cajaId);
		$serviciocaja->setEstado($estado);
		if($serviciocaja->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Serviciocaja
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Serviciocaja->delete($ids[$i])){
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
					case 'servicio_id':
						$condServicio=Utils::toSqlParamSearchGrid('nombre',$abrevoper,$strbusqueda);
						$Servicio=$this->Servicio->find($condServicio);
						if(count($Servicio)>0){
							$arrayIdsServicio=array();
							foreach($Servicio as $fila){
								$arrayIdsServicio[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsServicio);
							$abrevoper='in';
						}
					break;
					case 'caja_id':
						$condCaja=Utils::toSqlParamSearchGrid('numero',$abrevoper,$strbusqueda);
						$Caja=$this->Caja->find($condCaja);
						if(count($Caja)>0){
							$arrayIdsCaja=array();
							foreach($Caja as $fila){
								$arrayIdsCaja[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsCaja);
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
							case 'servicio_id':
								$condServicio=Utils::toSqlParamSearchGrid('nombre',$val['op'],$val['data']);
								$Servicio=$this->Servicio->find($condServicio);
								if(count($Servicio)>0){
									$arrayIdsServicio=array();
									foreach($Servicio as $fila){
										$arrayIdsServicio[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsServicio);
									$val['op']='in';
								}
							break;
							case 'caja_id':
								$condCaja=Utils::toSqlParamSearchGrid('numero',$val['op'],$val['data']);
								$Caja=$this->Caja->find($condCaja);
								if(count($Caja)>0){
									$arrayIdsCaja=array();
									foreach($Caja as $fila){
										$arrayIdsCaja[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsCaja);
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
					case 'servicio_id':
						$condServicio=Utils::toSqlParamSearchGrid('nombre','bw',$v);
						$Servicio=$this->Servicio->find($condServicio);
						if(count($Servicio)>0){
							$arrayIdsServicio=array();
							foreach($Servicio as $fila){
								$arrayIdsServicio[]=$fila->getId();
							}
							$v=join(',',$arrayIdsServicio);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'caja_id':
						$condCaja=Utils::toSqlParamSearchGrid('numero','bw',$v);
						$Caja=$this->Caja->find($condCaja);
						if(count($Caja)>0){
							$arrayIdsCaja=array();
							foreach($Caja as $fila){
								$arrayIdsCaja[]=$fila->getId();
							}
							$v=join(',',$arrayIdsCaja);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'estado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Serviciocaja->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Serviciocaja->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Serviciocaja=$resultado[$i];
			$Servicio=$Serviciocaja->getServicio();
			$Caja=$Serviciocaja->getCaja();
			$jqgrid->rows[]=array('id'=>$Serviciocaja->getServicio_id(),'cell'=>array($Servicio->getNombre(),$Caja->getNumeroCaja(),$Serviciocaja->getEstado()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

