<?php

class Datos extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $apellido;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Método para establecer el valor del campo apellido
	 * @param string $apellido
	 */
	public function setApellido($apellido){
		$this->apellido = $apellido;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo apellido
	 * @return string
	 */
	public function getApellido(){
		return $this->apellido;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

