<?php

class Grupopregunta extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nom_grupo;

	/**
	 * @var Date
	 */
	protected $cracion_at;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nom_grupo
	 * @param string $nom_grupo
	 */
	public function setNomGrupo($nom_grupo){
		$this->nom_grupo = $nom_grupo;
	}

	/**
	 * Método para establecer el valor del campo cracion_at
	 * @param Date $cracion_at
	 */
	public function setCracionAt($cracion_at){
		$this->cracion_at = $cracion_at;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nom_grupo
	 * @return string
	 */
	public function getNomGrupo(){
		return $this->nom_grupo;
	}

	/**
	 * Devuelve el valor del campo cracion_at
	 * @return Date
	 */
	public function getCracionAt(){
		return $this->cracion_at;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

