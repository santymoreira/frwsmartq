<?php

class Serievideo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $serie_id;

	/**
	 * @var integer
	 */
	protected $video_id;

	/**
	 * @var integer
	 */
	protected $prioridad;


	/**
	 * Método para establecer el valor del campo serie_id
	 * @param integer $serie_id
	 */
	public function setSerieId($serie_id){
		$this->serie_id = $serie_id;
	}

	/**
	 * Método para establecer el valor del campo video_id
	 * @param integer $video_id
	 */
	public function setVideoId($video_id){
		$this->video_id = $video_id;
	}

	/**
	 * Método para establecer el valor del campo prioridad
	 * @param integer $prioridad
	 */
	public function setPrioridad($prioridad){
		$this->prioridad = $prioridad;
	}


	/**
	 * Devuelve el valor del campo serie_id
	 * @return integer
	 */
	public function getSerieId(){
		return $this->serie_id;
	}

	/**
	 * Devuelve el valor del campo video_id
	 * @return integer
	 */
	public function getVideoId(){
		return $this->video_id;
	}

	/**
	 * Devuelve el valor del campo prioridad
	 * @return integer
	 */
	public function getPrioridad(){
		return $this->prioridad;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('serie_id','serie','id');
		$this->belongsTo('video_id','video','id');
	}

}

