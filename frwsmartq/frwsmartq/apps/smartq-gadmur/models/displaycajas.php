<?php

class Displaycajas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $cajanumero;

	/**
	 * @var integer
	 */
	protected $ubicacion;
        
        /**
	 * @var integer
	 */
	protected $llamo;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo cajanumero
	 * @param string $cajanumero
	 */
	public function setCajanumero($cajanumero){
		$this->cajanumero = $cajanumero;
	}

	/**
	 * Método para establecer el valor del campo ubicacion
	 * @param integer $ubicacion
	 */
	public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
	}
        
	/**
	 * Método para establecer el valor del campo llamo
	 * @param integer $llamo
	 */
	public function setLlamo($llamo){
		$this->llamo = $llamo;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo cajanumero
	 * @return string
	 */
	public function getCajanumero(){
		return $this->cajanumero;
	}

	/**
	 * Devuelve el valor del campo ubicacion
	 * @return integer
	 */
	public function getUbicacion(){
		return $this->ubicacion;
	}
        
	/**
	 * Devuelve el valor del campo llamo
	 * @return integer
	 */
	public function getLlamo(){
		return $this->llamo;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

