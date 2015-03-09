<?php

class Ubicacion extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre_ubicacion;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var Date
	 */
	protected $creacion_at;

	/**
	 * @var Date
	 */
	protected $estado;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nombre_ubicacion
	 * @param string $nombre_ubicacion
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Método para establecer el valor del campo nombre_ubicacion
	 * @param string $nombre_ubicacion
	 */
	public function setNombreUbicacion($nombre_ubicacion){
		$this->nombre_ubicacion = $nombre_ubicacion;
	}

	/**
	 * Método para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
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
	 * Devuelve el valor del campo nombre_ubicacion
	 * @return string
	 */
	public function getNombreUbicacion(){
		return $this->nombre_ubicacion;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo creacion_at
	 * @return Date
	 */
	public function getCreacionAt(){
		return $this->creacion_at;
	}

		/**
	 * Devuelve el valor del campo creacion_at
	 * @return Date
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

