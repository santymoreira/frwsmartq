<?php

class Sinchistorialcajas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $sucursal_id;

	/**
	 * @var Date
	 */
	protected $fecha_sincronizacion;

	/**
	 * @var string
	 */
	protected $hora_sincronizacion;

	/**
	 * @var string
	 */
	protected $registros_sincronizados;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo sucursal_id
	 * @param integer $sucursal_id
	 */
	public function setSucursalId($sucursal_id){
		$this->sucursal_id = $sucursal_id;
	}

	/**
	 * Método para establecer el valor del campo fecha_sincronizacion
	 * @param Date $fecha_sincronizacion
	 */
	public function setFechaSincronizacion($fecha_sincronizacion){
		$this->fecha_sincronizacion = $fecha_sincronizacion;
	}

	/**
	 * Método para establecer el valor del campo hora_sincronizacion
	 * @param string $hora_sincronizacion
	 */
	public function setHoraSincronizacion($hora_sincronizacion){
		$this->hora_sincronizacion = $hora_sincronizacion;
	}

	/**
	 * Método para establecer el valor del campo registros_sincronizados
	 * @param string $registros_sincronizados
	 */
	public function setRegistrosSincronizados($registros_sincronizados){
		$this->registros_sincronizados = $registros_sincronizados;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo sucursal_id
	 * @return integer
	 */
	public function getSucursalId(){
		return $this->sucursal_id;
	}

	/**
	 * Devuelve el valor del campo fecha_sincronizacion
	 * @return Date
	 */
	public function getFechaSincronizacion(){
		return $this->fecha_sincronizacion;
	}

	/**
	 * Devuelve el valor del campo hora_sincronizacion
	 * @return string
	 */
	public function getHoraSincronizacion(){
		return $this->hora_sincronizacion;
	}

	/**
	 * Devuelve el valor del campo registros_sincronizados
	 * @return string
	 */
	public function getRegistrosSincronizados(){
		return $this->registros_sincronizados;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('sucursal_id','sucursal','id');
	}

}

