<?php

/**
 * Controlador Uservideo
 *
 * @access public
 * @version 1.0
 */
class UservideoController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * usuario_id
	 *
	 * @var int
	 */
	public $usuarioId;

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
		$columnsGrid[]=array('name'=>'Id','index'=>'id');
		$columnsGrid[]=array('name'=>'Usuario','index'=>'usuario_id');
		$columnsGrid[]=array('name'=>'Video','index'=>'video_id');
		$columnsGrid[]=array('name'=>'Activo','index'=>'activo');
		$columnsGrid[]=array('name'=>'Orden','index'=>'orden');
		$columnsGrid[]=array('name'=>'Reproducir','index'=>'reproducir');
		$columnsGrid[]=array('name'=>'Creacion At','index'=>'creacion_at');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Uservideo/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Uservideo
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$uservideo = $this->Uservideo->findFirst($id);
		if($uservideo){
			Tag::displayTo('id', $uservideo->getId());
			Tag::displayTo('usuario_id', $uservideo->getUsuarioId());
			Tag::displayTo('video_id', $uservideo->getVideoId());
			Tag::displayTo('activo', $uservideo->getActivo());
			Tag::displayTo('orden', $uservideo->getOrden());
			Tag::displayTo('reproducir', $uservideo->getReproducir());
			Tag::displayTo('creacion_at', $uservideo->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Uservideo
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$usuarioId = $this->getPostParam("usuario_id", "int");
		$videoId = $this->getPostParam("video_id", "int");
		$activo = $this->getPostParam("activo", "int");
		$orden = $this->getPostParam("orden", "int");
		$reproducir = $this->getPostParam("reproducir", "int");
		$creacionAt = $this->getPostParam("creacion_at");
		$uservideo = new Uservideo();
		$uservideo->setId($id);
		$uservideo->setUsuarioId($usuarioId);
		$uservideo->setVideoId($videoId);
		$uservideo->setActivo($activo);
		$uservideo->setOrden($orden);
		$uservideo->setReproducir($reproducir);
		$uservideo->setCreacionAt($creacionAt);
		if($uservideo->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Uservideo
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Uservideo->delete($ids[$i])){
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
					case 'usuario_id':
						$condUsuario=Utils::toSqlParamSearchGrid('nombres',$abrevoper,$strbusqueda);
						$Usuario=$this->Usuario->find($condUsuario);
						if(count($Usuario)>0){
							$arrayIdsUsuario=array();
							foreach($Usuario as $fila){
								$arrayIdsUsuario[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsUsuario);
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
							case 'usuario_id':
								$condUsuario=Utils::toSqlParamSearchGrid('nombres',$val['op'],$val['data']);
								$Usuario=$this->Usuario->find($condUsuario);
								if(count($Usuario)>0){
									$arrayIdsUsuario=array();
									foreach($Usuario as $fila){
										$arrayIdsUsuario[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsUsuario);
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
					case 'id':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'usuario_id':
						$condUsuario=Utils::toSqlParamSearchGrid('nombres','bw',$v);
						$Usuario=$this->Usuario->find($condUsuario);
						if(count($Usuario)>0){
							$arrayIdsUsuario=array();
							foreach($Usuario as $fila){
								$arrayIdsUsuario[]=$fila->getId();
							}
							$v=join(',',$arrayIdsUsuario);
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
					case 'creacion_at':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Uservideo->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Uservideo->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Uservideo=$resultado[$i];
			$Usuario=$Uservideo->getUsuario();
			$Video=$Uservideo->getVideo();
			$jqgrid->rows[]=array('id'=>$Uservideo->getId(),'cell'=>array($Uservideo->getId(),$Usuario->getNombres(),$Video->getNombre(),$Uservideo->getActivo(),$Uservideo->getOrden(),$Uservideo->getReproducir(),$Uservideo->getCreacionAt()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

