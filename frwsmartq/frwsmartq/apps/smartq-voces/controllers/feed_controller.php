<?php

/**
 * Controlador Feed
 *
 * @access public
 * @version 1.0
 */
class FeedController extends ApplicationController {

    /**
     * id
     *
     * @var int
     */
    public $id;

    /**
     * categoriafeeds_id
     *
     * @var int
     */
    public $categoriafeedsId;

    /**
     * url_feed
     *
     * @var string
     */
    public $urlFeed;

    /**
     * activo
     *
     * @var int
     */
    public $activo;

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
        $columnsGrid[]=array('name'=>'Categoría','index'=>'categoriafeeds_id');
        $columnsGrid[]=array('name'=>'Url Feed','index'=>'url_feed','width'=>'250px');
        $columnsGrid[]=array('name'=>'Activo','index'=>'activo','width'=>'50px');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Feed/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Feed
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $feed = $this->Feed->findFirst($id);
        if($feed) {
            Tag::displayTo('id', $feed->getId());
            Tag::displayTo('categoriafeeds_id', $feed->getCategoriafeedsId());
            Tag::displayTo('url_feed', $feed->getUrlFeed());
            Tag::displayTo('activo', $feed->getActivo());
            Tag::displayTo('creacion_at', $feed->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Feed
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $categoriafeedsId = $this->getPostParam("categoriafeeds_id", "int");
        $urlFeed = $this->getPostParam("url_feed", "striptags", "extraspaces");
        $activo = $this->getPostParam("activo");
        if ($activo=="on") $activo=1; else $activo=0;
        $creacionAt = $this->getPostParam("creacion_at");
        $feed = new Feed();
        $feed->setId($id);
        $feed->setCategoriafeedsId($categoriafeedsId);
        $feed->setUrlFeed($urlFeed);
        $feed->setActivo($activo);
        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($feed->save()==false) {
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
     * Eliminar el Feed
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Feed->delete($ids[$i])) {
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
                    case 'categoriafeeds_id':
                        $condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria',$abrevoper,$strbusqueda);
                        $Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
                        if(count($Categoriafeeds)>0) {
                            $arrayIdsCategoriafeeds=array();
                            foreach($Categoriafeeds as $fila) {
                                $arrayIdsCategoriafeeds[]=$fila->getId();
                            }
                            $strbusqueda=join(',',$arrayIdsCategoriafeeds);
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
                            case 'categoriafeeds_id':
                                $condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria',$val['op'],$val['data']);
                                $Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
                                if(count($Categoriafeeds)>0) {
                                    $arrayIdsCategoriafeeds=array();
                                    foreach($Categoriafeeds as $fila) {
                                        $arrayIdsCategoriafeeds[]=$fila->getId();
                                    }
                                    $val['data']=join(',',$arrayIdsCategoriafeeds);
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
                    case 'categoriafeeds_id':
                        $condCategoriafeeds=Utils::toSqlParamSearchGrid('nombre_categoria','bw',$v);
                        $Categoriafeeds=$this->Categoriafeeds->find($condCategoriafeeds);
                        if(count($Categoriafeeds)>0) {
                            $arrayIdsCategoriafeeds=array();
                            foreach($Categoriafeeds as $fila) {
                                $arrayIdsCategoriafeeds[]=$fila->getId();
                            }
                            $v=join(',',$arrayIdsCategoriafeeds);
                            $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'in',$v);
                        }
                        else {
                            $condicion.=' AND 0';
                        }
                        break;
                    case 'url_feed':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                    case 'activo':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);
                        break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Feed->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Feed->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Feed=$resultado[$i];
            $Categoriafeeds=$Feed->getCategoriafeeds();

            $activo='<font color="#01CF00"><b>Si</b></font>';
            if($Feed->getActivo()=="0")
                $activo='<font color="red"><b>No</b></font>';

            $jqgrid->rows[]=array('id'=>$Feed->getId(),'cell'=>array($Categoriafeeds->getNombreCategoria(),$Feed->getUrlFeed(),$activo));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /*
     * Permite obtener la vista previa del feed
    */
    public function verFeedAction() {
        include 'lastRSS.php';
        $this->setResponse('ajax');
        $html="";
        // Options de base
        //$url_flux_rss = 'http://www.eluniverso.com/rss/deportes.xml';
        $url_flux_rss= $this->getPostParam('url_feed');
        $limite       = 5; // nombre d'actus � afficher

        // on cr�e un objet lastRSS
        $rss = new lastRSS;

        // options lastRSS
        $rss->cache_dir   = './cache'; // dossier pour le cache
        $rss->cache_time  = 3600;      // fr�quence de mise � jour du cache (en secondes)
        //$rss->date_format = 'Y-m-d H:i:s';     // format de la date (voir fonction date() pour syntaxe)
        $rss->date_format = 'j M Y g:i a';     // format de la date (voir fonction date() pour syntaxe)

        $rss->CDATA       = 'content'; // on retire les tags CDATA en conservant leur contenu

        // lecture du flux
        if ($rs = $rss->get($url_flux_rss)) {
            for($i=0;$i<$limite;$i++) {
                $fecha=         $rs['items'][$i]['pubDate'];
                $titulo=        $rs['items'][$i]['title'];
                $descripcion=   $rs['items'][$i]['description'];
                $html.="<strong>$fecha</strong> &middot; $titulo<br />";
                //$html.='<strong>'.$rs['items'][$i]['pubDate'].'</strong> &middot; <a href="'.$rs['items'][$i]['link'].'">'.$rs['items'][$i]['description'].'</a><br />';
            }
        }
        else {
            die ('Flux RSS non trouv�');
        }
        echo $html;
    }
}

