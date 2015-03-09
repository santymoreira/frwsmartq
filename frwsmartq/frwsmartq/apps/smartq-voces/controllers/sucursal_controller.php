<?php

/**
 * Controlador Sucursal
 *
 * @access public
 * @version 1.0
 */
class SucursalController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * alias_sucursal
     *
     * @var string
     */
    public $aliasSucursal;

    /**
     * host
     *
     * @var string
     */
    public $host;

    /**
     * nombre_bd
     *
     * @var string
     */
    public $nombreBd;

    /**
     * usuario_bd
     *
     * @var string
     */
    public $usuarioBd;

    /**
     * password_bd
     *
     * @var string
     */
    public $passwordBd;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

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
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[] = array('name' => 'Alias Sucursal', 'index' => 'alias_sucursal');
        $columnsGrid[] = array('name' => 'Host', 'index' => 'host');
        $columnsGrid[] = array('name' => 'Nombre Base de Datos', 'index' => 'nombre_bd');
        $columnsGrid[] = array('name' => 'Usuario Base de Datos', 'index' => 'usuario_bd');
        $columnsGrid[] = array('name' => 'Password Base de Datos', 'index' => 'password_bd');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Sucursal/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Sucursal
     *
     */
    public function editarAction($id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $sucursal = $this->Sucursal->findFirst($id);
        if ($sucursal) {
            Tag::displayTo('id', $sucursal->getId());
            Tag::displayTo('alias_sucursal', $sucursal->getAliasSucursal());
            Tag::displayTo('host', $sucursal->getHost());
            Tag::displayTo('nombre_bd', $sucursal->getNombreBd());
            Tag::displayTo('usuario_bd', $sucursal->getUsuarioBd());
            Tag::displayTo('password_bd', $sucursal->getPasswordBd());
            Tag::displayTo('creacion_at', $sucursal->getCreacionAt());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Sucursal
     *
     */
    public function guardarAction($isEdit = false, $id = null) {
        $aliasSucursal = $this->getPostParam("alias_sucursal", "striptags", "extraspaces");
        $host = $this->getPostParam("host", "striptags", "extraspaces");
        $nombreBd = $this->getPostParam("nombre_bd", "striptags", "extraspaces");
        $usuarioBd = $this->getPostParam("usuario_bd", "striptags", "extraspaces");
        $passwordBd = $this->getPostParam("password_bd", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");
        $sucursal = new Sucursal();
        $sucursal->setId($id);
        $sucursal->setAliasSucursal($aliasSucursal);
        $sucursal->setHost($host);
        $sucursal->setNombreBd($nombreBd);
        $sucursal->setUsuarioBd($usuarioBd);
        $sucursal->setPasswordBd($passwordBd);
        $sucursal->setCreacionAt($creacionAt);
        $action = ($isEdit == true) ? "editar" : 'nuevo';

        if ($isEdit == true) {
            if ($sucursal->save() == false) {
                Flash::error('Hubo un error guardando el registro.');
            } else {
                //busca el id de menu, busco con el nombre ya q en sucursal y menu son el mismo
                $menu = new Menu();
                $buscaMenu = $menu->findFirst("nombre= '$aliasSucursal'");
                $id_referencia = $buscaMenu->getId();
                $buscaSubMenu = $menu->find("idreferencia= $id_referencia");
                foreach ($buscaSubMenu as $result) {
                    $op = $result->getNombre();
                    $update_menu = new Menu();
                    switch ($op) {
                        case 'Turnos Con Ticket':
                            $ruta = "sucursal/verSucursales/1/$host";
                            $update_menu->updateAll("ruta = '$ruta'", "nombre = '$op'");
                            break;
                        case 'Turnos Sin Ticket':
                            $ruta = "sucursal/verSucursales/2/$host";
                            $update_menu->updateAll("ruta = '$ruta'", "nombre = '$op'");
                            break;
                    }
                }

                $this->setParamToView('save', 'true');
                if ($this->getQueryParam("exit") != "")
                    $this->setParamToView('exit', 'true');
                else {
                    if (!$isEdit) {
                        $action = 'editar';
                    }
                }
                Flash::success('Registro guardado con éxito.');
            }
        } else { //nuevo
            if ($sucursal->save() == false) {
                Flash::error('Hubo un error guardando el registro.');
            } else {
                $sucursal_id = $sucursal->getId();
                //primero guarda en menu principal... ejemplo: sucursal Quicentro Sur
                $ruta = "/";
                $moduloId = 3;
                $menu = new Menu();
                $menu->setId(null);
                $menu->setNombre($aliasSucursal);
                $menu->setRuta($ruta);
                $menu->setIdreferencia(53); //sucursales
                $menu->setEstado(1);
                $menu->setModuloId($moduloId);
                $menu->setOrden(1);
                $menu->setPrincipal(0);
                $menu->setPosicion("left");
                $menu->setTipoVentana("_self");
                if ($menu->save() == false) {
                    Flash::error("Hubo un error guardando el registro $aliasSucursal en menú.");
                } else {
                    $idreferencia = $menu->getId();
                    Flash::success("Registro $aliasSucursal guardado con éxito en menú.");
                }

                //segundo guarda los submenus de la sucursal
                $moduloId = 3;  //administracion
                //--busco la configuracion de la empresa, crea los menus de las sucursales pero con estado=0/1 segun el modulo que se le instale,
                //--si deseamos ver las dos opciones debemos actualizar desde la configuracion de la empresa
                $array_menus_no_permitidos = array();
                $empresa = new Empresa();
                $bucaEmpresa = $empresa->findFirst();
                $modulo_operadores = $bucaEmpresa->getModuloOperadores();
                $modulo_cajas = $bucaEmpresa->getModuloCajas();
                $estado_modulo_operadores = 1;
                $estado_modulo_cajeros = 1;
                if ($modulo_operadores==0)
                    $estado_modulo_operadores=0; //rol operadores con ticket
                if ($modulo_cajas==0)
                    $estado_modulo_cajeros=0; //rol operadores sin ticket
                
                $array_ops = array(array("nombre" => "Turnos Con Ticket", "num" => 1,'estado'=>$estado_modulo_operadores),
                        array("nombre" => "Turnos Sin Ticket", "num" => 2,'estado'=>$estado_modulo_cajeros),
                        //array("nombre" => "Estado Actual Usarios", "num" => 3)
                );
                foreach ($array_ops as $val) {
                    $ruta = "sucursal/verSucursales/{$val['num']}/$host";
                    $menu1 = new Menu();
                    $menu1->setId(null);
                    $menu1->setNombre($val['nombre']);
                    $menu1->setRuta($ruta);
                    $menu1->setIdreferencia($idreferencia);
                    $menu1->setEstado($val['estado']);
                    $menu1->setModuloId($moduloId);
                    $menu1->setOrden(1);
                    $menu1->setPrincipal(0);
                    $menu1->setPosicion("left");
                    $menu1->setTipoVentana("_self");
                    if ($menu1->save() == false) {
                        Flash::error("Hubo un error guardando el registro en menú.");
                    } else {
                        Flash::success("Registro guardado con éxito en menú.");
                    }
                }

                //--
                $ruta = "sucursal/estadoActualUsuarios/$sucursal_id";
                $menu1 = new Menu();
                $menu1->setId(null);
                $menu1->setNombre("Estado Actual Usuarios");
                $menu1->setRuta($ruta);
                $menu1->setIdreferencia($idreferencia);
                $menu1->setEstado(1);
                $menu1->setModuloId($moduloId);
                $menu1->setOrden(3);
                $menu1->setPrincipal(0);
                $menu1->setPosicion("left");
                $menu1->setTipoVentana("_self");
                if ($menu1->save() == false) {
                    Flash::error("Hubo un error guardando el registro en menú.");
                } else {
                    Flash::success("Registro guardado con éxito en menú.");
                }

                $this->setParamToView('save', 'true');
                if ($this->getQueryParam("exit") != "")
                    $this->setParamToView('exit', 'true');
                else {
                    if (!$isEdit) {
                        $action = 'editar';
                    }
                }
                Flash::success('Registro guardado con éxito.');
            }
        }
        $this->routeTo("action: $action", "id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Sucursal
     *
     */
    public function eliminarAction() {
        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                //busco el nombre de la sucursal
                $sucursal = new Sucursal();
                $buscaSucursal = $sucursal->findFirst("id=$ids[$i]");
                $alias_sucursal = $buscaSucursal->getAliasSucursal();
                //busco el id del menu principal con el nombre de la sucursal
                $menu = new Menu();
                $buscaMenu = $menu->findFirst("nombre='$alias_sucursal'");
                if ($buscaMenu) {
                    $arrar_ids = array();

                    $menu_id = $buscaMenu->getId();
                    $arrar_ids[] = $menu_id;

                    //busco el id de los submenus
                    $menu_2 = new Menu();
                    $buscaMenu_2 = $menu_2->find("idreferencia=$menu_id");

                    //elimino los submenus
                    foreach ($buscaMenu_2 as $r) {
                        $arrar_ids[] = $r->getId();
                    }

                    $id_s = implode(',', $arrar_ids);

                    //eliminar de permiso
                    $permiso = new Permiso();
                    $permiso->deleteAll("menu_id IN ($id_s)");
                    //elimino de menu
                    $menu_3 = new Menu();
                    $menu_3->deleteAll("id IN ($id_s)");
                }
                $this->Sucursal->delete($ids[$i]);
//                if (!$this->Sucursal->delete($ids[$i])) {
//                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
//                } else {
//                    $msg.="El registro $ids[$i] fue eliminado correctamente.";
//                }
            }
        }
        echo $msg;
    }

    public function obtenerDatosGridAction() {
        $this->setResponse('ajax');  // asignamos el tipo de respuesta para esta accion
        $pagina = $this->getPostParam('page'); // obtener el numero de pagina
        $limite = $this->getPostParam('rows'); // obtener el n�mero de filas que queremos tener en el grid
        $col_orden = $this->getPostParam('sidx'); // Obtener la fila de �ndice - es decir, cuando el usuario haga clic en la columna para ordenar
        $dir_orden = $this->getPostParam('sord'); // obtener la direcci�n de ordenado
        if (!$col_orden)
            $col_orden = 1;
        //construccion de condicion de consulta
        $condicion = '1';
        $buscar = $this->getPostParam('_search', 'stripslashes'); //obtenemos la variable que activa la busqueda
        $strbusqueda = $this->getPostParam('searchString');  // obtenemos la cadena de busqueda
        $campoBusqueda = $this->getPostParam('searchField'); // obtenemos el campo por el cual se va a realizar la busqueda
        $abrevoper = $this->getPostParam('searchOper');  // obtenemos el operador por el cual se var realizar la busqueda
        $filtroBusqueda = $this->getPostParam('filters', 'stripslashes');
        if ($buscar == 'true') { //verificamos si la busqueda es activada
            if ($strbusqueda != '') {    // construccion de la cadena de condicion para la busqueda normal
                switch ($campoBusqueda) {

                }
                $condicion.=' AND ' . Utils::toSqlParamSearchGrid($campoBusqueda, $abrevoper, $strbusqueda); // construimos la cadena de condicion en base al operador de busqueda
            } elseif ($filtroBusqueda) {
                $jsona = json_decode($filtroBusqueda, true);
                if (is_array($jsona)) {
                    $gopr = $jsona['groupOp'];
                    $rules = $jsona['rules'];
                    $i = 0;
                    foreach ($rules as $key => $val) {
                        $field = $val['field'];
                        switch ($field) {

                        }
                        $op = $val['op'];
                        $v = $val['data'];
                        if ($v && $op) {
                            $i++;
                            $v = Utils::toSqlParamSearchGrid($field, $op, $v);
                            if ($i == 1)
                                $condicion.=' AND ';
                            else
                                $condicion.= " " . $gopr . " ";
                            $condicion.= $v;
                        }
                    }
                }
            }
            //construimos la condicion por barra de busqueda del grid
            $sarr = $_POST;
            foreach ($sarr as $k => $v) {
                switch ($k) {
                    case 'alias_sucursal':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'host':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'nombre_bd':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'usuario_bd':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'password_bd':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Sucursal->count("conditions: $condicion");  //contar el numero total de registros existentes
        //obtenemos el numero de paginas para el grid
        if ($contar > 0) {
            $total_pags = ceil($contar / $limite);
        } else {
            $total_pags = 0;
        }
        if ($pagina > $total_pags)
            $pagina = $total_pags;
        $inicio = $limite * $pagina - $limite; // no poner $limite*($pagina - 1)
        if ($inicio < 0)
            $inicio = 0;
        $limite = $inicio + $limite;  // igualamos el limite al total de registros que se obtendra hasta la pagina actual
        $resultado = $this->Sucursal->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Sucursal = $resultado[$i];
            $jqgrid->rows[] = array('id' => $Sucursal->getId(), 'cell' => array($Sucursal->getAliasSucursal(), $Sucursal->getHost(), $Sucursal->getNombreBd(), $Sucursal->getUsuarioBd(), $Sucursal->getPasswordBd()));
            //$jqgrid->rows[] = array('id' => $Sucursal->getId(), 'cell' => array($Sucursal->getAliasSucursal(), $Sucursal->getHost()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /**
     * Funcion que permite probar la conexion con la sucrusal
     */
    public function probarSucursalAction() {
        // $this->setResponse("json");
        $aliasSucursal = $this->getPostParam("alias_sucursal", "striptags", "extraspaces");
        $host = $this->getPostParam("host", "striptags", "extraspaces");
        $nombreBd = $this->getPostParam("nombre_bd", "striptags", "extraspaces");
        $usuarioBd = $this->getPostParam("usuario_bd", "striptags", "extraspaces");
        $passwordBd = $this->getPostParam("password_bd", "striptags", "extraspaces");
        try {
            if (!($link = mysql_connect($host, $usuarioBd, $passwordBd))) {
                echo "Error de conexión con host.";
                //exit();
            } else {
                Flash::SUCCESS("Conexión con host exitosa.");
                //$error="bien";
            }
            if (!mysql_select_db($nombreBd, $link)) {
                Flash::ERROR("La base de datos no existe. <br>");
                //exit();
            } else {
                Flash::SUCCESS("Conexión con base de datos correcta");
            }
        } catch (Exception $e) { // o el tipo de Excepcion que requieras...
            Flash::ERROR("Error de conexión con host. Datos no son correctos o no está disponible<br>" . $e->getMessage());
            //echo $e->getMessage();
        }
    }

    public $tabla_sucursales;
    public $url_iframe;
    /**
     * Funcion que carga la vista en el frame para ver los reportes
     * de turnos con ticket y sin ticket
     */
    public function verSucursalesAction($op, $ip) {
        $this->setResponse('ajax');
        switch ($op) {
            case 1:
                $this->url_iframe = "http://$ip/smartq_reportes/ReportesConTicket/ReportesConTicket.php";
                $this->render('sucursal/reporte_turnos');      //carga la vista
                break;
            case 2:
                $this->url_iframe = "http://$ip/smartq_reportes/ReportesSinTicket/ReportesSinTicket.php";
                $this->render('sucursal/reporte_turnos');      //carga la vista
                break;
//            case 3:
//                $this->redirect("sucursal/estadoActualUsuarios/192.168.1.7");
//                break;
            default :
                echo "No existe";
                break;
        }
    }

    public $tabla;
    public function estadoActualUsuariosAction($sucursal_id) {
        $this->setResponse('ajax');
        //-- busco los datos de la sucursar
        $sucursal = new Sucursal();
        $buscaSucursal=$sucursal->findFirst("id=$sucursal_id");
        $host= $buscaSucursal->getHost();
        $nombre_bdd= $buscaSucursal->getNombreBd();
        $user_bdd= $buscaSucursal->getUsuarioBd();
        $password_bdd= $buscaSucursal->getPasswordBd();
        $db = DbLoader::factory('MySQL', array(
                "host"      => $host,
                "username"  => $user_bdd,
                "password"  => $password_bdd,
                "name"      =>  $nombre_bdd
        ));
        $html="";
        $result = $db->query("SELECT u.id AS usuario_id, u.nombres, u.login, c.id AS caja_id, c.numero_caja, ubi.nombre_ubicacion, p.nombre_pausa, cp.estado, cp.fecha_inicio, cp.hora_inicio, cp.fecha_fin, cp.hora_fin,
                CASE cp.estado
                WHEN 1 THEN SEC_TO_TIME(TIME_TO_SEC(CURTIME()) - TIME_TO_SEC(hora_inicio))
                ELSE  SEC_TO_TIME(TIME_TO_SEC(hora_fin) - TIME_TO_SEC(hora_inicio))
                END AS tiempo_transcurrido
                FROM usuario u
                JOIN grupousuario gu ON gu.usuario_id=u.id
                LEFT JOIN caja c ON c.usuario_actual=u.id
                left join ubicacion ubi on ubi.id=c.ubicacion_id
                LEFT JOIN caja_pausas cp ON (SELECT id FROM caja_pausas WHERE usuario_id=u.id AND fecha_inicio= CURDATE() ORDER BY id DESC LIMIT 1)=cp.id
                left join pausas p on p.id=cp.pausas_id
                WHERE grupo_id IN (5,7)");
        while ($row = $db->fetchArray($result)) {
            $login="<font color='red'><b>Inactivo</b></font>";
            if($row['login']==1)
                $login="<font color='green'><b>Activo</b></font>";
            $pausado="<font color=''><b>NO</b></font>";
            if($row['estado']==1)
                $pausado="<font color='green'><b>SI</b></font>";
            $html.="<tr>";
            $html.="<td align='center'> {$row['nombres']} </td>";
            $html.="<td align='center'> $login </td>";
            $html.="<td align='center'> {$row['numero_caja']} </td>";
            $html.="<td align='center'> {$row['nombre_ubicacion']} </td>";
            $html.="<td align='center'>  $pausado</td>";
            $html.="<td align='center'> {$row['nombre_pausa']} </td>";
            $html.="<td align='center'> {$row['hora_inicio']} </td>";
            $html.="<td align='center'> {$row['hora_fin']} </td>";
            $html.="<td align='center'> {$row['tiempo_transcurrido']} </td>";
            $html.="</tr>";
        }
        $this->tabla = $html;
    }
}
