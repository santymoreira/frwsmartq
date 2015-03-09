<?php

class Detallehorario extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $horario_id;

	/**
	 * @var string
	 */
	protected $dia;

	/**
	 * @var string
	 */
	protected $hora_inicial1;

	/**
	 * @var string
	 */
	protected $hora_final1;

	/**
	 * @var string
	 */
	protected $hora_inicial2;

	/**
	 * @var string
	 */
	protected $hora_final2;

	/**
	 * @var string
	 */
	protected $hora_inicial3;

	/**
	 * @var string
	 */
	protected $hora_final3;

	/**
	 * @var string
	 */
	protected $hora_inicial4;

	/**
	 * @var string
	 */
	protected $hora_final4;

	/**
	 * @var string
	 */
	protected $horas_laborables;

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
	 * Método para establecer el valor del campo horario_id
	 * @param integer $horario_id
	 */
	public function setHorarioId($horario_id){
		$this->horario_id = $horario_id;
	}

	/**
	 * Método para establecer el valor del campo dia
	 * @param string $dia
	 */
	public function setDia($dia){
		$this->dia = $dia;
	}

	/**
	 * Método para establecer el valor del campo hora_inicial1
	 * @param string $hora_inicial1
	 */
	public function setHoraInicial1($hora_inicial1){
		$this->hora_inicial1 = $hora_inicial1;
	}

	/**
	 * Método para establecer el valor del campo hora_final1
	 * @param string $hora_final1
	 */
	public function setHoraFinal1($hora_final1){
		$this->hora_final1 = $hora_final1;
	}

	/**
	 * Método para establecer el valor del campo hora_inicial2
	 * @param string $hora_inicial2
	 */
	public function setHoraInicial2($hora_inicial2){
		$this->hora_inicial2 = $hora_inicial2;
	}

	/**
	 * Método para establecer el valor del campo hora_final2
	 * @param string $hora_final2
	 */
	public function setHoraFinal2($hora_final2){
		$this->hora_final2 = $hora_final2;
	}

	/**
	 * Método para establecer el valor del campo hora_inicial3
	 * @param string $hora_inicial3
	 */
	public function setHoraInicial3($hora_inicial3){
		$this->hora_inicial3 = $hora_inicial3;
	}

	/**
	 * Método para establecer el valor del campo hora_final3
	 * @param string $hora_final3
	 */
	public function setHoraFinal3($hora_final3){
		$this->hora_final3 = $hora_final3;
	}

	/**
	 * Método para establecer el valor del campo hora_inicial4
	 * @param string $hora_inicial4
	 */
	public function setHoraInicial4($hora_inicial4){
		$this->hora_inicial4 = $hora_inicial4;
	}

	/**
	 * Método para establecer el valor del campo hora_final4
	 * @param string $hora_final4
	 */
	public function setHoraFinal4($hora_final4){
		$this->hora_final4 = $hora_final4;
	}

	/**
	 * Método para establecer el valor del campo horas_laborables
	 * @param string $horas_laborables
	 */
	public function setHorasLaborables($horas_laborables){
		$this->horas_laborables = $horas_laborables;
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
	 * Devuelve el valor del campo horario_id
	 * @return integer
	 */
	public function getHorarioId(){
		return $this->horario_id;
	}

	/**
	 * Devuelve el valor del campo dia
	 * @return string
	 */
	public function getDia(){
		return $this->dia;
	}

	/**
	 * Devuelve el valor del campo hora_inicial1
	 * @return string
	 */
	public function getHoraInicial1(){
		return $this->hora_inicial1;
	}

	/**
	 * Devuelve el valor del campo hora_final1
	 * @return string
	 */
	public function getHoraFinal1(){
		return $this->hora_final1;
	}

	/**
	 * Devuelve el valor del campo hora_inicial2
	 * @return string
	 */
	public function getHoraInicial2(){
		return $this->hora_inicial2;
	}

	/**
	 * Devuelve el valor del campo hora_final2
	 * @return string
	 */
	public function getHoraFinal2(){
		return $this->hora_final2;
	}

	/**
	 * Devuelve el valor del campo hora_inicial3
	 * @return string
	 */
	public function getHoraInicial3(){
		return $this->hora_inicial3;
	}

	/**
	 * Devuelve el valor del campo hora_final3
	 * @return string
	 */
	public function getHoraFinal3(){
		return $this->hora_final3;
	}

	/**
	 * Devuelve el valor del campo hora_inicial4
	 * @return string
	 */
	public function getHoraInicial4(){
		return $this->hora_inicial4;
	}

	/**
	 * Devuelve el valor del campo hora_final4
	 * @return string
	 */
	public function getHoraFinal4(){
		return $this->hora_final4;
	}

	/**
	 * Devuelve el valor del campo horas_laborables
	 * @return string
	 */
	public function getHorasLaborables(){
		return $this->horas_laborables;
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
		$this->belongsTo('horario_id','horario','id');
	}

}

