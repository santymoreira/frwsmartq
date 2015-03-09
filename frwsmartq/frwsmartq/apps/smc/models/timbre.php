<?php

class Timbre extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $estado_timbrar;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo estado_timbrar
	 * @param integer $estado_timbrar
	 */
	public function setEstadoTimbrar($estado_timbrar){
		$this->estado_timbrar = $estado_timbrar;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo estado_timbrar
	 * @return integer
	 */
	public function getEstadoTimbrar(){
		return $this->estado_timbrar;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

