<?php

/**
 * Controlador Pantallavideos
 *
 * @access public
 * @version 1.0
 */
class PantallavideosController extends ApplicationController {

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
	 * video_id
	 *
	 * @var int
	 */
	public $videoId;

	/**
	 * activo
	 *
	 * @var int
	 */
	public $activo;

	/**
	 * orden
	 *
	 * @var int
	 */
	public $orden;

	/**
	 * reproducir
	 *
	 * @var int
	 */
	public $reproducir;

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
		$columnsGrid[]=array('name'=>'Video','index'=>'video_id');
		$columnsGrid[]=array('name'=>'Activo','index'=>'activo');
		$columnsGrid[]=array('name'=>'Orden','index'=>'orden');
		$columnsGrid[]=array('name'=>'Reproducir','index'=>'reproducir');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Pantallavideos/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Pantallavideos
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$pantallavideos = $this->Pantallavideos->findFirst($id);
		if($pantallavideos){
			Tag::displayTo('id', $pantallavideos->getId());
			Tag::displayTo('pantalla_id', $pantallavideos->getPantallaId());
			Tag::displayTo('video_id', $pantallavideos->getVideoId());
			Tag::displayTo('activo', $pantallavideos->getActivo());
			Tag::displayTo('orden', $pantallavideos->getOrden());
			Tag::displayTo('reproducir', $pantallavideos->getReproducir());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Pantallavideos
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$pantallaId = $this->getPostParam("pantalla_id", "int");
		$videoId = $this->getPostParam("video_id", "int");
		$activo = $this->getPostParam("activo", "int");
		$orden = $this->getPostParam("orden", "int");
		$reproducir = $this->getPostParam("reproducir", "int");
		$pantallavideos = new Pantallavideos();
		$pantallavideos->setId($id);
		$pantallavideos->setPantallaId($pantallaId);
		$pantallavideos->setVideoId($videoId);
		$pantallavideos->setActivo($activo);
		$pantallavideos->setOrden($orden);
		$pantallavideos->setReproducir($reproducir);
		if($pantallavideos->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Pantallavideos
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Pantallavideos->delete($ids[$i])){
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
						$condPantalla=Utils::toSqlParamSearchGrid('numero',$abrevoper,$strbusqueda);
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
					case 'video_id':
						$condVideo=Utils::toSqlParamSearchGrid('nombre',$abrevoper,$strbusqueda);
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
							case 'pantalla_id':
								$condPantalla=Utils::toSqlParamSearchGrid('numero',$val['op'],$val['data']);
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
							case 'video_id':
								$condVideo=Utils::toSqlParamSearchGrid('nombre',$val['op'],$val['data']);
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
					case 'pantalla_id':
						$condPantalla=Utils::toSqlParamSearchGrid('numero','bw',$v);
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
					case 'video_id':
						$condVideo=Utils::toSqlParamSearchGrid('nombre','bw',$v);
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
					case 'activo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'orden':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'reproducir':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Pantallavideos->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Pantallavideos->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Pantallavideos=$resultado[$i];
			$Pantalla=$Pantallavideos->getPantalla();
			$Video=$Pantallavideos->getVideo();
			$jqgrid->rows[]=array('id'=>$Pantallavideos->getId(),'cell'=>array($Pantalla->getNumero(),$Video->getNombre(),$Pantallavideos->getActivo(),$Pantallavideos->getOrden(),$Pantallavideos->getReproducir()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

