<?php

class Sucursal extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $alias_sucursal;

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var string
	 */
	protected $nombre_bd;

	/**
	 * @var string
	 */
	protected $usuario_bd;

	/**
	 * @var string
	 */
	protected $password_bd;

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
	 * Método para establecer el valor del campo alias_sucursal
	 * @param string $alias_sucursal
	 */
	public function setAliasSucursal($alias_sucursal){
		$this->alias_sucursal = $alias_sucursal;
	}

	/**
	 * Método para establecer el valor del campo host
	 * @param string $host
	 */
	public function setHost($host){
		$this->host = $host;
	}

	/**
	 * Método para establecer el valor del campo nombre_bd
	 * @param string $nombre_bd
	 */
	public function setNombreBd($nombre_bd){
		$this->nombre_bd = $nombre_bd;
	}

	/**
	 * Método para establecer el valor del campo usuario_bd
	 * @param string $usuario_bd
	 */
	public function setUsuarioBd($usuario_bd){
		$this->usuario_bd = $usuario_bd;
	}

	/**
	 * Método para establecer el valor del campo password_bd
	 * @param string $password_bd
	 */
	public function setPasswordBd($password_bd){
		$this->password_bd = $password_bd;
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
	 * Devuelve el valor del campo alias_sucursal
	 * @return string
	 */
	public function getAliasSucursal(){
		return $this->alias_sucursal;
	}

	/**
	 * Devuelve el valor del campo host
	 * @return string
	 */
	public function getHost(){
		return $this->host;
	}

	/**
	 * Devuelve el valor del campo nombre_bd
	 * @return string
	 */
	public function getNombreBd(){
		return $this->nombre_bd;
	}

	/**
	 * Devuelve el valor del campo usuario_bd
	 * @return string
	 */
	public function getUsuarioBd(){
		return $this->usuario_bd;
	}

	/**
	 * Devuelve el valor del campo password_bd
	 * @return string
	 */
	public function getPasswordBd(){
		return $this->password_bd;
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

