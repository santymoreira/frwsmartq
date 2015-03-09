<?php

class Horariopublicidad extends ActiveRecord {

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
	protected $hora_inicio;

	/**
	 * @var string
	 */
	protected $hora_fin;

	/**
	 * @var string
	 */
	protected $tipo;

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
	 * Método para establecer el valor del campo hora_inicio
	 * @param string $hora_inicio
	 */
	public function setHoraInicio($hora_inicio){
		$this->hora_inicio = $hora_inicio;
	}

	/**
	 * Método para establecer el valor del campo hora_fin
	 * @param string $hora_fin
	 */
	public function setHoraFin($hora_fin){
		$this->hora_fin = $hora_fin;
	}

	/**
	 * Método para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
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
	 * Devuelve el valor del campo hora_inicio
	 * @return string
	 */
	public function getHoraInicio(){
		return $this->hora_inicio;
	}

	/**
	 * Devuelve el valor del campo hora_fin
	 * @return string
	 */
	public function getHoraFin(){
		return $this->hora_fin;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
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

