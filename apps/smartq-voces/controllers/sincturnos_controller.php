<?php

/**
 * Controlador Sincturnos
 *
 * @access public
 * @version 1.0
 */
class SincturnosController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * id_referencia
     *
     * @var int
     */
    public $idReferencia;

    /**
     * nombre_sucursal
     *
     * @var string
     */
    public $nombreSucursal;

    /**
     * base_datos
     *
     * @var string
     */
    public $baseDatos;

    /**
     * usuario
     *
     * @var string
     */
    public $usuario;

    /**
     * nombre_servicio
     *
     * @var string
     */
    public $nombreServicio;

    /**
     * numero_turno
     *
     * @var string
     */
    public $numeroTurno;

    /**
     * fecha_emision
     *
     */
    public $fechaEmision;

    /**
     * hora_emision
     *
     */
    public $horaEmision;

    /**
     * fecha_inicio_atencion
     *
     */
    public $fechaInicioAtencion;

    /**
     * hora_inicio_atencion
     *
     */
    public $horaInicioAtencion;

    /**
     * fecha_fin_atencion
     *
     */
    public $fechaFinAtencion;

    /**
     * hora_fin_atencion
     *
     */
    public $horaFinAtencion;

    /**
     * duracion
     *
     */
    public $duracion;

    /**
     * calificacion
     *
     * @var string
     */
    public $calificacion;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

    /**
     * Inicializador del controlador/
     *
     */

    public $lista_sucursales= array();

    public function initialize() {
        $this->setPersistance(true);
        /*$sucursal   =new Sucursal();
        $buscaSucursal = $sucursal->find("order: alias_sucursal");
        foreach($buscaSucursal as $result) {
            $this->lista_sucursales[$result->getId()]=$result->getAliasSucursal();
        }*/
    }

    /*
         * Para el menú de hacer la sincronización
    */
    public function sincronizarAction() {
        //$this->setPersistance(true);
    }

    /*
         * Función que realiza la sincronización
    */
    public function sincronizacionAction() {
        $this->setResponse("json");

        //INSERT INTO sincturnos (id_referencia, nombre_sucursal, usuario, nombre_servicio, numero_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion, fecha_fin_atencion, hora_fin_atencion, duracion, calificacion)
        //SELECT t.id AS id_referencia, alias_empresa, u.nombres AS usuario, s.nombre AS nombre_servicio,
        //numero AS numesistemaro_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion,
        //fecha_fin_atencion, hora_fin_atencion, duracion, calificacion
        //FROM servicio s, turnos t, caja c, usuario u, usercaja uc, empresa e
        //WHERE u.id=uc.usuario_id AND c.id=uc.caja_id AND s.id=t.servicio_id AND c.id=t.caja_id
        //AND (t.id NOT IN (SELECT id_referencia FROM sincturnos) OR e.alias_empresa NOT IN (SELECT nombre_sucursal FROM sincturnos));

        $sucursal_id=$this->getPostParam("sucursal_id");

        //$chk_sucursales=$this->getPostParam("chk_sucursales");
        $sucursal= new Sucursal();
        //foreach ($chk_sucursales as $key) {
            $buscaSucursal  =$sucursal->findFirst("id= $sucursal_id");
            //$sucursal_id    =$buscaSucursal->getId();
            $alias_sucursal =$buscaSucursal->getAliasSucursal();
            $ip             =$buscaSucursal->getHost();
            $nombre_bdd     =$buscaSucursal->getNombreBd();
            $user_bdd       =$buscaSucursal->getUsuarioBd();
            $password_bdd   =$buscaSucursal->getPasswordBd();

            $fecha_ultima_sinc  =$this->getPostParam("fecha_".$sucursal_id);
            $hora_ultima_sinc   =$this->getPostParam("hora_".$sucursal_id);

            //INICIO CREAR TABLA TEMPORAL
            $db1 = DbBase::rawConnect();    //db1 es para local
            //$db1->query("CREATE TEMPORARY TABLE tmp_turnos (id INT NOT NULL AUTO_INCREMENT , dato VARCHAR(50) , sucursal VARCHAR(50) , PRIMARY KEY (id));");
            //$db1->query("CREATE TEMPORARY TABLE tmp_turnos(id INT NOT NULL AUTO_INCREMENT , id_referencia INT , nombre_sucursal VARCHAR(50) , usuario VARCHAR(50) , nombre_servicio  VARCHAR(50) ,  numero_turno  VARCHAR(5) ,  fecha_emision  DATE ,  hora_emision  TIME ,  fecha_inicio_atencion  DATE ,  hora_inicio_atencion  TIME ,  fecha_fin_atencion  DATE ,  hora_fin_atencion  TIME ,  duracion  TIME ,  calificacion  VARCHAR(20) ,  creacion_at  DATE , PRIMARY KEY ( id )) ;");
            $db1->query("CREATE TEMPORARY TABLE tmp_turnos(id INT NOT NULL AUTO_INCREMENT , id_referencia INT , sucursal_id INT , usuario VARCHAR(50) , nombre_servicio  VARCHAR(50) ,  numero_turno  VARCHAR(5) ,  fecha_emision  DATE ,  hora_emision  TIME ,  fecha_inicio_atencion  DATE ,  hora_inicio_atencion  TIME ,  fecha_fin_atencion  DATE ,  hora_fin_atencion  TIME ,  duracion  TIME ,  calificacion  VARCHAR(20) , PRIMARY KEY ( id )) ;");
            //Flash::SUCCESS('Creada tabla temporal tmp_turnos');
            //FIN CREAR TABLA TEMPORAL

            //INICIO TRANSFERIR DATOS A SER SINCRONIZADOS EN LA TABLA TEMPORAL
            $db = DbLoader::factory('MySQL', array(
                    "host"      => $ip,
                    "username"  => $user_bdd,
                    "password"  => $password_bdd,
                    "name"      =>  $nombre_bdd
            ));
            $db->query("SELECT t.id AS id_referencia, alias_empresa, u.nombres AS usuario, s.nombre AS nombre_servicio,
                numero AS numero_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion,
                fecha_fin_atencion, hora_fin_atencion, duracion, calificacion
                FROM servicio s, turnos t, caja c, usuario u, usercaja uc, empresa e
                WHERE u.id=uc.usuario_id AND c.id=uc.caja_id AND s.id=t.servicio_id AND c.id=t.caja_id
                AND atendido=1 AND fecha_emision>='$fecha_ultima_sinc' and hora_emision>='$hora_ultima_sinc';");
            if (!empty($db)) {
                while($row = $db->fetchArray()) {
                    $idReferencia = $row['id_referencia'];
                    //$nombreSucursal = $row['alias_empresa'];
                    $sucursal_id=$sucursal_id;
                    $usuario = $row['usuario'];
                    $nombreServicio = $row['nombre_servicio'];
                    $numeroTurno = $row['numero_turno'];
                    $fechaEmision = $row['fecha_emision'];
                    $horaEmision = $row['hora_emision'];
                    $fechaInicioAtencion = $row['fecha_inicio_atencion'];
                    $horaInicioAtencion = $row['hora_inicio_atencion'];
                    $fechaFinAtencion = $row['fecha_fin_atencion'];
                    $horaFinAtencion = $row['hora_fin_atencion'];
                    $duracion = $row['duracion'];
                    $calificacion = $row['calificacion'];
                    $creacionAt = $this->getPostParam("creacion_at");
                    $db1->query("INSERT INTO tmp_turnos (id_referencia, sucursal_id, usuario,nombre_servicio,numero_turno,fecha_emision,hora_emision,fecha_inicio_atencion,hora_inicio_atencion,fecha_fin_atencion,hora_fin_atencion,duracion,calificacion)
                        VALUES ('$idReferencia','$sucursal_id','$usuario','$nombreServicio',$numeroTurno,'$fechaEmision','$horaEmision','$fechaInicioAtencion','$horaInicioAtencion','$fechaFinAtencion','$horaFinAtencion','$duracion','$calificacion')");
                }
            }
            //FIN TRANSFERIR DATOS A SER SINCRONIZADOS EN LA TABLA TEMPORAL

            //FIN INSERTAR EN LA TABLA DE LA SINCTURNOS
            //seleciona los turnos que están en la tabla temporal pero solo los que no se repiten en la tabla sincturnos
            $result=$db1->query("SELECT id_referencia, sucursal_id, usuario,nombre_servicio,numero_turno,fecha_emision,hora_emision,fecha_inicio_atencion,hora_inicio_atencion,fecha_fin_atencion,hora_fin_atencion,duracion,calificacion
            FROM tmp_turnos WHERE tmp_turnos.id_referencia NOT IN (SELECT id_referencia FROM sincturnos) OR tmp_turnos.sucursal_id NOT IN (SELECT sucursal_id FROM sincturnos);");
            if (!empty($db1)) {
                $cont_registros=0;
                while($row = $db1->fetchArray($result)) {
                    $cont_registros+=1;
                    $id=null;
                    $idReferencia = $row['id_referencia'];
                    //$nombreSucursal = $row['nombre_sucursal'];
                    $sucursalId=$row['sucursal_id'];
                    $baseDatos = $nombre_bdd;
                    $usuario = $row['usuario'];
                    $nombreServicio = $row['nombre_servicio'];
                    $numeroTurno = $row['numero_turno'];
                    $fechaEmision = $row['fecha_emision'];
                    $horaEmision = $row['hora_emision'];
                    $fechaInicioAtencion = $row['fecha_inicio_atencion'];
                    $horaInicioAtencion = $row['hora_inicio_atencion'];
                    $fechaFinAtencion = $row['fecha_fin_atencion'];
                    $horaFinAtencion = $row['hora_fin_atencion'];
                    $duracion = $row['duracion'];
                    $calificacion = $row['calificacion'];
                    $creacionAt = $this->getPostParam("creacion_at");
                    $sincturnos = new Sincturnos();
                    $sincturnos->setId($id);
                    $sincturnos->setIdReferencia($idReferencia);
                    $sincturnos->setSucursalId($sucursalId);
                    $sincturnos->setBaseDatos($baseDatos);
                    $sincturnos->setUsuario($usuario);
                    $sincturnos->setNombreServicio($nombreServicio);
                    $sincturnos->setNumeroTurno($numeroTurno);
                    $sincturnos->setFechaEmision($fechaEmision);
                    $sincturnos->setHoraEmision($horaEmision);
                    $sincturnos->setFechaInicioAtencion($fechaInicioAtencion);
                    $sincturnos->setHoraInicioAtencion($horaInicioAtencion);
                    $sincturnos->setFechaFinAtencion($fechaFinAtencion);
                    $sincturnos->setHoraFinAtencion($horaFinAtencion);
                    $sincturnos->setDuracion($duracion);
                    $sincturnos->setCalificacion($calificacion);
                    $sincturnos->setCreacionAt($creacionAt);
                    $sincturnos->save();
                }
                //Flash::NOTICE($cont_registros." registros sincronizados");
            }
            //FIN INSERTAR EN LA TABLA DE LA SINCTURNOS

            if (!empty($db) || !empty($query)) {
                //INICIO GUARDAR ESTA SINCRONIZACION
                $fecha_hoy=date("Y-m-d");
                $hora_hoy=date("H:i:s");
                $id_sh=null;
                $fechaSincronizacion = $fecha_hoy;
                $horaSincronizacion = $hora_hoy;
                $sinchistorial = new Sinchistorial();
                $sinchistorial->setId($id_sh);
                $sinchistorial->setSucursalId($sucursal_id);
                $sinchistorial->setFechaSincronizacion($fechaSincronizacion);
                $sinchistorial->setHoraSincronizacion($horaSincronizacion);
                $sinchistorial->setRegistrosSincronizados($cont_registros);
                $sinchistorial->save();
                //FIN GUARDAR ESTA SINCRONIZACION
            }
            /*if (!empty($db))
                Flash::SUCCESS('Sincronizacion Correcta.');
            else
                Flash::ERROR('Sincronizacion Incorrecta.');*/

        //}
            $datos= array("turnos_sincronizados"=>$cont_registros);
            return ($datos);
    }

    /*
         * Función que realiza la sincronización respaldo de la sincronzacion
    */
    public function sincronizacion1Action() {
        //INSERT INTO sincturnos (id_referencia, nombre_sucursal, usuario, nombre_servicio, numero_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion, fecha_fin_atencion, hora_fin_atencion, duracion, calificacion)
        //SELECT t.id AS id_referencia, alias_empresa, u.nombres AS usuario, s.nombre AS nombre_servicio,
        //numero AS numesistemaro_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion,
        //fecha_fin_atencion, hora_fin_atencion, duracion, calificacion
        //FROM servicio s, turnos t, caja c, usuario u, usercaja uc, empresa e
        //WHERE u.id=uc.usuario_id AND c.id=uc.caja_id AND s.id=t.servicio_id AND c.id=t.caja_id
        //AND (t.id NOT IN (SELECT id_referencia FROM sincturnos) OR e.alias_empresa NOT IN (SELECT nombre_sucursal FROM sincturnos));
        $sucursal="A1";

        $ip= $this->getPostParam('ip');
        $nombre_bdd= $this->getPostParam('nombre_bdd');
        $user_bdd= $this->getPostParam('user_bdd');
        $password_bdd= $this->getPostParam('password_bdd');

        //INICIO VER LA ULTIMA SINCRONIZACION
        $f_ultima_sinc='2010-01-01';
        $h_ultima_sinc='00:00:00';
        $sinchistorial= new Sinchistorial();
        $buscaSinchistorial= $sinchistorial->findFirst('order: id DESC');
        if (!empty($buscaSinchistorial)) {
            $f_ultima_sinc=$buscaSinchistorial->getFechaSincronizacion();
            $h_ultima_sinc=$buscaSinchistorial->getHoraSincronizacion();
            //echo $f_ultima_sinc." ".$h_ultima_sinc; die();
        }
        //FIN VER LA ULTIMA SINCRONIZACION

        /*$db = new Db();
        $db->connect("192.168.0.251", "root", "", "dbcolas_cca1");
        $result = $db->query("select t.id, s.nombre, u.nombres, numero, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion, fecha_fin_atencion, hora_fin_atencion, duracion, calificacion from servicio s, turnos t, caja c, usuario u, usercaja uc where u.id=uc.usuario_id and c.id=uc.caja_id and s.id=t.servicio_id and c.id=t.caja_id;");
        while($row = $db->fetchArray($result)) {
            print $row['t.id']."\n";
        }*/

        //INICIO SINCRONIZAR LOS DATOS DEL EQUIPO REMOTO
        $db = DbLoader::factory('MySQL', array(
                "host" => $ip,
                "username" => $user_bdd,
                "password" => $password_bdd,
                "name" =>  $nombre_bdd
        ));
        $db->query("select t.id as id_referencia, u.nombres as usuario, s.nombre as nombre_servicio,
numero as numero_turno, fecha_emision, hora_emision, fecha_inicio_atencion, hora_inicio_atencion,
fecha_fin_atencion, hora_fin_atencion, duracion, calificacion
from servicio s, turnos t, caja c, usuario u, usercaja uc
where u.id=uc.usuario_id and c.id=uc.caja_id and s.id=t.servicio_id and c.id=t.caja_id
AND fecha_emision>='$f_ultima_sinc' ;");
        if (!empty($db)) {
            while($row = $db->fetchArray()) {
                //print $row['id_referencia']."\n";
                $id=null;
                $idReferencia = $row['id_referencia'];
                $nombreSucursal = $sucursal;
                $baseDatos = $nombre_bdd;
                $usuario = $row['usuario'];
                $nombreServicio = $row['nombre_servicio'];
                $numeroTurno = $row['numero_turno'];
                $fechaEmision = $row['fecha_emision'];
                $horaEmision = $row['hora_emision'];
                $fechaInicioAtencion = $row['fecha_inicio_atencion'];
                $horaInicioAtencion = $row['hora_inicio_atencion'];
                $fechaFinAtencion = $row['fecha_fin_atencion'];
                $horaFinAtencion = $row['hora_fin_atencion'];
                $duracion = $row['duracion'];
                $calificacion = $row['calificacion'];
                $creacionAt = $this->getPostParam("creacion_at");
                $sincturnos = new Sincturnos();
                $sincturnos->setId($id);
                $sincturnos->setIdReferencia($idReferencia);
                $sincturnos->setNombreSucursal($nombreSucursal);
                $sincturnos->setBaseDatos($baseDatos);
                $sincturnos->setUsuario($usuario);
                $sincturnos->setNombreServicio($nombreServicio);
                $sincturnos->setNumeroTurno($numeroTurno);
                $sincturnos->setFechaEmision($fechaEmision);
                $sincturnos->setHoraEmision($horaEmision);
                $sincturnos->setFechaInicioAtencion($fechaInicioAtencion);
                $sincturnos->setHoraInicioAtencion($horaInicioAtencion);
                $sincturnos->setFechaFinAtencion($fechaFinAtencion);
                $sincturnos->setHoraFinAtencion($horaFinAtencion);
                $sincturnos->setDuracion($duracion);
                $sincturnos->setCalificacion($calificacion);
                $sincturnos->setCreacionAt($creacionAt);
                $sincturnos->save();
            }
        }
        //FIN SINCRONIZAR LOS DATOS DEL EQUIPO REMOTO

        //INICIO SINCRONIZAR LOS DATOS LOCALES
        $condicion="fecha_emision>='$f_ultima_sinc' AND hora_fin_atencion>'$h_ultima_sinc'" ;
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Caja", "Turnos", "Servicio", "Usercaja", "Usuario"),
                        "fields" => array(
                                "{#Turnos}.id",
                                "{#Usuario}.nombres",
                                "{#Servicio}.nombre",
                                "{#Turnos}.numero",
                                "{#Turnos}.fecha_emision",
                                "{#Turnos}.hora_emision",
                                "{#Turnos}.fecha_inicio_atencion",
                                "{#Turnos}.hora_inicio_atencion",
                                "{#Turnos}.fecha_fin_atencion",
                                "{#Turnos}.hora_fin_atencion",
                                "{#Turnos}.duracion",
                                "{#Turnos}.calificacion"),
                        "conditions" => $condicion
                //"order"=>"{#Turnos}.hora_inicio_atencion"
        ));
        if (!empty($query)) {
            $id=null;
            foreach($query->getResultSet() as $result) {
                $idReferencia = $result->getId();
                $nombreSucursal = $sucursal;
                $baseDatos = $nombre_bdd;
                $usuario = $result->getNombres();
                $nombreServicio = $result->getNombre();
                $numeroTurno = $result->getNumero();
                $fechaEmision = $result->getFechaEmision();
                $horaEmision = $result->getHoraEmision();
                $fechaInicioAtencion = $result->getFechaInicioAtencion();
                $horaInicioAtencion = $result->getHoraInicioAtencion();
                $fechaFinAtencion = $result->getFechaFinAtencion();
                $horaFinAtencion = $result->getHoraFinAtencion();
                $duracion = $result->getDuracion();
                $calificacion = $result->getCalificacion();
                //$creacionAt = $this->getPostParam("creacion_at");
                $sincturnos = new Sincturnos();
                $sincturnos->setId($id);
                $sincturnos->setIdReferencia($idReferencia);
                $sincturnos->setNombreSucursal($nombreSucursal);
                $sincturnos->setBaseDatos($baseDatos);
                $sincturnos->setUsuario($usuario);
                $sincturnos->setNombreServicio($nombreServicio);
                $sincturnos->setNumeroTurno($numeroTurno);
                $sincturnos->setFechaEmision($fechaEmision);
                $sincturnos->setHoraEmision($horaEmision);
                $sincturnos->setFechaInicioAtencion($fechaInicioAtencion);
                $sincturnos->setHoraInicioAtencion($horaInicioAtencion);
                $sincturnos->setFechaFinAtencion($fechaFinAtencion);
                $sincturnos->setHoraFinAtencion($horaFinAtencion);
                $sincturnos->setDuracion($duracion);
                $sincturnos->setCalificacion($calificacion);
                $sincturnos->setCreacionAt($creacionAt);
                $sincturnos->save();
            }
        }
        //FIN SINCRONIZAR LOS DATOS LOCALES
        if (!empty($db) || !empty($query)) {
            //INICIO GUARDAR ESTA SINCRONIZACION
            $fecha_hoy=date("Y-m-d");
            $hora_hoy=date("H:i:s");
            $id_sh=null;
            $fechaSincronizacion = $fecha_hoy;
            $horaSincronizacion = $hora_hoy;
            $sinchistorial = new Sinchistorial();
            $sinchistorial->setId($id_sh);
            $sinchistorial->setFechaSincronizacion($fechaSincronizacion);
            $sinchistorial->setHoraSincronizacion($horaSincronizacion);
            $sinchistorial->save();
            //FIN GUARDAR ESTA SINCRONIZACION
        }
        if (!empty($db))
            Flash::SUCCESS('Sincronizacion Correcta.');
        else
            Flash::ERROR('Sincronizacion Incorrecta.');


        ///VARIOS
        //$result=$db1->query("SELECT * FROM tmp_turnos;");
            //$db1->fetchOne($result);
            //        $res=mysql_query($sql);
            //        $row=mysql_fetch_array($res);

            //        $dia_respaldado=$row["fecha_de_ayer"];
            //        $dia_respaldado=strftime("%Y-%m-%d",strtotime($dia_respaldado));

            //        $FileName="nelson.sql";
            //        $backupRoute="c:/";
            //
            //        $localhost="localhost";
            //        $user="root";
            //        $passwd="";
            //        $db="dbsmartq";
            //
            //        $command = "mysqldump --opt --host='$localhost' --user='$user' --pass='$passwd' '$db' > ".$backupRoute.$FileName;
            //        exec($command);


            //        $ruta = "C:/backup";
            //        echo "<br>Destino del respaldo: <br>$ruta<br>";
            //        echo "Bloqueando tablas...<br>";
            //        $db1->query("lock tables turnos write");
            //        echo "Realizando respaldo....<br>";
            //
            //        $db1->query("BACKUP TABLE turnos TO '$ruta' ");
            //
            //        echo "Desbloqueando tablas...<br>";
            //        //mysql_query("UNLOCK TABLES",$link) or die(mysql_error());
            //
            //        echo "<font color ='#FF0000'><b>---- Respaldo concluido. Verifique la creación de los archivos. ----</b></font>";
            //
            //        die();

            //$db = DbBase::rawConnect();
            //                        $result = $db1->query("SELECT * FROM tmp_turnos");
            //                        while($row = $db1->fetchArray($result)) {
            //                            echo $row[1]."<br>";
            //                        }
    }

    /**
     * AcciÃ³n por defecto del controlador/
     *
     */
    public function indexAction() {
        $columnsGrid[]=array('name'=>'Id Referencia','index'=>'id_referencia');
        $columnsGrid[]=array('name'=>'Nombre Sucursal','index'=>'nombre_sucursal');
        $columnsGrid[]=array('name'=>'Base Datos','index'=>'base_datos');
        $columnsGrid[]=array('name'=>'Usuario','index'=>'usuario');
        $columnsGrid[]=array('name'=>'Nombre Servicio','index'=>'nombre_servicio');
        $columnsGrid[]=array('name'=>'Numero Turno','index'=>'numero_turno');
        $columnsGrid[]=array('name'=>'Fecha Emision','index'=>'fecha_emision');
        $columnsGrid[]=array('name'=>'Hora Emision','index'=>'hora_emision');
        $columnsGrid[]=array('name'=>'Fecha Inicio Atencion','index'=>'fecha_inicio_atencion');
        $columnsGrid[]=array('name'=>'Hora Inicio Atencion','index'=>'hora_inicio_atencion');
        $columnsGrid[]=array('name'=>'Fecha Fin Atencion','index'=>'fecha_fin_atencion');
        $columnsGrid[]=array('name'=>'Hora Fin Atencion','index'=>'hora_fin_atencion');
        $columnsGrid[]=array('name'=>'Duracion','index'=>'duracion');
        $columnsGrid[]=array('name'=>'Calificacion','index'=>'calificacion');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Sincturnos/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Sincturnos
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $sincturnos = $this->Sincturnos->findFirst($id);
        if($sincturnos) {
            Tag::displayTo('id', $sincturnos->getId());
            Tag::displayTo('id_referencia', $sincturnos->getIdReferencia());
            Tag::displayTo('nombre_sucursal', $sincturnos->getNombreSucursal());
            Tag::displayTo('base_datos', $sincturnos->getBaseDatos());
            Tag::displayTo('usuario', $sincturnos->getUsuario());
            Tag::displayTo('nombre_servicio', $sincturnos->getNombreServicio());
            Tag::displayTo('numero_turno', $sincturnos->getNumeroTurno());
            Tag::displayTo('fecha_emision', $sincturnos->getFechaEmision());
            Tag::displayTo('hora_emision', $sincturnos->getHoraEmision());
            Tag::displayTo('fecha_inicio_atencion', $sincturnos->getFechaInicioAtencion());
            Tag::displayTo('hora_inicio_atencion', $sincturnos->getHoraInicioAtencion());
            Tag::displayTo('fecha_fin_atencion', $sincturnos->getFechaFinAtencion());
            Tag::displayTo('hora_fin_atencion', $sincturnos->getHoraFinAtencion());
            Tag::displayTo('duracion', $sincturnos->getDuracion());
            Tag::displayTo('calificacion', $sincturnos->getCalificacion());
            Tag::displayTo('creacion_at', $sincturnos->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Sincturnos
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $idReferencia = $this->getPostParam("id_referencia", "int");
        $nombreSucursal = $this->getPostParam("nombre_sucursal", "striptags", "extraspaces");
        $baseDatos = $this->getPostParam("base_datos", "striptags", "extraspaces");
        $usuario = $this->getPostParam("usuario", "striptags", "extraspaces");
        $nombreServicio = $this->getPostParam("nombre_servicio", "striptags", "extraspaces");
        $numeroTurno = $this->getPostParam("numero_turno", "striptags", "extraspaces");
        $fechaEmision = $this->getPostParam("fecha_emision");
        $horaEmision = $this->getPostParam("hora_emision");
        $fechaInicioAtencion = $this->getPostParam("fecha_inicio_atencion");
        $horaInicioAtencion = $this->getPostParam("hora_inicio_atencion");
        $fechaFinAtencion = $this->getPostParam("fecha_fin_atencion");
        $horaFinAtencion = $this->getPostParam("hora_fin_atencion");
        $duracion = $this->getPostParam("duracion");
        $calificacion = $this->getPostParam("calificacion", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");
        $sincturnos = new Sincturnos();
        $sincturnos->setId($id);
        $sincturnos->setIdReferencia($idReferencia);
        $sincturnos->setNombreSucursal($nombreSucursal);
        $sincturnos->setBaseDatos($baseDatos);
        $sincturnos->setUsuario($usuario);
        $sincturnos->setNombreServicio($nombreServicio);
        $sincturnos->setNumeroTurno($numeroTurno);
        $sincturnos->setFechaEmision($fechaEmision);
        $sincturnos->setHoraEmision($horaEmision);
        $sincturnos->setFechaInicioAtencion($fechaInicioAtencion);
        $sincturnos->setHoraInicioAtencion($horaInicioAtencion);
        $sincturnos->setFechaFinAtencion($fechaFinAtencion);
        $sincturnos->setHoraFinAtencion($horaFinAtencion);
        $sincturnos->setDuracion($duracion);
        $sincturnos->setCalificacion($calificacion);
        $sincturnos->setCreacionAt($creacionAt);
        if($sincturnos->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            Flash::success('Registro guardado con Ã©xito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Sincturnos
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Sincturnos->delete($ids[$i])) {
                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
                }else {
                    $msg.="El registro $ids[$i] fue eliminado correctamente.";
                }
            }
        }
        echo $msg;
    }
    public function obtenerDatosGridAction() {
        $this->setResponse('ajax');  // asignamos el tipo de respuesta para esta accion
        $pagina = $this->getPostParam('page'); // obtener el numero de pagina
        $limite = $this->getPostParam('rows'); // obtener el número de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de índice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la dirección de ordenado
        if(!$col_orden) $col_orden =1;
        //construccion de condicion de consulta
        $condicion='1';
        $buscar=$this->getPostParam('_search','stripslashes'); //obtenemos la variable que activa la busqueda
        $strbusqueda=$this->getPostParam('searchString');  // obtenemos la cadena de busqueda
        $campoBusqueda=$this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda
        $abrevoper=$this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda
        $filtroBusqueda=$this->getPostParam('filters','stripslashes');
        if($buscar=='true') { //verificamos si la busqueda es activada
            if($strbusqueda!='') {    // construccion de la cadena de condicion para la busqueda normal
                switch($campoBusqueda) {
                }
                $condicion.=' AND '.Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
            }elseif($filtroBusqueda) {
                $jsona = json_decode($filtroBusqueda,true);
                if(is_array($jsona)) {
                    $gopr = $jsona['groupOp'];
                    $rules = $jsona['rules'];
                    $i =0;
                    foreach($rules as $key=>$val) {
                        $field = $val['field'];
                        switch($field) {
                        }
                        $op = $val['op'];
                        $v = $val['data'];
                        if($v && $op) {
                            $i++;
                            $v=Utils::toSqlParamSearchGrid($field, $op, $v);
                            if ($i == 1)
                                $condicion.=' AND ';
                            else
                                $condicion.= " " .$gopr." ";
                            $condicion.= $v;
                        }
                    }
                }
            }
            //construimos la condicion por barra de busqueda del grid
            $sarr = $_POST;
            foreach( $sarr as $k=>$v) {
                switch ($k) {
                    case 'id_referencia':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'nombre_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'base_datos':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'usuario':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'nombre_servicio':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'numero_turno':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fecha_emision':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_emision':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fecha_inicio_atencion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_inicio_atencion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'fecha_fin_atencion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'hora_fin_atencion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'duracion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'calificacion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Sincturnos->count("conditions: $condicion");  //contar el numero total de registros existentes
        //obtenemos el numero de paginas para el grid
        if( $contar >0 ) {
            $total_pags = ceil($contar/$limite);
        } else {
            $total_pags = 0;
        }
        if ($pagina > $total_pags) $pagina=$total_pags;
        $inicio = $limite*$pagina - $limite; // no poner $limite*($pagina - 1)
        if ($inicio<0) $inicio = 0;
        $limite=$inicio+$limite;  // igualamos el limite al total de registros que se obtendra hasta la pagina actual
        $resultado=$this->Sincturnos->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Sincturnos=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Sincturnos->getId(),'cell'=>array($Sincturnos->getIdReferencia(),$Sincturnos->getNombreSucursal(),$Sincturnos->getBaseDatos(),$Sincturnos->getUsuario(),$Sincturnos->getNombreServicio(),$Sincturnos->getNumeroTurno(),$Sincturnos->getFechaEmision(),$Sincturnos->getHoraEmision(),$Sincturnos->getFechaInicioAtencion(),$Sincturnos->getHoraInicioAtencion(),$Sincturnos->getFechaFinAtencion(),$Sincturnos->getHoraFinAtencion(),$Sincturnos->getDuracion(),$Sincturnos->getCalificacion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

