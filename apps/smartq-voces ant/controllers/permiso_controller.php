<?php

/**
 * Controlador Permiso
 *
 * @access public
 * @version 1.0
 */
class PermisoController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * menu_id
     *
     * @var int
     */
    public $menuId;

    /**
     * grupo_id
     *
     * @var int
     */
    public $grupoId;

    /**
     * hora_inicio
     *
     */
    public $horaInicio;

    /**
     * hora_fin
     *
     */
    public $horaFin;

    /**
     * permiso
     *
     * @var int
     */
    public $permiso;

    /**
     * creacion_at
     *
     * @var string
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
        $columnsGrid[] = array('name' => 'Rol', 'index' => 'grupo_id');
        $columnsGrid[] = array('name' => 'Menu', 'index' => 'menu_id');
        //$columnsGrid[] = array('name' => 'Hora Inicio', 'index' => 'hora_inicio');
        //$columnsGrid[] = array('name' => 'Hora Fin', 'index' => 'hora_fin');
        $columnsGrid[] = array('name' => 'Permiso', 'index' => 'permiso');
        //$columnsGrid[]=array('name'=>'Creacion At','index'=>'creacion_at');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * 
     */
    public $tabla;

//    SELECT m.id, nombre AS nombre_menu, permiso
//FROM menu m
//LEFT JOIN permiso p ON m.id=p.menu_id
//WHERE modulo_id=1
    public function rolPermisosAction() {
        $this->setResponse('ajax');
        //-- busco si el usuario conectado es superadmin
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $username = $dataUsuario->getUsername();

        //-- busco los modulos
        $m_sistema = "";
        $r_conf_sistema = "";
        if ($username != 'superadmin') {
            $m_sistema = "1,";  //1=menu Sistema
            $r_conf_sistema = "1,";  //1=rol configuradores del sistema
        }

        $modulo = new Modulo();
        $buscaModulo = $modulo->find("id NOT IN ($m_sistema 5,6,7,8)");
        $array_modulos = array();
        foreach ($buscaModulo as $result) {
            $array_modulos[] = $result->getId();
        }
        //-- buscar los permisos(rol/menu)
        //-- busco los grupos
        $grupo = new Grupo();
        $buscaGrupo = $grupo->find("id NOT IN ($r_conf_sistema 4,5,6,7)");
        $html = "<table><tr>";
        foreach ($buscaGrupo as $result) {
            //-- formo las cabecera de la tabla con los nombres del rol
            $html.="<th>{$result->getNombreLargo()}</th>";
        }
        $html.="</tr><tr>";
        foreach ($buscaGrupo as $result) {
            //-- formo las cabecera de la tabla con los nombres del rol
            //$modulos = array(2, 3);
            $html.="<td>";
            foreach ($array_modulos as $mod) {
                //echo "mod:".$mod; die();
                $modulo = new Modulo();
                $buscaModulo = $modulo->findFirst("id=$mod");
                //$menupermisos=$menu->obtenerArbolMenuPermisos($mod);
                $menupermisos = $this->grupoMenu($result->getId(), $mod, $idreferencia = 0);
                //echo "zzz:".$menupermisos; die(); 
                $contpermisos = '';
                if (isset($menupermisos)) {
                    //$contpermisos = Tag::treeview($mod, $menupermisos, "options: animated: 'fast',collapsed: true, unique: true", "class: filetree");
                    //echo $contpermisos; die(); 
                    //$tabs1[]=array('caption'=>$buscaModulo->getNombre(),'content'=>$contpermisos);
                    $html.="<b>Módulo: </b>" . $buscaModulo->getNombre() . "$menupermisos";
                }
            }
            //$a = Tag::accordion($tabs1,"options: event: 'mouseover', fillSpace: true,clearStyle: true ");
            //echo $a; die();
            $html.="</td>";
        }
        $html.="</tr><table>";
        $this->tabla = $html;
        // $this->render('permiso/rolPermisos');      //carga la vista
    }

    /**
     * 
     */
    function grupoMenu($grupo_id, $modulo_id = 0, $idreferencia = 0) {
        $obj_menus = new Menu();
        $html = '';
        if ($modulo_id > 0) {
            if ($idreferencia > 0) {
                $menus = $obj_menus->find("conditions: modulo_id=$modulo_id AND estado=1 AND idreferencia=$idreferencia", "order: orden asc");
            } else {
                $menus = $obj_menus->find("conditions: modulo_id=$modulo_id AND estado=1 AND principal=1", "order: orden asc");
            }
        } else {
            if ($idreferencia > 0)
                $menus = $obj_menus->find("conditions: idreferencia=$idreferencia AND estado=1", "order: orden asc");
            else
                $menus = $obj_menus->find("conditions: posicion='principal' AND estado=1", "order: orden asc");
        }

        foreach ($menus as $menu) {
            if ($obj_menus->count("conditions: idreferencia=" . $menu->getId()) > 0) {
                $html.="<li><span class='folder'>&nbsp;{$menu->getNombre()}</span>";
                $html.='<ul>' . self::grupoMenu($grupo_id, $menu->getModuloId(), $menu->getId()) . '</ul></li>';
            } else {
                $check = "";
                if ($grupo_id > 0) {
                    //$usuariomenu= new Usuariomenu();
                    $permisomenu = new Permiso();
                    //$buscaUsuariomenu= $usuariomenu->findFirst("usuario_id= $usuario_id AND menu_id={$menu->getId()}");
                    $buscaPermisoMenu = $permisomenu->findFirst("grupo_id= $grupo_id AND menu_id={$menu->getId()}");
                    if ($buscaPermisoMenu)
                        $check = "checked";
                }
                //$permiso = new Permiso();
                if ($grupo_id != 1 & $modulo_id == 1)    //1=rol Configuradores del sistema
                    $html.="<li><span >&nbsp;" . Tag::checkboxField("chk_menu_$grupo_id" . "[]", "checked: $check", "value: {$menu->getId()}", "disabled: disabled") . "{$menu->getNombre()}</span></li>";
                else
                    $html.="<li><span >&nbsp;" . Tag::checkboxField("chk_menu_$grupo_id" . "[]", "checked: $check", "value: {$menu->getId()}") . "{$menu->getNombre()}</span></li>";
            }
        }
        return $html;
    }

    public function asignarRolMenuAction() {
        //-- busco los grupos
        $menu = new Menu();
        $grupo = new Grupo();
        $permiso = new Permiso();
        $buscaGrupo = $grupo->find("id NOT IN (1,4,5,6,7)");
        foreach ($buscaGrupo as $result) {
            $array_menuId = $this->getPostParam("chk_menu_" . $result->getId());
            //print_r($array_menuId);
            //-- elimino todos los menus segun el grupo
            $permiso->deleteAll("grupo_id= {$result->getId()}");
            //-- creo nuevamente de los que estan seleccionados
            if ($array_menuId) {
                foreach ($array_menuId as $menu_id) {
                    $this->recursivo($result->getId(), $menu_id);
                }
            }
        }
    }

    public function recursivo($grupo_id, $menu_id) {
        //-- busco si tiene idreferencia
        $menu = new Menu();
        $permiso = new Permiso();
        $buscaMenu = $menu->findFirst("id=$menu_id AND idreferencia>0");
        if ($buscaMenu) { //si tiene idreferencia
            //echo "$menu_id tiene recursivo:" . $buscaMenu->getIdreferencia();
            $permiso->setMenuId($menu_id);
            $permiso->setGrupoid($grupo_id);
            $permiso->setPermiso(1);
            $permiso->save();
            self::recursivo($grupo_id, $buscaMenu->getIdreferencia());
        } else {
            //echo "$menu_id NO tiene recursivo";
            $permiso->setMenuId($menu_id);
            $permiso->setGrupoid($grupo_id);
            $permiso->setPermiso(1);
            $permiso->save();
        }
    }

    /**
     * Crear un Permiso/
     *
     */
    public function nuevoAction() {
        
    }

    /**
     * Editar el Permiso
     *
     */
    public function editarAction($id = null, $menu_id = null, $grupo_id = null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id, "int");
        $menuId = $filter->applyFilter($menu_id, "int");
        $grupoId = $filter->applyFilter($grupo_id, "int");
        $permiso = $this->Permiso->findFirst($id, $menuId, $grupoId);
        if ($permiso) {
            Tag::displayTo('id', $permiso->getId());
            Tag::displayTo('menu_id', $permiso->getMenuId());
            Tag::displayTo('grupo_id', $permiso->getGrupoId());
            Tag::displayTo('hora_inicio', $permiso->getHoraInicio());
            Tag::displayTo('hora_fin', $permiso->getHoraFin());
            Tag::displayTo('permiso', $permiso->getPermiso());
            Tag::displayTo('creacion_at', $permiso->getCreacionAt());
        } else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Permiso
     *
     */
    public function guardarAction($isEdit = false, $id = null, $menu_id = null, $grupo_id = null) {
        $menuId = $this->getPostParam("menu_id");
        $grupoId = $this->getPostParam("grupo_id");
        $horaInicio = $this->getPostParam("hora_inicio");
        $horaFin = $this->getPostParam("hora_fin");
        //$permiso = $this->getPostParam("permiso", "int");
        $permiso = $this->getPostParam("permiso");
        if ($permiso == 'on')
            $permiso_v = 1;
        else
            $permiso_v = 0;
        $creacionAt = $this->getPostParam("creacion_at", "striptags", "extraspaces");
        $permiso = new Permiso();
        $permiso->setId($id);
        $permiso->setMenuId($menuId);
        $permiso->setGrupoId($grupoId);
        $permiso->setHoraInicio($horaInicio);
        $permiso->setHoraFin($horaFin);
        $permiso->setPermiso($permiso_v);
        $permiso->setCreacionAt($creacionAt);

        $action = ($isEdit == true) ? "editar" : 'nuevo';
        if ($permiso->save() == false) {
            Flash::error('Hubo un error guardando el registro.');
            foreach ($permiso->getMessages() as $message)
                Flash::error($message->getMessage());
        } else {
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
        $this->routeTo("action: $action", "id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Permiso
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg = '';
        if ($this->getPostParam('oper') == 'del') {
            $ids = explode(',', $this->getPostParam('id'));
            for ($i = 0; $i < count($ids); $i++) {
                if (!$this->Permiso->delete($ids[$i])) {
                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
                } else {
                    $msg.="El registro $ids[$i] fue eliminado correctamente.";
                }
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
        //if(!$col_orden) 
        $col_orden = "grupo_id";
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
                    case 'menu_id':
                        $condMenu = Utils::toSqlParamSearchGrid('nombre', $abrevoper, $strbusqueda);
                        $Menu = $this->Menu->find($condMenu);
                        if (count($Menu) > 0) {
                            $arrayIdsMenu = array();
                            foreach ($Menu as $fila) {
                                $arrayIdsMenu[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsMenu);
                            $abrevoper = 'in';
                        }
                        break;
                    case 'grupo_id':
                        $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', $abrevoper, $strbusqueda);
                        $Grupo = $this->Grupo->find($condGrupo);
                        if (count($Grupo) > 0) {
                            $arrayIdsGrupo = array();
                            foreach ($Grupo as $fila) {
                                $arrayIdsGrupo[] = $fila->getId();
                            }
                            $strbusqueda = join(',', $arrayIdsGrupo);
                            $abrevoper = 'in';
                        }
                        break;
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
                            case 'menu_id':
                                $condMenu = Utils::toSqlParamSearchGrid('nombre', $val['op'], $val['data']);
                                $Menu = $this->Menu->find($condMenu);
                                if (count($Menu) > 0) {
                                    $arrayIdsMenu = array();
                                    foreach ($Menu as $fila) {
                                        $arrayIdsMenu[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsMenu);
                                    $val['op'] = 'in';
                                }
                                break;
                            case 'grupo_id':
                                $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', $val['op'], $val['data']);
                                $Grupo = $this->Grupo->find($condGrupo);
                                if (count($Grupo) > 0) {
                                    $arrayIdsGrupo = array();
                                    foreach ($Grupo as $fila) {
                                        $arrayIdsGrupo[] = $fila->getId();
                                    }
                                    $val['data'] = join(',', $arrayIdsGrupo);
                                    $val['op'] = 'in';
                                }
                                break;
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
                    case 'menu_id':
                        $condMenu = Utils::toSqlParamSearchGrid('nombre', 'bw', $v);
                        $Menu = $this->Menu->find($condMenu);
                        if (count($Menu) > 0) {
                            $arrayIdsMenu = array();
                            foreach ($Menu as $fila) {
                                $arrayIdsMenu[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsMenu);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'grupo_id':
                        $condGrupo = Utils::toSqlParamSearchGrid('nombre_largo', 'bw', $v);
                        $Grupo = $this->Grupo->find($condGrupo);
                        if (count($Grupo) > 0) {
                            $arrayIdsGrupo = array();
                            foreach ($Grupo as $fila) {
                                $arrayIdsGrupo[] = $fila->getId();
                            }
                            $v = join(',', $arrayIdsGrupo);
                            $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'in', $v);
                        } else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'hora_inicio':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'hora_fin':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'permiso':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                    case 'creacion_at':
                        $condicion.=' AND ' . Utils::toSqlParamSearchGrid($k, 'bw', $v);
                        break;
                }
            }
        }
        $orden = "$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar = $this->Permiso->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado = $this->Permiso->find("conditions: $condicion", "order: $orden", "limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid = null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if ($limite > $contar)
            $limite = $contar;
        for ($i = $inicio; $i < $limite; $i++) {
            $Permiso = $resultado[$i];
            $Menu = $Permiso->getMenu();
            $Grupo = $Permiso->getGrupo();
            $jqgrid->rows[] = array('id' => $Permiso->getId(), 'cell' => array($Grupo->getNombreLargo(), $Menu->getNombre(), $Permiso->getPermiso(), $Permiso->getCreacionAt()));
            
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

}
