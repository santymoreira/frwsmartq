<?php

class Display extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $formato;

	/**
	 * @var string
	 */
	protected $turnos;

	/**
	 * @var integer
	 */
	protected $chkbanner;

	/**
	 * @var integer
	 */
	protected $numvideos;

	/**
	 * @var string
	 */
	protected $horainicio;

	/**
	 * @var string
	 */
	protected $horafin;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo formato
	 * @param integer $formato
	 */
	public function setFormato($formato){
		$this->formato = $formato;
	}

	/**
	 * Método para establecer el valor del campo turnos
	 * @param string $turnos
	 */
	public function setTurnos($turnos){
		$this->turnos = $turnos;
	}

	/**
	 * Método para establecer el valor del campo chkbanner
	 * @param integer $chkbanner
	 */
	public function setChkbanner($chkbanner){
		$this->chkbanner = $chkbanner;
	}

	/**
	 * Método para establecer el valor del campo numvideos
	 * @param integer $numvideos
	 */
	public function setNumvideos($numvideos){
		$this->numvideos = $numvideos;
	}

	/**
	 * Método para establecer el valor del campo horainicio
	 * @param string $horainicio
	 */
	public function setHorainicio($horainicio){
		$this->horainicio = $horainicio;
	}

	/**
	 * Método para establecer el valor del campo horafin
	 * @param string $horafin
	 */
	public function setHorafin($horafin){
		$this->horafin = $horafin;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo formato
	 * @return integer
	 */
	public function getFormato(){
		return $this->formato;
	}

	/**
	 * Devuelve el valor del campo turnos
	 * @return string
	 */
	public function getTurnos(){
		return $this->turnos;
	}

	/**
	 * Devuelve el valor del campo chkbanner
	 * @return integer
	 */
	public function getChkbanner(){
		return $this->chkbanner;
	}

	/**
	 * Devuelve el valor del campo numvideos
	 * @return integer
	 */
	public function getNumvideos(){
		return $this->numvideos;
	}

	/**
	 * Devuelve el valor del campo horainicio
	 * @return string
	 */
	public function getHorainicio(){
		return $this->horainicio;
	}

	/**
	 * Devuelve el valor del campo horafin
	 * @return string
	 */
	public function getHorafin(){
		return $this->horafin;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
            
	}

}

