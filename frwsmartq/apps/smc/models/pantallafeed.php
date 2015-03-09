<?php

class Pantallafeed extends ActiveRecord {

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
	protected $feed_id;

	/**
	 * @var integer
	 */
	protected $categoriafeeds_id;

	/**
	 * @var integer
	 */
	protected $publicar_icono;

	/**
	 * @var integer
	 */
	protected $publicar_titulo;

	/**
	 * @var integer
	 */
	protected $publicar_fecha;

	/**
	 * @var integer
	 */
	protected $publicar_hora;

	/**
	 * @var integer
	 */
	protected $publicar_contenido;

	/**
	 * @var integer
	 */
	protected $limite_items;

	/**
	 * @var Date
	 */
	protected $fecha_inicio;

	/**
	 * @var Date
	 */
	protected $fecha_fin;

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
	 * Método para establecer el valor del campo pantalla_id
	 * @param integer $pantalla_id
	 */
	public function setPantallaId($pantalla_id){
		$this->pantalla_id = $pantalla_id;
	}

	/**
	 * Método para establecer el valor del campo feed_id
	 * @param integer $feed_id
	 */
	public function setFeedId($feed_id){
		$this->feed_id = $feed_id;
	}

	/**
	 * Método para establecer el valor del campo categoriafeeds_id
	 * @param integer $categoriafeeds_id
	 */
	public function setCategoriafeedsId($categoriafeeds_id){
		$this->categoriafeeds_id = $categoriafeeds_id;
	}

	/**
	 * Método para establecer el valor del campo publicar_icono
	 * @param integer $publicar_icono
	 */
	public function setPublicarIcono($publicar_icono){
		$this->publicar_icono = $publicar_icono;
	}

	/**
	 * Método para establecer el valor del campo publicar_titulo
	 * @param integer $publicar_titulo
	 */
	public function setPublicarTitulo($publicar_titulo){
		$this->publicar_titulo = $publicar_titulo;
	}

	/**
	 * Método para establecer el valor del campo publicar_fecha
	 * @param integer $publicar_fecha
	 */
	public function setPublicarFecha($publicar_fecha){
		$this->publicar_fecha = $publicar_fecha;
	}

	/**
	 * Método para establecer el valor del campo publicar_hora
	 * @param integer $publicar_hora
	 */
	public function setPublicarHora($publicar_hora){
		$this->publicar_hora = $publicar_hora;
	}

	/**
	 * Método para establecer el valor del campo publicar_contenido
	 * @param integer $publicar_contenido
	 */
	public function setPublicarContenido($publicar_contenido){
		$this->publicar_contenido = $publicar_contenido;
	}

	/**
	 * Método para establecer el valor del campo limite_items
	 * @param integer $limite_items
	 */
	public function setLimiteItems($limite_items){
		$this->limite_items = $limite_items;
	}

	/**
	 * Método para establecer el valor del campo fecha_inicio
	 * @param Date $fecha_inicio
	 */
	public function setFechaInicio($fecha_inicio){
		$this->fecha_inicio = $fecha_inicio;
	}

	/**
	 * Método para establecer el valor del campo fecha_fin
	 * @param Date $fecha_fin
	 */
	public function setFechaFin($fecha_fin){
		$this->fecha_fin = $fecha_fin;
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
	 * Devuelve el valor del campo pantalla_id
	 * @return integer
	 */
	public function getPantallaId(){
		return $this->pantalla_id;
	}

	/**
	 * Devuelve el valor del campo feed_id
	 * @return integer
	 */
	public function getFeedId(){
		return $this->feed_id;
	}

	/**
	 * Devuelve el valor del campo categoriafeeds_id
	 * @return integer
	 */
	public function getCategoriafeedsId(){
		return $this->categoriafeeds_id;
	}

	/**
	 * Devuelve el valor del campo publicar_icono
	 * @return integer
	 */
	public function getPublicarIcono(){
		return $this->publicar_icono;
	}

	/**
	 * Devuelve el valor del campo publicar_titulo
	 * @return integer
	 */
	public function getPublicarTitulo(){
		return $this->publicar_titulo;
	}

	/**
	 * Devuelve el valor del campo publicar_fecha
	 * @return integer
	 */
	public function getPublicarFecha(){
		return $this->publicar_fecha;
	}

	/**
	 * Devuelve el valor del campo publicar_hora
	 * @return integer
	 */
	public function getPublicarHora(){
		return $this->publicar_hora;
	}

	/**
	 * Devuelve el valor del campo publicar_contenido
	 * @return integer
	 */
	public function getPublicarContenido(){
		return $this->publicar_contenido;
	}

	/**
	 * Devuelve el valor del campo limite_items
	 * @return integer
	 */
	public function getLimiteItems(){
		return $this->limite_items;
	}

	/**
	 * Devuelve el valor del campo fecha_inicio
	 * @return Date
	 */
	public function getFechaInicio(){
		return $this->fecha_inicio;
	}

	/**
	 * Devuelve el valor del campo fecha_fin
	 * @return Date
	 */
	public function getFechaFin(){
		return $this->fecha_fin;
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
		$this->belongsTo('pantalla_id','pantalla','id');
		$this->belongsTo('feed_id','feed','id');
		$this->belongsTo('categoriafeeds_id','categoriafeeds','id');
	}

}

