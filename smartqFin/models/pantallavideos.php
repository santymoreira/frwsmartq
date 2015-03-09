<?php

class Pantallavideos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $pantalla_id;

	/**
	 * @var integer
	 */
	protected $video_id;

	/**
	 * @var integer
	 */
	protected $activo;

	/**
	 * @var integer
	 */
	protected $orden;

	/**
	 * @var integer
	 */
	protected $reproducir;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo pantalla_id
	 * @param integer $pantalla_id
	 */
	public function setPantallaId($pantalla_id){
		$this->pantalla_id = $pantalla_id;
	}

	/**
	 * Método para establecer el valor del campo video_id
	 * @param integer $video_id
	 */
	public function setVideoId($video_id){
		$this->video_id = $video_id;
	}

	/**
	 * Método para establecer el valor del campo activo
	 * @param integer $activo
	 */
	public function setActivo($activo){
		$this->activo = $activo;
	}

	/**
	 * Método para establecer el valor del campo orden
	 * @param integer $orden
	 */
	public function setOrden($orden){
		$this->orden = $orden;
	}

	/**
	 * Método para establecer el valor del campo reproducir
	 * @param integer $reproducir
	 */
	public function setReproducir($reproducir){
		$this->reproducir = $reproducir;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo pantalla_id
	 * @return integer
	 */
	public function getPantallaId(){
		return $this->pantalla_id;
	}

	/**
	 * Devuelve el valor del campo video_id
	 * @return integer
	 */
	public function getVideoId(){
		return $this->video_id;
	}

	/**
	 * Devuelve el valor del campo activo
	 * @return integer
	 */
	public function getActivo(){
		return $this->activo;
	}

	/**
	 * Devuelve el valor del campo orden
	 * @return integer
	 */
	public function getOrden(){
		return $this->orden;
	}

	/**
	 * Devuelve el valor del campo reproducir
	 * @return integer
	 */
	public function getReproducir(){
		return $this->reproducir;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('pantalla_id','pantalla','id');
		$this->belongsTo('video_id','video','id');
	}

}

