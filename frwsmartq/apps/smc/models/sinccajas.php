<?php

class Sinccajas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $sucursal_id;

	/**
	 * @var string
	 */
	protected $base_datos;

	/**
	 * @var integer
	 */
	protected $caja_id_sucursal;

	/**
	 * @var string
	 */
	protected $usuario_sucursal;

	/**
	 * @var integer
	 */
	protected $numero_caja_sucursal;

	/**
	 * @var integer
	 */
	protected $area_id_sucursal;

	/**
	 * @var string
	 */
	protected $area_nombre_sucursal;

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
	protected $hora_fin_atenciom;

	/**
	 * @var string
	 */
	protected $duracion;


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
	 * Método para establecer el valor del campo base_datos
	 * @param string $base_datos
	 */
	public function setBaseDatos($base_datos){
		$this->base_datos = $base_datos;
	}

	/**
	 * Método para establecer el valor del campo caja_id_sucursal
	 * @param integer $caja_id_sucursal
	 */
	public function setCajaIdSucursal($caja_id_sucursal){
		$this->caja_id_sucursal = $caja_id_sucursal;
	}

	/**
	 * Método para establecer el valor del campo usuario_sucursal
	 * @param string $usuario_sucursal
	 */
	public function setUsuarioSucursal($usuario_sucursal){
		$this->usuario_sucursal = $usuario_sucursal;
	}

	/**
	 * Método para establecer el valor del campo numero_caja_sucursal
	 * @param integer $numero_caja_sucursal
	 */
	public function setNumeroCajaSucursal($numero_caja_sucursal){
		$this->numero_caja_sucursal = $numero_caja_sucursal;
	}

	/**
	 * Método para establecer el valor del campo area_id_sucursal
	 * @param integer $area_id_sucursal
	 */
	public function setAreaIdSucursal($area_id_sucursal){
		$this->area_id_sucursal = $area_id_sucursal;
	}

	/**
	 * Método para establecer el valor del campo area_nombre_sucursal
	 * @param string $area_nombre_sucursal
	 */
	public function setAreaNombreSucursal($area_nombre_sucursal){
		$this->area_nombre_sucursal = $area_nombre_sucursal;
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
	 * Método para establecer el valor del campo hora_fin_atenciom
	 * @param string $hora_fin_atenciom
	 */
	public function setHoraFinAtenciom($hora_fin_atenciom){
		$this->hora_fin_atenciom = $hora_fin_atenciom;
	}

	/**
	 * Método para establecer el valor del campo duracion
	 * @param string $duracion
	 */
	public function setDuracion($duracion){
		$this->duracion = $duracion;
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
	 * Devuelve el valor del campo base_datos
	 * @return string
	 */
	public function getBaseDatos(){
		return $this->base_datos;
	}

	/**
	 * Devuelve el valor del campo caja_id_sucursal
	 * @return integer
	 */
	public function getCajaIdSucursal(){
		return $this->caja_id_sucursal;
	}

	/**
	 * Devuelve el valor del campo usuario_sucursal
	 * @return string
	 */
	public function getUsuarioSucursal(){
		return $this->usuario_sucursal;
	}

	/**
	 * Devuelve el valor del campo numero_caja_sucursal
	 * @return integer
	 */
	public function getNumeroCajaSucursal(){
		return $this->numero_caja_sucursal;
	}

	/**
	 * Devuelve el valor del campo area_id_sucursal
	 * @return integer
	 */
	public function getAreaIdSucursal(){
		return $this->area_id_sucursal;
	}

	/**
	 * Devuelve el valor del campo area_nombre_sucursal
	 * @return string
	 */
	public function getAreaNombreSucursal(){
		return $this->area_nombre_sucursal;
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
	 * Devuelve el valor del campo hora_fin_atenciom
	 * @return string
	 */
	public function getHoraFinAtenciom(){
		return $this->hora_fin_atenciom;
	}

	/**
	 * Devuelve el valor del campo duracion
	 * @return string
	 */
	public function getDuracion(){
		return $this->duracion;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('sucursal_id','sucursal','id');
	}

}

