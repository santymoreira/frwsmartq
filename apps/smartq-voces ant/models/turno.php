<?php

class Turno extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var Date
	 */
	protected $fechaEmision;

	/**
	 * @var string
	 */
	protected $horaEmision;

	/**
	 * @var integer
	 */
	protected $terceraEdad;

	/**
	 * @var integer
	 */
	protected $prioridad;

	/**
	 * @var integer
	 */
	protected $estado;

	/**
	 * @var integer
	 */
	protected $servicio_id;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
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
	 * Método para establecer el valor del campo terceraEdad
	 * @param integer $terceraEdad
	 */
	public function setTerceraEdad($terceraEdad){
		$this->terceraEdad = $terceraEdad;
	}

	/**
	 * Método para establecer el valor del campo prioridad
	 * @param integer $prioridad
	 */
	public function setPrioridad($prioridad){
		$this->prioridad = $prioridad;
	}

	/**
	 * Método para establecer el valor del campo estado
	 * @param integer $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Método para establecer el valor del campo servicio_id
	 * @param integer $servicio_id
	 */
	public function setServicioId($servicio_id){
		$this->servicio_id = $servicio_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
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
	 * Devuelve el valor del campo terceraEdad
	 * @return integer
	 */
	public function getTerceraEdad(){
		return $this->terceraEdad;
	}

	/**
	 * Devuelve el valor del campo prioridad
	 * @return integer
	 */
	public function getPrioridad(){
		return $this->prioridad;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return integer
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo servicio_id
	 * @return integer
	 */
	public function getServicioId(){
		return $this->servicio_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

