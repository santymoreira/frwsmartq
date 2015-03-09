<?php

class Menu extends ActiveRecord {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $modulo_id;

    /**
     * @var string
     */
    protected $nombre;

    /**
     * @var string
     */
    protected $ruta;

    /**
     * @var integer
     */
    protected $idreferencia;

    /**
     * @var integer
     */
    protected $estado;

    /**
     * @var integer
     */
    protected $orden;

    /**
     * @var integer
     */
    protected $principal;

    /**
     * @var string
     */
    protected $posicion;

    /**
     * @var string
     */
    protected $tipo_ventana;

    /**
     * Método para establecer el valor del campo id
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Método para establecer el valor del campo modulo_id
     * @param integer $modulo_id
     */
    public function setModuloId($modulo_id) {
        $this->modulo_id = $modulo_id;
    }

    /**
     * Método para establecer el valor del campo nombre
     * @param string $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Método para establecer el valor del campo ruta
     * @param string $ruta
     */
    public function setRuta($ruta) {
        $this->ruta = $ruta;
    }

    /**
     * Método para establecer el valor del campo idreferencia
     * @param integer $idreferencia
     */
    public function setIdreferencia($idreferencia) {
        $this->idreferencia = $idreferencia;
    }

    /**
     * Método para establecer el valor del campo estado
     * @param integer $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * Método para establecer el valor del campo orden
     * @param integer $orden
     */
    public function setOrden($orden) {
        $this->orden = $orden;
    }

    /**
     * Método para establecer el valor del campo principal
     * @param integer $principal
     */
    public function setPrincipal($principal) {
        $this->principal = $principal;
    }

    /**
     * Método para establecer el valor del campo posicion
     * @param string $posicion
     */
    public function setPosicion($posicion) {
        $this->posicion = $posicion;
    }

    /**
     * Método para establecer el valor del campo tipo_ventana
     * @param string $tipo_ventana
     */
    public function setTipoVentana($tipo_ventana) {
        $this->tipo_ventana = $tipo_ventana;
    }

    /**
     * Devuelve el valor del campo id
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Devuelve el valor del campo modulo_id
     * @return integer
     */
    public function getModuloId() {
        return $this->modulo_id;
    }

    /**
     * Devuelve el valor del campo nombre
     * @return string
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Devuelve el valor del campo ruta
     * @return string
     */
    public function getRuta() {
        return $this->ruta;
    }

    /**
     * Devuelve el valor del campo idreferencia
     * @return integer
     */
    public function getIdreferencia() {
        return $this->idreferencia;
    }

    /**
     * Devuelve el valor del campo estado
     * @return integer
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Devuelve el valor del campo orden
     * @return integer
     */
    public function getOrden() {
        return $this->orden;
    }

    /**
     * Devuelve el valor del campo principal
     * @return integer
     */
    public function getPrincipal() {
        return $this->principal;
    }

    /**
     * Devuelve el valor del campo posicion
     * @return string
     */
    public function getPosicion() {
        return $this->posicion;
    }

    /**
     * Devuelve el valor del campo tipo_ventana
     * @return string
     */
    public function getTipoVentana() {
        return $this->tipo_ventana;
    }

    /**
     * Método inicializador de la Entidad
     */
    protected function initialize() {
        $this->belongsTo('modulo_id', 'modulo', 'id');
    }

//
//    SELECT m.id, modulo_id, nombre, ruta, idreferencia, estado, orden, principal, posicion, tipo_ventana 
//FROM menu m, permiso p
//WHERE m.id=p.menu_id AND grupo_id=1
//AND idreferencia=2 ORDER BY orden asc
    public function obtenerArbolMenu($grupo_id, $modulo_id = 0, $idreferencia = 0) {
        //print_r($array_menus_no_permitidos); die();
        //echo "grupo:$grupo_id modulo:$modulo_id referencia:$idreferencia";
        $html = '';
        if ($modulo_id > 0) {
            if ($idreferencia > 0) {
                //$menus=$this->find("conditions: modulo_id=$modulo_id AND idreferencia=$idreferencia","order: orden asc");
                //INICIO NELSON
                $condicion = "{#Menu}.modulo_id=$modulo_id AND {#Permiso}.grupo_id=$grupo_id AND {#Menu}.idreferencia=$idreferencia AND {#Menu}.estado=1";
                $query = new ActiveRecordJoin(array(
                    "entities" => array("Menu", "Permiso"),
                    "fields" => array(
                        "{#Menu}.id",
                        "{#Menu}.modulo_id",
                        "{#Menu}.nombre",
                        "{#Menu}.ruta",
                        "{#Menu}.tipo_ventana",),
                    "conditions" => $condicion,
                    "order" => "orden asc"
                ));
                //FIN NELSON
            } else {
                //$menus=$this->find("conditions: modulo_id=$modulo_id AND estado=1 AND principal=1","order: orden asc");
                //INICIO NELSON
                $condicion = "{#Menu}.modulo_id=$modulo_id AND {#Permiso}.grupo_id=$grupo_id AND estado=1 AND principal=1";
                $query = new ActiveRecordJoin(array(
                    "entities" => array("Menu", "Permiso"),
                    "fields" => array(
                        "{#Menu}.id",
                        "{#Menu}.modulo_id",
                        "{#Menu}.nombre",
                        "{#Menu}.ruta",
                        "{#Menu}.tipo_ventana",),
                    "conditions" => $condicion,
                    "order" => "orden asc"
                ));
                //FIN NELSON
            }
        }

        /* else {
          if($idreferencia>0)
          $menus=$this->find("conditions: idreferencia=$idreferencia AND estado=1","order: orden asc");
          else
          $menus=$this->find("conditions: posicion='principal' AND estado=1","order: orden asc");
          } */


        foreach ($query->getResultSet() as $menu) {
//            die();
            //foreach($menus as $menu) {
            if ($this->count("conditions: idreferencia=" . $menu->getId()) > 0) {
                $html.="<li><span class='folder'>&nbsp;{$menu->getNombre()}</span>";
                $html.='<ul>' . self::obtenerArbolMenu($grupo_id, $menu->getModuloId(), $menu->getId()) . '</ul></li>';
            } else { //idreferencia = 0
                //if (!in_array($menu->getId(), $array_menus_no_permitidos)) {
                    if ($menu->getRuta() == "/")
                        $ruta = "";
                    else if ($menu->getRuta() != "")
                        $ruta = "javascript:cargarPagina('{$menu->getRuta()}','{$menu->getTipoVentana()}')";
                    else
                        $ruta = 'javascript:void(0)';
                    $html.="<li><span ><a href=\"$ruta\" style='text-decoration:none'>&nbsp;{$menu->getNombre()}</a></span></li>";
                //}
            }
        }
        return $html;
    }

}
