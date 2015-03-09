<?php

/**
 * Controlador Status
 *
 * @access public
 * @version 1.0
 */
class StatusController extends ApplicationController {

    public $servidor_node;

    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        
    }

    /**
     * Crear un Preguntas/
     *
     */
    public function verAction() {
        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst();
        //$this->carpeta = $buscaEmpresa->getCarpeta();
        $this->servidor_node = $buscaEmpresa->getServidorNode();
        $this->setResponse('ajax');
        $this->render('status/index');      //carga la vista
    }

    public function actualizarDatosAction() {

        $this->setResponse("json");
        $usuario_id = $this->getPostParam('usuario_id');

        $estado_login = "";
        $numero_caja = "";
        $ubicacion = "";
        $turnos_atendidos = "";
        $turnos_anulados = "";
        $turnos_transferidos = "";
        $fecha_hoy = date('Y-m-d');
        //--busco ele estado actual del usuario
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT u.id AS usuario_id, u.nombres, u.login, c.id AS caja_id, c.numero_caja, ubi.nombre_ubicacion 
        FROM usuario u
        LEFT JOIN caja c ON c.usuario_actual=u.id
        LEFT JOIN ubicacion ubi ON ubi.id=c.ubicacion_id
        WHERE u.id= $usuario_id");
        while ($row = $db->fetchArray($result)) {
            $estado_login = $row['login'];
            $numero_caja = $row['numero_caja'];
            $ubicacion = $row['nombre_ubicacion'];
        }

        //--busco los turnos atendidos
        $result = $db->query("SELECT COUNT(*) AS turnos_atendidos FROM turnos
        WHERE fecha_emision = '$fecha_hoy' AND atendido=1 AND id_username = $usuario_id");
        while ($row = $db->fetchArray($result)) {
            $turnos_atendidos = $row['turnos_atendidos'];
        }
        //--busco los turnos anulados
        $result = $db->query("SELECT COUNT(*) AS turnos_anulados FROM turnos
        WHERE fecha_emision = '$fecha_hoy' AND rechazado=1 AND id_username = $usuario_id");
        while ($row = $db->fetchArray($result)) {
            $turnos_anulados = $row['turnos_anulados'];
        }

        //--busco los turnos tansferidos
        $result4 = $db->query("SELECT COUNT(*) AS turnos_transferidos FROM turnos_transferidos
                WHERE fecha_emision = '$fecha_hoy' AND id_user_transfiere = $usuario_id");
        while ($row4 = $db->fetchArray($result4)) {
            $turnos_transferidos = $row4['turnos_transferidos'];
        }

        $datos = array('turnos_atendidos' => $turnos_atendidos, 'turnos_anulados' => $turnos_anulados, 
            'turnos_transferidos' => $turnos_transferidos, 'estado_login'=>$estado_login, 'numero_caja'=>$numero_caja,'ubicacion'=>$ubicacion);
        return($datos);
    }

}
