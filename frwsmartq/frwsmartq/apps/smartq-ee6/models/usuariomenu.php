<?php

class Usuariomenu extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $usuario_id;

	/**
	 * @var integer
	 */
	protected $menu_id;


	/**
	 * Método para establecer el valor del campo usuario_id
	 * @param integer $usuario_id
	 */
	public function setUsuarioId($usuario_id){
		$this->usuario_id = $usuario_id;
	}

	/**
	 * Método para establecer el valor del campo menu_id
	 * @param integer $menu_id
	 */
	public function setMenuId($menu_id){
		$this->menu_id = $menu_id;
	}


	/**
	 * Devuelve el valor del campo usuario_id
	 * @return integer
	 */
	public function getUsuarioId(){
		return $this->usuario_id;
	}

	/**
	 * Devuelve el valor del campo menu_id
	 * @return integer
	 */
	public function getMenuId(){
		return $this->menu_id;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
		$this->belongsTo('usuario_id','usuario','id');
		$this->belongsTo('menu_id','menu','id');
	}

}

