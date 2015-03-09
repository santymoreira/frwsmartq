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
    /**
     * Método inicializador de la Entidad
     */
    protected function initialize() {
    }

}

