<?php

class Sincturnos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $id_referencia;

	/**
	 * @var integer
	 */
	protected $sucursal_id;

	/**
	 * @var string
	 */
	protected $base_datos;

	/**
	 * @var string
	 */
	protected $usuario;

        /**
	 * @var integer
	 */
	protected $numero_modulo;

         /**
	 * @var integer
	 */
	protected $modulo_id_sucursal;

	/**
	 * @var string
	 */
	protected $nombre_servicio;

        /**
	 * @var string
	 */
	protected $letra;

        /**
	 * @var integer
	 */
	protected $servicio_id_sucursal;

	/**
	 * @var string
	 */
	protected $numero_turno;

	/**
	 * @var Date
	 */
	protected $fecha_emision;

	/**
	 * @var string
	 */
	protected $hora_emision;

        /**
	 * @var string
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
	protected $rechazado;

	/**
	 * @var string
	 */
	protected $calificacion;

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
	 * Método para establecer el valor del campo id_referencia
	 * @param integer $id_referencia
	 */
	public function setIdReferencia($id_referencia){
		$this->id_referencia = $id_referencia;
	}

	/**
	 * Método para establecer el valor del campo nombre_sucursal
	 * @param string $nombre_sucursal
	 */
	public function setSucursalId($sucursal_id){
		$this->sucursal_id = $sucursal_id;
	}

	/**
	 * Método para establecer el valor del campo base_datos
	 * @param string $base_datos
	 */
	public function setBaseDatos($base_datos){
		$this->base_datos = $base_datos;
	}

	/**
	 * Método para establecer el valor del campo usuario
	 * @param string $usuario
	 */
	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}

        /**
	 * Método para establecer el valor del campo usuario
	 * @param string $usuario
	 */
	public function setNumeroModulo($numero_modulo){
		$this->numero_modulo = $numero_modulo;
	}

        /**
	 * Método para establecer el valor del campo usuario
	 * @param string $usuario
	 */
	public function setModuloIdSucursal($modulo_id_sucursal){
		$this->modulo_id_sucursal = $modulo_id_sucursal;
	}

	/**
	 * Método para establecer el valor del campo nombre_servicio
	 * @param string $nombre_servicio
	 */
	public function setNombreServicio($nombre_servicio){
		$this->nombre_servicio = $nombre_servicio;
	}

        /**
	 * Método para establecer el valor del campo nombre_servicio
	 * @param string $nombre_servicio
	 */
	public function setLetra($letra){
		$this->letra = $letra;
	}

        /**
	 * Método para establecer el valor del campo usuario
	 * @param string $usuario
	 */
	public function setServicioIdSucursal($servicio_id_sucursal){
		$this->servicio_id_sucursal = $servicio_id_sucursal;
	}

	/**
	 * Método para establecer el valor del campo numero_turno
	 * @param string $numero_turno
	 */
	public function setNumeroTurno($numero_turno){
		$this->numero_turno = $numero_turno;
	}

	/**
	 * Método para establecer el valor del campo fecha_emision
	 * @param Date $fecha_emision
	 */
	public function setFechaEmision($fecha_emision){
		$this->fecha_emision = $fecha_emision;
	}

	/**
	 * Método para establecer el valor del campo hora_emision
	 * @param string $hora_emision
	 */
	public function setHoraEmision($hora_emision){
		$this->hora_emision = $hora_emision;
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
	public function setAtendido($atendido){
		$this->atendido = $atendido;
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
	 * Método para establecer el valor del campo duracion
	 * @param string $duracion
	 */
	public function setRechazado($rechazado){
		$this->rechazado = $rechazado;
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
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo id_referencia
	 * @return integer
	 */
	public function getIdReferencia(){
		return $this->id_referencia;
	}

	/**
	 * Devuelve el valor del campo nombre_sucursal
	 * @return string
	 */
	public function getSucursalId(){
		return $this->sucursal_id;
	}

	/**
	 * Devuelve el valor del campo base_datos
	 * @return string
	 */
	public function getBaseDatos(){
		return $this->base_datos;
	}

	/**
	 * Devuelve el valor del campo usuario
	 * @return string
	 */
	public function getUsuario(){
		return $this->usuario;
	}

        /**
	 * Devuelve el valor del campo usuario
	 * @return string
	 */
	public function getNumeroModulo(){
		return $this->numero_modulo;
	}

        /**
	 * Devuelve el valor del campo usuario
	 * @return string
	 */
	public function getModuloIdSucursal(){
		return $this->modulo_id_sucursal;
	}

	/**
	 * Devuelve el valor del campo nombre_servicio
	 * @return string
	 */
	public function getNombreServicio(){
		return $this->nombre_servicio;
	}

        /**
	 * Devuelve el valor del campo nombre_servicio
	 * @return string
	 */
	public function getLetra(){
		return $this->letra;
	}

        /**
	 * Devuelve el valor del campo usuario
	 * @return string
	 */
	public function getServicioIdSucursal(){
		return $this->servicio_id_sucursal;
	}

	/**
	 * Devuelve el valor del campo numero_turno
	 * @return string
	 */
	public function getNumeroTurno(){
		return $this->numero_turno;
	}

	/**
	 * Devuelve el valor del campo fecha_emision
	 * @return Date
	 */
	public function getFechaEmision(){
		return $this->fecha_emision;
	}

	/**
	 * Devuelve el valor del campo hora_emision
	 * @return string
	 */
	public function getHoraEmision(){
		return $this->hora_emision;
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
	public function getAtendido(){
		return $this->atendido;
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
	 * Devuelve el valor del campo duracion
	 * @return string
	 */
	public function getRechazado(){
		return $this->rechazado;
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
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

