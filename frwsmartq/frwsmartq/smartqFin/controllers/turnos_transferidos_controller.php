<?php

/**
 * Controlador Turnos_transferidos
 *
 * @access public
 * @version 1.0
 */
class Turnos_transferidosController extends ApplicationController {

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
	 * estado
	 *
	 * @var int
	 */
	public $estado;

	/**
	 * caja_id
	 *
	 * @var int
	 */
	public $cajaId;

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
	 * fecha_inicio_atencion
	 *
	 */
	public $fechaInicioAtencion;

	/**
	 * hora_inicio_atencion
	 *
	 */
	public $horaInicioAtencion;

	/**
	 * fecha_fin_atencion
	 *
	 */
	public $fechaFinAtencion;

	/**
	 * hora_fin_atencion
	 *
	 */
	public $horaFinAtencion;

	/**
	 * duracion
	 *
	 */
	public $duracion;

	/**
	 * rechazado
	 *
	 * @var int
	 */
	public $rechazado;

	/**
	 * calificacion
	 *
	 * @var string
	 */
	public $calificacion;

	/**
	 * permiso_cajas
	 *
	 * @var string
	 */
	public $permisoCajas;

	/**
	 * Inicializador del controlador/
	 *
	 */
	public function initialize(){
		$this->setPersistance(true);
	}

	/**
	 * Acci贸n por defecto del controlador/
	 *
	 */
	public function indexAction(){
		$columnsGrid[]=array('name'=>'Servicio','index'=>'servicio_id');
		$columnsGrid[]=array('name'=>'N煤mero','index'=>'numero');
		$columnsGrid[]=array('name'=>'Fecha Emisi贸n','index'=>'fecha_emision');
		$columnsGrid[]=array('name'=>'Hora Emisi贸n','index'=>'hora_emision');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
		$columnsGrid[]=array('name'=>'M贸dulo','index'=>'caja_id');
		$columnsGrid[]=array('name'=>'Por Atender','index'=>'por_atender');
		$columnsGrid[]=array('name'=>'Atendido','index'=>'atendido');
		$columnsGrid[]=array('name'=>'Fecha Inicio Atenci贸n','index'=>'fecha_inicio_atencion');
		$columnsGrid[]=array('name'=>'Hora Inicio Atenci贸n','index'=>'hora_inicio_atencion');
		$columnsGrid[]=array('name'=>'Fecha Fin Atenci贸n','index'=>'fecha_fin_atencion');
		$columnsGrid[]=array('name'=>'Hora Fin Atenci贸n','index'=>'hora_fin_atencion');
		$columnsGrid[]=array('name'=>'Duraci贸n','index'=>'duracion');
		$columnsGrid[]=array('name'=>'Rechazado','index'=>'rechazado');
		$columnsGrid[]=array('name'=>'Calificaci贸n','index'=>'calificacion');
		$columnsGrid[]=array('name'=>'Permiso Cajas','index'=>'permiso_cajas');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un TurnosTransferidos/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el TurnosTransferidos
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$turnosTransferidos = $this->Turnos_transferidos->findFirst($id);
		if($turnos_transferidos){
			Tag::displayTo('id', $turnosTransferidos->getId());
			Tag::displayTo('servicio_id', $turnosTransferidos->getServicioId());
			Tag::displayTo('numero', $turnosTransferidos->getNumero());
			Tag::displayTo('fecha_emision', $turnosTransferidos->getFechaEmision());
			Tag::displayTo('hora_emision', $turnosTransferidos->getHoraEmision());
			Tag::displayTo('estado', $turnosTransferidos->getEstado());
			Tag::displayTo('caja_id', $turnosTransferidos->getCajaId());
			Tag::displayTo('por_atender', $turnosTransferidos->getPorAtender());
			Tag::displayTo('atendido', $turnosTransferidos->getAtendido());
			Tag::displayTo('fecha_inicio_atencion', $turnosTransferidos->getFechaInicioAtencion());
			Tag::displayTo('hora_inicio_atencion', $turnosTransferidos->getHoraInicioAtencion());
			Tag::displayTo('fecha_fin_atencion', $turnosTransferidos->getFechaFinAtencion());
			Tag::displayTo('hora_fin_atencion', $turnosTransferidos->getHoraFinAtencion());
			Tag::displayTo('duracion', $turnosTransferidos->getDuracion());
			Tag::displayTo('rechazado', $turnosTransferidos->getRechazado());
			Tag::displayTo('calificacion', $turnosTransferidos->getCalificacion());
			Tag::displayTo('permiso_cajas', $turnosTransferidos->getPermisoCajas());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el TurnosTransferidos
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$servicioId = $this->getPostParam("servicio_id", "int");
		$numero = $this->getPostParam("numero", "int");
		$fechaEmision = $this->getPostParam("fecha_emision");
		$horaEmision = $this->getPostParam("hora_emision");
		$estado = $this->getPostParam("estado", "int");
		$cajaId = $this->getPostParam("caja_id", "int");
		$porAtender = $this->getPostParam("por_atender", "int");
		$atendido = $this->getPostParam("atendido", "int");
		$fechaInicioAtencion = $this->getPostParam("fecha_inicio_atencion");
		$horaInicioAtencion = $this->getPostParam("hora_inicio_atencion");
		$fechaFinAtencion = $this->getPostParam("fecha_fin_atencion");
		$horaFinAtencion = $this->getPostParam("hora_fin_atencion");
		$duracion = $this->getPostParam("duracion");
		$rechazado = $this->getPostParam("rechazado", "int");
		$calificacion = $this->getPostParam("calificacion", "striptags", "extraspaces");
		$permisoCajas = $this->getPostParam("permiso_cajas", "striptags", "extraspaces");
		$turnosTransferidos = new TurnosTransferidos();
		$turnosTransferidos->setId($id);
		$turnosTransferidos->setServicioId($servicioId);
		$turnosTransferidos->setNumero($numero);
		$turnosTransferidos->setFechaEmision($fechaEmision);
		$turnosTransferidos->setHoraEmision($horaEmision);
		$turnosTransferidos->setEstado($estado);
		$turnosTransferidos->setCajaId($cajaId);
		$turnosTransferidos->setPorAtender($porAtender);
		$turnosTransferidos->setAtendido($atendido);
		$turnosTransferidos->setFechaInicioAtencion($fechaInicioAtencion);
		$turnosTransferidos->setHoraInicioAtencion($horaInicioAtencion);
		$turnosTransferidos->setFechaFinAtencion($fechaFinAtencion);
		$turnosTransferidos->setHoraFinAtencion($horaFinAtencion);
		$turnosTransferidos->setDuracion($duracion);
		$turnosTransferidos->setRechazado($rechazado);
		$turnosTransferidos->setCalificacion($calificacion);
		$turnosTransferidos->setPermisoCajas($permisoCajas);
		if($turnosTransferidos->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con 茅xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el TurnosTransferidos
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Turnos_transferidos->delete($ids[$i])){
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
		 if($buscar=='true'){ //verificamos si la busqueda es activada
			if($strbusqueda!=''){    // construccion de la cadena de condicion para la busqueda normal 
				switch($campoBusqueda){
					case 'servicio_id':
						$condServicio=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
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
						$condCaja=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
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
								$condServicio=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
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
								$condCaja=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
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
						$condServicio=Utils::toSqlParamSearchGrid('id','bw',$v);
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
					case 'estado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'caja_id':
						$condCaja=Utils::toSqlParamSearchGrid('id','bw',$v);
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
					case 'por_atender':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'atendido':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_inicio_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicio_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_fin_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_fin_atencion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'duracion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'rechazado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'calificacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'permiso_cajas':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Turnos_transferidos->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Turnos_transferidos->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Turnos_transferidos=$resultado[$i];
			$Servicio=$Turnos_transferidos->getServicio();
			$Caja=$Turnos_transferidos->getCaja();
			$jqgrid->rows[]=array('id'=>$Turnos_transferidos->getId(),'cell'=>array($Servicio->getId(),$Turnos_transferidos->getNumero(),$Turnos_transferidos->getFechaEmision(),$Turnos_transferidos->getHoraEmision(),$Turnos_transferidos->getEstado(),$Caja->getId(),$Turnos_transferidos->getPorAtender(),$Turnos_transferidos->getAtendido(),$Turnos_transferidos->getFechaInicioAtencion(),$Turnos_transferidos->getHoraInicioAtencion(),$Turnos_transferidos->getFechaFinAtencion(),$Turnos_transferidos->getHoraFinAtencion(),$Turnos_transferidos->getDuracion(),$Turnos_transferidos->getRechazado(),$Turnos_transferidos->getCalificacion(),$Turnos_transferidos->getPermisoCajas()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

