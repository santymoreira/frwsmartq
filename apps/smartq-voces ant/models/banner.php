<?php

class Banner extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $ubicacion;

	/**
	 * @var integer
	 */
	protected $serie;

	/**
	 * @var integer
	 */
	protected $posicion;

	/**
	 * @var integer
	 */
	protected $display_id;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo ubicacion
	 * @param string $ubicacion
	 */
	public function setUbicacion($ubicacion){
		$this->ubicacion = $ubicacion;
	}

	/**
	 * Método para establecer el valor del campo serie
	 * @param integer $serie
	 */
	public function setSerie($serie){
		$this->serie = $serie;
	}

	/**
	 * Método para establecer el valor del campo posicion
	 * @param integer $posicion
	 */
	public function setPosicion($posicion){
		$this->posicion = $posicion;
	}

	/**
	 * Método para establecer el valor del campo display_id
	 * @param integer $display_id
	 */
	public function setDisplayId($display_id){
		$this->display_id = $display_id;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo ubicacion
	 * @return string
	 */
	public function getUbicacion(){
		return $this->ubicacion;
	}

	/**
	 * Devuelve el valor del campo serie
	 * @return integer
	 */
	public function getSerie(){
		return $this->serie;
	}

	/**
	 * Devuelve el valor del campo posicion
	 * @return integer
	 */
	public function getPosicion(){
		return $this->posicion;
	}

	/**
	 * Devuelve el valor del campo display_id
	 * @return integer
	 */
	public function getDisplayId(){
		return $this->display_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('display_id','display','id');
	}

}

