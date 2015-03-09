<?php

class Uservideo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $usuario_id;

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
	 * Método para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
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
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
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
		$this->belongsTo('usuario_id','usuario','id');
		$this->belongsTo('video_id','video','id');
	}

}

