<?php
class LoginController extends ApplicationController {

    public function initialize() {
        $usuario_pantalla="";
        $ip_equipo = $_SERVER['REMOTE_ADDR'];
        $sql=new ActiveRecordJoin(array("entities"=>array ("Usuario","Pantalla","Userpantalla"),
                        "fields"=>array ("{#Usuario}.username"),
                        "conditions"=>"{#Pantalla}.ip_equipo= '$ip_equipo'"));
        foreach ($sql->getResultSet() as $result) {
            $usuario_pantalla= $result->getUsername();
        }
        Tag::displayTo('usuario', $usuario_pantalla);
    }
    public function indexAction() {
        if(SessionNamespace::exists("datosUsuarioSMC"))
            $this->redirect("template",0);
    }
    public function validarUsuarioAction() {
        $reglas = array(
                "usuario" => array(
                        "filter" => "alpha",
                        "message" => "Por favor ingrese su nombre de usuario"
                )
        );
        if($this->validateRequired($reglas)==true) {
            $username = $this->getPostParam("usuario", "alpha");
            $password = base64_encode($this->getPostParam("password"));
            $usuario = false;
            $sistema = false;
            /*if($username=='admin') {
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
            }else {*/
            $usuario = $this->Usuario->findFirst("username='$username' AND estado='Activo'");
            if($usuario!==false) {
                if($username == $usuario->getUsername()) {
                    if($password == $usuario->getPassword()) {
                        $successAuth = true;
                        $nombreip = $_SERVER['REMOTE_ADDR'];
                        $nombrem =  gethostbyaddr($nombreip);
                        if($nombrem=="localhost") {
                            $nombrem =  PHP_uname('n');
                            $nombreip = gethostbyname($nombrem);
                        }
                        $dataUsuario = SessionNamespace::add('datosUsuarioSMC');
                        $dataUsuario->setId($usuario->getId());
                        $dataUsuario->setNombre($usuario->getNombres());
                        $dataUsuario->setUsername($usuario->getUsername());
                        $dataUsuario->setPassword(base64_decode($usuario->getPassword()));
                        //$dataUsuario->setLogInTime(time());
                        $dataUsuario->setHostname($nombrem);
                        $dataUsuario->setIp($nombreip);
                        $dataUsuario->setEsOperador(0);
                        $dataUsuario->setSesionId("");
                        $dataUsuario->setFechaInicio(date("Y-m-d"));
                        $dataUsuario->setHoraInicio(date("H:i:s"));
                        //$dataUsuario->setUbicacionId("");
                        $fun = new Funciones();
                        //$fun->guardarControlAcceso($dataUsuario);

                        $iduser= $usuario->getId();
                        //Consulto si es un usuario dispensador
                        $usuario_dispensador= new Userdispensador();
                        $es_dispensador = $usuario_dispensador->findFirst("usuario_id=$iduser");
                        //Consulto si es un usuario cajero
                        $usuario_caja= new Usercaja();
                        $es_cajero = $usuario_caja->findFirst("usuario_id=$iduser");
                        //Consulto si es un usuario cajero
                        $usuario_pantalla= new Userpantalla();
                        $es_pantalla = $usuario_pantalla->findFirst("usuario_id=$iduser");
                        //aqui cargar datos de  auditoria
                        if (!empty($es_dispensador)) {
                            $this->redirect("dispensadorservicio",0); //muestra el dispensador de servicios
                        } else if (!empty($es_cajero)) {
                            //$this->redirect("operador/index",0); //muestra la ventana del operador
                            $grupo_usuario= new Grupousuario();
                            $buscaGrupo = $grupo_usuario->findFirst("usuario_id=$iduser");
                            $id_grupo= $buscaGrupo->getGrupoId();
                            if ($id_grupo == 5) //5 para operadores
                                Router::routeTo("controller: operador", "action: index");
                            if ($id_grupo == 7)  //3 cajeros
                                Router::routeTo("controller: cajero", "action: index");

                            //INICIO GUARDAR LA SESION
                            $sesiones = new Sesiones();
                            $sesiones->updateAll("estado='Incompleta'","usuario_id=$iduser AND duracion='00:00:00'");

                            $sesion_id=null;
                            $usuarioId = $iduser;
                            $cajaId = $es_cajero->getCajaId();
                            $estado = "Activo";
                            $ip = $nombreip;
                            $fechaInicio = date("Y-m-d");
                            $horaInicio = date("H:i:s");

                            $caja = new Caja();
                            $buscaCaja= $caja->findFirst("id=$cajaId");
                            $ubicacionId= $buscaCaja->getUbicacionId();
                            //$fechaFin = $this->getPostParam("fecha_fin");
                            //$horaFin = $this->getPostParam("hora_fin");
                            $creacionAt = $this->getPostParam("creacion_at");
                            $sesiones = new Sesiones();
                            $sesiones->setId($sesion_id);
                            $sesiones->setUsuarioId($usuarioId);
                            $sesiones->setCajaId($cajaId);
                            $sesiones->setUbicacionId($ubicacionId);
                            $sesiones->setEstado($estado);
                            $sesiones->setIp($ip);
                            $sesiones->setFechaInicio($fechaInicio);
                            $sesiones->setHoraInicio($horaInicio);
                            $sesiones->setFechaFin("0000-00-00");
                            $sesiones->setHoraFin("00:00:00");
                            $sesiones->setDuracion("00:00:00");
                            $sesiones->setCreacionAt($creacionAt);
                            $sesiones->save();

                            $db = DbBase::rawConnect();
                            $result = $db->query("SELECT id FROM sesiones WHERE usuario_id=$usuarioId AND caja_id=$cajaId ORDER BY id DESC LIMIT 1;");
                            while($row = $db->fetchArray($result)) {
                                $dataUsuario->setSesionId($row['id']);
                            }
                            $dataUsuario->setEsOperador(1);
                            //FIN GUARDAR LA SESI�N
                        } else if (!empty($es_pantalla)) {
                            $nombre_ubicacion = "";
                            $db = DbBase::rawConnect();

                            //$result = $db->query("SELECT nombre_ubicacion FROM usuario u, pantalla p, userpantalla up, ubicacion ub WHERE u.id=up.usuario_id AND p.id=up.pantalla_id AND ub.id=p.ubicacion_id AND u.id= $iduser;");
                            $result = $db->query("SELECT p.id, tipo_pantalla FROM usuario u, pantalla p, userpantalla up WHERE u.id=up.usuario_id AND p.id=up.pantalla_id AND u.id= $iduser;");
                            while($row = $db->fetchArray($result)) {
                                //print $row['servicio_id']."\n";
                                $id_pantalla=$row[0];
                                $tipo_pantalla=$row[1];
                            }

                            //INICIO ACTULIZACION DE ORDEN EN PANTALLA
                            $pantallavideos = new Pantallavideos();
                            $condicion1 ="pantalla_id= $id_pantalla";
                            $condicion2 ="pantalla_id= $id_pantalla AND orden=1";
                            $pantallavideos->updateAll("reproducir=0","conditions: $condicion1");
                            $pantallavideos->updateAll("reproducir=1","conditions: $condicion2");
                            //FIN ACTULIZACION DE ORDEN EN PANTALLA

                            if ($tipo_pantalla == "Pantalla Cajero") {
                                $this->redirect("display/pantalla_sin_ticket",0);
                            }else {
                                //$this->redirect("display/pantalla_con_ticket",0);
                                $this->redirect("display/pantalla",0);
                            }
                        } else {
                            $this->redirect("template",0); //muestra el menu del usuario administrador
                        }
                    }else {
                        $this->addValidationMessage("Password Incorrecto");
                        $this->routeTo("action: index");
                    }
                }else {
                    $this->addValidationMessage("Usuario Incorrecto");
                    $this->routeTo("action: index");
                }
            }else {
                $successAuth = false;
                $this->addValidationMessage("El usuario no existe o esta bloqueado, comuniquese con el administrador");
                $this->routeTo("action: index");
            }
            //}
        }else {
            $this->routeTo("action: index");
        }

    }
    public function salirAction() {

        if(SessionNamespace::exists("datosUsuarioSMC")) {
            $datos = SessionNamespace::get('datosUsuarioSMC');
            $esOperador = $datos->getEsOperador();
            $sesionId = $datos->getSesionId();
            //INICIO TERMINAR LA SESION
            //echo "sesion= ".$esOperador." es op=".$sesionId;
            //$esOperador=0;
            if ($esOperador==1) {
                $fechaInicio= $datos->getFechaInicio();
                $horaInicio= $datos->getHoraInicio();
                $fechaFin = date("Y-m-d");
                $horaFin = date("H:i:s");
                $fun= new Funciones();
                $duracion= $fun->difFecha($fechaInicio." ".$horaInicio, $fechaFin." ".$horaFin);
                $sesiones = new Sesiones();
                //$sesiones->setId($this->sesionId);
                //$sesiones->setFechaFin($fechaFin);
                //$sesiones->setHoraFin($horaFin);
                //$sesiones->save();
                $condicion="id=$sesionId";
                $sesiones->updateAll("fecha_fin='$fechaFin', hora_fin='$horaFin',estado='Inactivo', duracion='$duracion'","conditions: $condicion");
            }
            //FIN TERMINAR LA SESION
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
    public $num_preguntas;
    public $num_calificaciones;
    public $num_modulo;
    public function pantallacalificadorMatrizAction() {
        $this->iniciarPantallaSecundaria(); //llamada a funci�n
        $preguntas= new Preguntas();
        $this->num_preguntas = $preguntas->count("conditions: publicar=1","order: orden");
        if ($this->num_preguntas>5)
            $this->num_preguntas=5;
    }

    public $carpeta;
    public $foto;
    public function iniciarPantallaSecundaria() {
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $idsesion = $dataUsuario->getId();
        $usuario= $dataUsuario->getNombre();

        $sql=new ActiveRecordJoin(array("entities"=>array ("Usuario","Caja","Usercaja"),
                        "fields"=>array ("
                                {#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Caja}.descripcion",
                                "{#Usuario}.foto"),
                        "conditions"=>"{#Usercaja}.usuario_id=$idsesion"));
        foreach ($sql->getResultSet() as $result) {
            $idcaja= $result->getId();
            $numcaja1= $result->getNumeroCaja();
            $foto= $result->getFoto();
            //Tag::displayTo('foto', $result->getFoto());
        }

        $turnoactual=0;
        $turnoespera=0;
        //Tag::displayTo('actual1', $turnoactual);
        //Tag::displayTo('espera1', $turnoespera);
        Tag::displayTo('numcaja', $numcaja1);
        Tag::displayTo('idcaja', $idcaja);
        Tag::displayTo('usuario', $usuario);
        Tag::displayTo('foto', $foto);

        $empresa= new Empresa();
        $buscaEmpresa= $empresa->findFirst("columns: carpeta");
        $this->carpeta= $buscaEmpresa->getCarpeta();
    }
}
?>
