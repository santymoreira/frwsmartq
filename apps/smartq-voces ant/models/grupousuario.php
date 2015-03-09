<?php

class Grupousuario extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $grupo_id;

    /**
     * @var integer
     */
    protected $usuario_id;

    /**
     * @var Date
     */
    protected $creacion_at;

    /**
     * Método para establecer el valor del campo id
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Método para establecer el valor del campo grupo_id
     * @param integer $grupo_id
     */
    public function setGrupoId($grupo_id) {
        $this->grupo_id = $grupo_id;
    }

    /**
     * Método para establecer el valor del campo usuario_id
     * @param integer $usuario_id
     */
    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    /**
     * Método para establecer el valor del campo creacion_at
     * @param Date $creacion_at
     */
    public function setCreacionAt($creacion_at) {
        $this->creacion_at = $creacion_at;
    }

    /**
     * Devuelve el valor del campo id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo grupo_id
     * @return integer
     */
    public function getGrupoId() {
        return $this->grupo_id;
    }

    /**
     * Devuelve el valor del campo usuario_id
     * @return integer
     */
    public function getUsuarioId() {
        return $this->usuario_id;
    }

    /**
     * Devuelve el valor del campo creacion_at
     * @return Date
     */
    public function getCreacionAt() {
        return $this->creacion_at;
    }

    /**
     * Método inicializador de la Entidad
     */
    protected function initialize() {
        $this->belongsTo('grupo_id', 'grupo', 'id');
        $this->belongsTo('usuario_id', 'usuario', 'id');
    }

}
