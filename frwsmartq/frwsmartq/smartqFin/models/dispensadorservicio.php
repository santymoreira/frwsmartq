<?php

class Dispensadorservicio extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $servicio_id;

	/**
	 * @var integer
	 */
	protected $dispensador_id;


	/**
	 * Método para establecer el valor del campo servicio_id
	 * @param integer $servicio_id
	 */
	public function setServicioId($servicio_id){
		$this->servicio_id = $servicio_id;
	}

	/**
	 * Método para establecer el valor del campo dispensador_id
	 * @param integer $dispensador_id
	 */
	public function setDispensadorId($dispensador_id){
		$this->dispensador_id = $dispensador_id;
	}


	/**
	 * Devuelve el valor del campo servicio_id
	 * @return integer
	 */
	public function getServicioId(){
		return $this->servicio_id;
	}

	/**
	 * Devuelve el valor del campo dispensador_id
	 * @return integer
	 */
	public function getDispensadorId(){
		return $this->dispensador_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('servicio_id','servicio','id');
		$this->belongsTo('dispensador_id','dispensador','id');
	}

}

