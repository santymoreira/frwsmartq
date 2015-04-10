<?php

class Dispensador extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $descripcion;
    protected $tipo_dispensador;
    protected $usuario_id;
    protected $impresion;

    /**
     * Método para establecer el valor del campo id
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Método para establecer el valor del campo descripcion
     * @param string $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function setTipoDispensador($tipo_dispensador) {
        $this->tipo_dispensador = $tipo_dispensador;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

        public function setImpresion($impresion) {
        $this->impresion = $impresion;
    }

    /**
     * Devuelve el valor del campo id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo descripcion
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getTipoDispensador() {
        return $this->tipo_dispensador;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

        public function getImpresion() {
        return $this->impresion;
    }


    /**
     * Método inicializador de la Entidad
     */
    protected function initialize() {
        $this->belongsTo('usuario_id', 'usuario', 'id');
    }

}
