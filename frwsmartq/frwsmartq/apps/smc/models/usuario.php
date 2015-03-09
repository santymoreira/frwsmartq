<?php

class Usuario extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombres;

	/**
	 * @var string
	 */
	protected $ci;

	/**
	 * @var string
	 */
	protected $telefono;

	/**
	 * @var string
	 */
	protected $movil;

	/**
	 * @var string
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var Date
	 */
	protected $actclave;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var string
	 */
	protected $foto;

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
	 * Método para establecer el valor del campo nombres
	 * @param string $nombres
	 */
	public function setNombres($nombres){
		$this->nombres = $nombres;
	}

	/**
	 * Método para establecer el valor del campo ci
	 * @param string $ci
	 */
	public function setCi($ci){
		$this->ci = $ci;
	}

	/**
	 * Método para establecer el valor del campo telefono
	 * @param string $telefono
	 */
	public function setTelefono($telefono){
		$this->telefono = $telefono;
	}

	/**
	 * Método para establecer el valor del campo movil
	 * @param string $movil
	 */
	public function setMovil($movil){
		$this->movil = $movil;
	}

	/**
	 * Método para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Método para establecer el valor del campo username
	 * @param string $username
	 */
	public function setUsername($username){
		$this->username = $username;
	}

	/**
	 * Método para establecer el valor del campo password
	 * @param string $password
	 */
	public function setPassword($password){
		$this->password = $password;
	}

	/**
	 * Método para establecer el valor del campo actclave
	 * @param Date $actclave
	 */
	public function setActclave($actclave){
		$this->actclave = $actclave;
	}

	/**
	 * Método para establecer el valor del campo email
	 * @param string $email
	 */
	public function setEmail($email){
		$this->email = $email;
	}

	/**
	 * Método para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Método para establecer el valor del campo foto
	 * @param string $foto
	 */
	public function setFoto($foto){
		$this->foto = $foto;
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
	 * Devuelve el valor del campo nombres
	 * @return string
	 */
	public function getNombres(){
		return $this->nombres;
	}

	/**
	 * Devuelve el valor del campo ci
	 * @return string
	 */
	public function getCi(){
		return $this->ci;
	}

	/**
	 * Devuelve el valor del campo telefono
	 * @return string
	 */
	public function getTelefono(){
		return $this->telefono;
	}

	/**
	 * Devuelve el valor del campo movil
	 * @return string
	 */
	public function getMovil(){
		return $this->movil;
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo username
	 * @return string
	 */
	public function getUsername(){
		return $this->username;
	}

	/**
	 * Devuelve el valor del campo password
	 * @return string
	 */
	public function getPassword(){
		return $this->password;
	}

	/**
	 * Devuelve el valor del campo actclave
	 * @return Date
	 */
	public function getActclave(){
		return $this->actclave;
	}

	/**
	 * Devuelve el valor del campo email
	 * @return string
	 */
	public function getEmail(){
		return $this->email;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo foto
	 * @return string
	 */
	public function getFoto(){
		return $this->foto;
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

