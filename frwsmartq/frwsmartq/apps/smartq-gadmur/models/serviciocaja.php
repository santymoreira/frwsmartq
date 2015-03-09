<?php

class Serviciocaja extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $servicio_id;

	/**
	 * @var integer
	 */
	protected $caja_id;

	/**
	 * @var integer
	 */
	protected $estado;

        protected $secundario;

        protected $llamar;


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
	 * Método para establecer el valor del campo estado
	 * @param integer $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

        public function setSecundario($secundario){
		$this->secundario = $secundario;
	}

        public function setLlamar($llamar){
		$this->llamar = $llamar;
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
	 * Devuelve el valor del campo estado
	 * @return integer
	 */
	public function getEstado(){
		return $this->estado;
	}

        public function getSecundario(){
		return $this->secundario;
	}

        public function getLlamar(){
		return $this->llamar;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('servicio_id','servicio','id');
		$this->belongsTo('caja_id','caja','id');
	}

}

