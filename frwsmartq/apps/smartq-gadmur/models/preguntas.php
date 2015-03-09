<?php

class Preguntas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

        /**
	 * @var integer
	 */
	protected $id_grupopregunta;
        
	/**
	 * @var string
	 */
	protected $nom_pregunta;

	/**
	 * @var integer
	 */
	protected $publicar;

	/**
	 * @var integer
	 */
	protected $orden;

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
	 * Método para establecer el valor del campo id_grupopregunta
	 * @param integer $id_grupopregunta
	 */
	public function setIdGrupopregunta($id_grupopregunta){
		$this->id_grupopregunta = $id_grupopregunta;
	}
        
	/**
	 * Método para establecer el valor del campo nom_pregunta
	 * @param string $nom_pregunta
	 */
	public function setNomPregunta($nom_pregunta){
		$this->nom_pregunta = $nom_pregunta;
	}

	/**
	 * Método para establecer el valor del campo publicar
	 * @param integer $publicar
	 */
	public function setPublicar($publicar){
		$this->publicar = $publicar;
	}

	/**
	 * Método para establecer el valor del campo orden
	 * @param integer $orden
	 */
	public function setOrden($orden){
		$this->orden = $orden;
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
	 * Devuelve el valor del campo id_grupopregunta
	 * @return integer
	 */
	public function getIdGrupopregunta(){
		return $this->id_grupopregunta;
	}
        
	/**
	 * Devuelve el valor del campo nom_pregunta
	 * @return string
	 */
	public function getNomPregunta(){
		return $this->nom_pregunta;
	}

	/**
	 * Devuelve el valor del campo publicar
	 * @return integer
	 */
	public function getPublicar(){
		return $this->publicar;
	}

	/**
	 * Devuelve el valor del campo orden
	 * @return integer
	 */
	public function getOrden(){
		return $this->orden;
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
            $this->belongsTo('id_grupopregunta', 'grupopregunta', 'id');
	}

}

