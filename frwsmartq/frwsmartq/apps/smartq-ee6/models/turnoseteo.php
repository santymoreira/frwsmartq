<?php

class Turnoseteo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $fraseinicial;

	/**
	 * @var integer
	 */
	protected $chkfraseinicial;

	/**
	 * @var string
	 */
	protected $empresa;

	/**
	 * @var integer
	 */
	protected $chkempresa;

	/**
	 * @var string
	 */
	protected $logo;

	/**
	 * @var integer
	 */
	protected $chklogo;

	/**
	 * @var integer
	 */
	protected $chkinicial;

	/**
	 * @var integer
	 */
	protected $chkservicio;
        /**
	 * @var integer
	 */
	protected $chkubicacion;
	/**
	 * @var integer
	 */
	protected $chkfecha;

	/**
	 * @var integer
	 */
	protected $chktiempoespera;

	/**
	 * @var integer
	 */
	protected $chkturnoespera;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo fraseinicial
	 * @param string $fraseinicial
	 */
	public function setFraseinicial($fraseinicial){
		$this->fraseinicial = $fraseinicial;
	}

	/**
	 * Método para establecer el valor del campo chkfraseinicial
	 * @param integer $chkfraseinicial
	 */
	public function setChkfraseinicial($chkfraseinicial){
		$this->chkfraseinicial = $chkfraseinicial;
	}

	/**
	 * Método para establecer el valor del campo empresa
	 * @param string $empresa
	 */
	public function setEmpresa($empresa){
		$this->empresa = $empresa;
	}

	/**
	 * Método para establecer el valor del campo chkempresa
	 * @param integer $chkempresa
	 */
	public function setChkempresa($chkempresa){
		$this->chkempresa = $chkempresa;
	}

	/**
	 * Método para establecer el valor del campo logo
	 * @param string $logo
	 */
	public function setLogo($logo){
		$this->logo = $logo;
	}

	/**
	 * Método para establecer el valor del campo chklogo
	 * @param integer $chklogo
	 */
	public function setChklogo($chklogo){
		$this->chklogo = $chklogo;
	}

	/**
	 * Método para establecer el valor del campo chkinicial
	 * @param integer $chkinicial
	 */
	public function setChkinicial($chkinicial){
		$this->chkinicial = $chkinicial;
	}

	/**
	 * Método para establecer el valor del campo chkservicio
	 * @param integer $chkservicio
	 */
	public function setChkservicio($chkservicio){
		$this->chkservicio = $chkservicio;
	}
        /**
	 * Método para establecer el valor del campo chkservicio
	 * @param integer $chkservicio
	 */
	public function setChkubicacion($chkubicacion){
		$this->chkubicacion = $chkubicacion;
	}
	/**
	 * Método para establecer el valor del campo chkfecha
	 * @param integer $chkfecha
	 */
	public function setChkfecha($chkfecha){
		$this->chkfecha = $chkfecha;
	}

	/**
	 * Método para establecer el valor del campo chktiempoespera
	 * @param integer $chktiempoespera
	 */
	public function setChktiempoespera($chktiempoespera){
		$this->chktiempoespera = $chktiempoespera;
	}

	/**
	 * Método para establecer el valor del campo chkturnoespera
	 * @param integer $chkturnoespera
	 */
	public function setChkturnoespera($chkturnoespera){
		$this->chkturnoespera = $chkturnoespera;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo fraseinicial
	 * @return string
	 */
	public function getFraseinicial(){
		return $this->fraseinicial;
	}

	/**
	 * Devuelve el valor del campo chkfraseinicial
	 * @return integer
	 */
	public function getChkfraseinicial(){
		return $this->chkfraseinicial;
	}

	/**
	 * Devuelve el valor del campo empresa
	 * @return string
	 */
	public function getEmpresa(){
		return $this->empresa;
	}

	/**
	 * Devuelve el valor del campo chkempresa
	 * @return integer
	 */
	public function getChkempresa(){
		return $this->chkempresa;
	}

	/**
	 * Devuelve el valor del campo logo
	 * @return string
	 */
	public function getLogo(){
		return $this->logo;
	}

	/**
	 * Devuelve el valor del campo chklogo
	 * @return integer
	 */
	public function getChklogo(){
		return $this->chklogo;
	}

	/**
	 * Devuelve el valor del campo chkinicial
	 * @return integer
	 */
	public function getChkinicial(){
		return $this->chkinicial;
	}

	/**
	 * Devuelve el valor del campo chkservicio
	 * @return integer
	 */
	public function getChkservicio(){
		return $this->chkservicio;
	}

        /**
	 * Devuelve el valor del campo chkservicio
	 * @return integer
	 */
	public function getChkubicacion(){
		return $this->chkubicacion;
	}

	/**
	 * Devuelve el valor del campo chkfecha
	 * @return integer
	 */
	public function getChkfecha(){
		return $this->chkfecha;
	}

	/**
	 * Devuelve el valor del campo chktiempoespera
	 * @return integer
	 */
	public function getChktiempoespera(){
		return $this->chktiempoespera;
	}

	/**
	 * Devuelve el valor del campo chkturnoespera
	 * @return integer
	 */
	public function getChkturnoespera(){
		return $this->chkturnoespera;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

