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
	 * hora_transferido
	 *
	 */
	public $horaTransferido;

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
	 * letra
	 *
	 * @var string
	 */
	public $letra;

	/**
	 * remitente
	 *
	 * @var string
	 */
	public $remitente;

	/**
	 * ubicacion_id
	 *
	 * @var int
	 */
	public $ubicacionId;

	/**
	 * tipo
	 *
	 * @var string
	 */
	public $tipo;

	/**
	 * fecha_atender
	 *
	 */
	public $fechaAtender;

	/**
	 * hora_atender
	 *
	 */
	public $horaAtender;

	/**
	 * adm_revisado
	 *
	 * @var int
	 */
	public $admRevisado;

	/**
	 * id_username
	 *
	 * @var int
	 */
	public $idUsername;

	/**
	 * id_user_atiende
	 *
	 * @var int
	 */
	public $idUserAtiende;

	/**
	 * id_user_transfiere
	 *
	 * @var int
	 */
	public $idUserTransfiere;

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
		$columnsGrid[]=array('name'=>'Id','index'=>'id');
		$columnsGrid[]=array('name'=>'Servicio','index'=>'servicio_id');
		$columnsGrid[]=array('name'=>'Numero','index'=>'numero');
		$columnsGrid[]=array('name'=>'Fecha Emision','index'=>'fecha_emision');
		$columnsGrid[]=array('name'=>'Hora Emision','index'=>'hora_emision');
		$columnsGrid[]=array('name'=>'Hora Transferido','index'=>'hora_transferido');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
		$columnsGrid[]=array('name'=>'Caja','index'=>'caja_id');
		$columnsGrid[]=array('name'=>'Por Atender','index'=>'por_atender');
		$columnsGrid[]=array('name'=>'Atendido','index'=>'atendido');
		$columnsGrid[]=array('name'=>'Fecha Inicio Atencion','index'=>'fecha_inicio_atencion');
		$columnsGrid[]=array('name'=>'Hora Inicio Atencion','index'=>'hora_inicio_atencion');
		$columnsGrid[]=array('name'=>'Fecha Fin Atencion','index'=>'fecha_fin_atencion');
		$columnsGrid[]=array('name'=>'Hora Fin Atencion','index'=>'hora_fin_atencion');
		$columnsGrid[]=array('name'=>'Duracion','index'=>'duracion');
		$columnsGrid[]=array('name'=>'Rechazado','index'=>'rechazado');
		$columnsGrid[]=array('name'=>'Calificacion','index'=>'calificacion');
		$columnsGrid[]=array('name'=>'Permiso Cajas','index'=>'permiso_cajas');
		$columnsGrid[]=array('name'=>'Letra','index'=>'letra');
		$columnsGrid[]=array('name'=>'Remitente','index'=>'remitente');
		$columnsGrid[]=array('name'=>'Ubicacion','index'=>'ubicacion_id');
		$columnsGrid[]=array('name'=>'Tipo','index'=>'tipo');
		$columnsGrid[]=array('name'=>'Fecha Atender','index'=>'fecha_atender');
		$columnsGrid[]=array('name'=>'Hora Atender','index'=>'hora_atender');
		$columnsGrid[]=array('name'=>'Adm Revisado','index'=>'adm_revisado');
		$columnsGrid[]=array('name'=>'Id Username','index'=>'id_username');
		$columnsGrid[]=array('name'=>'Id User Atiende','index'=>'id_user_atiende');
		$columnsGrid[]=array('name'=>'Id User Transfiere','index'=>'id_user_transfiere');
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
			Tag::displayTo('hora_transferido', $turnosTransferidos->getHoraTransferido());
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
			Tag::displayTo('letra', $turnosTransferidos->getLetra());
			Tag::displayTo('remitente', $turnosTransferidos->getRemitente());
			Tag::displayTo('ubicacion_id', $turnosTransferidos->getUbicacionId());
			Tag::displayTo('tipo', $turnosTransferidos->getTipo());
			Tag::displayTo('fecha_atender', $turnosTransferidos->getFechaAtender());
			Tag::displayTo('hora_atender', $turnosTransferidos->getHoraAtender());
			Tag::displayTo('adm_revisado', $turnosTransferidos->getAdmRevisado());
			Tag::displayTo('id_username', $turnosTransferidos->getIdUsername());
			Tag::displayTo('id_user_atiende', $turnosTransferidos->getIdUserAtiende());
			Tag::displayTo('id_user_transfiere', $turnosTransferidos->getIdUserTransfiere());
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
		$horaTransferido = $this->getPostParam("hora_transferido");
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
		$letra = $this->getPostParam("letra", "striptags", "extraspaces");
		$remitente = $this->getPostParam("remitente", "striptags", "extraspaces");
		$ubicacionId = $this->getPostParam("ubicacion_id", "int");
		$tipo = $this->getPostParam("tipo", "striptags", "extraspaces");
		$fechaAtender = $this->getPostParam("fecha_atender");
		$horaAtender = $this->getPostParam("hora_atender");
		$admRevisado = $this->getPostParam("adm_revisado", "int");
		$idUsername = $this->getPostParam("id_username", "int");
		$idUserAtiende = $this->getPostParam("id_user_atiende", "int");
		$idUserTransfiere = $this->getPostParam("id_user_transfiere", "int");
		$turnosTransferidos = new TurnosTransferidos();
		$turnosTransferidos->setId($id);
		$turnosTransferidos->setServicioId($servicioId);
		$turnosTransferidos->setNumero($numero);
		$turnosTransferidos->setFechaEmision($fechaEmision);
		$turnosTransferidos->setHoraEmision($horaEmision);
		$turnosTransferidos->setHoraTransferido($horaTransferido);
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
		$turnosTransferidos->setLetra($letra);
		$turnosTransferidos->setRemitente($remitente);
		$turnosTransferidos->setUbicacionId($ubicacionId);
		$turnosTransferidos->setTipo($tipo);
		$turnosTransferidos->setFechaAtender($fechaAtender);
		$turnosTransferidos->setHoraAtender($horaAtender);
		$turnosTransferidos->setAdmRevisado($admRevisado);
		$turnosTransferidos->setIdUsername($idUsername);
		$turnosTransferidos->setIdUserAtiende($idUserAtiende);
		$turnosTransferidos->setIdUserTransfiere($idUserTransfiere);
		if($turnosTransferidos->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
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
					case 'ubicacion_id':
						$condUbicacion=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Ubicacion=$this->Ubicacion->find($condUbicacion);
						if(count($Ubicacion)>0){
							$arrayIdsUbicacion=array();
							foreach($Ubicacion as $fila){
								$arrayIdsUbicacion[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsUbicacion);
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
							case 'ubicacion_id':
								$condUbicacion=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Ubicacion=$this->Ubicacion->find($condUbicacion);
								if(count($Ubicacion)>0){
									$arrayIdsUbicacion=array();
									foreach($Ubicacion as $fila){
										$arrayIdsUbicacion[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsUbicacion);
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
					case 'id':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
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
					case 'hora_transferido':
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
					case 'letra':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'remitente':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'ubicacion_id':
						$condUbicacion=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Ubicacion=$this->Ubicacion->find($condUbicacion);
						if(count($Ubicacion)>0){
							$arrayIdsUbicacion=array();
							foreach($Ubicacion as $fila){
								$arrayIdsUbicacion[]=$fila->getId();
							}
							$v=join(',',$arrayIdsUbicacion);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'tipo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_atender':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_atender':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'adm_revisado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'id_username':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'id_user_atiende':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'id_user_transfiere':
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
			$Ubicacion=$Turnos_transferidos->getUbicacion();
			$jqgrid->rows[]=array('id'=>$Turnos_transferidos->getId(),'cell'=>array($Turnos_transferidos->getId(),$Servicio->getId(),$Turnos_transferidos->getNumero(),$Turnos_transferidos->getFechaEmision(),$Turnos_transferidos->getHoraEmision(),$Turnos_transferidos->getHoraTransferido(),$Turnos_transferidos->getEstado(),$Caja->getId(),$Turnos_transferidos->getPorAtender(),$Turnos_transferidos->getAtendido(),$Turnos_transferidos->getFechaInicioAtencion(),$Turnos_transferidos->getHoraInicioAtencion(),$Turnos_transferidos->getFechaFinAtencion(),$Turnos_transferidos->getHoraFinAtencion(),$Turnos_transferidos->getDuracion(),$Turnos_transferidos->getRechazado(),$Turnos_transferidos->getCalificacion(),$Turnos_transferidos->getPermisoCajas(),$Turnos_transferidos->getLetra(),$Turnos_transferidos->getRemitente(),$Ubicacion->getId(),$Turnos_transferidos->getTipo(),$Turnos_transferidos->getFechaAtender(),$Turnos_transferidos->getHoraAtender(),$Turnos_transferidos->getAdmRevisado(),$Turnos_transferidos->getIdUsername(),$Turnos_transferidos->getIdUserAtiende(),$Turnos_transferidos->getIdUserTransfiere()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

