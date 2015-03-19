<?php

/**
 * Controlador Turnos
 *
 * @access public
 * @version 1.0
 */
class TurnosController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * servicio_id
	 *
	 * @var int
	 */
	public $servicioId;

	/**
	 * numero
	 *
	 * @var int
	 */
	public $numero;

	/**
	 * fecha_emision
	 *
	 */
	public $fechaEmision;

	/**
	 * hora_emision
	 *
	 */
	public $horaEmision;

	/**
	 * caja
	 *
	 * @var int
	 */
	public $caja;

	/**
	 * por_atender
	 *
	 * @var int
	 */
	public $porAtender;

	/**
	 * atendido
	 *
	 * @var int
	 */
	public $atendido;

	/**
	 * fecha_atencion
	 *
	 */
	public $fechaAtencion;

	/**
	 * hora_atencion
	 *
	 */
	public $horaAtencion;

	/**
	 * rechazado
	 *
	 * @var int
	 */
	public $rechazado;

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
		$columnsGrid[]=array('name'=>'Numero','index'=>'numero');
		$columnsGrid[]=array('name'=>'Fecha Emision','index'=>'fecha_emision');
		$columnsGrid[]=array('name'=>'Hora Emision','index'=>'hora_emision');
		$columnsGrid[]=array('name'=>'Caja','index'=>'caja');
		$columnsGrid[]=array('name'=>'Por Atender','index'=>'por_atender');
		$columnsGrid[]=array('name'=>'Atendido','index'=>'atendido');
		$columnsGrid[]=array('name'=>'Fecha Atencion','index'=>'fecha_atencion');
		$columnsGrid[]=array('name'=>'Hora Atencion','index'=>'hora_atencion');
		$columnsGrid[]=array('name'=>'Rechazado','index'=>'rechazado');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Turnos/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Turnos
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$turnos = $this->Turnos->findFirst($id);
		if($turnos){
			Tag::displayTo('id', $turnos->getId());
			Tag::displayTo('servicio_id', $turnos->getServicioId());
			Tag::displayTo('numero', $turnos->getNumero());
			Tag::displayTo('fecha_emision', $turnos->getFechaEmision());
			Tag::displayTo('hora_emision', $turnos->getHoraEmision());
			Tag::displayTo('caja', $turnos->getCaja());
			Tag::displayTo('por_atender', $turnos->getPorAtender());
			Tag::displayTo('atendido', $turnos->getAtendido());
			Tag::displayTo('fecha_atencion', $turnos->getFechaAtencion());
			Tag::displayTo('hora_atencion', $turnos->getHoraAtencion());
			Tag::displayTo('rechazado', $turnos->getRechazado());
			Tag::displayTo('estado', $turnos->getEstado());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Turnos
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$servicioId = $this->getPostParam("servicio_id", "int");
		$numero = $this->getPostParam("numero", "int");
		$fechaEmision = $this->getPostParam("fecha_emision");
		$horaEmision = $this->getPostParam("hora_emision");
		$caja = $this->getPostParam("caja", "int");
		$porAtender = $this->getPostParam("por_atender", "int");
		$atendido = $this->getPostParam("atendido", "int");
		$fechaAtencion = $this->getPostParam("fecha_atencion");
		$horaAtencion = $this->getPostParam("hora_atencion");
		$rechazado = $this->getPostParam("rechazado", "int");
		$estado = $this->getPostParam("estado", "int");
		$turnos = new Turnos();
		$turnos->setId($id);
		$turnos->setServicioId($servicioId);
		$turnos->setNumero($numero);
		$turnos->setFechaEmision($fechaEmision);
		$turnos->setHoraEmision($horaEmision);
		$turnos->setCaja($caja);
		$turnos->setPorAtender($porAtender);
		$turnos->setAtendido($atendido);
		$turnos->setFechaAtencion($fechaAtencion);
		$turnos->setHoraAtencion($horaAtencion);
		$turnos->setRechazado($rechazado);
		$turnos->setEstado($estado);
		if($turnos->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Turnos
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Turnos->delete($ids[$i])){
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
					case 'numero':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_emision':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_emision':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'caja':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'por_atender':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'atendido':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'rechazado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'estado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Turnos->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Turnos->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Turnos=$resultado[$i];
			$Servicio=$Turnos->getServicio();
			$jqgrid->rows[]=array('id'=>$Turnos->getId(),'cell'=>array($Servicio->getNombre(),$Turnos->getNumero(),$Turnos->getFechaEmision(),$Turnos->getHoraEmision(),$Turnos->getCaja(),$Turnos->getPorAtender(),$Turnos->getAtendido(),$Turnos->getFechaAtencion(),$Turnos->getHoraAtencion(),$Turnos->getRechazado(),$Turnos->getEstado()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

