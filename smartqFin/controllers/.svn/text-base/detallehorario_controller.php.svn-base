<?php

/**
 * Controlador Detallehorario
 *
 * @access public
 * @version 1.0
 */
class DetallehorarioController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * horario_id
	 *
	 * @var int
	 */
	public $horarioId;

	/**
	 * dia
	 *
	 * @var string
	 */
	public $dia;

	/**
	 * hora_inicial1
	 *
	 */
	public $horaInicial1;

	/**
	 * hora_final1
	 *
	 */
	public $horaFinal1;

	/**
	 * hora_inicial2
	 *
	 */
	public $horaInicial2;

	/**
	 * hora_final2
	 *
	 */
	public $horaFinal2;

	/**
	 * hora_inicial3
	 *
	 */
	public $horaInicial3;

	/**
	 * hora_final3
	 *
	 */
	public $horaFinal3;

	/**
	 * hora_inicial4
	 *
	 */
	public $horaInicial4;

	/**
	 * hora_final4
	 *
	 */
	public $horaFinal4;

	/**
	 * horas_laborables
	 *
	 */
	public $horasLaborables;

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
		$columnsGrid[]=array('name'=>'Horario','index'=>'horario_id');
		$columnsGrid[]=array('name'=>'Dia','index'=>'dia');
		$columnsGrid[]=array('name'=>'Hora Inicial1','index'=>'hora_inicial1');
		$columnsGrid[]=array('name'=>'Hora Final1','index'=>'hora_final1');
		$columnsGrid[]=array('name'=>'Hora Inicial2','index'=>'hora_inicial2');
		$columnsGrid[]=array('name'=>'Hora Final2','index'=>'hora_final2');
		$columnsGrid[]=array('name'=>'Hora Inicial3','index'=>'hora_inicial3');
		$columnsGrid[]=array('name'=>'Hora Final3','index'=>'hora_final3');
		$columnsGrid[]=array('name'=>'Hora Inicial4','index'=>'hora_inicial4');
		$columnsGrid[]=array('name'=>'Hora Final4','index'=>'hora_final4');
		$columnsGrid[]=array('name'=>'Horas Laborables','index'=>'horas_laborables');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Detallehorario/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Detallehorario
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$detallehorario = $this->Detallehorario->findFirst($id);
		if($detallehorario){
			Tag::displayTo('id', $detallehorario->getId());
			Tag::displayTo('horario_id', $detallehorario->getHorarioId());
			Tag::displayTo('dia', $detallehorario->getDia());
			Tag::displayTo('hora_inicial1', $detallehorario->getHoraInicial1());
			Tag::displayTo('hora_final1', $detallehorario->getHoraFinal1());
			Tag::displayTo('hora_inicial2', $detallehorario->getHoraInicial2());
			Tag::displayTo('hora_final2', $detallehorario->getHoraFinal2());
			Tag::displayTo('hora_inicial3', $detallehorario->getHoraInicial3());
			Tag::displayTo('hora_final3', $detallehorario->getHoraFinal3());
			Tag::displayTo('hora_inicial4', $detallehorario->getHoraInicial4());
			Tag::displayTo('hora_final4', $detallehorario->getHoraFinal4());
			Tag::displayTo('horas_laborables', $detallehorario->getHorasLaborables());
			Tag::displayTo('creacion_at', $detallehorario->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Detallehorario
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$horarioId = $this->getPostParam("horario_id", "int");
		$dia = $this->getPostParam("dia", "striptags", "extraspaces");
		$horaInicial1 = $this->getPostParam("hora_inicial1");
		$horaFinal1 = $this->getPostParam("hora_final1");
		$horaInicial2 = $this->getPostParam("hora_inicial2");
		$horaFinal2 = $this->getPostParam("hora_final2");
		$horaInicial3 = $this->getPostParam("hora_inicial3");
		$horaFinal3 = $this->getPostParam("hora_final3");
		$horaInicial4 = $this->getPostParam("hora_inicial4");
		$horaFinal4 = $this->getPostParam("hora_final4");
		$horasLaborables = $this->getPostParam("horas_laborables");
		$creacionAt = $this->getPostParam("creacion_at");
		$detallehorario = new Detallehorario();
		$detallehorario->setId($id);
		$detallehorario->setHorarioId($horarioId);
		$detallehorario->setDia($dia);
		$detallehorario->setHoraInicial1($horaInicial1);
		$detallehorario->setHoraFinal1($horaFinal1);
		$detallehorario->setHoraInicial2($horaInicial2);
		$detallehorario->setHoraFinal2($horaFinal2);
		$detallehorario->setHoraInicial3($horaInicial3);
		$detallehorario->setHoraFinal3($horaFinal3);
		$detallehorario->setHoraInicial4($horaInicial4);
		$detallehorario->setHoraFinal4($horaFinal4);
		$detallehorario->setHorasLaborables($horasLaborables);
		$detallehorario->setCreacionAt($creacionAt);
		if($detallehorario->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Detallehorario
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Detallehorario->delete($ids[$i])){
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
					case 'horario_id':
						$condHorario=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Horario=$this->Horario->find($condHorario);
						if(count($Horario)>0){
							$arrayIdsHorario=array();
							foreach($Horario as $fila){
								$arrayIdsHorario[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsHorario);
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
							case 'horario_id':
								$condHorario=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Horario=$this->Horario->find($condHorario);
								if(count($Horario)>0){
									$arrayIdsHorario=array();
									foreach($Horario as $fila){
										$arrayIdsHorario[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsHorario);
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
					case 'horario_id':
						$condHorario=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Horario=$this->Horario->find($condHorario);
						if(count($Horario)>0){
							$arrayIdsHorario=array();
							foreach($Horario as $fila){
								$arrayIdsHorario[]=$fila->getId();
							}
							$v=join(',',$arrayIdsHorario);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'dia':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicial1':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_final1':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicial2':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_final2':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicial3':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_final3':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicial4':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_final4':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'horas_laborables':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Detallehorario->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Detallehorario->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Detallehorario=$resultado[$i];
			$Horario=$Detallehorario->getHorario();
			$jqgrid->rows[]=array('id'=>$Detallehorario->getId(),'cell'=>array($Horario->getId(),$Detallehorario->getDia(),$Detallehorario->getHoraInicial1(),$Detallehorario->getHoraFinal1(),$Detallehorario->getHoraInicial2(),$Detallehorario->getHoraFinal2(),$Detallehorario->getHoraInicial3(),$Detallehorario->getHoraFinal3(),$Detallehorario->getHoraInicial4(),$Detallehorario->getHoraFinal4(),$Detallehorario->getHorasLaborables()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

