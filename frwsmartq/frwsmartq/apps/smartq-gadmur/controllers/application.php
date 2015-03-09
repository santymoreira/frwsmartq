<?php

/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 * @access public
 **/
class ControllerBase {

	public function init(){
		//Core::info();
                //Router::routeTo('controller: dispensadorservicio');

                Router::routeTo('controller: login');
	}
        public function combos(){
            //array de opciones para combo estatico de estados
            $estado = array(
                '1' => 'Activo',
                '0' => 'Inactivo'
            );
            //array de opciones para combo estatico de tipos de presentacion
            $tipo = array(
                'back-end' => 'back-end',
                'front-end' => 'front-end'
            );
            $this->setParamToView("tipo", $tipo);//carga en una variable los tipos de pantalla de la aplicacion
            $this->setParamToView("estado", $estado);//carga en una variable el estado de un registro
        }

}

