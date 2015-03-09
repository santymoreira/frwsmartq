<?php

class TurnosTransferidos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $servicio_id;

	/**
	 * @var integer
	 */
	protected $numero;

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
	protected $hora_transferido;

	/**
	 * @var integer
	 */
	protected $estado;

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
	 * @var integer
	 */
	protected $rechazado;

	/**
	 * @var string
	 */
	protected $calificacion;

	/**
	 * @var string
	 */
	protected $permiso_cajas;

	/**
	 * @var string
	 */
	protected $letra;

	/**
	 * @var integer
	 */
	protected $remitente;

	/**
	 * @var integer
	 */
	protected $ubicacion_id;

	/**
	 * @var string
	 */
	protected $tipo;

	/**
	 * @var Date
	 */
	protected $fecha_atender;

	/**
	 * @var string
	 */
	protected $hora_atender;

	/**
	 * @var integer
	 */
	protected $adm_revisado;

	/**
	 * @var integer
	 */
	protected $id_user_atiende;

	/**
	 * @var integer
	 */
	protected $id_user_transfiere;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo servicio_id
	 * @param integer $servicio_id
	 */
	public function setServicioId($servicio_id){
		$this->servicio_id = $servicio_id;
	}

	/**
	 * Método para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
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
	 * Método para establecer el valor del campo hora_transferido
	 * @param string $hora_transferido
	 */
	public function setHoraTransferido($hora_transferido){
		$this->hora_transferido = $hora_transferido;
	}

	/**
	 * Método para establecer el valor del campo estado
	 * @param integer $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
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
	 * Método para establecer el valor del campo rechazado
	 * @param integer $rechazado
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
	 * Método para establecer el valor del campo permiso_cajas
	 * @param string $permiso_cajas
	 */
	public function setPermisoCajas($permiso_cajas){
		$this->permiso_cajas = $permiso_cajas;
	}

	/**
	 * Método para establecer el valor del campo letra
	 * @param string $letra
	 */
	public function setLetra($letra){
		$this->letra = $letra;
	}

	/**
	 * Método para establecer el valor del campo remitente
	 * @param integer $remitente
	 */
	public function setRemitente($remitente){
		$this->remitente = $remitente;
	}

	/**
	 * Método para establecer el valor del campo ubicacion_id
	 * @param integer $ubicacion_id
	 */
	public function setUbicacionId($ubicacion_id){
		$this->ubicacion_id = $ubicacion_id;
	}

	/**
	 * Método para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}

	/**
	 * Método para establecer el valor del campo fecha_atender
	 * @param Date $fecha_atender
	 */
	public function setFechaAtender($fecha_atender){
		$this->fecha_atender = $fecha_atender;
	}

	/**
	 * Método para establecer el valor del campo hora_atender
	 * @param string $hora_atender
	 */
	public function setHoraAtender($hora_atender){
		$this->hora_atender = $hora_atender;
	}

	/**
	 * Método para establecer el valor del campo adm_revisado
	 * @param integer $adm_revisado
	 */
	public function setAdmRevisado($adm_revisado){
		$this->adm_revisado = $adm_revisado;
	}

	/**
	 * Método para establecer el valor del campo id_user_atiende
	 * @param integer $id_user_atiende
	 */
	public function setIdUserAtiende($id_user_atiende){
		$this->id_user_atiende = $id_user_atiende;
	}

	/**
	 * Método para establecer el valor del campo id_user_transfiere
	 * @param integer $id_user_transfiere
	 */
	public function setIdUserTransfiere($id_user_transfiere){
		$this->id_user_transfiere = $id_user_transfiere;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo servicio_id
	 * @return integer
	 */
	public function getServicioId(){
		return $this->servicio_id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
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
	 * Devuelve el valor del campo hora_transferido
	 * @return string
	 */
	public function getHoraTransferido(){
		return $this->hora_transferido;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return integer
	 */
	public function getEstado(){
		return $this->estado;
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
	 * Devuelve el valor del campo rechazado
	 * @return integer
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
	 * Devuelve el valor del campo permiso_cajas
	 * @return string
	 */
	public function getPermisoCajas(){
		return $this->permiso_cajas;
	}

	/**
	 * Devuelve el valor del campo letra
	 * @return string
	 */
	public function getLetra(){
		return $this->letra;
	}

	/**
	 * Devuelve el valor del campo remitente
	 * @return integer
	 */
	public function getRemitente(){
		return $this->remitente;
	}

	/**
	 * Devuelve el valor del campo ubicacion_id
	 * @return integer
	 */
	public function getUbicacionId(){
		return $this->ubicacion_id;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

	/**
	 * Devuelve el valor del campo fecha_atender
	 * @return Date
	 */
	public function getFechaAtender(){
		return $this->fecha_atender;
	}

	/**
	 * Devuelve el valor del campo hora_atender
	 * @return string
	 */
	public function getHoraAtender(){
		return $this->hora_atender;
	}

	/**
	 * Devuelve el valor del campo adm_revisado
	 * @return integer
	 */
	public function getAdmRevisado(){
		return $this->adm_revisado;
	}

	/**
	 * Devuelve el valor del campo id_user_atiende
	 * @return integer
	 */
	public function getIdUserAtiende(){
		return $this->id_user_atiende;
	}

	/**
	 * Devuelve el valor del campo id_user_transfiere
	 * @return integer
	 */
	public function getIdUserTransfiere(){
		return $this->id_user_transfiere;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('servicio_id','servicio','id');
		$this->belongsTo('caja_id','caja','id');
		$this->belongsTo('ubicacion_id','ubicacion','id');
	}

}

