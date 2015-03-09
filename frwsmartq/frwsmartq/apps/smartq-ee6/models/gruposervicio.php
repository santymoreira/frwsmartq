<?php

class Gruposervicio extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre_grupo_servicio;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo nombre_grupo_servicio
	 * @param string $nombre_grupo_servicio
	 */
	public function setNombreGrupoServicio($nombre_grupo_servicio){
		$this->nombre_grupo_servicio = $nombre_grupo_servicio;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre_grupo_servicio
	 * @return string
	 */
	public function getNombreGrupoServicio(){
		return $this->nombre_grupo_servicio;
	}

	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
	}

}

