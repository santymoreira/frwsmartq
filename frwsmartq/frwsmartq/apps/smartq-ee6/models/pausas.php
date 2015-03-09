<?php

class Pausas extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre_pausa;

	/**
	 * @var string
	 */
	protected $maximo_permitido;

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
	 * Método para establecer el valor del campo nombre_pausa
	 * @param string $nombre_pausa
	 */
	public function setNombrePausa($nombre_pausa){
		$this->nombre_pausa = $nombre_pausa;
	}

	/**
	 * Método para establecer el valor del campo maximo_permitido
	 * @param string $maximo_permitido
	 */
	public function setMaximoPermitido($maximo_permitido){
		$this->maximo_permitido = $maximo_permitido;
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
	 * Devuelve el valor del campo nombre_pausa
	 * @return string
	 */
	public function getNombrePausa(){
		return $this->nombre_pausa;
	}

	/**
	 * Devuelve el valor del campo maximo_permitido
	 * @return string
	 */
	public function getMaximoPermitido(){
		return $this->maximo_permitido;
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

