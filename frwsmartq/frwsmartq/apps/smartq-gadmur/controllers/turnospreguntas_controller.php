<?php

/**
 * Controlador Turnospreguntas
 *
 * @access public
 * @version 1.0
 */
class TurnospreguntasController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * preguntas_id
	 *
	 * @var int
	 */
	public $preguntasId;

	/**
	 * caja_id
	 *
	 * @var int
	 */
	public $cajaId;

	/**
	 * turnos_id
	 *
	 * @var int
	 */
	public $turnosId;

	/**
	 * puntuacion
	 *
	 * @var int
	 */
	public $puntuacion;

	/**
	 * fecha
	 *
	 */
	public $fecha;

	/**
	 * hora
	 *
	 */
	public $hora;

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
		$columnsGrid[]=array('name'=>'Preguntas','index'=>'preguntas_id');
		$columnsGrid[]=array('name'=>'Caja','index'=>'caja_id');
		$columnsGrid[]=array('name'=>'Turnos','index'=>'turnos_id');
		$columnsGrid[]=array('name'=>'Puntuacion','index'=>'puntuacion');
		$columnsGrid[]=array('name'=>'Fecha','index'=>'fecha');
		$columnsGrid[]=array('name'=>'Hora','index'=>'hora');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Turnospreguntas/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Turnospreguntas
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$turnospreguntas = $this->Turnospreguntas->findFirst($id);
		if($turnospreguntas){
			Tag::displayTo('id', $turnospreguntas->getId());
			Tag::displayTo('preguntas_id', $turnospreguntas->getPreguntasId());
			Tag::displayTo('caja_id', $turnospreguntas->getCajaId());
			Tag::displayTo('turnos_id', $turnospreguntas->getTurnosId());
			Tag::displayTo('puntuacion', $turnospreguntas->getPuntuacion());
			Tag::displayTo('fecha', $turnospreguntas->getFecha());
			Tag::displayTo('hora', $turnospreguntas->getHora());
			Tag::displayTo('creacion_at', $turnospreguntas->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Turnospreguntas
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$preguntasId = $this->getPostParam("preguntas_id", "int");
		$cajaId = $this->getPostParam("caja_id", "int");
		$turnosId = $this->getPostParam("turnos_id", "int");
		$puntuacion = $this->getPostParam("puntuacion", "int");
		$fecha = $this->getPostParam("fecha");
		$hora = $this->getPostParam("hora");
		$creacionAt = $this->getPostParam("creacion_at");
		$turnospreguntas = new Turnospreguntas();
		$turnospreguntas->setId($id);
		$turnospreguntas->setPreguntasId($preguntasId);
		$turnospreguntas->setCajaId($cajaId);
		$turnospreguntas->setTurnosId($turnosId);
		$turnospreguntas->setPuntuacion($puntuacion);
		$turnospreguntas->setFecha($fecha);
		$turnospreguntas->setHora($hora);
		$turnospreguntas->setCreacionAt($creacionAt);
		if($turnospreguntas->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Turnospreguntas
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Turnospreguntas->delete($ids[$i])){
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
					case 'preguntas_id':
						$condPreguntas=Utils::toSqlParamSearchGrid('nom_pregunta',$abrevoper,$strbusqueda);
						$Preguntas=$this->Preguntas->find($condPreguntas);
						if(count($Preguntas)>0){
							$arrayIdsPreguntas=array();
							foreach($Preguntas as $fila){
								$arrayIdsPreguntas[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsPreguntas);
							$abrevoper='in';
						}
					break;
					case 'caja_id':
						$condCaja=Utils::toSqlParamSearchGrid('numero_caja',$abrevoper,$strbusqueda);
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
					case 'turnos_id':
						$condTurnos=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Turnos=$this->Turnos->find($condTurnos);
						if(count($Turnos)>0){
							$arrayIdsTurnos=array();
							foreach($Turnos as $fila){
								$arrayIdsTurnos[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsTurnos);
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
							case 'preguntas_id':
								$condPreguntas=Utils::toSqlParamSearchGrid('nom_pregunta',$val['op'],$val['data']);
								$Preguntas=$this->Preguntas->find($condPreguntas);
								if(count($Preguntas)>0){
									$arrayIdsPreguntas=array();
									foreach($Preguntas as $fila){
										$arrayIdsPreguntas[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsPreguntas);
									$val['op']='in';
								}
							break;
							case 'caja_id':
								$condCaja=Utils::toSqlParamSearchGrid('numero_caja',$val['op'],$val['data']);
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
							case 'turnos_id':
								$condTurnos=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Turnos=$this->Turnos->find($condTurnos);
								if(count($Turnos)>0){
									$arrayIdsTurnos=array();
									foreach($Turnos as $fila){
										$arrayIdsTurnos[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsTurnos);
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
					case 'preguntas_id':
						$condPreguntas=Utils::toSqlParamSearchGrid('nom_pregunta','bw',$v);
						$Preguntas=$this->Preguntas->find($condPreguntas);
						if(count($Preguntas)>0){
							$arrayIdsPreguntas=array();
							foreach($Preguntas as $fila){
								$arrayIdsPreguntas[]=$fila->getId();
							}
							$v=join(',',$arrayIdsPreguntas);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'caja_id':
						$condCaja=Utils::toSqlParamSearchGrid('numero_caja','bw',$v);
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
					case 'turnos_id':
						$condTurnos=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Turnos=$this->Turnos->find($condTurnos);
						if(count($Turnos)>0){
							$arrayIdsTurnos=array();
							foreach($Turnos as $fila){
								$arrayIdsTurnos[]=$fila->getId();
							}
							$v=join(',',$arrayIdsTurnos);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'puntuacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Turnospreguntas->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Turnospreguntas->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Turnospreguntas=$resultado[$i];
			$Preguntas=$Turnospreguntas->getPreguntas();
			$Caja=$Turnospreguntas->getCaja();
			$Turnos=$Turnospreguntas->getTurnos();
			$jqgrid->rows[]=array('id'=>$Turnospreguntas->getId(),'cell'=>array($Preguntas->getNom_pregunta(),$Caja->getNumero_caja(),$Turnos->getId(),$Turnospreguntas->getPuntuacion(),$Turnospreguntas->getFecha(),$Turnospreguntas->getHora()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

