<?php

class Inicioturnos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $inicio_turno;

	/**
	 * @var integer
	 */
	protected $fin_turno;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo inicio_turno
	 * @param integer $inicio_turno
	 */
	public function setInicioTurno($inicio_turno){
		$this->inicio_turno = $inicio_turno;
	}

	/**
	 * Método para establecer el valor del campo fin_turno
	 * @param integer $fin_turno
	 */
	public function setFinTurno($fin_turno){
		$this->fin_turno = $fin_turno;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo inicio_turno
	 * @return integer
	 */
	public function getInicioTurno(){
		return $this->inicio_turno;
	}

	/**
	 * Devuelve el valor del campo fin_turno
	 * @return integer
	 */
	public function getFinTurno(){
		return $this->fin_turno;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

