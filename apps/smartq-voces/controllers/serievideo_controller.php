<?php

/**
 * Controlador Serievideo
 *
 * @access public
 * @version 1.0
 */
class SerievideoController extends ApplicationController {

	/**
	 * serie_id
	 *
	 * @var int
	 */
	public $serieId;

	/**
	 * video_id
	 *
	 * @var int
	 */
	public $videoId;

	/**
	 * prioridad
	 *
	 * @var int
	 */
	public $prioridad;

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
		$columnsGrid[]=array('name'=>'Serie','index'=>'serie_id');
		$columnsGrid[]=array('name'=>'Video','index'=>'video_id');
		$columnsGrid[]=array('name'=>'Prioridad','index'=>'prioridad');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Serievideo/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Serievideo
	 *
	 */
	public function editarAction($serie_id=null,$video_id=null){

		$filter = new Filter();
		$serieId = $filter->applyFilter($serieId,"int");
		$videoId = $filter->applyFilter($videoId,"int");
		$serievideo = $this->Serievideo->findFirst($serie_id,$video_id);
		if($serievideo){
			Tag::displayTo('serie_id', $serievideo->getSerieId());
			Tag::displayTo('video_id', $serievideo->getVideoId());
			Tag::displayTo('prioridad', $serievideo->getPrioridad());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Serievideo
	 *
	 */
	public function guardarAction($isEdit=false,$serie_id=null,$video_id=null){

		$prioridad = $this->getPostParam("prioridad", "int");
		$serievideo = new Serievideo();
		$serievideo->setSerieId($serieId);
		$serievideo->setVideoId($videoId);
		$serievideo->setPrioridad($prioridad);
		if($serievideo->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Serievideo
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Serievideo->delete($ids[$i])){
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
					case 'serie_id':
						$condSerie=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Serie=$this->Serie->find($condSerie);
						if(count($Serie)>0){
							$arrayIdsSerie=array();
							foreach($Serie as $fila){
								$arrayIdsSerie[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsSerie);
							$abrevoper='in';
						}
					break;
					case 'video_id':
						$condVideo=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Video=$this->Video->find($condVideo);
						if(count($Video)>0){
							$arrayIdsVideo=array();
							foreach($Video as $fila){
								$arrayIdsVideo[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsVideo);
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
							case 'serie_id':
								$condSerie=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Serie=$this->Serie->find($condSerie);
								if(count($Serie)>0){
									$arrayIdsSerie=array();
									foreach($Serie as $fila){
										$arrayIdsSerie[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsSerie);
									$val['op']='in';
								}
							break;
							case 'video_id':
								$condVideo=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Video=$this->Video->find($condVideo);
								if(count($Video)>0){
									$arrayIdsVideo=array();
									foreach($Video as $fila){
										$arrayIdsVideo[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsVideo);
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
					case 'serie_id':
						$condSerie=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Serie=$this->Serie->find($condSerie);
						if(count($Serie)>0){
							$arrayIdsSerie=array();
							foreach($Serie as $fila){
								$arrayIdsSerie[]=$fila->getId();
							}
							$v=join(',',$arrayIdsSerie);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'video_id':
						$condVideo=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Video=$this->Video->find($condVideo);
						if(count($Video)>0){
							$arrayIdsVideo=array();
							foreach($Video as $fila){
								$arrayIdsVideo[]=$fila->getId();
							}
							$v=join(',',$arrayIdsVideo);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'prioridad':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Serievideo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Serievideo->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Serievideo=$resultado[$i];
			$Serie=$Serievideo->getSerie();
			$Video=$Serievideo->getVideo();
			$jqgrid->rows[]=array('id'=>$Serievideo->getSerie_id(),'cell'=>array($Serie->getId(),$Video->getId(),$Serievideo->getPrioridad()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

