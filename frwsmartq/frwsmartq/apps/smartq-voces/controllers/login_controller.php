<?php

class LoginController extends ApplicationController {

    public $id_grupopregunta;
    public $num_preguntas;
    public $num_calificaciones;
    public $num_modulo;
    public $carpeta;

    public function initialize() {
        $this->setPersistance(true);
    }

    public function indexAction() {
        if (SessionNamespace::exists("datosUsuarioSMC"))
            $this->redirect("template", 0);
    }

    public function validarUsuarioAction() {
        $reglas = array(
            "usuario" => array(
                "filter" => "alpha",
                "message" => "Por favor ingrese su nombre de usuario"
            )
        );
        if ($this->validateRequired($reglas) == true) {
            $username = $this->getPostParam("usuario", "alpha");
            $password = base64_encode($this->getPostParam("password"));
            $usuario = false;
            $sistema = false;
            /* if($username=='admin') {
              $sistema = $this->Sistema->findFirst();
              if($sistema!==false) {
              if($sistema->getClaveadmin() == $password) {
              $successAuth = true;
              $this->routeTo("controller: sistema");
              }else {
              $this->addValidationMessage("Password de Administrador incorrecto");
              $this->routeTo("action: index");
              }
              }else {
              $this->addValidationMessage("Existen problemas para conectarse como administrador");
              $this->routeTo("action: index");
              }
              }else { */
            $usuario = $this->Usuario->findFirst("username='$username' AND estado='Activo'"); //si existe el usuario
            if ($usuario !== false) {
                if ($username == $usuario->getUsername()) {
                    if ($username == 'superadmin') {
                        if ($password == '') {
                            $this->addValidationMessage("Ingrese la clave.");
                            $this->routeTo("action: index");
                        } else if ($password == base64_encode('smcpw2014')) {
                            $successAuth = true;
                            $nombreip = $_SERVER['REMOTE_ADDR'];
                            $nombrem = gethostbyaddr($nombreip);
                            if ($nombrem == "localhost") {
                                $nombrem = PHP_uname('n');
                                $nombreip = gethostbyname($nombrem);
                            }
                            $dataUsuario = SessionNamespace::add('datosUsuarioSMC');
                            $dataUsuario->setId($usuario->getId());
                            $dataUsuario->setNombre($usuario->getNombres());
                            $dataUsuario->setUsername($usuario->getUsername());
                            $dataUsuario->setPassword(base64_decode($usuario->getPassword()));
                            $dataUsuario->setHostname($nombrem);
                            $dataUsuario->setIp($nombreip);
                            $dataUsuario->setEsOperador(0);
                            $dataUsuario->setSesionId("");
                            $dataUsuario->setFechaInicio(date("Y-m-d"));
                            $dataUsuario->setHoraInicio(date("H:i:s"));
                            $fun = new Funciones();
                            $this->redirect("grupo/seleccionarGrupo");
                        } else {
                            $this->addValidationMessage("Clave incorrecta.");
                            $this->routeTo("action: index");
                        } 
                    } else if ($password == $usuario->getPassword()) {
                        //-- buscamos si no ha iniciado la sesion en otro lado
                        //$sesiones= new Sesiones();
                        $sesion = $this->Sesiones->findFirst("usuario_id='{$usuario->getId()}' AND estado='Activo' AND fecha_inicio='" . date("Y-m-d") . "'");
                        if ($sesion and (1 == 0)) { //si existe la sesion envia alerta
                            $this->addValidationMessage("El usuario {$usuario->getUsername()} ya ha iniciado una sesion.");
                            $this->routeTo("action: index");
                        } else {
                            $successAuth = true;
                            $nombreip = $_SERVER['REMOTE_ADDR'];
                            $nombrem = gethostbyaddr($nombreip);
                            if ($nombrem == "localhost") {
                                $nombrem = PHP_uname('n');
                                $nombreip = gethostbyname($nombrem);
                            }
                            $dataUsuario = SessionNamespace::add('datosUsuarioSMC');
                            $dataUsuario->setId($usuario->getId());
                            $dataUsuario->setNombre($usuario->getNombres());
                            $dataUsuario->setUsername($usuario->getUsername());
                            $dataUsuario->setPassword(base64_decode($usuario->getPassword()));
                            $dataUsuario->setHostname($nombrem);
                            $dataUsuario->setIp($nombreip);
                            $dataUsuario->setEsOperador(0);
                            $dataUsuario->setSesionId("");
                            $dataUsuario->setFechaInicio(date("Y-m-d"));
                            $dataUsuario->setHoraInicio(date("H:i:s"));
                            $fun = new Funciones();

                            $iduser = $usuario->getId();

                            //actualizamos usuario.login en 1 para todos los usuarios (operador, pantalla, etc)
                            $usuario = new Usuario();
                            $condicion = "id= $iduser";
                            $usuario->updateAll("login=1", "conditions: $condicion");

                            $this->redirect("grupo/seleccionarGrupo");
                        }
                    } else {
                        $this->addValidationMessage("Password Incorrecto");
                        $this->routeTo("action: index");
                    }
                } else {
                    $this->addValidationMessage("Usuario Incorrecto");
                    $this->routeTo("action: index");
                }
            } else {
                $successAuth = false;
                $this->addValidationMessage("El usuario no existe o esta bloqueado, comuniquese con el administrador");
                $this->routeTo("action: index");
            }
            //}
        } else {
            $this->routeTo("action: index");
        }
    }

    public function salirAction() {

        if (SessionNamespace::exists("datosUsuarioSMC")) {
            $datos = SessionNamespace::get('datosUsuarioSMC');
            $esOperador = $datos->getEsOperador();
            $sesionId = $datos->getSesionId();
            $usuario_id = $datos->getId();
            //INICIO TERMINAR LA SESION
            //echo "sesion= ".$esOperador." es op=".$sesionId;
            //$esOperador=0;
            if ($esOperador == 1) {
                $fechaInicio = $datos->getFechaInicio();
                $horaInicio = $datos->getHoraInicio();
                $fechaFin = date("Y-m-d");
                $horaFin = date("H:i:s");
                $fun = new Funciones();
                $duracion = $fun->difFecha($fechaInicio . " " . $horaInicio, $fechaFin . " " . $horaFin);
                $sesiones = new Sesiones();
                //$sesiones->setId($this->sesionId);
                //$sesiones->setFechaFin($fechaFin);
                //$sesiones->setHoraFin($horaFin);
                //$sesiones->save();
                $sesiones->updateAll("fecha_fin='$fechaFin', hora_fin='$horaFin',estado='Inactivo', duracion='$duracion'", "id=$sesionId");
                $caja = new Caja();
                $caja->updateAll("usuario_actual='(NULL)'", "usuario_actual=$usuario_id");
                //die();
            }
            //FIN TERMINAR LA SESION
            //actualizamos usuario.login en 0 para todos los usuarios (operador, pantalla, etc)
            $usuario = new Usuario();
            $usuario->updateAll("login=0", "id= $usuario_id");
        }
        SessionNamespace::drop("datosUsuarioSMC");
        $this->routeTo("action: index");
    }

    public function pantallainicialAction() {
        $this->iniciarPantallaSecundaria();
    }

    public function pantallacalificadorAction() {
        $this->iniciarPantallaSecundaria();
    }

    public function pantallapublicadorAction() {
        $this->iniciarPantallaSecundaria();
    }

    /*
     * Funci�n que permite abrir la ventana popup del Calificador de MAtriz
     */

    public function pantallacalificadorMatrizAction() {
        $this->iniciarPantallaSecundaria(); //llamada a funcion
    }

    public function iniciarPantallaSecundaria() {
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $idsesion = $dataUsuario->getId();
        $usuario = $dataUsuario->getNombre();

        //busco los datos del usuario
        $buscaUsuario = $this->Usuario->findFirst("id=$idsesion");
        $foto = $buscaUsuario->getFoto();

        $nombreip = $_SERVER['REMOTE_ADDR'];
        $caja = new Caja();
        $buscaCaja = $caja->findFirst("ip='$nombreip'");
        $idcaja = $buscaCaja->getId();
        $numcaja1 = $buscaCaja->getNumeroCaja();
        $tipo_calificacion_operador = $buscaCaja->getTipoCalificacionOperador();
        $t = explode(';', $tipo_calificacion_operador);
        if (count($t) >= 2) {
            $this->id_grupopregunta = $t[1];
            $preguntas = new Preguntas();
            $this->num_preguntas = $preguntas->count("conditions: publicar=1 AND id_grupopregunta=$this->id_grupopregunta", "order: orden");
            if ($this->num_preguntas > 8)
                $this->num_preguntas = 8;
        }


        $turnoactual = 0;
        $turnoespera = 0;
        //Tag::displayTo('actual1', $turnoactual);
        //Tag::displayTo('espera1', $turnoespera);
        Tag::displayTo('numcaja', $numcaja1);
        Tag::displayTo('idcaja', $idcaja);
        Tag::displayTo('usuario', $usuario);
        Tag::displayTo('foto', $foto);

        $empresa = new Empresa();
        $buscaEmpresa = $empresa->findFirst("columns: carpeta");
        $this->carpeta = $buscaEmpresa->getCarpeta();
    }

    /*     * ***** FUNCIONES PARA CALIFICACION PARA CAJAS ********* */

    /**
     * Se hace diferente funcion porque esta ligado a una vista que puede ser
     * diferente con la de atencion con tickets
     */
    public function pantallacalificadorCajasAction() {
        $this->iniciarPantallaSecundaria();
    }

    /**
     * Funcion que permite abrir la ventana popup del Calificador de MAtriz
     * vista: login/pantallacalificadorMatriz
     */
    public function pantallacalificadorMatrizCajasAction() {
        $this->iniciarPantallaSecundaria(); //llamada a funci�n
        $preguntas = new Preguntas();
        $this->num_preguntas = $preguntas->count("conditions: publicar=1", "order: orden");
        if ($this->num_preguntas > 8)
            $this->num_preguntas = 8;
    }

}

?>
