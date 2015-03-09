<?php

/**
 * Controlador Banner
 *
 * @access public
 * @version 1.0
 */
class BannerController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ubicacion
	 *
	 * @var string
	 */
	public $ubicacion;

	/**
	 * serie
	 *
	 * @var int
	 */
	public $serie;

	/**
	 * posicion
	 *
	 * @var int
	 */
	public $posicion;

	/**
	 * display_id
	 *
	 * @var int
	 */
	public $displayId;

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
		$columnsGrid[]=array('name'=>'Ubicacion','index'=>'ubicacion');
		$columnsGrid[]=array('name'=>'Serie','index'=>'serie');
		$columnsGrid[]=array('name'=>'Posicion','index'=>'posicion');
		$columnsGrid[]=array('name'=>'Display','index'=>'display_id');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Banner/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Banner
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$banner = $this->Banner->findFirst($id);
		if($banner){
			Tag::displayTo('id', $banner->getId());
			Tag::displayTo('ubicacion', $banner->getUbicacion());
			Tag::displayTo('serie', $banner->getSerie());
			Tag::displayTo('posicion', $banner->getPosicion());
			Tag::displayTo('display_id', $banner->getDisplayId());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Banner
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$ubicacion = $this->getPostParam("ubicacion", "striptags", "extraspaces");
		$serie = $this->getPostParam("serie", "int");
		$posicion = $this->getPostParam("posicion", "int");
		$displayId = $this->getPostParam("display_id", "int");
                Flash::notice("antes");
                $this->loadMovementAction();

                $banner = new Banner();
		$banner->setId($id);
		$banner->setUbicacion($ubicacion);
		$banner->setSerie($serie);
		$banner->setPosicion($posicion);
		$banner->setDisplayId($displayId);


//
//  		if($banner->save()==false){
//			Flash::error('Hubo un error guardando el registro.');
//		}else {
//			Flash::success('Registro guardado con Ã©xito.');
//		}
//
		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el Banner
	 *
	 */

         public function SubirAction(){

             $nombre= $this->getPostParam("banner");
           //  $this->uploadFile('foto', 'http://localhost/frameworkpw/public/img');
           //  Flash::notice($nombre);
           //
            //  Load::lib('upload');
            //  Upload::
            //  $this->uploadFile($nombre,  '/localhost/frameworkpw/public/img');

            //if($_FILES['video']['name']!=NULL)
            //{
                  //$uploadfile_temporal=$_FILES['video']['tmp_name'];
                  $uploadfile_temporal='/localhost/frameworkpw/public/img';
                  //$uploadfile_nombre="video/".$_FILES['video']['name'];
                  $uploadfile_nombre=$nombre;
                  //$ruta_video='video/'.$_FILES['video']['name'];
                  $ruta_video='/localhost/frameworkpw/public/img';
                  if (is_uploaded_file($uploadfile_temporal))
                  {
                    move_uploaded_file($uploadfile_temporal,$uploadfile_nombre);
                    $ingresar="insert into video_blog (ruta) values ('$uploadfile_nombre')";
                    //if(mysql_query($ingresar,$conexion))
//                    {
//                      echo ("El video ha sido ingresado correctamente");
//                    }
//                    else
//                    {
//                      echo ("Error al ingresar el video");
//                    }
                  }

              //  }

                          }

    	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Banner->delete($ids[$i])){
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
					case 'display_id':
						$condDisplay=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Display=$this->Display->find($condDisplay);
						if(count($Display)>0){
							$arrayIdsDisplay=array();
							foreach($Display as $fila){
								$arrayIdsDisplay[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsDisplay);
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
							case 'display_id':
								$condDisplay=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Display=$this->Display->find($condDisplay);
								if(count($Display)>0){
									$arrayIdsDisplay=array();
									foreach($Display as $fila){
										$arrayIdsDisplay[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsDisplay);
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
					case 'ubicacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'serie':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'posicion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'display_id':
						$condDisplay=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Display=$this->Display->find($condDisplay);
						if(count($Display)>0){
							$arrayIdsDisplay=array();
							foreach($Display as $fila){
								$arrayIdsDisplay[]=$fila->getId();
							}
							$v=join(',',$arrayIdsDisplay);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Banner->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Banner->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Banner=$resultado[$i];
			$Display=$Banner->getDisplay();
			$jqgrid->rows[]=array('id'=>$Banner->getId(),'cell'=>array($Banner->getUbicacion(),$Banner->getSerie(),$Banner->getPosicion(),$Display->getId()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

