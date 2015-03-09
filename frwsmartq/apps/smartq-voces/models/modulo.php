<?php

class Modulo extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var integer
	 */
	protected $estado;

	/**
	 * @var string
	 */
	protected $ruta;

	/**
	 * @var string
	 */
	protected $tipo;


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
	 * Método para establecer el valor del campo estado
	 * @param integer $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}

	/**
	 * Método para establecer el valor del campo ruta
	 * @param string $ruta
	 */
	public function setRuta($ruta){
		$this->ruta = $ruta;
	}

	/**
	 * Método para establecer el valor del campo tipo
	 * @param string $tipo
	 */
	public function setTipo($tipo){
		$this->tipo = $tipo;
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
	 * Devuelve el valor del campo estado
	 * @return integer
	 */
	public function getEstado(){
		return $this->estado;
	}

	/**
	 * Devuelve el valor del campo ruta
	 * @return string
	 */
	public function getRuta(){
		return $this->ruta;
	}

	/**
	 * Devuelve el valor del campo tipo
	 * @return string
	 */
	public function getTipo(){
		return $this->tipo;
	}

        /*
         * funcion para opbtener los modulo de acceso de los usuarios
         */
        /*public function obtenerModulosUsuario($usuario_id){
                           
                $clsgusuario = new Grupousuario();
                $grupousuario=$clsgusuario->find("usuario_id=$usuario_id");

                if($grupousuario->count()>0){
                    $lstgusuario='';
                    $coma='';
                    foreach($grupousuario as $gusuario){
                        $lstgusuario.=$coma.$gusuario->getGrupoId();
                        $coma=',';
                        //$lstgusuario = $gusuario->getGrupoId();
                    }
                    $clspermiso=new Permiso();
                    $permisousuario=$clspermiso->find("grupo_id IN ($lstgusuario) AND permiso>0");
                    if($permisousuario){
                        $lstpermod='';
                        $coma='';
                        foreach($permisousuario as $pusuario){
                            $lstpermod.=$coma.$pusuario->getGrupoId();
                            $coma=',';
                        }
                        return $this->find("id IN ($lstpermod) AND estado=1");
                    }
                }else{
                    Flash::notice("Hubo errores al cargar los permisos");
                }               
             
               return false;

        }*/



}

