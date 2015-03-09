<?php

class Feed extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $categoriafeeds_id;

	/**
	 * @var string
	 */
	protected $url_feed;

	/**
	 * @var integer
	 */
	protected $activo;

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
	 * Método para establecer el valor del campo categoriafeeds_id
	 * @param integer $categoriafeeds_id
	 */
	public function setCategoriafeedsId($categoriafeeds_id){
		$this->categoriafeeds_id = $categoriafeeds_id;
	}

	/**
	 * Método para establecer el valor del campo url_feed
	 * @param string $url_feed
	 */
	public function setUrlFeed($url_feed){
		$this->url_feed = $url_feed;
	}

	/**
	 * Método para establecer el valor del campo activo
	 * @param integer $activo
	 */
	public function setActivo($activo){
		$this->activo = $activo;
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
	 * Devuelve el valor del campo categoriafeeds_id
	 * @return integer
	 */
	public function getCategoriafeedsId(){
		return $this->categoriafeeds_id;
	}

	/**
	 * Devuelve el valor del campo url_feed
	 * @return string
	 */
	public function getUrlFeed(){
		return $this->url_feed;
	}

	/**
	 * Devuelve el valor del campo activo
	 * @return integer
	 */
	public function getActivo(){
		return $this->activo;
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
		$this->belongsTo('categoriafeeds_id','categoriafeeds','id');
	}

}

