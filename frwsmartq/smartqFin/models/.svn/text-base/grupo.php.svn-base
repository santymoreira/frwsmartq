<?php

class Grupo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $modulo_id;

	/**
	 * @var string
	 */
	protected $nombre_largo;

	/**
	 * @var string
	 */
	protected $nombre_corto;

	/**
	 * @var string
	 */
	protected $descripcion;

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
	 * Método para establecer el valor del campo modulo_id
	 * @param integer $modulo_id
	 */
	public function setModuloId($modulo_id){
		$this->modulo_id = $modulo_id;
	}

	/**
	 * Método para establecer el valor del campo nombre_largo
	 * @param string $nombre_largo
	 */
	public function setNombreLargo($nombre_largo){
		$this->nombre_largo = $nombre_largo;
	}

	/**
	 * Método para establecer el valor del campo nombre_corto
	 * @param string $nombre_corto
	 */
	public function setNombreCorto($nombre_corto){
		$this->nombre_corto = $nombre_corto;
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
	 * Devuelve el valor del campo modulo_id
	 * @return integer
	 */
	public function getModuloId(){
		return $this->modulo_id;
	}

	/**
	 * Devuelve el valor del campo nombre_largo
	 * @return string
	 */
	public function getNombreLargo(){
		return $this->nombre_largo;
	}

	/**
	 * Devuelve el valor del campo nombre_corto
	 * @return string
	 */
	public function getNombreCorto(){
		return $this->nombre_corto;
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
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
            $this->belongsTo('modulo_id','modulo','id');
	}

}

