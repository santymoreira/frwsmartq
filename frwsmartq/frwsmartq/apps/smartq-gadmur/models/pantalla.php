<?php

class Pantalla extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $numero;

	/**
	 * @var string
	 */
	protected $descripcion;

	/**
	 * @var integer
	 */
	protected $ubicacion_id;

	/**
	 * @var integer
	 */
	protected $timbre;

	/**
	 * @var string
	 */
	protected $ip_equipo;

	/**
	 * @var string
	 */
	protected $tipo_pantalla;

	/**
	 * @var integer
	 */
	protected $con_ticket;

	/**
	 * @var string
	 */
	protected $color_turnos;

	/**
	 * @var string
	 */
	protected $color_noticias;

	/**
	 * @var string
	 */
	protected $color_reloj;

	/**
	 * @var integer
	 */
	protected $efecto_turno_superior;

	/**
	 * @var Date
	 */
	protected $creacion_at;
        
        /**
	 * @var integer
	 */
	protected $tono;
        
	/**
	 * @var integer
	 */
	protected $tiempo_tono;
	
	/**
	 * @var integer
	 */
	protected $ventana;	
        
        /**
	 * @var string
	 */
	protected $formato_voz;
        
        /**
	 * @var string
	 */
	protected $plantilla;


	/**
	 * Método para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Método para establecer el valor del campo numero
	 * @param integer $numero
	 */
	public function setNumero($numero){
		$this->numero = $numero;
	}

	/**
	 * Método para establecer el valor del campo descripcion
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	/**
	 * Método para establecer el valor del campo ubicacion_id
	 * @param integer $ubicacion_id
	 */
	public function setUbicacionId($ubicacion_id){
		$this->ubicacion_id = $ubicacion_id;
	}

	/**
	 * Método para establecer el valor del campo timbre
	 * @param integer $timbre
	 */
	public function setTimbre($timbre){
		$this->timbre = $timbre;
	}

	/**
	 * Método para establecer el valor del campo ip_equipo
	 * @param string $ip_equipo
	 */
	public function setIpEquipo($ip_equipo){
		$this->ip_equipo = $ip_equipo;
	}

	/**
	 * Método para establecer el valor del campo tipo_pantalla
	 * @param string $tipo_pantalla
	 */
	public function setTipoPantalla($tipo_pantalla){
		$this->tipo_pantalla = $tipo_pantalla;
	}

	/**
	 * Método para establecer el valor del campo con_ticket
	 * @param integer $con_ticket
	 */
	public function setConTicket($con_ticket){
		$this->con_ticket = $con_ticket;
	}

	/**
	 * Método para establecer el valor del campo color_turnos
	 * @param string $color_turnos
	 */
	public function setColorTurnos($color_turnos){
		$this->color_turnos = $color_turnos;
	}

	/**
	 * Método para establecer el valor del campo color_noticias
	 * @param string $color_noticias
	 */
	public function setColorNoticias($color_noticias){
		$this->color_noticias = $color_noticias;
	}

	/**
	 * Método para establecer el valor del campo color_reloj
	 * @param string $color_reloj
	 */
	public function setColorReloj($color_reloj){
		$this->color_reloj = $color_reloj;
	}

	/**
	 * Método para establecer el valor del campo efecto_turno_superior
	 * @param integer $efecto_turno_superior
	 */
	public function setEfectoTurnoSuperior($efecto_turno_superior){
		$this->efecto_turno_superior = $efecto_turno_superior;
	}

	/**
	 * Método para establecer el valor del campo creacion_at
	 * @param Date $creacion_at
	 */
	public function setCreacionAt($creacion_at){
		$this->creacion_at = $creacion_at;
	}
        
        /**
	 * M�todo para establecer el valor del campo tono
	 * @param integer $tono
	 */
	public function setTono($tono){
		$this->tono = $tono;
	}
        
	/**
	 * M�todo para establecer el valor del campo tiempo_tono
	 * @param integer $tiempo_tono
	 */
	public function setTiempoTono($tiempo_tono){
		$this->tiempo_tono = $tiempo_tono;
	}
	
	/**
	 * M�todo para establecer el valor del campo ventana
	 * @param integer $ventana
	 */
	public function setVentana($ventana){
		$this->ventana = $ventana;
	}
        
        /**
	 * M�todo para establecer el valor del campo ventana
	 * @param integer $ventana
	 */
	public function setFormatoVoz($formato_voz){
		$this->formato_voz = $formato_voz;
	}
        
        /**
	 * M�todo para establecer el valor del campo ventana
	 * @param integer $ventana
	 */
	public function setPlantilla($plantilla){
		$this->plantilla = $plantilla;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo numero
	 * @return integer
	 */
	public function getNumero(){
		return $this->numero;
	}

	/**
	 * Devuelve el valor del campo descripcion
	 * @return string
	 */
	public function getDescripcion(){
		return $this->descripcion;
	}

	/**
	 * Devuelve el valor del campo ubicacion_id
	 * @return integer
	 */
	public function getUbicacionId(){
		return $this->ubicacion_id;
	}

	/**
	 * Devuelve el valor del campo timbre
	 * @return integer
	 */
	public function getTimbre(){
		return $this->timbre;
	}

	/**
	 * Devuelve el valor del campo ip_equipo
	 * @return string
	 */
	public function getIpEquipo(){
		return $this->ip_equipo;
	}

	/**
	 * Devuelve el valor del campo tipo_pantalla
	 * @return string
	 */
	public function getTipoPantalla(){
		return $this->tipo_pantalla;
	}

	/**
	 * Devuelve el valor del campo con_ticket
	 * @return integer
	 */
	public function getConTicket(){
		return $this->con_ticket;
	}

	/**
	 * Devuelve el valor del campo color_turnos
	 * @return string
	 */
	public function getColorTurnos(){
		return $this->color_turnos;
	}

	/**
	 * Devuelve el valor del campo color_noticias
	 * @return string
	 */
	public function getColorNoticias(){
		return $this->color_noticias;
	}

	/**
	 * Devuelve el valor del campo color_reloj
	 * @return string
	 */
	public function getColorReloj(){
		return $this->color_reloj;
	}

	/**
	 * Devuelve el valor del campo efecto_turno_superior
	 * @return integer
	 */
	public function getEfectoTurnoSuperior(){
		return $this->efecto_turno_superior;
	}

	/**
	 * Devuelve el valor del campo creacion_at
	 * @return Date
	 */
	public function getCreacionAt(){
		return $this->creacion_at;
	}

        /**
	 * Devuelve el valor del campo tono
	 * @return integer
	 */
	public function getTono(){
		return $this->tono;
	}
        
	/**
	 * Devuelve el valor del campo tiempo_tono
	 * @return integer
	 */
	public function getTiempoTono(){
		return $this->tiempo_tono;
	}
	
	/**
	 * Devuelve el valor del campo ventana
	 * @return integer
	 */
	public function getVentana(){
		return $this->ventana;
	}
        
        /**
	 * Devuelve el valor del campo ventana
	 * @return integer
	 */
	public function getFormatoVoz(){
		return $this->formato_voz;
	}
        
        /**
	 * Devuelve el valor del campo ventana
	 * @return integer
	 */
	public function getPlantilla(){
		return $this->plantilla;
	}
        
	/**
	 * Método inicializador de la Entidad
	 */
	protected function initialize(){
            $this->belongsTo('ubicacion_id','ubicacion','id');
	}

}

