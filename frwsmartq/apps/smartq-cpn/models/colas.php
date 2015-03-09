<?php

class Colas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $caja_id;

	/**
	 * @var integer
	 */
	protected $por_atender;

	/**
	 * @var integer
	 */
	protected $atendido;

	/**
	 * @var Date
	 */
	protected $fecha_inicio_atencion;

	/**
	 * @var string
	 */
	protected $hora_inicio_atencion;

	/**
	 * @var Date
	 */
	protected $fecha_fin_atencion;

	/**
	 * @var string
	 */
	protected $hora_fin_atencion;

	/**
	 * @var string
	 */
	protected $duracion;

	/**
	 * @var string
	 */
	protected $calificacion;

	/**
	 * @var Date
	 */
	protected $creacion_at;

        /**
	 * @var integer
	 */
	protected $id_username;

	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo caja_id
	 * @param integer $caja_id
	 */
	public function setCajaId($caja_id){
		$this->caja_id = $caja_id;
	}

	/**
	 * Método para establecer el valor del campo por_atender
	 * @param integer $por_atender
	 */
	public function setPorAtender($por_atender){
		$this->por_atender = $por_atender;
	}

	/**
	 * Método para establecer el valor del campo atendido
	 * @param integer $atendido
	 */
	public function setAtendido($atendido){
		$this->atendido = $atendido;
	}

	/**
	 * Método para establecer el valor del campo fecha_inicio_atencion
	 * @param Date $fecha_inicio_atencion
	 */
	public function setFechaInicioAtencion($fecha_inicio_atencion){
		$this->fecha_inicio_atencion = $fecha_inicio_atencion;
	}

	/**
	 * Método para establecer el valor del campo hora_inicio_atencion
	 * @param string $hora_inicio_atencion
	 */
	public function setHoraInicioAtencion($hora_inicio_atencion){
		$this->hora_inicio_atencion = $hora_inicio_atencion;
	}

	/**
	 * Método para establecer el valor del campo fecha_fin_atencion
	 * @param Date $fecha_fin_atencion
	 */
	public function setFechaFinAtencion($fecha_fin_atencion){
		$this->fecha_fin_atencion = $fecha_fin_atencion;
	}

	/**
	 * Método para establecer el valor del campo hora_fin_atencion
	 * @param string $hora_fin_atencion
	 */
	public function setHoraFinAtencion($hora_fin_atencion){
		$this->hora_fin_atencion = $hora_fin_atencion;
	}

	/**
	 * Método para establecer el valor del campo duracion
	 * @param string $duracion
	 */
	public function setDuracion($duracion){
		$this->duracion = $duracion;
	}

	/**
	 * Método para establecer el valor del campo calificacion
	 * @param string $calificacion
	 */
	public function setCalificacion($calificacion){
		$this->calificacion = $calificacion;
	}

	/**
	 * Método para establecer el valor del campo creacion_at
	 * @param Date $creacion_at
	 */
	public function setCreacionAt($creacion_at){
		$this->creacion_at = $creacion_at;
	}
        
	/**
	 * Método para establecer el valor del campo del usuario
	 * @param Integer $id_username
	 */
	public function setIdUsername($id_username){
		$this->id_username = $id_username;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo caja_id
	 * @return integer
	 */
	public function getCajaId(){
		return $this->caja_id;
	}

	/**
	 * Devuelve el valor del campo por_atender
	 * @return integer
	 */
	public function getPorAtender(){
		return $this->por_atender;
	}

	/**
	 * Devuelve el valor del campo atendido
	 * @return integer
	 */
	public function getAtendido(){
		return $this->atendido;
	}

	/**
	 * Devuelve el valor del campo fecha_inicio_atencion
	 * @return Date
	 */
	public function getFechaInicioAtencion(){
		return $this->fecha_inicio_atencion;
	}

	/**
	 * Devuelve el valor del campo hora_inicio_atencion
	 * @return string
	 */
	public function getHoraInicioAtencion(){
		return $this->hora_inicio_atencion;
	}

	/**
	 * Devuelve el valor del campo fecha_fin_atencion
	 * @return Date
	 */
	public function getFechaFinAtencion(){
		return $this->fecha_fin_atencion;
	}

	/**
	 * Devuelve el valor del campo hora_fin_atencion
	 * @return string
	 */
	public function getHoraFinAtencion(){
		return $this->hora_fin_atencion;
	}

	/**
	 * Devuelve el valor del campo duracion
	 * @return string
	 */
	public function getDuracion(){
		return $this->duracion;
	}

	/**
	 * Devuelve el valor del campo calificacion
	 * @return string
	 */
	public function getCalificacion(){
		return $this->calificacion;
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
	public function getIdUsername(){
		return $this->id_username;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('caja_id','caja','id');
	}

}

