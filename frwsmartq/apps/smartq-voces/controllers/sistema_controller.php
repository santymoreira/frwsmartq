<?php

/**
 * Controlador Sistema
 *
 * @access public
 * @version 1.0
 */
class SistemaController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * nombrecomercial
	 *
	 * @var string
	 */
	public $nombrecomercial;

	/**
	 * razonsocial
	 *
	 * @var string
	 */
	public $razonsocial;

	/**
	 * logentradasalida
	 *
	 * @var int
	 */
	public $logentradasalida;

	/**
	 * logmodificacion
	 *
	 * @var int
	 */
	public $logmodificacion;

	/**
	 * logvisualizacion
	 *
	 * @var int
	 */
	public $logvisualizacion;

	/**
	 * intentoslogin
	 *
	 * @var int
	 */
	public $intentoslogin;

	/**
	 * tiemposesion
	 *
	 */
	public $tiemposesion;

	/**
	 * validezclave
	 *
	 * @var int
	 */
	public $validezclave;

	/**
	 * contenidoclave
	 *
	 * @var string
	 */
	public $contenidoclave;

	/**
	 * modclaveacceso
	 *
	 * @var int
	 */
	public $modclaveacceso;

	/**
	 * emailadmin
	 *
	 * @var string
	 */
	public $emailadmin;

	/**
	 * claveadmin
	 *
	 * @var string
	 */
	public $claveadmin;

	/**
	 * logosuperior
	 *
	 * @var string
	 */
	public $logosuperior;

	/**
	 * logoreporte
	 *
	 * @var string
	 */
	public $logoreporte;

	/**
	 * logoprincipal
	 *
	 * @var string
	 */
	public $logoprincipal;

	/**
	 * idioma
	 *
	 * @var string
	 */
	public $idioma;

	/**
	 * simbolomoneda
	 *
	 * @var string
	 */
	public $simbolomoneda;

	/**
	 * formatoreporte
	 *
	 * @var string
	 */
	public $formatoreporte;

	/**
	 * notificarbloqueo
	 *
	 * @var int
	 */
	public $notificarbloqueo;

	/**
	 * plantilla
	 *
	 * @var string
	 */
	public $plantilla;

	/**
	 * numerodecimal
	 *
	 * @var int
	 */
	public $numerodecimal;

	/**
	 * tamanocontrasena
	 *
	 * @var int
	 */
	public $tamanocontrasena;

	/**
	 * Inicializador del controlador/
	 *
	 */
	public function initialize(){
		//$this->setPersistance(true);
                $contar =$this->Sistema->count();
                $this->setParamToView("contar", $contar);
                if(!SessionNamespace::exists("datosUsuarioSMC")){
                    Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
                    die ();
                 }
	}

	/**
	 * Acción por defecto del controlador/
	 *
	 */
	public function indexAction(){
                $this->setResponse('ajax');
		$columnsGrid[]=array('name'=>'Nombre Comercial','index'=>'nombrecomercial');
		$columnsGrid[]=array('name'=>'Razon Social','index'=>'razonsocial');
		//$columnsGrid[]=array('name'=>'Log Entrada/Salida','index'=>'logentradasalida');
		//$columnsGrid[]=array('name'=>'Log Modificación','index'=>'logmodificacion');
		//$columnsGrid[]=array('name'=>'Log Visualizacion','index'=>'logvisualizacion');
		$columnsGrid[]=array('name'=>'Intentos Login','index'=>'intentoslogin', 'align'=>'center');
		$columnsGrid[]=array('name'=>'Tiempo Sesion Min.','index'=>'tiemposesion', 'align'=>'center');
		//$columnsGrid[]=array('name'=>'Validez Clave','index'=>'validezclave');
		//$columnsGrid[]=array('name'=>'Contenido  Clave','index'=>'contenidoclave');
		//$columnsGrid[]=array('name'=>'Modclaveacceso','index'=>'modclaveacceso');
		$columnsGrid[]=array('name'=>'Email Administrador','index'=>'emailadmin','width'=>150);
		//$columnsGrid[]=array('name'=>'Clave Admin','index'=>'claveadmin');
		//$columnsGrid[]=array('name'=>'Logo Superior','index'=>'logosuperior');
		//$columnsGrid[]=array('name'=>'Logo Reporte','index'=>'logoreporte');
		//$columnsGrid[]=array('name'=>'Logo Principal','index'=>'logoprincipal');
		//$columnsGrid[]=array('name'=>'Idioma','index'=>'idioma');
		//$columnsGrid[]=array('name'=>'Simbolo Moneda','index'=>'simbolomoneda');
		$columnsGrid[]=array('name'=>'Formato Reporte','index'=>'formatoreporte','align'=>'center');
		//$columnsGrid[]=array('name'=>'Notificar Bloqueo','index'=>'notificarbloqueo');
		//$columnsGrid[]=array('name'=>'Plantilla','index'=>'plantilla');
		//$columnsGrid[]=array('name'=>'Numero decimal','index'=>'numerodecimal');
		//$columnsGrid[]=array('name'=>'Tamaño Contrasena','index'=>'tamanocontrasena');
		Tag::setColumnsToGrid($columnsGrid);
	}

	/**
	 * Crear un Sistema/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Editar el Sistema
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$sistema = $this->Sistema->findFirst($id);
		if($sistema){
			Tag::displayTo('id', $sistema->getId());
			Tag::displayTo('nombrecomercial', $sistema->getNombrecomercial());
			Tag::displayTo('razonsocial', $sistema->getRazonsocial());
			Tag::displayTo('logentradasalida', $sistema->getLogentradasalida());
			Tag::displayTo('logmodificacion', $sistema->getLogmodificacion());
			Tag::displayTo('logvisualizacion', $sistema->getLogvisualizacion());
			Tag::displayTo('intentoslogin', $sistema->getIntentoslogin());
			Tag::displayTo('tiemposesion', $sistema->getTiemposesion());
			Tag::displayTo('validezclave', $sistema->getValidezclave());
			Tag::displayTo('contenidoclave', $sistema->getContenidoclave());
			Tag::displayTo('modclaveacceso', $sistema->getModclaveacceso());
			Tag::displayTo('emailadmin', $sistema->getEmailadmin());
			Tag::displayTo('claveadmin', base64_decode($sistema->getClaveadmin()));
			Tag::displayTo('logosuperior', $sistema->getLogosuperior());
			Tag::displayTo('logoreporte', $sistema->getLogoreporte());
			Tag::displayTo('logoprincipal', $sistema->getLogoprincipal());
			Tag::displayTo('idioma', $sistema->getIdioma());
			Tag::displayTo('simbolomoneda', $sistema->getSimbolomoneda());
			Tag::displayTo('formatoreporte', $sistema->getFormatoreporte());
			Tag::displayTo('notificarbloqueo', $sistema->getNotificarbloqueo());
			Tag::displayTo('plantilla', $sistema->getPlantilla());
			Tag::displayTo('numerodecimal', $sistema->getNumerodecimal());
			Tag::displayTo('tamanocontrasena', $sistema->getTamanocontrasena());
			
		}else {
			Flash::error('Registro no encontrado.');
			//$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Sistema
	 *
	 */
	public function guardarAction($isEdit=false,$id=null){

		$nombrecomercial = $this->getPostParam("nombrecomercial", "striptags", "extraspaces");
		$razonsocial = $this->getPostParam("razonsocial", "striptags", "extraspaces");
		$logentradasalida = $this->getPostParam("logentradasalida", "int");
		$logmodificacion = $this->getPostParam("logmodificacion", "int");
		$logvisualizacion = $this->getPostParam("logvisualizacion", "int");
		$intentoslogin = $this->getPostParam("intentoslogin", "int");
		$tiemposesion = $this->getPostParam("tiemposesion");
		$validezclave = $this->getPostParam("validezclave", "int");
		$contenidoclave = $this->getPostParam("contenidoclave", "striptags", "extraspaces");
		$modclaveacceso = $this->getPostParam("modclaveacceso", "int");
		$emailadmin = $this->getPostParam("emailadmin", "striptags", "extraspaces");
		$claveadmin = $this->getPostParam("claveadmin", "striptags", "extraspaces");
		$logosuperior = $this->getPostParam("logosuperior", "striptags", "extraspaces");
		$logoreporte = $this->getPostParam("logoreporte", "striptags", "extraspaces");
		$logoprincipal = $this->getPostParam("logoprincipal", "striptags", "extraspaces");
		$idioma = $this->getPostParam("idioma", "striptags", "extraspaces");
		$simbolomoneda = $this->getPostParam("simbolomoneda", "striptags", "extraspaces");
		$formatoreporte = $this->getPostParam("formatoreporte", "striptags", "extraspaces");
		$notificarbloqueo = $this->getPostParam("notificarbloqueo", "int");
		$plantilla = $this->getPostParam("plantilla", "striptags", "extraspaces");
		$numerodecimal = $this->getPostParam("numerodecimal", "int");
		$tamanocontrasena = $this->getPostParam("tamanocontrasena", "int");
		$sistema = new Sistema();
		$sistema->setId($id);
		$sistema->setNombrecomercial($nombrecomercial);
		$sistema->setRazonsocial($razonsocial);
		$sistema->setLogentradasalida($logentradasalida);
		$sistema->setLogmodificacion($logmodificacion);
		$sistema->setLogvisualizacion($logvisualizacion);
		$sistema->setIntentoslogin($intentoslogin);
		$sistema->setTiemposesion($tiemposesion);
		$sistema->setValidezclave($validezclave);
		$sistema->setContenidoclave($contenidoclave);
		$sistema->setModclaveacceso($modclaveacceso);
		$sistema->setEmailadmin($emailadmin);
		$sistema->setClaveadmin(base64_encode($claveadmin));
		$sistema->setLogosuperior($logosuperior);
		$sistema->setLogoreporte($logoreporte);
		$sistema->setLogoprincipal($logoprincipal);
		$sistema->setIdioma($idioma);
		$sistema->setSimbolomoneda($simbolomoneda);
		$sistema->setFormatoreporte($formatoreporte);
		$sistema->setNotificarbloqueo($notificarbloqueo);
		$sistema->setPlantilla($plantilla);
		$sistema->setNumerodecimal($numerodecimal);
		$sistema->setTamanocontrasena($tamanocontrasena);
                $action=($isEdit==true) ? "editar" : 'nuevo';
		if($sistema->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			if(!$isEdit)
                           $id=$sistema->maximum("id");
                        $this->setParamToView('save','true');
                        if($this->getQueryParam("exit")!=""){
                            $this->setParamToView('exit','true');

                        }
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
	 * Eliminar el Sistema
	 *
	 */
	public function eliminarAction(){

		$this->setResponse('ajax');
		$msg='';
		if($this->getPostParam('oper')=='del'){
			$ids=explode(',',$this->getPostParam('id'));
			for($i=0;$i<count($ids);$i++){
				if(!$this->Sistema->delete($ids[$i])){
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
				$condicion.=' AND '.Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
			}elseif($filtroBusqueda){
				$jsona = json_decode($filtroBusqueda,true);
				if(is_array($jsona)){
					$gopr = $jsona['groupOp'];
					$rules = $jsona['rules'];
					$i =0;
					foreach($rules as $key=>$val) {
						$field = $val['field'];
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
					case 'nombrecomercial':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'razonsocial':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logentradasalida':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logmodificacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logvisualizacion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'intentoslogin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'tiemposesion':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'validezclave':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'contenidoclave':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'modclaveacceso':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'emailadmin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'claveadmin':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logosuperior':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logoreporte':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'logoprincipal':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'idioma':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'simbolomoneda':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'formatoreporte':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'notificarbloqueo':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'plantilla':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'numerodecimal':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					case 'tamanocontrasena':
						$condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
					
				}
			}
		}
		$orden="$col_orden $dir_orden";  //construimos la cadena de orden
		 //comparar que el grupo no este en grupo_usuario
		$contar =$this->Sistema->count("conditions: $condicion");  //contar el numero total de registros existentes
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
		$resultado=$this->Sistema->find("conditions: $condicion","order: $orden","limit: $limite");
		//construimos el resultado para el grid como objeto
		$jqgrid=null;
		@$jqgrid->page = $pagina;   //pagina de navegacion actual
		@$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
		@$jqgrid->records = $contar;  // numero total de registros obtenidos
		if($limite>$contar)$limite=$contar;
		for($i=$inicio;$i<$limite;$i++){
			$Sistema=$resultado[$i];
			//$jqgrid->rows[]=array('id'=>$Sistema->getId(),'cell'=>array($Sistema->getNombrecomercial(),$Sistema->getRazonsocial(),$Sistema->getLogentradasalida(),$Sistema->getLogmodificacion(),$Sistema->getLogvisualizacion(),$Sistema->getIntentoslogin(),$Sistema->getTiemposesion(),$Sistema->getValidezclave(),$Sistema->getContenidoclave(),$Sistema->getModclaveacceso(),$Sistema->getEmailadmin(),$Sistema->getClaveadmin(),$Sistema->getLogosuperior(),$Sistema->getLogoreporte(),$Sistema->getLogoprincipal(),$Sistema->getIdioma(),$Sistema->getSimbolomoneda(),$Sistema->getFormatoreporte(),$Sistema->getNotificarbloqueo(),$Sistema->getPlantilla(),$Sistema->getNumerodecimal(),$Sistema->getTamanocontrasena()));
                        $jqgrid->rows[]=array('id'=>$Sistema->getId(),'cell'=>array($Sistema->getNombrecomercial(),$Sistema->getRazonsocial(),$Sistema->getIntentoslogin(),$Sistema->getTiemposesion(),$Sistema->getEmailadmin(),$Sistema->getFormatoreporte()));
		}
                //print(base64_decode($Sistema->getClaveadmin()));die();
		//impresion de la respuesta formato json
		 echo $this->jsonEncode($jqgrid);
	}
}

