<?php

class Controlacceso extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var Date
	 */
	protected $sesion_inicio;

	/**
	 * @var string
	 */
	protected $hora_inicio;

	/**
	 * @var Date
	 */
	protected $sesion_fin;

	/**
	 * @var string
	 */
	protected $hora_fin;

	/**
	 * @var string
	 */
	protected $duracion;

	/**
	 * @var integer
	 */
	protected $estado;

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
	 * Método para establecer el valor del campo ip
	 * @param string $ip
	 */
	public function setIp($ip){
		$this->ip = $ip;
	}

	/**
	 * Método para establecer el valor del campo sesion_inicio
	 * @param Date $sesion_inicio
	 */
	public function setSesionInicio($sesion_inicio){
		$this->sesion_inicio = $sesion_inicio;
	}

	/**
	 * Método para establecer el valor del campo hora_inicio
	 * @param string $hora_inicio
	 */
	public function setHoraInicio($hora_inicio){
		$this->hora_inicio = $hora_inicio;
	}

	/**
	 * Método para establecer el valor del campo sesion_fin
	 * @param Date $sesion_fin
	 */
	public function setSesionFin($sesion_fin){
		$this->sesion_fin = $sesion_fin;
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
	 * Método para establecer el valor del campo estado
	 * @param integer $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
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
	 * Devuelve el valor del campo ip
	 * @return string
	 */
	public function getIp(){
		return $this->ip;
	}

	/**
	 * Devuelve el valor del campo sesion_inicio
	 * @return Date
	 */
	public function getSesionInicio(){
		return $this->sesion_inicio;
	}

	/**
	 * Devuelve el valor del campo hora_inicio
	 * @return string
	 */
	public function getHoraInicio(){
		return $this->hora_inicio;
	}

	/**
	 * Devuelve el valor del campo sesion_fin
	 * @return Date
	 */
	public function getSesionFin(){
		return $this->sesion_fin;
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
	 * Devuelve el valor del campo estado
	 * @return integer
	 */
	public function getEstado(){
		return $this->estado;
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
	}

}

