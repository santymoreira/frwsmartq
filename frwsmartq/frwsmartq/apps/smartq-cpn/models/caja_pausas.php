<?php

class CajaPausas extends ActiveRecord {

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
	 * @var integer
	 */
	protected $pausas_id;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var Date
	 */
	protected $fecha_inicio;

	/**
	 * @var string
	 */
	protected $hora_inicio;

	/**
	 * @var Date
	 */
	protected $fecha_fin;

	/**
	 * @var string
	 */
	protected $hora_fin;

	/**
	 * @var string
	 */
	protected $duracion;

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
	 * Método para establecer el valor del campo pausas_id
	 * @param integer $pausas_id
	 */
	public function setPausasId($pausas_id){
		$this->pausas_id = $pausas_id;
	}

	/**
	 * Método para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Método para establecer el valor del campo fecha_inicio
	 * @param Date $fecha_inicio
	 */
	public function setFechaInicio($fecha_inicio){
		$this->fecha_inicio = $fecha_inicio;
	}

	/**
	 * Método para establecer el valor del campo hora_inicio
	 * @param string $hora_inicio
	 */
	public function setHoraInicio($hora_inicio){
		$this->hora_inicio = $hora_inicio;
	}

	/**
	 * Método para establecer el valor del campo fecha_fin
	 * @param Date $fecha_fin
	 */
	public function setFechaFin($fecha_fin){
		$this->fecha_fin = $fecha_fin;
	}

	/**
	 * Método para establecer el valor del campo hora_fin
	 * @param string $hora_fin
	 */
	public function setHoraFin($hora_fin){
		$this->hora_fin = $hora_fin;
	}

	/**
	 * Método para establecer el valor del campo duracion
	 * @param string $duracion
	 */
	public function setDuracion($duracion){
		$this->duracion = $duracion;
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
	 * Devuelve el valor del campo pausas_id
	 * @return integer
	 */
	public function getPausasId(){
		return $this->pausas_id;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo fecha_inicio
	 * @return Date
	 */
	public function getFechaInicio(){
		return $this->fecha_inicio;
	}

	/**
	 * Devuelve el valor del campo hora_inicio
	 * @return string
	 */
	public function getHoraInicio(){
		return $this->hora_inicio;
	}

	/**
	 * Devuelve el valor del campo fecha_fin
	 * @return Date
	 */
	public function getFechaFin(){
		return $this->fecha_fin;
	}

	/**
	 * Devuelve el valor del campo hora_fin
	 * @return string
	 */
	public function getHoraFin(){
		return $this->hora_fin;
	}

	/**
	 * Devuelve el valor del campo duracion
	 * @return string
	 */
	public function getDuracion(){
		return $this->duracion;
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
		$this->belongsTo('pausas_id','pausas','id');
	}

}

