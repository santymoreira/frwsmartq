<?php

class Calificacion extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nom_calificacion;

	/**
	 * @var integer
	 */
	protected $puntos;

	/**
	 * @var integer
	 */
	protected $orden;

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
	 * Método para establecer el valor del campo nom_calificacion
	 * @param string $nom_calificacion
	 */
	public function setNomCalificacion($nom_calificacion){
		$this->nom_calificacion = $nom_calificacion;
	}

	/**
	 * Método para establecer el valor del campo puntos
	 * @param integer $puntos
	 */
	public function setPuntos($puntos){
		$this->puntos = $puntos;
	}

	/**
	 * Método para establecer el valor del campo orden
	 * @param integer $orden
	 */
	public function setOrden($orden){
		$this->orden = $orden;
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
	 * Devuelve el valor del campo nom_calificacion
	 * @return string
	 */
	public function getNomCalificacion(){
		return $this->nom_calificacion;
	}

	/**
	 * Devuelve el valor del campo puntos
	 * @return integer
	 */
	public function getPuntos(){
		return $this->puntos;
	}

	/**
	 * Devuelve el valor del campo orden
	 * @return integer
	 */
	public function getOrden(){
		return $this->orden;
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

