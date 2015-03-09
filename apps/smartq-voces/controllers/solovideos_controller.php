<?php

/**
 * Controlador Display
 *
 * @access public
 * @version 1.0
 */
class SoloVideosController extends ApplicationController {

	public $logo;

	public $titulo;

	public $video;

	public $publicidad;

	public function initialize() {
        
        $this->setPersistance(true);

        /*if (!SessionNamespace::exists("datosUsuarioSMC")) {
            Flash::addMessage("Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente", Flash::WARNING);
            $this->redirect("login");
        }
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');*/
        //$this->usuario_id = $dataUsuario->getId();
    }

    public function indexAction() {
    	
    }

    public function mostrarAction(){
    	/*$ventana = '';
		$db = DbBase::rawConnect();
		$result2 = $db->query("SELECT logo, video, titulo, publicidad FROM pantalla WHERE tipo_pantalla = 'Pantalla Operador' LIMIT 1 ;");
		while ($row = $db->fetchArray($result2)) {
    		$ventana = $row['ventana'];
		}
		asd*/
    }
}
