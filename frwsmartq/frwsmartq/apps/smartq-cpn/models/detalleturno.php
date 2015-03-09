<?php

class Detalleturno extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var integer
	 */
	protected $turno_id;

	/**
	 * @var integer
	 */
	protected $servicio_id;

	/**
	 * @var integer
	 */
	protected $caja_id;

	/**
	 * @var Date
	 */
	protected $fechaEmision;

	/**
	 * @var string
	 */
	protected $horaEmision;

	/**
	 * @var Date
	 */
	protected $fechaAtencion;

	/**
	 * @var string
	 */
	protected $horaAtencion;


	/**
	 * Método para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
	}

	/**
	 * Método para establecer el valor del campo turno_id
	 * @param integer $turno_id
	 */
	public function setTurnoId($turno_id){
		$this->turno_id = $turno_id;
	}

	/**
	 * Método para establecer el valor del campo servicio_id
	 * @param integer $servicio_id
	 */
	public function setServicioId($servicio_id){
		$this->servicio_id = $servicio_id;
	}

	/**
	 * Método para establecer el valor del campo caja_id
	 * @param integer $caja_id
	 */
	public function setCajaId($caja_id){
		$this->caja_id = $caja_id;
	}

	/**
	 * Método para establecer el valor del campo fechaEmision
	 * @param Date $fechaEmision
	 */
	public function setFechaEmision($fechaEmision){
		$this->fechaEmision = $fechaEmision;
	}

	/**
	 * Método para establecer el valor del campo horaEmision
	 * @param string $horaEmision
	 */
	public function setHoraEmision($horaEmision){
		$this->horaEmision = $horaEmision;
	}

	/**
	 * Método para establecer el valor del campo fechaAtencion
	 * @param Date $fechaAtencion
	 */
	public function setFechaAtencion($fechaAtencion){
		$this->fechaAtencion = $fechaAtencion;
	}

	/**
	 * Método para establecer el valor del campo horaAtencion
	 * @param string $horaAtencion
	 */
	public function setHoraAtencion($horaAtencion){
		$this->horaAtencion = $horaAtencion;
	}


	/**
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
	}

	/**
	 * Devuelve el valor del campo turno_id
	 * @return integer
	 */
	public function getTurnoId(){
		return $this->turno_id;
	}

	/**
	 * Devuelve el valor del campo servicio_id
	 * @return integer
	 */
	public function getServicioId(){
		return $this->servicio_id;
	}

	/**
	 * Devuelve el valor del campo caja_id
	 * @return integer
	 */
	public function getCajaId(){
		return $this->caja_id;
	}

	/**
	 * Devuelve el valor del campo fechaEmision
	 * @return Date
	 */
	public function getFechaEmision(){
		return $this->fechaEmision;
	}

	/**
	 * Devuelve el valor del campo horaEmision
	 * @return string
	 */
	public function getHoraEmision(){
		return $this->horaEmision;
	}

	/**
	 * Devuelve el valor del campo fechaAtencion
	 * @return Date
	 */
	public function getFechaAtencion(){
		return $this->fechaAtencion;
	}

	/**
	 * Devuelve el valor del campo horaAtencion
	 * @return string
	 */
	public function getHoraAtencion(){
		return $this->horaAtencion;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('usuario_id','usuario','id');
		$this->belongsTo('turno_id','turno','id');
		$this->belongsTo('servicio_id','servicio','id');
		$this->belongsTo('caja_id','caja','id');
	}

}

