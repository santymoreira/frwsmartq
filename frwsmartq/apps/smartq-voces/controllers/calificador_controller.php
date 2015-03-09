<?php

/**
 * Controlador Calificador
 *
 * @access public
 * @version 1.0
 */
class CalificadorController extends ApplicationController {

    public $carpeta;
    public $servidor_node;
    //public $caja_id;
    public $numero_modulo;
    public $tipo_calificacion;
    public $foto;   //foto del operador
    public $nombre_operador;
    public $ip_calificador;

//    public $id_grupopregunta;
//    public $num_preguntas;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction($tablet) {
        //--para buscar el servidor node
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        $this->carpeta = $buscaEmpresa->getCarpeta();
        $this->servidor_node = $buscaEmpresa->getServidorNode();

        //---para buscar el numero de caja
//        $this->caja_id = $buscaCaja->getId();
//        $this->tipo_calificacion = $buscaCaja->getTipoCalificacionOperador();
//        $this->numero_modulo = $buscaCaja->getNumeroCaja();
        //$this->render('calificador/pantallacalificador1');
        //$nombreip = $_SERVER['REMOTE_ADDR'];    //ip del calificador
        $nombreip = $tablet;
        $caja = new Caja();
        $buscaCaja = $caja->find("ip_calificador='$nombreip'");
        if (count($buscaCaja) > 0) {
            foreach ($buscaCaja as $row) {
                //$row->getNumeroCaja();
                //$this->caja_id = $buscaCaja->getId();
                $this->tipo_calificacion = $row->getTipoCalificacionOperador();
                $this->numero_modulo = $row->getNumeroCaja();
                $this->ip_calificador = $row->getIpCalificador();

                if ($row->getUsuarioActual() > 0) {
                    //-- busco los datos del usuario
                    $usuario = new Usuario();
                    $buscaUsuario = $usuario->findFirst("id={$row->getUsuarioActual()}");

                    $this->foto = $buscaUsuario->getFoto();
                    $this->nombre_operador = $buscaUsuario->getNombres();

                    $idcaja = $row->getId();
                    $numcaja1 = $row->getNumeroCaja();
                    $tipo_calificacion_operador = $row->getTipoCalificacionOperador();
                    $t = explode(';', $tipo_calificacion_operador);
                    if (count($t) >= 2) {
                        $this->id_grupopregunta = $t[1];
                        $preguntas = new Preguntas();
                        $this->num_preguntas = $preguntas->count("conditions: publicar=1 AND id_grupopregunta=$this->id_grupopregunta", "order: orden");
                        if ($this->num_preguntas > 8)
                            $this->num_preguntas = 8;
                    }
                    $this->render('calificador/inicio_calificador');
                } else {
                    echo "El operador no ha iniciado sesión.";
                }
            }
        } else {
            echo "Este calificador con IP $nombreip no está asignado a un operador.";
        }
    }

    public function subir_fotoAction() {
        //$this->setResponse('ajax');
        $this->setResponse('json');
        $src = $this->getPostParam("src");
        $turno_id = $this->getPostParam("turno_id");


        $db = DbBase::rawConnect();
        $db->query("update turnos set foto='$src' where id=$turno_id");
        $datos = array('exito' => '1');
        return($datos);
    }

    public function cargarInicialAction() {
        $this->setResponse('ajax');
        $this->render('calificador/pantallainicial');
    }

    public function cargarPublicacionAction() {
        $this->setResponse('ajax');
        $this->render('calificador/pantallapublicador');
    }

    public $tiempoCalificador = 10;

    public function cargarCalificacionAction() {
        $this->setResponse('ajax');
        $this->render('calificador/pantallacalificador');
    }

    public function cargarCalificacionMatrizAction() {
        $this->setResponse('ajax');
        $this->render('calificador/pantallacalificadorMatriz');
    }

}
