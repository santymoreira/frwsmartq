<?php

/**
 * Controlador Pantallafeed
 *
 * @access public
 * @version 1.0
 */
class PantallafeedController extends ApplicationController {

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
	 * feed_id
	 *
	 * @var int
	 */
	public $feedId;

	/**
	 * categoriafeeds_id
	 *
	 * @var int
	 */
	public $categoriafeedsId;

	/**
	 * publicar_icono
	 *
	 * @var int
	 */
	public $publicarIcono;

	/**
	 * publicar_titulo
	 *
	 * @var int
	 */
	public $publicarTitulo;

	/**
	 * publicar_fecha
	 *
	 * @var int
	 */
	public $publicarFecha;

	/**
	 * publicar_hora
	 *
	 * @var int
	 */
	public $publicarHora;

	/**
	 * publicar_contenido
	 *
	 * @var int
	 */
	public $publicarContenido;

	/**
	 * limite_items
	 *
	 * @var int
	 */
	public $limiteItems;

	/**
	 * fecha_inicio
	 *
	 */
	public $fechaInicio;

	/**
	 * fecha_fin
	 *
	 */
	public $fechaFin;

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
		$columnsGrid[]=array('name'=>'Pantalla','index'=>'pantalla_id');
		$columnsGrid[]=array('name'=>'Feed','index'=>'feed_id');
		$columnsGrid[]=array('name'=>'Categoriafeeds','index'=>'categoriafeeds_id');
		$columnsGrid[]=array('name'=>'Publicar Icono','index'=>'publicar_icono');
		$columnsGrid[]=array('name'=>'Publicar Titulo','index'=>'publicar_titulo');
		$columnsGrid[]=array('name'=>'Publicar Fecha','index'=>'publicar_fecha');
		$columnsGrid[]=array('name'=>'Publicar Hora','index'=>'publicar_hora');
		$columnsGrid[]=array('name'=>'Publicar Contenido','index'=>'publicar_contenido');
		$columnsGrid[]=array('name'=>'LÃ­mite Items','index'=>'limite_items');
		$columnsGrid[]=array('name'=>'Fecha Inicio','index'=>'fecha_inicio');
		$columnsGrid[]=array('name'=>'Fecha Fin','index'=>'fecha_fin');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Pantallafeed/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Pantallafeed
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$pantallafeed = $this->Pantallafeed->findFirst($id);
		if($pantallafeed){
			Tag::displayTo('id', $pantallafeed->getId());
			Tag::displayTo('pantalla_id', $pantallafeed->getPantallaId());
			Tag::displayTo('feed_id', $pantallafeed->getFeedId());
			Tag::displayTo('categoriafeeds_id', $pantallafeed->getCategoriafeedsId());
			Tag::displayTo('publicar_icono', $pantallafeed->getPublicarIcono());
			Tag::displayTo('publicar_titulo', $pantallafeed->getPublicarTitulo());
			Tag::displayTo('publicar_fecha', $pantallafeed->getPublicarFecha());
			Tag::displayTo('publicar_hora', $pantallafeed->getPublicarHora());
			Tag::displayTo('publicar_contenido', $pantallafeed->getPublicarContenido());
			Tag::displayTo('limite_items', $pantallafeed->getLimiteItems());
			Tag::displayTo('fecha_inicio', $pantallafeed->getFechaInicio());
			Tag::displayTo('fecha_fin', $pantallafeed->getFechaFin());
			Tag::displayTo('creacion_at', $pantallafeed->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Pantallafeed
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$pantallaId = $this->getPostParam("pantalla_id", "int");
		$feedId = $this->getPostParam("feed_id", "int");
		$categoriafeedsId = $this->getPostParam("categoriafeeds_id", "int");
		$publicarIcono = $this->getPostParam("publicar_icono", "int");
		$publicarTitulo = $this->getPostParam("publicar_titulo", "int");
		$publicarFecha = $this->getPostParam("publicar_fecha", "int");
		$publicarHora = $this->getPostParam("publicar_hora", "int");
		$publicarContenido = $this->getPostParam("publicar_contenido", "int");
		$limiteItems = $this->getPostParam("limite_items", "int");
		$fechaInicio = $this->getPostParam("fecha_inicio");
		$fechaFin = $this->getPostParam("fecha_fin");
		$creacionAt = $this->getPostParam("creacion_at");
		$pantallafeed = new Pantallafeed();
		$pantallafeed->setId($id);
		$pantallafeed->setPantallaId($pantallaId);
		$pantallafeed->setFeedId($feedId);
		$pantallafeed->setCategoriafeedsId($categoriafeedsId);
		$pantallafeed->setPublicarIcono($publicarIcono);
		$pantallafeed->setPublicarTitulo($publicarTitulo);
		$pantallafeed->setPublicarFecha($publicarFecha);
		$pantallafeed->setPublicarHora($publicarHora);
		$pantallafeed->setPublicarContenido($publicarContenido);
		$pantallafeed->setLimiteItems($limiteItems);
		$pantallafeed->setFechaInicio($fechaInicio);
		$pantallafeed->setFechaFin($fechaFin);
		$pantallafeed->setCreacionAt($creacionAt);
		if($pantallafeed->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con Ã©xito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Pantallafeed
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Pantallafeed->delete($ids[$i])){
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
					case 'feed_id':
						$condFeed=Utils::toSqlParamSearchGrid('url_feed',$abrevoper,$strbusqueda);
						$Feed=$this->Feed->find($condFeed);
						if(count($Feed)>0){
							$arrayIdsFeed=array();
							foreach($Feed as $fila){
								$arrayIdsFeed[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsFeed);
							$abrevoper='in';
						}
					break;
					case 'categoriafeeds_id':
						$condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria',$abrevoper,$strbusqueda);
						$Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
						if(count($Categoriafeeds)>0){
							$arrayIdsCategoriafeeds=array();
							foreach($Categoriafeeds as $fila){
								$arrayIdsCategoriafeeds[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsCategoriafeeds);
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
							case 'feed_id':
								$condFeed=Utils::toSqlParamSearchGrid('url_feed',$val['op'],$val['data']);
								$Feed=$this->Feed->find($condFeed);
								if(count($Feed)>0){
									$arrayIdsFeed=array();
									foreach($Feed as $fila){
										$arrayIdsFeed[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsFeed);
									$val['op']='in';
								}
							break;
							case 'categoriafeeds_id':
								$condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria',$val['op'],$val['data']);
								$Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
								if(count($Categoriafeeds)>0){
									$arrayIdsCategoriafeeds=array();
									foreach($Categoriafeeds as $fila){
										$arrayIdsCategoriafeeds[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsCategoriafeeds);
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
					case 'feed_id':
						$condFeed=Utils::toSqlParamSearchGrid('url_feed','bw',$v);
						$Feed=$this->Feed->find($condFeed);
						if(count($Feed)>0){
							$arrayIdsFeed=array();
							foreach($Feed as $fila){
								$arrayIdsFeed[]=$fila->getId();
							}
							$v=join(',',$arrayIdsFeed);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'categoriafeeds_id':
						$condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria','bw',$v);
						$Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
						if(count($Categoriafeeds)>0){
							$arrayIdsCategoriafeeds=array();
							foreach($Categoriafeeds as $fila){
								$arrayIdsCategoriafeeds[]=$fila->getId();
							}
							$v=join(',',$arrayIdsCategoriafeeds);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'publicar_icono':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'publicar_titulo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'publicar_fecha':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'publicar_hora':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'publicar_contenido':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'limite_items':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_inicio':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'fecha_fin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Pantallafeed->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Pantallafeed->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Pantallafeed=$resultado[$i];
			$Pantalla=$Pantallafeed->getPantalla();
			$Feed=$Pantallafeed->getFeed();
			$Categoriafeeds=$Pantallafeed->getCategoriafeeds();
			$jqgrid->rows[]=array('id'=>$Pantallafeed->getId(),'cell'=>array($Pantalla->getDescripcion(),$Feed->getUrl_feed(),$Categoriafeeds->getNombre_categoria(),$Pantallafeed->getPublicarIcono(),$Pantallafeed->getPublicarTitulo(),$Pantallafeed->getPublicarFecha(),$Pantallafeed->getPublicarHora(),$Pantallafeed->getPublicarContenido(),$Pantallafeed->getLimiteItems(),$Pantallafeed->getFechaInicio(),$Pantallafeed->getFechaFin()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

