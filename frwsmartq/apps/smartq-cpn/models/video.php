<?php

class Video extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $ubicacion;

	/**
	 * @var integer
	 */
	protected $duracion;

	/**
	 * @var integer
	 */
	protected $activo;

	/**
	 * @var Date
	 */
	protected $creacion_at;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Método para establecer el valor del campo ubicacion
	 * @param string $ubicacion
	 */
	public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
	}

	/**
	 * Método para establecer el valor del campo duracion
	 * @param integer $duracion
	 */
	public function setDuracion($duracion){
		$this->duracion = $duracion;
	}

	/**
	 * Método para establecer el valor del campo activo
	 * @param integer $activo
	 */
	public function setActivo($activo){
		$this->activo = $activo;
	}

	/**
	 * Método para establecer el valor del campo creacion_at
	 * @param Date $creacion_at
	 */
	public function setCreacionAt($creacion_at){
		$this->creacion_at = $creacion_at;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo ubicacion
	 * @return string
	 */
	public function getUbicacion(){
		return $this->ubicacion;
	}

	/**
	 * Devuelve el valor del campo duracion
	 * @return integer
	 */
	public function getDuracion(){
		return $this->duracion;
	}

	/**
	 * Devuelve el valor del campo activo
	 * @return integer
	 */
	public function getActivo(){
		return $this->activo;
	}

	/**
	 * Devuelve el valor del campo creacion_at
	 * @return Date
	 */
	public function getCreacionAt(){
		return $this->creacion_at;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

