<?php

/**
 * Controlador Menu
 *
 * @access public
 * @version 1.0
 */
class MenuController extends ApplicationController {

	/**
	 * id
	 *
	 * @var int
	 */
	public $id;

	/**
	 * nombre
	 *
	 * @var string
	 */
	public $nombre;

	/**
	 * ruta
	 *
	 * @var string
	 */
	public $ruta;

	/**
	 * idreferencia
	 *
	 * @var int
	 */
	public $idreferencia;

	/**
	 * estado
	 *
	 * @var int
	 */
	public $estado;

	/**
	 * modulo_id
	 *
	 * @var int
	 */
	public $moduloId;

	/**
	 * orden
	 *
	 * @var int
	 */
	public $orden;

	/**
	 * principal
	 *
	 * @var int
	 */
	public $principal;

	/**
	 * posicion
	 *
	 * @var string
	 */
	public $posicion;

	/**
	 * tipo_ventana
	 *
	 * @var string
	 */
	public $tipoVentana;

	/**
	 * Condiciones de búsqueda temporales
	 *
	 * @var string
	 */
	public $condiciones;

	/**
	 * Ordenamiento de la visualización
	 *
	 * @var string
	 */
	public $ordenamiento;

	/**
	 * Página actual en la visualización
	 *
	 * @var int
	 */
	public $pagina;

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
		$this->id = '';
		Tag::displayTo('id', '');
		$this->nombre = '';
		Tag::displayTo('nombre', '');
		$this->ruta = '';
		Tag::displayTo('ruta', '');
		$this->idreferencia = '';
		Tag::displayTo('idreferencia', '');
		$this->estado = '';
		Tag::displayTo('estado', '');
		$this->moduloId = '';
		Tag::displayTo('moduloId', '');
		$this->orden = '';
		Tag::displayTo('orden', '');
		$this->principal = '';
		Tag::displayTo('principal', '');
		$this->posicion = '';
		Tag::displayTo('posicion', '');
		$this->tipoVentana = '';
		Tag::displayTo('tipoVentana', '');
	}

	/**
	 * Crear un Menu/
	 *
	 */
	public function nuevoAction(){

	}

	/**
	 * Realiza una busqueda de registros en Menu
	 *
	 */
	public function buscarAction(){

		$id = $this->getPostParam("id");
		$nombre = $this->getPostParam("nombre");
		$ruta = $this->getPostParam("ruta");
		$idreferencia = $this->getPostParam("idreferencia");
		$estado = $this->getPostParam("estado");
		$moduloId = $this->getPostParam("moduloId");
		$orden = $this->getPostParam("orden");
		$principal = $this->getPostParam("principal");
		$posicion = $this->getPostParam("posicion");
		$tipoVentana = $this->getPostParam("tipoVentana");

		$condiciones = array();
		if($id!==""){
		$id = $this->getPostParam("id", "int");
			$condiciones[] = "id = '$id'";
		}
		if($nombre!==""){
			$nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
			$nombre = preg_replace("/[ ]+/", " ", $nombre);
			$nombre = str_replace(" ", "%", $nombre);
			$condiciones[] = "nombre LIKE '%$nombre%'";
		}
		if($ruta!==""){
			$ruta = $this->getPostParam("ruta", "striptags", "extraspaces");
			$ruta = preg_replace("/[ ]+/", " ", $ruta);
			$ruta = str_replace(" ", "%", $ruta);
			$condiciones[] = "ruta LIKE '%$ruta%'";
		}
		if($idreferencia!==""){
		$idreferencia = $this->getPostParam("idreferencia", "int");
			$condiciones[] = "idreferencia = '$idreferencia'";
		}
		if($estado!==""){
		$estado = $this->getPostParam("estado", "int");
			$condiciones[] = "estado = '$estado'";
		}
		if($moduloId!="@"){
		$moduloId = $this->getPostParam("moduloId", "int");
			$condiciones[] = "modulo_id = '$moduloId'";
		}
		if($orden!==""){
		$orden = $this->getPostParam("orden", "int");
			$condiciones[] = "orden = '$orden'";
		}
		if($principal!==""){
		$principal = $this->getPostParam("principal", "int");
			$condiciones[] = "principal = '$principal'";
		}
		if($posicion!==""){
			$posicion = $this->getPostParam("posicion", "striptags", "extraspaces");
			$posicion = preg_replace("/[ ]+/", " ", $posicion);
			$posicion = str_replace(" ", "%", $posicion);
			$condiciones[] = "posicion LIKE '%$posicion%'";
		}
		if($tipoVentana!==""){
			$tipoVentana = $this->getPostParam("tipoVentana", "striptags", "extraspaces");
			$tipoVentana = preg_replace("/[ ]+/", " ", $tipoVentana);
			$tipoVentana = str_replace(" ", "%", $tipoVentana);
			$condiciones[] = "tipo_ventana LIKE '%$tipoVentana%'";
		}
		if(count($condiciones)>0){
			$this->condiciones = join(" OR ", $condiciones);
		} else {
			$this->condiciones = "";
		}
		$this->ordenamiento = "1";
		$this->routeTo("action: visualizar");
	}

	/**
	 * Visualiza los registros encontrados en la busqueda
	 *
	 */
	public function visualizarAction(){

		$controllerRequest = ControllerRequest::getInstance();
		if($controllerRequest->isSetQueryParam("ordenar")){
			$posibleOrdenar = array(
				"id" => "id",
				"nombre" => "nombre",
				"ruta" => "ruta",
				"idreferencia" => "idreferencia",
				"estado" => "estado",
				"moduloId" => "modulo_id",
				"orden" => "orden",
				"principal" => "principal",
				"posicion" => "posicion",
				"tipoVentana" => "tipo_ventana"
			);
			$ordenar = $controllerRequest->getParamQuery("ordenar", "alpha");
			if(isset($posibleOrdenar[$ordenar])==true){
				$this->ordenamiento = $posibleOrdenar[$ordenar];
			} else {
				$this->ordenamiento = "1";
			}
		}
		if($controllerRequest->isSetRequestParam("pagina")){
			$this->pagina = $controllerRequest->getParamRequest("pagina", "int");
		} else {
			$this->pagina = 1;
		}
		if($this->condiciones!=""){
			$resultados = $this->Menu->find($this->condiciones, "order: {$this->ordenamiento}");
		} else {
			$resultados = $this->Menu->find("order: {$this->ordenamiento}");
		}

		$this->setParamToView("resultados", $resultados);
	}

	/**
	 * Editar el Menu
	 *
	 */
	public function editarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$menu = $this->Menu->findFirst($id);
		if($menu){
			Tag::displayTo('id', $menu->getId());
			Tag::displayTo('nombre', $menu->getNombre());
			Tag::displayTo('ruta', $menu->getRuta());
			Tag::displayTo('idreferencia', $menu->getIdreferencia());
			Tag::displayTo('estado', $menu->getEstado());
			Tag::displayTo('moduloId', $menu->getModuloId());
			Tag::displayTo('orden', $menu->getOrden());
			Tag::displayTo('principal', $menu->getPrincipal());
			Tag::displayTo('posicion', $menu->getPosicion());
			Tag::displayTo('tipoVentana', $menu->getTipoVentana());
		}else {
			Flash::error('Registro no encontrado.');
			$this->routeTo('action: index');
		}
	}

	/**
	 * Guardar el Menu
	 *
	 */
	public function guardarAction($isEdit=false){

		$id = $this->getPostParam("id", "int");
		$nombre = $this->getPostParam("nombre", "striptags", "extraspaces");
		$ruta = $this->getPostParam("ruta", "striptags", "extraspaces");
		$idreferencia = $this->getPostParam("idreferencia", "int");
		$estado = $this->getPostParam("estado", "int");
		$moduloId = $this->getPostParam("moduloId", "int");
		$orden = $this->getPostParam("orden", "int");
		$principal = $this->getPostParam("principal", "int");
		$posicion = $this->getPostParam("posicion", "striptags", "extraspaces");
		$tipoVentana = $this->getPostParam("tipoVentana", "striptags", "extraspaces");
		$menu = new Menu();
		$menu->setId($id);
		$menu->setNombre($nombre);
		$menu->setRuta($ruta);
		$menu->setIdreferencia($idreferencia);
		$menu->setEstado($estado);
		$menu->setModuloId($moduloId);
		$menu->setOrden($orden);
		$menu->setPrincipal($principal);
		$menu->setPosicion($posicion);
		$menu->setTipoVentana($tipoVentana);
		if($menu->save()==false){
			Flash::error('Hubo un error guardando el registro.');
		}else {
			Flash::success('Registro guardado con éxito.');
		}

		$this->routeTo('action: '.($isEdit==true ? 'index' : 'nuevo'));
	}

	/**
	 * Eliminar el Menu
	 *
	 */
	public function eliminarAction($id=null){

		$filter = new Filter();
		$id = $filter->applyFilter($id,"int");
		$menu = $this->Menu->count('id = '.$id);
		if($menu==1){
			if(!$this->Menu->delete('id = '.$id)){
				Flash::error('El registro no pudo ser eliminado.');
			}else {
				Flash::success('El registro fue eliminado correctamente.');
			}
		}else {
			Flash::error('Registro no encontrado.');
		}
		$this->routeTo('action: index');
	}


        
}

