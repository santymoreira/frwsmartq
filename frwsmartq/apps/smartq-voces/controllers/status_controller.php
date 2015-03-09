<?php

/**
 * Controlador Status
 *
 * @access public
 * @version 1.0
 */
class StatusController extends ApplicationController {


    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {

    }

    /**
     * Crear un Preguntas/
     *
     */
    public function verAction() {
        $this->setResponse('ajax');
        $this->render('status/index');      //carga la vista
    }

    
}

