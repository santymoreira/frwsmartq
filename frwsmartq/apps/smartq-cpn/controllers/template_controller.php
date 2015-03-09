<?php

class TemplateController extends ApplicationController {
        
        public $menu_usuario=array();
        
      //  public $resultado;
	public function indexAction(){
            
	}
       public function initialize(){
            $user = new Funciones();
            if(!SessionNamespace::exists("datosUsuarioSMC")){
                    Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
                    $this->routeTo("controller: login","action: index");
            }else{
                $dataUsuario = SessionNamespace::get('datosUsuarioSMC');
                $sesion = Session::getId();
                if ($user->usuarioConectado($sesion)==false) {
                    SessionNamespace::drop("datosUsuarioSMC");
                    Flash::error("<b>Desconectado.</b><br/>Su usuario fue desconectado. Por favor, con&eacute;ctese nuevamente ");
                    $this->routeTo("controller: login","action: index");;
                }
            }
                         
            $modulo=new Modulo();
            $menu=new Menu();
            $datosUsuario=SessionNamespace::get("datosUsuarioSMC");
            $modulos=$modulo->obtenerModulosUsuario($datosUsuario->getId());
            if($modulos!=false){
                foreach ($modulos as $mod){
                    //$htmlMenu = $menu->obtenerPermisos($mod->getId());
                    $htmlMenu=$menu->obtenerArbolMenu($datosUsuario->getId(),$mod->getId());
                    $this->menu_usuario[]=array('idModulo'=>$mod->getId(),'nombre_modulo'=>$mod->getNombre(),'menu'=>$htmlMenu);
                }
            }
       }
       
       

 }

