<?php

class Permiso extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $menu_id;

	/**
	 * @var integer
	 */
	protected $grupo_id;

	/**
	 * @var string
	 */
	protected $hora_inicio;

	/**
	 * @var string
	 */
	protected $hora_fin;

	/**
	 * @var integer
	 */
	protected $permiso;

	/**
	 * @var string
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
	 * Método para establecer el valor del campo menu_id
	 * @param integer $menu_id
	 */
	public function setMenuId($menu_id){
		$this->menu_id = $menu_id;
	}

	/**
	 * Método para establecer el valor del campo grupo_id
	 * @param integer $grupo_id
	 */
	public function setGrupoId($grupo_id){
		$this->grupo_id = $grupo_id;
	}

	/**
	 * Método para establecer el valor del campo hora_inicio
	 * @param string $hora_inicio
	 */
	public function setHoraInicio($hora_inicio){
		$this->hora_inicio = $hora_inicio;
	}

	/**
	 * Método para establecer el valor del campo hora_fin
	 * @param string $hora_fin
	 */
	public function setHoraFin($hora_fin){
		$this->hora_fin = $hora_fin;
	}

	/**
	 * Método para establecer el valor del campo permiso
	 * @param integer $permiso
	 */
	public function setPermiso($permiso){
		$this->permiso = $permiso;
	}

	/**
	 * Método para establecer el valor del campo creacion_at
	 * @param string $creacion_at
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
	 * Devuelve el valor del campo menu_id
	 * @return integer
	 */
	public function getMenuId(){
		return $this->menu_id;
	}

	/**
	 * Devuelve el valor del campo grupo_id
	 * @return integer
	 */
	public function getGrupoId(){
		return $this->grupo_id;
	}

	/**
	 * Devuelve el valor del campo hora_inicio
	 * @return string
	 */
	public function getHoraInicio(){
		return $this->hora_inicio;
	}

	/**
	 * Devuelve el valor del campo hora_fin
	 * @return string
	 */
	public function getHoraFin(){
		return $this->hora_fin;
	}

	/**
	 * Devuelve el valor del campo permiso
	 * @return integer
	 */
	public function getPermiso(){
		return $this->permiso;
	}

	/**
	 * Devuelve el valor del campo creacion_at
	 * @return string
	 */
	public function getCreacionAt(){
		return $this->creacion_at;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('menu_id','menu','id');
		$this->belongsTo('grupo_id','grupo','id');
	}

}

