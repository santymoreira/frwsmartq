<?php

class Servicio extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $nombre;

    /**
     * @var string
     */
    protected $letra;

    /**
     * @var string
     */
    protected $descripcion;

    /**
     * @var integer
     */
    protected $estado;

    /**
     * @var integer
     */
    protected $inicio;

    /**
     * @var integer
     */
    protected $fin;

    /**
     * @var integer
     */
    protected $actual;

    /**
     * @var integer
     */
    protected $ubicacion_id;

    /**
     * @var string
     */
    protected $estilo_letra;

    /**
     * @var integer
     */
    protected $gruposervicio_id;

    /**
     * @var string
     */
    protected $tiempo_maximo;

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
     * Método para establecer el valor del campo nombre
     * @param string $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Método para establecer el valor del campo letra
     * @param string $letra
     */
    public function setLetra($letra) {
        $this->letra = $letra;
    }

    /**
     * Método para establecer el valor del campo descripcion
     * @param string $descripcion
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    /**
     * Método para establecer el valor del campo estado
     * @param integer $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Método para establecer el valor del campo inicio
     * @param integer $inicio
     */
    public function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    /**
     * Método para establecer el valor del campo fin
     * @param integer $fin
     */
    public function setFin($fin) {
        $this->fin = $fin;
    }

    /**
     * Método para establecer el valor del campo actual
     * @param integer $actual
     */
    public function setActual($actual) {
        $this->actual = $actual;
    }

    /**
     * Método para establecer el valor del campo ubicacion_id
     * @param integer $ubicacion_id
     */
    public function setUbicacionId($ubicacion_id) {
        $this->ubicacion_id = $ubicacion_id;
    }

    /**
     * Método para establecer el valor del campo estilo_letra
     * @param string $estilo_letra
     */
    public function setEstiloLetra($estilo_letra) {
        $this->estilo_letra = $estilo_letra;
    }

    /**
     * Método para establecer el valor del campo gruposervicio_id
     * @param integer $gruposervicio_id
     */
    public function setGruposervicioId($gruposervicio_id) {
        $this->gruposervicio_id = $gruposervicio_id;
    }

    /**
     * Método para establecer el valor del campo tiempo_maximo
     * @param string $tiempo_maximo
     */
    public function setTiempoMaximo($tiempo_maximo) {
        $this->tiempo_maximo = $tiempo_maximo;
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
     * Devuelve el valor del campo nombre
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Devuelve el valor del campo letra
     * @return string
     */
    public function getLetra() {
        return $this->letra;
    }

    /**
     * Devuelve el valor del campo descripcion
     * @return string
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Devuelve el valor del campo estado
     * @return integer
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Devuelve el valor del campo inicio
     * @return integer
     */
    public function getInicio() {
        return $this->inicio;
    }

    /**
     * Devuelve el valor del campo fin
     * @return integer
     */
    public function getFin() {
        return $this->fin;
    }

    /**
     * Devuelve el valor del campo actual
     * @return integer
     */
    public function getActual() {
        return $this->actual;
    }

    /**
     * Devuelve el valor del campo ubicacion_id
     * @return integer
     */
    public function getUbicacionId() {
        return $this->ubicacion_id;
    }

    /**
     * Devuelve el valor del campo estilo_letra
     * @return string
     */
    public function getEstiloLetra() {
        return $this->estilo_letra;
    }

    /**
     * Devuelve el valor del campo gruposervicio_id
     * @return integer
     */
    public function getGruposervicioId() {
        return $this->gruposervicio_id;
    }
    
    /**
     * Devuelve el valor del campo tiempo_maximo
     * @return string
     */
    public function getTiempoMaximo() {
        return $this->tiempo_maximo;
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
        $this->belongsTo('ubicacion_id','ubicacion','id');
        $this->belongsTo('gruposervicio_id','gruposervicio','id');
    }

}

