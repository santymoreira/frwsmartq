<?php

class Serie extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $display_id;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo display_id
	 * @param integer $display_id
	 */
	public function setDisplayId($display_id){
		$this->display_id = $display_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo display_id
	 * @return integer
	 */
	public function getDisplayId(){
		return $this->display_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

