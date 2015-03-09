<?php

class Empresa extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombrecomercial;

	/**
	 * @var string
	 */
	protected $alias_empresa;

	/**
	 * @var string
	 */
	protected $razonsocial;

	/**
	 * @var integer
	 */
	protected $matriz;

	/**
	 * @var string
	 */
	protected $direccion;

	/**
	 * @var integer
	 */
	protected $modulo_operadores;

	/**
	 * @var integer
	 */
	protected $modulo_cajas;

	/**
	 * @var integer
	 */
	protected $calif_4botones_teclado;

	/**
	 * @var integer
	 */
	protected $calif_4botones_pantalla;

	/**
	 * @var integer
	 */
	protected $calif_matriz_pantalla;

	/**
	 * @var integer
	 */
	protected $dispensador_simple;

	/**
	 * @var integer
	 */
	protected $dispensador_touch;

	/**
	 * @var integer
	 */
	protected $dispensador_botonera;

	/**
	 * @var integer
	 */
	protected $dispensador_touch_pequenia;

	/**
	 * @var string
	 */
	protected $carpeta;

	/**
	 * @var integer
	 */
	protected $seleccion_operador;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nombrecomercial
	 * @param string $nombrecomercial
	 */
	public function setNombrecomercial($nombrecomercial){
		$this->nombrecomercial = $nombrecomercial;
	}

	/**
	 * Método para establecer el valor del campo alias_empresa
	 * @param string $alias_empresa
	 */
	public function setAliasEmpresa($alias_empresa){
		$this->alias_empresa = $alias_empresa;
	}

	/**
	 * Método para establecer el valor del campo razonsocial
	 * @param string $razonsocial
	 */
	public function setRazonsocial($razonsocial){
		$this->razonsocial = $razonsocial;
	}

	/**
	 * Método para establecer el valor del campo matriz
	 * @param integer $matriz
	 */
	public function setMatriz($matriz){
		$this->matriz = $matriz;
	}

	/**
	 * Método para establecer el valor del campo direccion
	 * @param string $direccion
	 */
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}

	/**
	 * Método para establecer el valor del campo modulo_operadores
	 * @param integer $modulo_operadores
	 */
	public function setModuloOperadores($modulo_operadores){
		$this->modulo_operadores = $modulo_operadores;
	}

	/**
	 * Método para establecer el valor del campo modulo_cajas
	 * @param integer $modulo_cajas
	 */
	public function setModuloCajas($modulo_cajas){
		$this->modulo_cajas = $modulo_cajas;
	}

	/**
	 * Método para establecer el valor del campo calif_4botones_teclado
	 * @param integer $calif_4botones_teclado
	 */
	public function setCalif4botonesTeclado($calif_4botones_teclado){
		$this->calif_4botones_teclado = $calif_4botones_teclado;
	}

	/**
	 * Método para establecer el valor del campo calif_4botones_pantalla
	 * @param integer $calif_4botones_pantalla
	 */
	public function setCalif4botonesPantalla($calif_4botones_pantalla){
		$this->calif_4botones_pantalla = $calif_4botones_pantalla;
	}

	/**
	 * Método para establecer el valor del campo calif_matriz_pantalla
	 * @param integer $calif_matriz_pantalla
	 */
	public function setCalifMatrizPantalla($calif_matriz_pantalla){
		$this->calif_matriz_pantalla = $calif_matriz_pantalla;
	}

	/**
	 * Método para establecer el valor del campo dispensador_simple
	 * @param integer $dispensador_simple
	 */
	public function setDispensadorSimple($dispensador_simple){
		$this->dispensador_simple = $dispensador_simple;
	}

	/**
	 * Método para establecer el valor del campo dispensador_touch
	 * @param integer $dispensador_touch
	 */
	public function setDispensadorTouch($dispensador_touch){
		$this->dispensador_touch = $dispensador_touch;
	}

	/**
	 * Método para establecer el valor del campo dispensador_botonera
	 * @param integer $dispensador_botonera
	 */
	public function setDispensadorBotonera($dispensador_botonera){
		$this->dispensador_botonera = $dispensador_botonera;
	}

	/**
	 * Método para establecer el valor del campo dispensador_touch_pequenia
	 * @param integer $dispensador_touch_pequenia
	 */
	public function setDispensadorTouchPequenia($dispensador_touch_pequenia){
		$this->dispensador_touch_pequenia = $dispensador_touch_pequenia;
	}

	/**
	 * Método para establecer el valor del campo carpeta
	 * @param string $carpeta
	 */
	public function setCarpeta($carpeta){
		$this->carpeta = $carpeta;
	}

	/**
	 * Método para establecer el valor del campo seleccion_operador
	 * @param integer $seleccion_operador
	 */
	public function setSeleccionOperador($seleccion_operador){
		$this->seleccion_operador = $seleccion_operador;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombrecomercial
	 * @return string
	 */
	public function getNombrecomercial(){
		return $this->nombrecomercial;
	}

	/**
	 * Devuelve el valor del campo alias_empresa
	 * @return string
	 */
	public function getAliasEmpresa(){
		return $this->alias_empresa;
	}

	/**
	 * Devuelve el valor del campo razonsocial
	 * @return string
	 */
	public function getRazonsocial(){
		return $this->razonsocial;
	}

	/**
	 * Devuelve el valor del campo matriz
	 * @return integer
	 */
	public function getMatriz(){
		return $this->matriz;
	}

	/**
	 * Devuelve el valor del campo direccion
	 * @return string
	 */
	public function getDireccion(){
		return $this->direccion;
	}

	/**
	 * Devuelve el valor del campo modulo_operadores
	 * @return integer
	 */
	public function getModuloOperadores(){
		return $this->modulo_operadores;
	}

	/**
	 * Devuelve el valor del campo modulo_cajas
	 * @return integer
	 */
	public function getModuloCajas(){
		return $this->modulo_cajas;
	}

	/**
	 * Devuelve el valor del campo calif_4botones_teclado
	 * @return integer
	 */
	public function getCalif4botonesTeclado(){
		return $this->calif_4botones_teclado;
	}

	/**
	 * Devuelve el valor del campo calif_4botones_pantalla
	 * @return integer
	 */
	public function getCalif4botonesPantalla(){
		return $this->calif_4botones_pantalla;
	}

	/**
	 * Devuelve el valor del campo calif_matriz_pantalla
	 * @return integer
	 */
	public function getCalifMatrizPantalla(){
		return $this->calif_matriz_pantalla;
	}

	/**
	 * Devuelve el valor del campo dispensador_simple
	 * @return integer
	 */
	public function getDispensadorSimple(){
		return $this->dispensador_simple;
	}

	/**
	 * Devuelve el valor del campo dispensador_touch
	 * @return integer
	 */
	public function getDispensadorTouch(){
		return $this->dispensador_touch;
	}

	/**
	 * Devuelve el valor del campo dispensador_botonera
	 * @return integer
	 */
	public function getDispensadorBotonera(){
		return $this->dispensador_botonera;
	}

	/**
	 * Devuelve el valor del campo dispensador_touch_pequenia
	 * @return integer
	 */
	public function getDispensadorTouchPequenia(){
		return $this->dispensador_touch_pequenia;
	}

	/**
	 * Devuelve el valor del campo carpeta
	 * @return string
	 */
	public function getCarpeta(){
		return $this->carpeta;
	}

	/**
	 * Devuelve el valor del campo seleccion_operador
	 * @return integer
	 */
	public function getSeleccionOperador(){
		return $this->seleccion_operador;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

