<?php

/**
 * Controlador Usuario
 *
 * @access public
 * @version 1.0
 */
class UsuarioController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * nombres
     *
     * @var string
     */
    public $nombres;

    /**
     * ci
     *
     * @var string
     */
    public $ci;

    /**
     * telefono
     *
     * @var string
     */
    public $telefono;

    /**
     * movil
     *
     * @var string
     */
    public $movil;

    /**
     * estado
     *
     * @var string
     */
    public $estado;

    /**
     * username
     *
     * @var string
     */
    public $username;

    /**
     * password
     *
     * @var string
     */
    public $password;

    /**
     * actclave
     *
     */
    public $actclave;

    /**
     * email
     *
     * @var string
     */
    public $email;

    /**
     * descripcion
     *
     */
    public $descripcion;

    /**
     * foto
     *
     * @var string
     */
    public $foto;

    /**
     * creacion_at
     *
     */
    public $creacionAt;

    public $carpeta;

    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        $empresa= new Empresa();
        $buscaEmpresa=$empresa->findFirst();
        $this->carpeta=$buscaEmpresa->getCarpeta();
    }

    /**
     * Acción por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[]=array('name'=>'Nombres','index'=>'nombres');

        $columnsGrid[]=array('name'=>'Grupo de acceso','index'=>'grupo','width'=>'120');

        $columnsGrid[]=array('name'=>'Username','index'=>'username','width'=>'85');
        $columnsGrid[]=array('name'=>'Descripción','index'=>'descripcion','width'=>'180');
        $columnsGrid[]=array('name'=>'Ci','index'=>'ci','width'=>'80');
        $columnsGrid[]=array('name'=>'Teléfono','index'=>'telefono','width'=>'80');
        $columnsGrid[]=array('name'=>'Movil','index'=>'movil','width'=>'80');
        //$columnsGrid[]=array('name'=>'Estado','index'=>'estado');
        $columnsGrid[]=array('name'=>'Foto','index'=>'foto');
        $columnsGrid[]=array('name'=>'Email','index'=>'email');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Usuario/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Usuario
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $usuario = $this->Usuario->findFirst($id);
        if($usuario) {
            Tag::displayTo('id', $usuario->getId());
            Tag::displayTo('nombres', $usuario->getNombres());
            Tag::displayTo('ci', $usuario->getCi());
            Tag::displayTo('telefono', $usuario->getTelefono());
            Tag::displayTo('movil', $usuario->getMovil());
            //Tag::displayTo('estado', $usuario->getEstado());
            Tag::displayTo('username', $usuario->getUsername());
            Tag::displayTo('password', base64_decode($usuario->getPassword()));
            Tag::displayTo('cpassword', base64_decode($usuario->getPassword()));
            Tag::displayTo('actclave', $usuario->getActclave());
            Tag::displayTo('email', $usuario->getEmail());
            Tag::displayTo('descripcion', $usuario->getDescripcion());
            //Tag::displayTo('foto', $usuario->getFoto());
            Tag::displayTo('foto1', $usuario->getFoto());
            Tag::displayTo('creacion_at', $usuario->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    public $array_menus= array();
    function buscarMenu($menu_id) {
        $menu= new Menu();
        $buscaMenu= $menu->findFirst("id=$menu_id");
        if ($buscaMenu->getIdreferencia()!=0) {
            //echo "menu_id: ".$menu_id." idereferencia: ".$buscaMenu->getIdreferencia();
            $this->buscarMenu($buscaMenu->getIdreferencia());
        } //else
        //echo $menu_id."<br>";
        $this->array_menus[]=$menu_id;
    }

    public function subirFoto() {
        //INICIO SUBIR FOTO
        $img = $_FILES['foto']['name']; // almaceno el nombre del archivo subido
        $img2 = $img; //nombre de la nueva imagen achicada
        $tipo = $_FILES['foto']['type'];  // almaceno el tipo de archivo
        $dir = "public/img/".$this->carpeta."/fotos/"; // selecciono la carpta donde almaceno las imagenes
        $tam_imagen = $_FILES['foto']['size'];
        $anchomax = 120;
        //aca controlo que el archivo subido sea JPG
        if ($tipo == "image/jpeg" && $tam_imagen < 1000000) {
            copy($_FILES['foto']['tmp_name'], $dir.$img);
            // es JPG entonces abtengo el tama�o en pixel de la imagen subida
            $tamanos = getimagesize($dir.$img);
            $ancho = $tamanos[0];
            $alto =  $tamanos[1];
            // controlo que no mida de ancho mas que  el limite
            if ($ancho > $anchomax) {

                //$nuevoalto = round($anchomax / $ancho * $alto); //calculo el nuevo alto
                $nuevoalto = 150;//round($anchomax / $alto * $ancho); //calculo el nuevo alto
                $imagenoriginal = imagecreatefromjpeg($dir.$img); //tomo la imagen original
                $imagennueva = imagecreatetruecolor($anchomax,$nuevoalto); // creo el lienzo de la imagen nueva
                imagecopyresampled($imagennueva, $imagenoriginal, 0, 0, 0, 0, $anchomax, $nuevoalto, $ancho, $alto);// cambio el mana�o
                imagejpeg($imagennueva, $dir.$img2); // guardo el archivo nuevo
                $archivo = $dir.$img2;
            } else {
                //esto es si la imagen no excedia el ancho
                $archivo = $dir.$img;
                //guardo el archivo original
                copy($_FILES['foto']['tmp_name'], $archivo);
            };

            //$muestra = "<img src=\"".$archivo."\">";
        }
        return ($img);
    }

    /**
     * Guardar el Usuario
     *
     */
    public function guardarAction($isEdit=false,$id=null) {
        $this->array_menus= array();
        $nombres = $this->getPostParam("nombres", "striptags", "extraspaces");
        $ci = $this->getPostParam("ci", "striptags", "extraspaces");
        $telefono = $this->getPostParam("telefono", "striptags", "extraspaces");
        $movil = $this->getPostParam("movil", "striptags", "extraspaces");
        //$estado = $this->getPostParam("estado", "striptags", "extraspaces");
        $estado= "Activo";
        $username = $this->getPostParam("username", "striptags", "extraspaces");
        $password = $this->getPostParam("password", "striptags", "extraspaces");
        $actclave = $this->getPostParam("actclave");
        $email = $this->getPostParam("email", "striptags", "extraspaces");
        $descripcion = $this->getPostParam("descripcion");
        $foto = $this->getPostParam("foto");        //boton explorar
        $foto1 = $this->getPostParam("foto1");
        $creacionAt = $this->getPostParam("creacion_at");

        //verificación si el nombre de usuario existe
        /*$verifica_persona = $this->Usuario->findFirst("conditions: username='$username'","columns: id");
        if($verifica_persona){
            Flash::notice("Ya existe el nombre de usuario "+$verifica_persona->getUsername());
            return;
        }*/
        //fin de la verificación de personas existentes

        $grupo_admin            =$this->getPostParam("chk_grupo_0");
        $grupo_dispensador      =$this->getPostParam("chk_grupo_4");
        $grupo_oper_con_ticket  =$this->getPostParam("chk_grupo_5");
        $grupo_pantalla         =$this->getPostParam("chk_grupo_6");
        $grupo_oper_sin_ticket  =$this->getPostParam("chk_grupo_7");

        if ($grupo_admin=='0') { //si ha seleccionado sistema
            $menu_ids = $this->getPostParam("chk_menu");    //obtener todos los menus seleccionados para el usuario
            $forma_menu_ids="";
            $coma='';
            foreach($menu_ids as $menu_id) {
                //echo $menu_id;
                $forma_menu_ids.=$coma.$menu_id;
                $coma=',';
            }

            $usuariomenu= new Usuariomenu();
            $menu= new Menu();
            foreach($menu_ids as $menu_id) {
                $this->buscarMenu($menu_id);
            }
        }

        if ($isEdit==true) {
            $img = $_FILES['foto']['name']; // almaceno el nombre del archivo subido
            if ($img!="")
                $img=$this->subirFoto();
            else
                $img=$foto1;
        } else
            $img=$this->subirFoto();

        $usuario = new Usuario();
        $usuario->setId($id);
        $usuario->setNombres($nombres);
        $usuario->setCi($ci);
        $usuario->setTelefono($telefono);
        $usuario->setMovil($movil);
        $usuario->setEstado($estado);
        $usuario->setUsername($username);
        $usuario->setPassword(base64_encode($password));
        $usuario->setActclave($actclave);
        $usuario->setEmail($email);
        $usuario->setDescripcion($descripcion);
        $usuario->setFoto($img);
        $usuario->setCreacionAt($creacionAt);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($usuario->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            if ($isEdit==true) {
                $usuariomenu= new Usuariomenu();
                $grupo_usuario = new Grupousuario();

                if ($grupo_admin=='0') {  //si ha seleccionado sistema
                    //INICIO ELIMINAR LOS DATOS ANTERIORES
                    //grupo usuario
                    $grupo_usuario->deleteAll($whereCondition="usuario_id= $id");
                    //usuario menu
                    $usuariomenu->deleteAll($whereCondition="usuario_id= $id");
                    //FIN ELIMINAR LOS DATOS ANTERIORES

                    //INICIO GUARDAR EL GRUPO_ID EN TABLA GRUPOUSUARIO
                    $condicion="{#Menu}.id IN ($forma_menu_ids) GROUP BY {#Grupo}.id";
                    $query = new ActiveRecordJoin(array(
                                    "entities" => array("Modulo", "Grupo", "Menu"),
                                    "fields" => array(
                                            "{#Grupo}.id"),
                                    "conditions" => $condicion
                    ));
                    foreach($query->getResultSet() as $result) {
                        $grupo_usuario->setUsuarioId($id);
                        $grupo_usuario->setGrupoid($result->getId());
                        $grupo_usuario->save();
                    }
                    //FIN GUARDAR EL GRUPO_ID EN TABLA GRUPOUSUARIO

                    //INICIO GUARDAR DATOS EN LA TABLA USUARIOMENU
                    $menu= new Menu();
                    foreach (array_flip(array_flip($this->array_menus)) as $menu_id) {
                        $usuariomenu->setUsuarioId($id);
                        $usuariomenu->setMenuId($menu_id);
                        $usuariomenu->save();
                    }
                    //FIN GUARDAR DATOS EN LA TABLA USUARIOMENU
                }
            } else if ($isEdit==false) { //nuevo
                $maxusuario=$usuario->maximum("id");
                if ($grupo_admin=='0') {  //si ha seleccionado sistema
                    //INICIO GUARDAR EL GRUPO_ID EN TABLA GRUPOUSUARIO
                    $condicion="{#Menu}.id IN ($forma_menu_ids) GROUP BY {#Grupo}.id";
                    $query = new ActiveRecordJoin(array(
                                    "entities" => array("Modulo", "Grupo", "Menu"),
                                    "fields" => array(
                                            "{#Grupo}.id"),
                                    "conditions" => $condicion
                    ));
                    foreach($query->getResultSet() as $result) {
                        $grupo_usuario = new Grupousuario();
                        $grupo_usuario->setUsuarioId($maxusuario);
                        $grupo_usuario->setGrupoid($result->getId());
                        $grupo_usuario->save();
                    }
                    //FIN GUARDAR EL GRUPO_ID EN TABLA GRUPOUSUARIO

                    //INICIO GUARDAR DATOS EN LA TABLA USUARIOMENU
                    $usuariomenu= new Usuariomenu();
                    $menu= new Menu();
                    foreach (array_flip(array_flip($this->array_menus)) as $menu_id) {
                        $usuariomenu->setUsuarioId($maxusuario);
                        $usuariomenu->setMenuId($menu_id);
                        $usuariomenu->save();
                    }
                    //FIN GUARDAR DATOS EN LA TABLA USUARIOMENU
                }
                if ($grupo_dispensador=='4') {  //si ha seleccionado dispensadores
                    $grupo_usuario = new Grupousuario();
                    $grupo_usuario->setUsuarioId($maxusuario);
                    $grupo_usuario->setGrupoid(4);
                    $grupo_usuario->save();
                }
                if ($grupo_oper_con_ticket=='5') {  //si ha seleccionado operador con ticket
                    $grupo_usuario = new Grupousuario();
                    $grupo_usuario->setUsuarioId($maxusuario);
                    $grupo_usuario->setGrupoid(5);
                    $grupo_usuario->save();
                }
                if ($grupo_pantalla=='6') {  //si ha seleccionado pantalla
                    $grupo_usuario = new Grupousuario();
                    $grupo_usuario->setUsuarioId($maxusuario);
                    $grupo_usuario->setGrupoid(6);
                    $grupo_usuario->save();
                }
                if ($grupo_oper_sin_ticket=='7') {  //si ha seleccionado operador sin ticket
                    $grupo_usuario = new Grupousuario();
                    $grupo_usuario->setUsuarioId($maxusuario);
                    $grupo_usuario->setGrupoid(7);
                    $grupo_usuario->save();
                }
            }

            $this->setParamToView('save','true');
            if($this->getQueryParam("exit")!="")
                $this->setParamToView('exit','true');
            else {
                if(!$isEdit) {
                    $action='editar';
                }
            }
            Flash::success('Registro guardado con éxito.');
        }
        $this->routeTo("action: $action","id: $id");
        //$this->routeTo('action: index');
    }

    /**
     * Eliminar el Usuario
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if ($ids[$i]==1) {      //no se puede eliminar el admin
                    $msg.="El registro $ids[$i] no pudo ser eliminado.";
                } else {
                    $conditions="usuario_id=".$ids[$i];

                    $sesiones           = new Sesiones();       //ligado a operador
                    $caja_pausas        = new CajaPausas();     //ligado a operador

                    $grupo_usuario      = new Grupousuario();

                    $user_caja          = new Usercaja();
                    $user_dispensador   = new Userdispensador();
                    $user_pantalla      = new Userpantalla();
                    $usuario_menu       = new Usuariomenu();

                    $sesiones->deleteAll($conditions);
                    $caja_pausas->deleteAll($conditions);

                    $grupo_usuario->deleteAll($conditions);
                    $user_caja->deleteAll($conditions);
                    $user_dispensador->deleteAll($conditions);
                    $user_pantalla->deleteAll($conditions);
                    $usuario_menu->deleteAll($conditions);
                    if(!$this->Usuario->delete($ids[$i])) {
                        $msg.="El registro $ids[$i] no pudo ser eliminado.";
                    }else {
                        $msg.="El registro $ids[$i] fue eliminado correctamente.";
                    }
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
                    case 'nombres':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'ci':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'telefono':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'movil':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'estado':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'username':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'email':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'descripcion':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Usuario->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Usuario->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Usuario=$resultado[$i];

            if ($Usuario->getUsername()!="superadmin") {
                //INICIO OBTENER EL GRUPO DE ACCESO
                $grupo_acceso="";
                $usuario_id=$Usuario->getId();
                $condicion="{#Usuario}.id = $usuario_id";
                $query = new ActiveRecordJoin(array(
                                "entities" => array("Usuario", "Grupo", "Grupousuario"),
                                "fields" => array(
                                        "{#Grupo}.nombre_largo"),
                                "conditions" => $condicion
                ));
                foreach($query->getResultSet() as $result) {
                    $grupo_acceso=$result->getNombreLargo();
                }
                //FIN OBTENER GRUPO DE ACCESO

                //$jqgrid->rows[]=array('id'=>$Usuario->getId(),'cell'=>array($Usuario->getNombres(),$Usuario->getCi(),$Usuario->getTelefono(),$Usuario->getMovil(),$Usuario->getEstado(),$Usuario->getUsername(),$Usuario->getEmail(),$Usuario->getDescripcion()));
                $direccion = $this->carpeta."/fotos/".$Usuario->getFoto();
                $jqgrid->rows[]=array('id'=>$Usuario->getId(),'cell'=>array($Usuario->getNombres(),$grupo_acceso,$Usuario->getUsername(),$Usuario->getDescripcion(),$Usuario->getCi(),$Usuario->getTelefono(),$Usuario->getMovil(),"<center>".Tag::image($direccion,'height: 75px')."</center>",/*$Usuario->getEstado(),*/$Usuario->getEmail(),$Usuario->getFoto()));
            }
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

