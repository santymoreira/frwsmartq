<?php

/**
 * Controlador Horariopublicidad
 *
 * @access public
 * @version 1.0
 */
class HorariopublicidadController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * pantalla_id
	 *
	 * @var int
	 */
	public $pantallaId;

	/**
	 * hora_inicio
	 *
	 */
	public $horaInicio;

	/**
	 * hora_fin
	 *
	 */
	public $horaFin;

	/**
	 * tipo
	 *
	 * @var string
	 */
	public $tipo;

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
	 * Acción por defecto del controlador/
	 *
	 */
	public function indexAction(){
		$columnsGrid[]=array('name'=>'Pantalla','index'=>'pantalla_id');
		$columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio');
		$columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin');
		$columnsGrid[]=array('name'=>'Tipo','index'=>'tipo');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Horariopublicidad/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Horariopublicidad
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$horariopublicidad = $this->Horariopublicidad->findFirst($id);
		if($horariopublicidad){
			Tag::displayTo('id', $horariopublicidad->getId());
			Tag::displayTo('pantalla_id', $horariopublicidad->getPantallaId());
			Tag::displayTo('hora_inicio', $horariopublicidad->getHoraInicio());
			Tag::displayTo('hora_fin', $horariopublicidad->getHoraFin());
			Tag::displayTo('tipo', $horariopublicidad->getTipo());
			Tag::displayTo('creacion_at', $horariopublicidad->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Horariopublicidad
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$pantallaId = $this->getPostParam("pantalla_id", "int");
		$horaInicio = $this->getPostParam("hora_inicio");
		$horaFin = $this->getPostParam("hora_fin");
		$tipo = $this->getPostParam("tipo", "striptags", "extraspaces");
		$creacionAt = $this->getPostParam("creacion_at");
		$horariopublicidad = new Horariopublicidad();
		$horariopublicidad->setId($id);
		$horariopublicidad->setPantallaId($pantallaId);
		$horariopublicidad->setHoraInicio($horaInicio);
		$horariopublicidad->setHoraFin($horaFin);
		$horariopublicidad->setTipo($tipo);
		$horariopublicidad->setCreacionAt($creacionAt);
		if($horariopublicidad->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Horariopublicidad
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Horariopublicidad->delete($ids[$i])){
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
		$limite = $this->getPostParam('rows'); // obtener el n�mero de filas que queremos tener en el grid
		$col_orden = $this->getPostParam('sidx'); // Obtener la fila de �ndice - es decir, cuando el usuario haga clic en la columna para ordenar
		$dir_orden = $this->getPostParam('sord'); // obtener la direcci�n de ordenado
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
					case 'pantalla_id':
						$condPantalla=Utils::toSqlParamSearchGrid('descripcion',$abrevoper,$strbusqueda);
						$Pantalla=$this->Pantalla->find($condPantalla);
						if(count($Pantalla)>0){
							$arrayIdsPantalla=array();
							foreach($Pantalla as $fila){
								$arrayIdsPantalla[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsPantalla);
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
							case 'pantalla_id':
								$condPantalla=Utils::toSqlParamSearchGrid('descripcion',$val['op'],$val['data']);
								$Pantalla=$this->Pantalla->find($condPantalla);
								if(count($Pantalla)>0){
									$arrayIdsPantalla=array();
									foreach($Pantalla as $fila){
										$arrayIdsPantalla[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsPantalla);
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
					case 'pantalla_id':
						$condPantalla=Utils::toSqlParamSearchGrid('descripcion','bw',$v);
						$Pantalla=$this->Pantalla->find($condPantalla);
						if(count($Pantalla)>0){
							$arrayIdsPantalla=array();
							foreach($Pantalla as $fila){
								$arrayIdsPantalla[]=$fila->getId();
							}
							$v=join(',',$arrayIdsPantalla);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'hora_inicio':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_fin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'tipo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Horariopublicidad->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Horariopublicidad->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Horariopublicidad=$resultado[$i];
			$Pantalla=$Horariopublicidad->getPantalla();
			$jqgrid->rows[]=array('id'=>$Horariopublicidad->getId(),'cell'=>array($Pantalla->getDescripcion(),$Horariopublicidad->getHoraInicio(),$Horariopublicidad->getHoraFin(),$Horariopublicidad->getTipo()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

