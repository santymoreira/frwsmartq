<?php

/**
 * Controlador Sinccajas
 *
 * @access public
 * @version 1.0
 */
class SinccajasController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * sucursal_id
     *
     * @var int
     */
    public $sucursalId;

    /**
     * base_datos
     *
     * @var string
     */
    public $baseDatos;

    /**
     * caja_id_sucursal
     *
     * @var int
     */
    public $cajaIdSucursal;

    /**
     * usuario_sucursal
     *
     * @var string
     */
    public $usuarioSucursal;

    /**
     * numero_caja_sucursal
     *
     * @var int
     */
    public $numeroCajaSucursal;

    /**
     * area_id_sucursal
     *
     * @var int
     */
    public $areaIdSucursal;

    /**
     * area_nombre_sucursal
     *
     * @var string
     */
    public $areaNombreSucursal;

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
     * hora_fin_atenciom
     *
     */
    public $horaFinAtenciom;

    /**
     * duracion
     *
     */
    public $duracion;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
    }

    /**
     * AcciÃ³n por defecto del controlador/
     *

     *
     *
     */
    public function  sincronizarCajasAction() {
    }
    public function sincronizacionAction() {
        $this->setResponse("json");
        $sucursal_id=$this->getPostParam("sucursal_id");
        $sucursal= new Sucursal();
        $buscaSucursal  =$sucursal->findFirst("id= $sucursal_id");
        $alias_sucursal =$buscaSucursal->getAliasSucursal();
        $ip             =$buscaSucursal->getHost();
        $nombre_bdd     =$buscaSucursal->getNombreBd();
        $user_bdd       =$buscaSucursal->getUsuarioBd();
        $password_bdd   =$buscaSucursal->getPasswordBd();
        $fecha_ultima_sinc  =$this->getPostParam("fecha_".$sucursal_id);
        $hora_ultima_sinc   =$this->getPostParam("hora_".$sucursal_id);

        //INICIO CREAR TABLA TEMPORAL
        $db1 = DbBase::rawConnect();    //db1 es para local
        $db1->query("CREATE TEMPORARY TABLE tmp_colas(id INT NOT NULL AUTO_INCREMENT, sucursal_id INT ,caja_id INT , usuario VARCHAR(50) ,  numero_caja  INT ,area_nombre VARCHAR(100)  ,fecha_inicio_atencion  DATE ,  hora_inicio_atencion  TIME ,  fecha_fin_atencion  DATE ,  hora_fin_atencion  TIME ,  duracion  TIME  , PRIMARY KEY ( id )) ;");
        //Flash::SUCCESS('Creada tabla temporal tmp_turnos');
        //FIN CREAR TABLA  COLAS TEMPORAL

        //INICIO TRANSFERIR DATOS A SER SINCRONIZADOS EN LA TABLA TEMPORAL
        $db = DbLoader::factory('MySQL', array(
                "host"      => $ip,
                "username"  => $user_bdd,
                "password"  => $password_bdd,
                "name"      =>  $nombre_bdd
        ));
        $db->query("SELECT e.alias_empresa,c.id as caja_id, u.nombres AS usuario,c.numero_caja AS numero_caja,fecha_inicio_atencion, hora_inicio_atencion,
                fecha_fin_atencion, hora_fin_atencion, duracion
                FROM colas t, caja c, usuario u, usercaja uc, empresa e
                WHERE u.id=uc.usuario_id AND c.id=uc.caja_id AND c.id=t.caja_id
                AND atendido=1 AND fecha_inicio_atencion>='$fecha_ultima_sinc' AND hora_inicio_atencion>='$hora_ultima_sinc';");
        if (!empty($db)) {
            while($row = $db->fetchArray()) {
//                $idReferencia = $row['id_referencia'];
//                //$nombreSucursal = $row['alias_empresa'];
                $sucursal_id=$sucursal_id;
                $caja_id=$row['caja_id'];
                $usuario = $row['usuario'];
//                $nombreServicio = $row['nombre_servicio'];
                $numeroTurno = $row['numero_caja'];
//                $fechaEmision = $row['fecha_emision'];
                $fechaInicioAtencion = $row['fecha_inicio_atencion'];
                $horaInicioAtencion = $row['hora_inicio_atencion'];
                $fechaFinAtencion = $row['fecha_fin_atencion'];
                $horaFinAtencion = $row['hora_fin_atencion'];
                $duracion = $row['duracion'];
                $db1->query("INSERT INTO tmp_colas (sucursal_id,caja_id, usuario,numero_caja,fecha_inicio_atencion,hora_inicio_atencion,fecha_fin_atencion,hora_fin_atencion,duracion)
                        VALUES ('$sucursal_id','$caja_id','$usuario',$numeroTurno,'$fechaInicioAtencion','$horaInicioAtencion','$fechaFinAtencion','$horaFinAtencion','$duracion')");
            }
        }
        //FIN TRANSFERIR DATOS A SER SINCRONIZADOS EN LA TABLA TEMPORAL
//       echo("Finalizo con exito la guarda ebn un a tabla te3mporal");
        //FIN INSERTAR EN LA TABLA DE LA SINCCAJAS
        //seleciona las cajas que están en la tabla temporal pero solo los que no se repiten en la tabla sinccajas
        $result=$db1->query("SELECT sucursal_id,caja_id,usuario,numero_caja,fecha_inicio_atencion,hora_inicio_atencion,fecha_fin_atencion,hora_fin_atencion,duracion
            FROM tmp_colas WHERE tmp_colas.sucursal_id NOT IN (SELECT sucursal_id FROM sinccajas);");//tmp_colas.id_referencia NOT IN (SELECT id_referencia FROM sincturnos) OR
        if (!empty($db1)) {
            $cont_registros=0;
            while($row = $db1->fetchArray($result)) {
                $cont_registros+=1;
                $id=null;
//                $idReferencia = $row['id_referencia'];
                //$nombreSucursal = $row['nombre_sucursal'];
                $sucursalId=$row['sucursal_id'];
                $baseDatos = $nombre_bdd;
                $usuario = $row['usuario'];
//                $nombreServicio = $row['nombre_servicio'];
                $numeroTurno = $row['numero_caja'];
//                $areaIdSucursal=1;
//                $fechaEmision = $row['fecha_emision'];
//                $horaEmision = $row['hora_emision'];
                $id_caja=$row['caja_id'];
                $fechaInicioAtencion = $row['fecha_inicio_atencion'];
                $horaInicioAtencion = $row['hora_inicio_atencion'];
                $fechaFinAtencion = $row['fecha_fin_atencion'];
                $horaFinAtencion = $row['hora_fin_atencion'];
                $duracion = $row['duracion'];
                $sinccajas = new sinccajas();
                $sinccajas->setId($id);
                $sinccajas->setSucursalId($sucursalId);
                $sinccajas->setBaseDatos($baseDatos);
                $sinccajas ->setCajaIdSucursal($id_caja);
                $sinccajas->setUsuarioSucursal($usuario);
                $sinccajas->setNumeroCajaSucursal($numeroTurno);
                $sinccajas ->setAreaIdSucursal(1);
                $sinccajas ->setAreaNombreSucursal('Area1');
                $sinccajas->setFechaInicioAtencion($fechaInicioAtencion);
                $sinccajas->setHoraInicioAtencion($horaInicioAtencion);
                $sinccajas->setFechaFinAtencion($fechaFinAtencion);
                $sinccajas->setHoraFinAtenciom($horaFinAtencion);
                $sinccajas->setDuracion($duracion);
                if($sinccajas->save()==false) {
                    foreach($sinccajas->getMessages() as $message) {
                        Flash::error($message->getMessage());
                    }
                }
            }
            //Flash::NOTICE($cont_registros." registros sincronizados");
        }
        //FIN INSERTAR EN LA TABLA DE LA SINCCAJAS
//      echo("Empezo a guardar en el historial");
        if (!empty($db) || !empty($query)) {
            //INICIO GUARDAR ESTA SINCRONIZACION
            $fecha_hoy=date("Y-m-d");
            $hora_hoy=date("H:i:s");
            $id_sh=null;
            $fechaSincronizacion = $fecha_hoy;
            $horaSincronizacion = $hora_hoy;
            $sinchistorialCajas = new Sinchistorialcajas();
            $sinchistorialCajas->setId($id_sh);
            $sinchistorialCajas->setSucursalId($sucursal_id);
            $sinchistorialCajas->setFechaSincronizacion($fechaSincronizacion);
            $sinchistorialCajas->setHoraSincronizacion($horaSincronizacion);
            $sinchistorialCajas->setRegistrosSincronizados($cont_registros);
            $sinchistorialCajas->save();
            //FIN GUARDAR ESTA SINCRONIZACION
        }
        /*if (!empty($db))
                Flash::SUCCESS('Sincronizacion Correcta.');
            else
                Flash::ERROR('Sincronizacion Incorrecta.');*/

        //}
//        echo("Termino la sincronizacion");
        $datos= array("turnos_sincronizados"=>$cont_registros);
        return ($datos);
    }
    public function indexAction() {
        $columnsGrid[]=array('name'=>'Sucursal','index'=>'sucursal_id');
        $columnsGrid[]=array('name'=>'Base Datos','index'=>'base_datos');
        $columnsGrid[]=array('name'=>'Caja Sucursal','index'=>'caja_id_sucursal');
        $columnsGrid[]=array('name'=>'Usuario Sucursal','index'=>'usuario_sucursal');
        $columnsGrid[]=array('name'=>'Numero Caja Sucursal','index'=>'numero_caja_sucursal');
        $columnsGrid[]=array('name'=>'Area Sucursal','index'=>'area_id_sucursal');
        $columnsGrid[]=array('name'=>'Area Nombre Sucursal','index'=>'area_nombre_sucursal');
        $columnsGrid[]=array('name'=>'Fecha Inicio Atencion','index'=>'fecha_inicio_atencion');
        $columnsGrid[]=array('name'=>'Hora Inicio Atencion','index'=>'hora_inicio_atencion');
        $columnsGrid[]=array('name'=>'Fecha Fin Atencion','index'=>'fecha_fin_atencion');
        $columnsGrid[]=array('name'=>'Hora Fin Atenciom','index'=>'hora_fin_atenciom');
        $columnsGrid[]=array('name'=>'Duracion','index'=>'duracion');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Sinccajas/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Sinccajas
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $sinccajas = $this->Sinccajas->findFirst($id);
        if($sinccajas) {
            Tag::displayTo('id', $sinccajas->getId());
            Tag::displayTo('sucursal_id', $sinccajas->getSucursalId());
            Tag::displayTo('base_datos', $sinccajas->getBaseDatos());
            Tag::displayTo('caja_id_sucursal', $sinccajas->getCajaIdSucursal());
            Tag::displayTo('usuario_sucursal', $sinccajas->getUsuarioSucursal());
            Tag::displayTo('numero_caja_sucursal', $sinccajas->getNumeroCajaSucursal());
            Tag::displayTo('area_id_sucursal', $sinccajas->getAreaIdSucursal());
            Tag::displayTo('area_nombre_sucursal', $sinccajas->getAreaNombreSucursal());
            Tag::displayTo('fecha_inicio_atencion', $sinccajas->getFechaInicioAtencion());
            Tag::displayTo('hora_inicio_atencion', $sinccajas->getHoraInicioAtencion());
            Tag::displayTo('fecha_fin_atencion', $sinccajas->getFechaFinAtencion());
            Tag::displayTo('hora_fin_atenciom', $sinccajas->getHoraFinAtenciom());
            Tag::displayTo('duracion', $sinccajas->getDuracion());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Sinccajas
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $sucursalId = $this->getPostParam("sucursal_id", "int");
        $baseDatos = $this->getPostParam("base_datos", "striptags", "extraspaces");
        $cajaIdSucursal = $this->getPostParam("caja_id_sucursal", "int");
        $usuarioSucursal = $this->getPostParam("usuario_sucursal", "striptags", "extraspaces");
        $numeroCajaSucursal = $this->getPostParam("numero_caja_sucursal", "int");
        $areaIdSucursal = $this->getPostParam("area_id_sucursal", "int");
        $areaNombreSucursal = $this->getPostParam("area_nombre_sucursal", "striptags", "extraspaces");
        $fechaInicioAtencion = $this->getPostParam("fecha_inicio_atencion");
        $horaInicioAtencion = $this->getPostParam("hora_inicio_atencion");
        $fechaFinAtencion = $this->getPostParam("fecha_fin_atencion");
        $horaFinAtenciom = $this->getPostParam("hora_fin_atenciom");
        $duracion = $this->getPostParam("duracion");
        $sinccajas = new Sinccajas();
        $sinccajas->setId($id);
        $sinccajas->setSucursalId($sucursalId);
        $sinccajas->setBaseDatos($baseDatos);
        $sinccajas->setCajaIdSucursal($cajaIdSucursal);
        $sinccajas->setUsuarioSucursal($usuarioSucursal);
        $sinccajas->setNumeroCajaSucursal($numeroCajaSucursal);
        $sinccajas->setAreaIdSucursal($areaIdSucursal);
        $sinccajas->setAreaNombreSucursal($areaNombreSucursal);
        $sinccajas->setFechaInicioAtencion($fechaInicioAtencion);
        $sinccajas->setHoraInicioAtencion($horaInicioAtencion);
        $sinccajas->setFechaFinAtencion($fechaFinAtencion);
        $sinccajas->setHoraFinAtenciom($horaFinAtenciom);
        $sinccajas->setDuracion($duracion);
        if($sinccajas->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            Flash::success('Registro guardado con Ã©xito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Sinccajas
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Sinccajas->delete($ids[$i])) {
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
                    case 'sucursal_id':
                        $condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal',$abrevoper,$strbusqueda);
                        $Sucursal=$this->Sucursal->find($condSucursal);
                        if(count($Sucursal)>0) {
                            $arrayIdsSucursal=array();
                            foreach($Sucursal as $fila) {
                                $arrayIdsSucursal[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsSucursal);
                            $abrevoper='in';
                        }
                        break;
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
                            case 'sucursal_id':
                                $condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal',$val['op'],$val['data']);
                                $Sucursal=$this->Sucursal->find($condSucursal);
                                if(count($Sucursal)>0) {
                                    $arrayIdsSucursal=array();
                                    foreach($Sucursal as $fila) {
                                        $arrayIdsSucursal[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsSucursal);
                                    $val['op']='in';
                                }
                                break;
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
                    case 'sucursal_id':
                        $condSucursal=Utils::toSqlParamSearchGrid('alias_sucursal','bw',$v);
                        $Sucursal=$this->Sucursal->find($condSucursal);
                        if(count($Sucursal)>0) {
                            $arrayIdsSucursal=array();
                            foreach($Sucursal as $fila) {
                                $arrayIdsSucursal[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsSucursal);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'base_datos':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'caja_id_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'usuario_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'numero_caja_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'area_id_sucursal':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'area_nombre_sucursal':
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
                    case 'hora_fin_atenciom':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'duracion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Sinccajas->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Sinccajas->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Sinccajas=$resultado[$i];
            $Sucursal=$Sinccajas->getSucursal();
            $jqgrid->rows[]=array('id'=>$Sinccajas->getId(),'cell'=>array($Sucursal->getAlias_sucursal(),$Sinccajas->getBaseDatos(),$Sinccajas->getCajaIdSucursal(),$Sinccajas->getUsuarioSucursal(),$Sinccajas->getNumeroCajaSucursal(),$Sinccajas->getAreaIdSucursal(),$Sinccajas->getAreaNombreSucursal(),$Sinccajas->getFechaInicioAtencion(),$Sinccajas->getHoraInicioAtencion(),$Sinccajas->getFechaFinAtencion(),$Sinccajas->getHoraFinAtenciom(),$Sinccajas->getDuracion()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

