<?php

class Caja extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $numero_caja;

    /**
     * @var string
     */
    protected $descripcion;

    /**
     * @var integer
     */
    protected $estado;

    /**
     * @var string
     */
    protected $usuario;

    /**
     * @var string
     */
    protected $tipo_calificacion_operador;

    /**
     * @var integer
     */
    protected $horario_id;

    /**
     * @var integer
     */
    protected $transferir_uno;

    /**
     * @var integer
     */
    protected $transferir_todos;

    /**
     * @var integer
     */
    protected $ubicacion_id;

    /**
     * @var integer
     */
    protected $ip;

    /**
     * @var integer
     */
    protected $tiempo;

    /**
     * @var integer
     */
    protected $timbre;
    
    /**
     * @var integer
     */
    protected $calificar;

    /**
     * @var string
     */
    protected $usuario_actual;
    
    /**
     * @var string
     */
    protected $ip_calificador;

    /**
     * Método para establecer el valor del campo id
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Método para establecer el valor del campo numero_caja
     * @param string $numero_caja
     */
    public function setNumeroCaja($numero_caja) {
        $this->numero_caja = $numero_caja;
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
     * Método para establecer el valor del campo usuario
     * @param string $usuario
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * Método para establecer el valor del campo tipo_calificacion_operador
     * @param string $tipo_calificacion_operador
     */
    public function setTipoCalificacionOperador($tipo_calificacion_operador) {
        $this->tipo_calificacion_operador = $tipo_calificacion_operador;
    }

    /**
     * Método para establecer el valor del campo horario_id
     * @param integer $horario_id
     */
    public function setHorarioId($horario_id) {
        $this->horario_id = $horario_id;
    }

    /**
     * Método para establecer el valor del campo transferir_uno
     * @param integer $transferir_uno
     */
    public function setTransferirUno($transferir_uno) {
        $this->transferir_uno = $transferir_uno;
    }

    /**
     * Método para establecer el valor del campo transferir_todos
     * @param integer $transferir_todos
     */
    public function setTransferirTodos($transferir_todos) {
        $this->transferir_todos = $transferir_todos;
    }

    /**
     * Método para establecer el valor del campo ubicacion_id
     * @param integer $ubicacion_id
     */
    public function setUbicacionId($ubicacion_id) {
        $this->ubicacion_id = $ubicacion_id;
    }

    /**
     * Método para establecer el valor del campo ubicacion_id
     * @param integer $ubicacion_id
     */
    public function setIp($ip) {
        $this->ip = $ip;
    }

    /**
     * M�todo para establecer el valor del tiempo para el calificador
     * @param integer $tiempo
     */
    public function setTiempo($tiempo) {
        $this->tiempo = $tiempo;
    }
    
    /**
     * M�todo para establecer el valor del timbre para el calificador
     * @param integer $timbre
     */
    public function setTimbre($timbre) {
        $this->timbre = $timbre;
    }
    
    /**
     * M�todo para establecer el valor del calificar para el calificador
     * @param integer $calificar
     */
    public function setCalificar($calificar) {
        $this->calificar = $calificar;
    }
    
    /**
     * M�todo para establecer el valor del calificar para el calificador
     * @param integer $calificar
     */
    public function setUsuarioActual($usuario_actual) {
        $this->usuario_actual = $usuario_actual;
    }
    
    /**
     * M�todo para establecer el valor del calificar para el calificador
     * @param integer $calificar
     */
    public function setIpCalificador($ip_calificador) {
        $this->ip_calificador = $ip_calificador;
    }
    
    /**
     * Devuelve el valor del campo id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo numero_caja
     * @return string
     */
    public function getNumeroCaja() {
        return $this->numero_caja;
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
     * Devuelve el valor del campo usuario
     * @return string
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Devuelve el valor del campo tipo_calificacion_operador
     * @return string
     */
    public function getTipoCalificacionOperador() {
        return $this->tipo_calificacion_operador;
    }

    /**
     * Devuelve el valor del campo horario_id
     * @return integer
     */
    public function getHorarioId() {
        return $this->horario_id;
    }

    /**
     * Devuelve el valor del campo transferir_uno
     * @return integer
     */
    public function getTransferirUno() {
        return $this->transferir_uno;
    }

    /**
     * Devuelve el valor del campo transferir_todos
     * @return integer
     */
    public function getTransferirTodos() {
        return $this->transferir_todos;
    }

    /**
     * Devuelve el valor del campo ubicacion_id
     * @return integer
     */
    public function getUbicacionId() {
        return $this->ubicacion_id;
    }

    /**
     * Devuelve el valor del campo ubicacion_id
     * @return integer
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * Devuelve el valor del campo tiempo
     * @return integer
     */
    public function getTiempo() {
        return $this->tiempo;
    }
    
    /**
     * Devuelve el valor del campo timbre
     * @return integer
     */
    public function getTimbre() {
        return $this->timbre;
    }
    
    /**
     * Devuelve el valor del campo calificar
     * @return integer
     */
    public function getCalificar() {
        return $this->calificar;
    }
    
    /**
     * Devuelve el valor del campo calificar
     * @return integer
     */
    public function getUsuarioActual() {
        return $this->usuario_actual;
    }
    
    /**
     * Devuelve el valor del campo calificar
     * @return integer
     */
    public function getIpCalificador() {
        return $this->ip_calificador;
    }
    
    /**
     * Método inicializador de la Entidad
     */
    protected function initialize() {
        $this->belongsTo('horario_id', 'horario', 'id');
        $this->belongsTo('ubicacion_id', 'ubicacion', 'id');
    }

}

