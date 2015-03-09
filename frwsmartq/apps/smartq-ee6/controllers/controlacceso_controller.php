<?php

/**
 * Controlador Controlacceso
 *
 * @access public
 * @version 1.0
 */
class ControlaccesoController extends ApplicationController {

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
	 * ip
	 *
	 * @var string
	 */
	public $ip;

	/**
	 * sesion_inicio
	 *
	 */
	public $sesionInicio;

	/**
	 * hora_inicio
	 *
	 */
	public $horaInicio;

	/**
	 * sesion_fin
	 *
	 */
	public $sesionFin;

	/**
	 * hora_fin
	 *
	 */
	public $horaFin;

	/**
	 * duracion
	 *
	 */
	public $duracion;

	/**
	 * estado
	 *
	 * @var int
	 */
	public $estado;

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
		$user = new Funciones();
            if(!SessionNamespace::exists("datosUsuarioSMC")){
                    Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
                    $this->routeTo("controller: login","action: index");
            }else{
                $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
                $sesion = Session::getId();
                if ($user->usuarioConectado($sesion)==false) {
                    Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
                    $this->routeTo("controller: login","action: index");;
                }
            }
	}

	/**
	 * Acción por defecto del controlador/
	 *
	 */
	public function indexAction(){
                $this->setResponse('ajax');
		$columnsGrid[]=array('name'=>'Estado','index'=>'estado','width'=> 70);
		$columnsGrid[]=array('name'=>'Usuario','index'=>'usuario_id');
		$columnsGrid[]=array('name'=>'Ip','index'=>'ip');
		$columnsGrid[]=array('name'=>'Sesion Inicio','index'=>'sesion_inicio');
		//$columnsGrid[]=array('name'=>'Hora Inicio','index'=>'hora_inicio');
		$columnsGrid[]=array('name'=>'Sesion Fin','index'=>'sesion_fin');
		//$columnsGrid[]=array('name'=>'Hora Fin','index'=>'hora_fin');
		//$columnsGrid[]=array('name'=>'Duracion','index'=>'duracion');
		 Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Controlacceso/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Controlacceso
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$controlacceso = $this->Controlacceso->findFirst($id);
		if($controlacceso){
			Tag::displayTo('id', $controlacceso->getId());
			Tag::displayTo('usuario_id', $controlacceso->getUsuarioId());
			Tag::displayTo('ip', $controlacceso->getIp());
			Tag::displayTo('sesion_inicio', $controlacceso->getSesionInicio());
			Tag::displayTo('hora_inicio', $controlacceso->getHoraInicio());
			Tag::displayTo('sesion_fin', $controlacceso->getSesionFin());
			Tag::displayTo('hora_fin', $controlacceso->getHoraFin());
			Tag::displayTo('duracion', $controlacceso->getDuracion());
			Tag::displayTo('estado', $controlacceso->getEstado());
			Tag::displayTo('creacion_at', $controlacceso->getCreacionAt());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Controlacceso
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$usuarioId = $this->getPostParam("usuario_id", "int");
		$ip = $this->getPostParam("ip", "striptags", "extraspaces");
		$sesionInicio = $this->getPostParam("sesion_inicio");
		$horaInicio = $this->getPostParam("hora_inicio");
		$sesionFin = $this->getPostParam("sesion_fin");
		$horaFin = $this->getPostParam("hora_fin");
		$duracion = $this->getPostParam("duracion");
		$estado = $this->getPostParam("estado", "int");
		$creacionAt = $this->getPostParam("creacion_at");
                if(is_null($id))
                    $id=$grupo->maximum("id")+1;
		$controlacceso = new Controlacceso();
		$controlacceso->setId($id);
		$controlacceso->setUsuarioId($usuarioId);
		$controlacceso->setIp($ip);
		$controlacceso->setSesionInicio($sesionInicio);
		$controlacceso->setHoraInicio($horaInicio);
		$controlacceso->setSesionFin($sesionFin);
		$controlacceso->setHoraFin($horaFin);
		$controlacceso->setDuracion($duracion);
		$controlacceso->setEstado($estado);
		$controlacceso->setCreacionAt($creacionAt);
                $action=($isEdit==true) ? "editar" : 'nuevo';
		if($controlacceso->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			$this->setParamToView('save','true');
                     if($this->getQueryParam("exit")!="")
                          $this->setParamToView('exit','true');
                     else{
                          if(!$isEdit){
                              $action='editar';
                          }
                     }
                     Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo("action: $action","id: $id");
	}

	/**
	 * Eliminar el Controlacceso
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Controlacceso->delete($ids[$i])){
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
					case 'usuario_id':
						$condUsuario=Utils::toSqlParamSearchGrid('ci',$abrevoper,$strbusqueda);
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
								$condUsuario=Utils::toSqlParamSearchGrid('ci',$val['op'],$val['data']);
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
					case 'usuario_id':
						$condUsuario=Utils::toSqlParamSearchGrid('ci','bw',$v);
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
					case 'ip':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'sesion_inicio':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_inicio':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'sesion_fin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'hora_fin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'duracion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'estado':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Controlacceso->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Controlacceso->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		 @$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Controlacceso=$resultado[$i];
                        $estado='<font color="#01CF00"><b>Conectado</b></font>';
                        if($Controlacceso->getEstado()==0)
                           $estado='<font color="red"><b>Desconectado</b></font>';
			$Usuario=$Controlacceso->getUsuario();
                        $sesionInicio = $Controlacceso->getSesionInicio()." ".$Controlacceso->getHoraInicio();
                        $sesionFin = $Controlacceso->getSesionFin()." ".$Controlacceso->getHoraFin();
			//$jqgrid->rows[]=array('id'=>$Controlacceso->getId(),'cell'=>array($Usuario->getCi(),$Controlacceso->getIp(),$Controlacceso->getSesionInicio(),$Controlacceso->getHoraInicio(),$Controlacceso->getSesionFin(),$Controlacceso->getHoraFin(),$Controlacceso->getDuracion(),$Controlacceso->getEstado()));
                        $jqgrid->rows[]=array('id'=>$Controlacceso->getId(),'cell'=>array($estado,$Usuario->getUsername(),$Controlacceso->getIp(),$sesionInicio,$sesionFin));
		}
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

