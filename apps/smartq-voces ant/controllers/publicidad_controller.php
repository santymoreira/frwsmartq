<?php

/**
 * Controlador Publicidad
 *
 * @access public
 * @version 1.0
 */
class PublicidadController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * archivo_publicidad
     *
     * @var string
     */
    public $archivoPublicidad;

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
        $columnsGrid[]=array('name'=>'Archivo Publicidad','index'=>'archivo_publicidad');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Publicidad/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Publicidad
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $publicidad = $this->Publicidad->findFirst($id);
        if($publicidad) {
            Tag::displayTo('id', $publicidad->getId());
            //Tag::displayTo('archivo_publicidad', $publicidad->getArchivoPublicidad());
            Tag::displayTo('archivo_publicidad_aux', $publicidad->getArchivoPublicidad());
            Tag::displayTo('creacion_at', $publicidad->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    public function subirArchivo() {
        //INICIO SUBIR FOTO
        $img = $_FILES['archivo_publicidad']['name']; // almaceno el nombre del archivo subido
        $img2 = $img; //nombre de la nueva imagen achicada
        $tipo = $_FILES['archivo_publicidad']['type'];  // almaceno el tipo de archivo
        //$dir = 'public/img/publicidad/'; // selecciono la carpta donde almaceno las imagenes
        $dir = "public/img/".$this->carpeta."/publicidad/"; // selecciono la carpta donde almaceno las imagenes
        //$tam_imagen = $_FILES['foto']['size'];
        $anchomax = 120;

        //aca controlo que el archivo subido sea JPG
        //if ($tipo == "image/jpeg" && $tam_imagen < 1000000) {
            copy($_FILES['archivo_publicidad']['tmp_name'], $dir.$img);
            // es JPG entonces abtengo el tama�o en pixel de la imagen subida
            $tamanos = getimagesize($dir.$img);
            $ancho = $tamanos[0];
            $alto =  $tamanos[1];
            // controlo que no mida de ancho mas que  el limite
            
//            if ($ancho > $anchomax) {
//
//                //$nuevoalto = round($anchomax / $ancho * $alto); //calculo el nuevo alto
//                $nuevoalto = 150;//round($anchomax / $alto * $ancho); //calculo el nuevo alto
//                $imagenoriginal = imagecreatefromjpeg($dir.$img); //tomo la imagen original
//                $imagennueva = imagecreatetruecolor($anchomax,$nuevoalto); // creo el lienzo de la imagen nueva
//                imagecopyresampled($imagennueva, $imagenoriginal, 0, 0, 0, 0, $anchomax, $nuevoalto, $ancho, $alto);// cambio el mana�o
//                imagejpeg($imagennueva, $dir.$img2); // guardo el archivo nuevo
//                $archivo = $dir.$img2;
//            } else {
                //esto es si la imagen no excedia el ancho
                $archivo = $dir.$img;
                //guardo el archivo original
                copy($_FILES['archivo_publicidad']['tmp_name'], $archivo);
//            };

            //$muestra = "<img src=\"".$archivo."\">";
        //}
        return ($img);
    }

    /**
     * Guardar el Publicidad
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $archivoPublicidad = $this->getPostParam("archivo_publicidad", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");

        $archivoPublicidad_aux = $this->getPostParam("archivo_publicidad_aux");        //valor de la base de datos

        if ($isEdit==true) {
            $archivoPublicidad = $_FILES['archivo_publicidad']['name']; // almaceno el nombre del archivo subido
            if ($archivoPublicidad!="")
                $archivoPublicidad=$this->subirArchivo();
            else
                $archivoPublicidad=$archivoPublicidad_aux;
        } else
            $archivoPublicidad=$this->subirArchivo();


        $publicidad = new Publicidad();
        $publicidad->setId($id);
        $publicidad->setArchivoPublicidad($archivoPublicidad);
        $publicidad->setCreacionAt($creacionAt);

        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($publicidad->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
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
     * Eliminar el Publicidad
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Publicidad->delete($ids[$i])) {
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
                    case 'archivo_publicidad':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Publicidad->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Publicidad->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Publicidad=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Publicidad->getId(),'cell'=>array($Publicidad->getArchivoPublicidad()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }
}

