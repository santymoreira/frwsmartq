<?php

class Turnospreguntas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $preguntas_id;

	/**
	 * @var integer
	 */
	protected $caja_id;

	/**
	 * @var integer
	 */
	protected $turnos_id;

	/**
	 * @var integer
	 */
	protected $puntuacion;

	/**
	 * @var Date
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $hora;

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
	 * Método para establecer el valor del campo preguntas_id
	 * @param integer $preguntas_id
	 */
	public function setPreguntasId($preguntas_id){
		$this->preguntas_id = $preguntas_id;
	}

	/**
	 * Método para establecer el valor del campo caja_id
	 * @param integer $caja_id
	 */
	public function setCajaId($caja_id){
		$this->caja_id = $caja_id;
	}

	/**
	 * Método para establecer el valor del campo turnos_id
	 * @param integer $turnos_id
	 */
	public function setTurnosId($turnos_id){
		$this->turnos_id = $turnos_id;
	}

	/**
	 * Método para establecer el valor del campo puntuacion
	 * @param integer $puntuacion
	 */
	public function setPuntuacion($puntuacion){
		$this->puntuacion = $puntuacion;
	}

	/**
	 * Método para establecer el valor del campo fecha
	 * @param Date $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Método para establecer el valor del campo hora
	 * @param string $hora
	 */
	public function setHora($hora){
		$this->hora = $hora;
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
	 * Devuelve el valor del campo preguntas_id
	 * @return integer
	 */
	public function getPreguntasId(){
		return $this->preguntas_id;
	}

	/**
	 * Devuelve el valor del campo caja_id
	 * @return integer
	 */
	public function getCajaId(){
		return $this->caja_id;
	}

	/**
	 * Devuelve el valor del campo turnos_id
	 * @return integer
	 */
	public function getTurnosId(){
		return $this->turnos_id;
	}

	/**
	 * Devuelve el valor del campo puntuacion
	 * @return integer
	 */
	public function getPuntuacion(){
		return $this->puntuacion;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return Date
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo hora
	 * @return string
	 */
	public function getHora(){
		return $this->hora;
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
		$this->belongsTo('preguntas_id','preguntas','id');
		$this->belongsTo('caja_id','caja','id');
		$this->belongsTo('turnos_id','turnos','id');
	}

}

