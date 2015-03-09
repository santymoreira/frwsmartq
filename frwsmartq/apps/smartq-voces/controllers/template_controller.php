<?php

class TemplateController extends ApplicationController {

    public $menu_usuario = array();

    public function initialize() {
        $user = new Funciones();
        if (!SessionNamespace::exists("datosUsuarioSMC")) {
            //Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
            //$this->routeTo("controller: login","action: index");
            Flash::addMessage("Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente", Flash::WARNING);
            $this->redirect("login/salir");
        }
    }

    public function indexAction() {
        $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
        $grupo_id = $dataUsuario->getRolSeleccionado();
        if (in_array($grupo_id, array(4, 5, 6, 7))) {
            $this->redirect("grupo/seleccionarGrupo");
        } else {
            $modulo = new Modulo();
            $menu = new Menu();
            $datosUsuario = SessionNamespace::get("datosUsuarioSMC");
            //$modulos = $modulo->obtenerModulosUsuario($datosUsuario->getId());
            //$modulos = array(1,2,3);
            //-- busco los modulos que tiene el rol

            $db = DbBase::rawConnect();
            $result = $db->query("SELECT modulo.id AS modulo_id, modulo.nombre AS modulo_nombre, grupo_id
            FROM modulo, menu, permiso p
            WHERE p.menu_id=menu.id AND modulo.id=menu.modulo_id AND grupo_id = $grupo_id AND modulo.estado=1 AND menu.estado=1 AND permiso=1 
            GROUP BY modulo_id");

            if ($db->numRows($result) > 0) {
                //echo $db->numRows($result); die();
                while ($row = $db->fetchArray($result)) {
                    $htmlMenu = $menu->obtenerArbolMenu($grupo_id, $row['modulo_id'],0);
                    $this->menu_usuario[] = array('idModulo' => $row['modulo_id'], 'nombre_modulo' => $row['modulo_nombre'], 'menu' => $htmlMenu);
                }
            } else {
                Flash::addMessage("El rol seleccionado no tiene asignado menús. Comuníquese con el administrador del sistema.", Flash::WARNING);
                //header("Location:" . $_SERVER['HTTP_REFERER']);
                $this->redirect("grupo/seleccionarGrupo");
            }

            /* if ($modulos != false) {
              foreach ($modulos as $mod) {
              //echo $mod;
              //$htmlMenu = $menu->obtenerPermisos($mod->getId());
              //$htmlMenu = $menu->obtenerArbolMenu($datosUsuario->getId(), $mod->getId());
              $htmlMenu = $menu->obtenerArbolMenu($grupo_id, $mod->getId());
              //$htmlMenu = $menu->obtenerArbolMenu($grupo_id, $mod);
              $this->menu_usuario[] = array('idModulo' => $mod->getId(), 'nombre_modulo' => $mod->getNombre(), 'menu' => $htmlMenu);
              //$this->menu_usuario[] = array('idModulo' => $mod, 'nombre_modulo' => "nombre", 'menu' => $htmlMenu);
              }
              } */
//            die();
        }
    }

}
