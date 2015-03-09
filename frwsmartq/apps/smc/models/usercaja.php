<?php

class Usercaja extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var integer
	 */
	protected $caja_id;

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
	 * Método para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
	}

	/**
	 * Método para establecer el valor del campo caja_id
	 * @param integer $caja_id
	 */
	public function setCajaId($caja_id){
		$this->caja_id = $caja_id;
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
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
	}

	/**
	 * Devuelve el valor del campo caja_id
	 * @return integer
	 */
	public function getCajaId(){
		return $this->caja_id;
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
		$this->belongsTo('usuario_id','usuario','id');
		$this->belongsTo('caja_id','caja','id');
	}

}

