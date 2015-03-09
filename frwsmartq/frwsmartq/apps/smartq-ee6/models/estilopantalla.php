<?php

class Estilopantalla extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $pantalla_id;

	/**
	 * @var string
	 */
	protected $contenido;

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
	 * Método para establecer el valor del campo pantalla_id
	 * @param integer $pantalla_id
	 */
	public function setPantallaId($pantalla_id){
		$this->pantalla_id = $pantalla_id;
	}

	/**
	 * Método para establecer el valor del campo contenido
	 * @param string $contenido
	 */
	public function setContenido($contenido){
		$this->contenido = $contenido;
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
	 * Devuelve el valor del campo pantalla_id
	 * @return integer
	 */
	public function getPantallaId(){
		return $this->pantalla_id;
	}

	/**
	 * Devuelve el valor del campo contenido
	 * @return string
	 */
	public function getContenido(){
		return $this->contenido;
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
		$this->belongsTo('pantalla_id','pantalla','id');
	}

}

