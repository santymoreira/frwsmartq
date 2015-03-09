<?php

class Horario extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre_horario;

	/**
	 * @var string
	 */
	protected $descripcion_horario;

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
	 * Método para establecer el valor del campo nombre_horario
	 * @param string $nombre_horario
	 */
	public function setNombreHorario($nombre_horario){
		$this->nombre_horario = $nombre_horario;
	}

	/**
	 * Método para establecer el valor del campo descripcion_horario
	 * @param string $descripcion_horario
	 */
	public function setDescripcionHorario($descripcion_horario){
		$this->descripcion_horario = $descripcion_horario;
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
	 * Devuelve el valor del campo nombre_horario
	 * @return string
	 */
	public function getNombreHorario(){
		return $this->nombre_horario;
	}

	/**
	 * Devuelve el valor del campo descripcion_horario
	 * @return string
	 */
	public function getDescripcionHorario(){
		return $this->descripcion_horario;
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

