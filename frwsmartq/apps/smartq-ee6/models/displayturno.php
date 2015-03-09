<?php

class Displayturno extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $displayId;

	/**
	 * @var integer
	 */
	protected $numeroturno;

	/**
	 * @var integer
	 */
	protected $fecha;

	/**
	 * @var string
	 */
	protected $cajanumero;
        protected $turno;
        protected $ubicacion;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

        public function setTurno($turno){
		$this->turno = $turno;
	}

	/**
	 * Método para establecer el valor del campo displayId
	 * @param integer $displayId
	 */
	public function setDisplayId($displayId){
		$this->displayId = $displayId;
	}

	/**
	 * Método para establecer el valor del campo numeroturno
	 * @param integer $numeroturno
	 */
	public function setNumeroturno($numeroturno){
		$this->numeroturno = $numeroturno;
	}

	/**
	 * Método para establecer el valor del campo valor
	 * @param integer $valor
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Método para establecer el valor del campo cajanumero
	 * @param integer $cajanumero
	 */
	public function setCajanumero($cajanumero){
		$this->cajanumero = $cajanumero;
	}

        public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

        public function getTurno(){
		return $this->turno;
	}

	/**
	 * Devuelve el valor del campo displayId
	 * @return integer
	 */
	public function getDisplayId(){
		return $this->displayId;
	}

	/**
	 * Devuelve el valor del campo numeroturno
	 * @return integer
	 */
	public function getNumeroturno(){
		return $this->numeroturno;
	}

	/**
	 * Devuelve el valor del campo valor
	 * @return integer
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo cajanumero
	 * @return integer
	 */
	public function getCajanumero(){
		return $this->cajanumero;
	}
        public function getUbicacion(){
		return $this->ubicacion;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
            $this->belongsTo('ubicacion','pantalla','ubicacion_id');
	}

}

