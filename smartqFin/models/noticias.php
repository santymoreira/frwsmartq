<?php

class Noticias extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $titulo;

	/**
	 * @var string
	 */
	protected $noticia;

	/**
	 * @var integer
	 */
	protected $publicar;

	/**
	 * @var Date
	 */
	protected $fecha_inicio_publicacion;

	/**
	 * @var Date
	 */
	protected $fecha_fin_publicacion;

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
	 * Método para establecer el valor del campo titulo
	 * @param string $titulo
	 */
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}

	/**
	 * Método para establecer el valor del campo noticia
	 * @param string $noticia
	 */
	public function setNoticia($noticia){
		$this->noticia = $noticia;
	}

	/**
	 * Método para establecer el valor del campo publicar
	 * @param integer $publicar
	 */
	public function setPublicar($publicar){
		$this->publicar = $publicar;
	}

	/**
	 * Método para establecer el valor del campo fecha_inicio_publicacion
	 * @param Date $fecha_inicio_publicacion
	 */
	public function setFechaInicioPublicacion($fecha_inicio_publicacion){
		$this->fecha_inicio_publicacion = $fecha_inicio_publicacion;
	}

	/**
	 * Método para establecer el valor del campo fecha_fin_publicacion
	 * @param Date $fecha_fin_publicacion
	 */
	public function setFechaFinPublicacion($fecha_fin_publicacion){
		$this->fecha_fin_publicacion = $fecha_fin_publicacion;
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
	 * Devuelve el valor del campo titulo
	 * @return string
	 */
	public function getTitulo(){
		return $this->titulo;
	}

	/**
	 * Devuelve el valor del campo noticia
	 * @return string
	 */
	public function getNoticia(){
		return $this->noticia;
	}

	/**
	 * Devuelve el valor del campo publicar
	 * @return integer
	 */
	public function getPublicar(){
		return $this->publicar;
	}

	/**
	 * Devuelve el valor del campo fecha_inicio_publicacion
	 * @return Date
	 */
	public function getFechaInicioPublicacion(){
		return $this->fecha_inicio_publicacion;
	}

	/**
	 * Devuelve el valor del campo fecha_fin_publicacion
	 * @return Date
	 */
	public function getFechaFinPublicacion(){
		return $this->fecha_fin_publicacion;
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
	}

}

