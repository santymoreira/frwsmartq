<?php

class Configuracionsistema extends ActiveRecord {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var integer
	 */
	protected $activar_calificador;

	/**
	 * @var integer
	 */
	protected $activar_difusion;

	/**
	 * @var integer
	 */
	protected $publicar_noticias;

	/**
	 * @var string
	 */
	protected $velocidad_publicacion;

	/**
	 * @var string
	 */
	protected $pc_difusion;

	/**
	 * @var string
	 */
	protected $puerto;

	/**
	 * @var integer
	 */
	protected $ver_tiempo_maximo;

	/**
	 * @var integer
	 */
	protected $ver_tiempo_atencion;

	/**
	 * @var string
	 */
	protected $ubicacion_impresora;

	/**
	 * @var string
	 */
	protected $nombre_impresora;

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
	 * @var integer
	 */
	protected $logo;	
	
	/**
	 * @var integer
	 */
	protected $logo_sinticket;

	/**
	 * @var integer
	 */
	protected $logo_conticket;	
	
	/**
	 * @var integer
	 */
	protected $logo_calificador;	

	/**
	 * @var integer
	 */
	protected $logo_ticket;		
	
	/**
	 * @var integer
	 */
	protected $pantalla_ticket;			

	/**
	 * M�todo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * M�todo para establecer el valor del campo activar_calificador
	 * @param integer $activar_calificador
	 */
	public function setActivarCalificador($activar_calificador){
		$this->activar_calificador = $activar_calificador;
	}

	/**
	 * M�todo para establecer el valor del campo activar_difusion
	 * @param integer $activar_difusion
	 */
	public function setActivarDifusion($activar_difusion){
		$this->activar_difusion = $activar_difusion;
	}

	/**
	 * M�todo para establecer el valor del campo publicar_noticias
	 * @param integer $publicar_noticias
	 */
	public function setPublicarNoticias($publicar_noticias){
		$this->publicar_noticias = $publicar_noticias;
	}

	/**
	 * M�todo para establecer el valor del campo velocidad_publicacion
	 * @param string $velocidad_publicacion
	 */
	public function setVelocidadPublicacion($velocidad_publicacion){
		$this->velocidad_publicacion = $velocidad_publicacion;
	}

	/**
	 * M�todo para establecer el valor del campo pc_difusion
	 * @param string $pc_difusion
	 */
	public function setPcDifusion($pc_difusion){
		$this->pc_difusion = $pc_difusion;
	}

	/**
	 * M�todo para establecer el valor del campo puerto
	 * @param string $puerto
	 */
	public function setPuerto($puerto){
		$this->puerto = $puerto;
	}

	/**
	 * M�todo para establecer el valor del campo ver_tiempo_maximo
	 * @param integer $ver_tiempo_maximo
	 */
	public function setVerTiempoMaximo($ver_tiempo_maximo){
		$this->ver_tiempo_maximo = $ver_tiempo_maximo;
	}

	/**
	 * M�todo para establecer el valor del campo ver_tiempo_atencion
	 * @param integer $ver_tiempo_atencion
	 */
	public function setVerTiempoAtencion($ver_tiempo_atencion){
		$this->ver_tiempo_atencion = $ver_tiempo_atencion;
	}

	/**
	 * M�todo para establecer el valor del campo ubicacion_impresora
	 * @param string $ubicacion_impresora
	 */
	public function setUbicacionImpresora($ubicacion_impresora){
		$this->ubicacion_impresora = $ubicacion_impresora;
	}

	/**
	 * M�todo para establecer el valor del campo nombre_impresora
	 * @param string $nombre_impresora
	 */
	public function setNombreImpresora($nombre_impresora){
		$this->nombre_impresora = $nombre_impresora;
	}

	/**
	 * M�todo para establecer el valor del campo creacion_at
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
	 * M�todo para establecer el valor del campo logo
	 * @param integer $logo
	 */
	public function setLogo($logo){
		$this->logo = $logo;
	}	
	
	/**
	 * M�todo para establecer el valor del campo logo_sinticket
	 * @param integer $logo_sinticket
	 */
	public function setLogoSinticket($logo_sinticket){
		$this->logo_sinticket = $logo_sinticket;
	}		
	
	/**
	 * M�todo para establecer el valor del campo logo_conticket
	 * @param integer $logo_conticket
	 */
	public function setLogoConticket($logo_conticket){
		$this->logo_conticket = $logo_conticket;
	}	

	/**
	 * M�todo para establecer el valor del campo logo_calificador
	 * @param integer $logo_calificador
	 */
	public function setLogoCalificador($logo_calificador){
		$this->logo_calificador = $logo_calificador;
	}	

	/**
	 * M�todo para establecer el valor del campo logo_ticket
	 * @param integer $logo_ticket
	 */
	public function setLogoTicket($logo_ticket){
		$this->logo_ticket = $logo_ticket;
	}		

	/**
	 * M�todo para establecer el valor del campo pantalla_ticket
	 * @param integer $pantalla_ticket
	 */
	public function setPantallaTicket($pantalla_ticket){
		$this->pantalla_ticket = $pantalla_ticket;
	}	
	
	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo activar_calificador
	 * @return integer
	 */
	public function getActivarCalificador(){
		return $this->activar_calificador;
	}

	/**
	 * Devuelve el valor del campo activar_difusion
	 * @return integer
	 */
	public function getActivarDifusion(){
		return $this->activar_difusion;
	}

	/**
	 * Devuelve el valor del campo publicar_noticias
	 * @return integer
	 */
	public function getPublicarNoticias(){
		return $this->publicar_noticias;
	}

	/**
	 * Devuelve el valor del campo velocidad_publicacion
	 * @return string
	 */
	public function getVelocidadPublicacion(){
		return $this->velocidad_publicacion;
	}

	/**
	 * Devuelve el valor del campo pc_difusion
	 * @return string
	 */
	public function getPcDifusion(){
		return $this->pc_difusion;
	}

	/**
	 * Devuelve el valor del campo puerto
	 * @return string
	 */
	public function getPuerto(){
		return $this->puerto;
	}

	/**
	 * Devuelve el valor del campo ver_tiempo_maximo
	 * @return integer
	 */
	public function getVerTiempoMaximo(){
		return $this->ver_tiempo_maximo;
	}

	/**
	 * Devuelve el valor del campo ver_tiempo_atencion
	 * @return integer
	 */
	public function getVerTiempoAtencion(){
		return $this->ver_tiempo_atencion;
	}

	/**
	 * Devuelve el valor del campo ubicacion_impresora
	 * @return string
	 */
	public function getUbicacionImpresora(){
		return $this->ubicacion_impresora;
	}

	/**
	 * Devuelve el valor del campo nombre_impresora
	 * @return string
	 */
	public function getNombreImpresora(){
		return $this->nombre_impresora;
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
	 * Devuelve el valor del campo logo
	 * @return integer
	 */
	public function getLogo(){
		return $this->logo;
	}	

	/**
	 * Devuelve el valor del campo logo_sinticket
	 * @return integer
	 */
	public function getLogoSinticket(){
		return $this->logo_sinticket;
	}		
	
	
	/**
	 * Devuelve el valor del campo logo_conticket
	 * @return integer
	 */
	public function getLogoConticket(){
		return $this->logo_conticket;
	}	
	
	/**
	 * Devuelve el valor del campo logo_calificador
	 * @return integer
	 */
	public function getLogoCalificador(){
		return $this->logo_calificador;
	}	

	/**
	 * Devuelve el valor del campo logo_ticket
	 * @return integer
	 */
	public function getLogoTicket(){
		return $this->logo_ticket;
	}	

	/**
	 * Devuelve el valor del campo pantalla_ticket
	 * @return integer
	 */
	public function getPantallaTicket(){
		return $this->pantalla_ticket;
	}		
	
	/**
	 * M�todo inicializador de la Entidad
	 */
	protected function initialize(){
	}


}

