<?php

/**
 * Controlador Grupo_menu
 *
 * @access public
 * @version 1.0
 */
class Grupo_menuController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * grupo_id
	 *
	 * @var int
	 */
	public $grupoId;

	/**
	 * menu_id
	 *
	 * @var int
	 */
	public $menuId;

	/**
	 * permitir_acceso
	 *
	 * @var int
	 */
	public $permitirAcceso;

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
		$columnsGrid[]=array('name'=>'Grupo','index'=>'grupo_id');
		$columnsGrid[]=array('name'=>'Menu','index'=>'menu_id');
		$columnsGrid[]=array('name'=>'Permitir Acceso','index'=>'permitir_acceso');
		$columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio');
		$columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un GrupoMenu/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el GrupoMenu
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$grupoMenu = $this->Grupo_menu->findFirst($id);
		if($grupo_menu){
			Tag::displayTo('id', $grupoMenu->getId());
			Tag::displayTo('grupo_id', $grupoMenu->getGrupoId());
			Tag::displayTo('menu_id', $grupoMenu->getMenuId());
			Tag::displayTo('permitir_acceso', $grupoMenu->getPermitirAcceso());
			Tag::displayTo('hora_inicio', $grupoMenu->getHoraInicio());
			Tag::displayTo('hora_fin', $grupoMenu->getHoraFin());
			Tag::displayTo('creacion_at', $grupoMenu->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el GrupoMenu
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$grupoId = $this->getPostParam("grupo_id", "int");
		$menuId = $this->getPostParam("menu_id", "int");
		$permitirAcceso = $this->getPostParam("permitir_acceso", "int");
		$horaInicio = $this->getPostParam("hora_inicio");
		$horaFin = $this->getPostParam("hora_fin");
		$creacionAt = $this->getPostParam("creacion_at");
		$grupoMenu = new GrupoMenu();
		$grupoMenu->setId($id);
		$grupoMenu->setGrupoId($grupoId);
		$grupoMenu->setMenuId($menuId);
		$grupoMenu->setPermitirAcceso($permitirAcceso);
		$grupoMenu->setHoraInicio($horaInicio);
		$grupoMenu->setHoraFin($horaFin);
		$grupoMenu->setCreacionAt($creacionAt);
		if($grupoMenu->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo('action: index');
	}

	/**
	 * Eliminar el GrupoMenu
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Grupo_menu->delete($ids[$i])){
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
					case 'grupo_id':
						$condGrupo=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Grupo=$this->Grupo->find($condGrupo);
						if(count($Grupo)>0){
							$arrayIdsGrupo=array();
							foreach($Grupo as $fila){
								$arrayIdsGrupo[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsGrupo);
							$abrevoper='in';
						}
					break;
					case 'menu_id':
						$condMenu=Utils::toSqlParamSearchGrid('id',$abrevoper,$strbusqueda);
						$Menu=$this->Menu->find($condMenu);
						if(count($Menu)>0){
							$arrayIdsMenu=array();
							foreach($Menu as $fila){
								$arrayIdsMenu[]=$fila->getId();
							}
							$strbusqueda=join(',',$arrayIdsMenu);
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
							case 'grupo_id':
								$condGrupo=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Grupo=$this->Grupo->find($condGrupo);
								if(count($Grupo)>0){
									$arrayIdsGrupo=array();
									foreach($Grupo as $fila){
										$arrayIdsGrupo[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsGrupo);
									$val['op']='in';
								}
							break;
							case 'menu_id':
								$condMenu=Utils::toSqlParamSearchGrid('id',$val['op'],$val['data']);
								$Menu=$this->Menu->find($condMenu);
								if(count($Menu)>0){
									$arrayIdsMenu=array();
									foreach($Menu as $fila){
										$arrayIdsMenu[]=$fila->getId();
									}
									$val['data']=join(',',$arrayIdsMenu);
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
					case 'grupo_id':
						$condGrupo=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Grupo=$this->Grupo->find($condGrupo);
						if(count($Grupo)>0){
							$arrayIdsGrupo=array();
							foreach($Grupo as $fila){
								$arrayIdsGrupo[]=$fila->getId();
							}
							$v=join(',',$arrayIdsGrupo);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'menu_id':
						$condMenu=Utils::toSqlParamSearchGrid('id','bw',$v);
						$Menu=$this->Menu->find($condMenu);
						if(count($Menu)>0){
							$arrayIdsMenu=array();
							foreach($Menu as $fila){
								$arrayIdsMenu[]=$fila->getId();
							}
							$v=join(',',$arrayIdsMenu);
							$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
						}
						else{
							$condicion.=' AND 0';
						}
						break;
					case 'permitir_acceso':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicio':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_fin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Grupo_menu->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Grupo_menu->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Grupo_menu=$resultado[$i];
			$Grupo=$Grupo_menu->getGrupo();
			$Menu=$Grupo_menu->getMenu();
			$jqgrid->rows[]=array('id'=>$Grupo_menu->getId(),'cell'=>array($Grupo->getId(),$Menu->getId(),$Grupo_menu->getPermitirAcceso(),$Grupo_menu->getHoraInicio(),$Grupo_menu->getHoraFin()));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

