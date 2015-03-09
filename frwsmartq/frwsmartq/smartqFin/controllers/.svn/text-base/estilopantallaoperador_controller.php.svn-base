<?php

/**
 * Controlador Estilopantallaoperador
 *
 * @access public
 * @version 1.0
 */
class EstilopantallaoperadorController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * contenido
     *
     */
    public $contenido;

    public $parametros;

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
     * AcciÃ³n por defecto del controlador/
     *
     */
    public function indexAction() {
        $this->setResponse('ajax');
        $columnsGrid[]=array('name'=>'Contenido','index'=>'contenido');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Estilopantallaoperador/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Estilopantallaoperador
     *
     */
    public function editarAction($id=null) {
        $id=1;

        $ciudades = "wi_pli=626 he_pli=156 fl_pli=cubopantalla_CPN";
        $ciudades = explode(" " , $ciudades);



        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $estilopantallaoperador = $this->Estilopantallaoperador->findFirst($id);


        if($estilopantallaoperador) {
            $parametros=$estilopantallaoperador->getParametros();
            $parametros = explode(" " , $parametros);
            //Tag::displayTo('id', $estilopantallaoperador->getId());
            //Tag::displayTo('contenido', $estilopantallaoperador->getContenido());
            //Tag::displayTo('creacion_at', $estilopantallaoperador->getCreacionAt());
            foreach ($parametros as $key => $valor) {
                $variable=substr($valor,0,6);
                $val=substr($valor,7);
                Tag::displayTo($variable, $val);
            }
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    public function pruebaAction() {

    //$array_pli=array();
    //        $val= "'w_pli'=>685,'h_pli'=>156";
    //        $array_pli=array($val);
    //        $filter = new Filter();
    //        $id=1;
    //        $id = $filter->applyFilter($id,"int");
    //        $estilopantallaoperador = $this->Estilopantallaoperador->findFirst($id);
    //
    //        //Tag::displayTo('id', $estilopantallaoperador->getId());
    //        //Tag::displayTo('contenido', $estilopantallaoperador->getContenido());
    //        //Tag::displayTo('creacion_at', $estilopantallaoperador->getCreacionAt());
    //            foreach($array_pli as $key => $valor) {
    //                echo "key:".$key." valor:".$valor;
    //                $v=array($valor);
    //                foreach($v as $key2 => $valor2) {
    //                //echo $array_pli['w_pli'];
    //                    echo "key:".$key2." valor:".$valor2;
    //                }
    //            }
        $ep= new Estilopantallaoperador();
        $buscaEp= $ep->findFirst();
        //$parametros=$buscaEp->getParametros();
        echo "id: ".$buscaEp->getId();
        echo "parametros: ".$buscaEp->getParametros();
            //$parametros = explode(" " , $parametros);
        $ciudades = "wi_pli=626 he_pli=156 fl_pli=cubopantalla_CPN";
        $ciudades = explode(" " , $ciudades);
        foreach ($ciudades as $key => $valor) {
            $variable=substr($valor,7);
            $val=substr($valor,0,6);
            echo "key:".$key." valor:".$valor." variable:".$variable." valor".$val."<br>";

        }

    }

    /**
     * Guardar el Estilopantallaoperador
     *
     */
    public function guardarAction($isEdit=false,$id=null) {
        $id=1;
        $contenido = $this->getPostParam("contenido");
        $creacionAt = $this->getPostParam("creacion_at");
        $estilopantallaoperador = new Estilopantallaoperador();
        $estilopantallaoperador->setId($id);
        $estilopantallaoperador->setContenido($contenido);
        $estilopantallaoperador->setCreacionAt($creacionAt);
        if($estilopantallaoperador->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            Flash::success('Registro guardado con Ã©xito.');
        }

        $this->routeTo('action: index');
    }

    /**
     * Eliminar el Estilopantallaoperador
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Estilopantallaoperador->delete($ids[$i])) {
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
                    case 'contenido':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Estilopantallaoperador->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Estilopantallaoperador->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Estilopantallaoperador=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Estilopantallaoperador->getId(),'cell'=>array($Estilopantallaoperador->getContenido()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /*
     * Permite guardar el estilo para la pantalla
     */
    public function guardarEstiloAction() {
        $this->setResponse('ajax');
        //Parámetros del fondo
        $im_fon = $this->getPostParam("im_fon");
        //Parámetros logo de la institución
        $xx_pli = $this->getPostParam("xx_pli");
        $yy_pli = $this->getPostParam("yy_pli");
        $wi_pli = $this->getPostParam("wi_pli");
        $he_pli = $this->getPostParam("he_pli");
        $fl_pli = $this->getPostParam("fl_pli");

        //Parámetros nombre de la empresa
        $xx_pno = $this->getPostParam("xx_pno"); //.fondo
        $yy_pno = $this->getPostParam("yy_pno");
        $wi_pno = $this->getPostParam("wi_pno");
        $he_pno = $this->getPostParam("he_pno");
        $fu_pno = $this->getPostParam("fu_pno");
        $no_pno = $this->getPostParam("no_pno"); //nombre de la empresa
        $al_pno = $this->getPostParam("al_pno"); //alineación
        $tl_pno = $this->getPostParam("tl_pno"); //tamaño de letra
        $cl_pno = $this->getPostParam("cl_pno"); //color de letra

        $parametros="im_fon=".$im_fon.
        " wi_pli=".$wi_pli." he_pli=".$he_pli." fl_pli=".$fl_pli.
        " wi_pno=".$wi_pno." he_pno=".$he_pno." no_pno=".$no_pno." tl_pno=".$tl_pno." cl_pno=".$cl_pno;
        $html="";
        $html.="
<input id='tiempo' type='hidden' value='' style='font-size: 12px'/> <!-- Tiempo de duracion del video -->
<input id='nombre' type='hidden' value='' style='font-size: 12px'/> <!-- Se pone la direccion del video para embeber -->
<input id='reproducir' type='hidden' value='' style='font-size: 12px'/> <!-- Se pone 1 para reproducir el timbre -->
<input id='duracion' type='hidden' value='' style='font-size: 12px'/> <!-- Duración de efecto del mensaje -->

<style type='text/css'>
    /*FONDO DE LA PANTALLA*/
    .fondo{
        background-image:url('../../img/".$im_fon."');
        height:766px;
        background-repeat:no-repeat;
    }

    /*BORDE DE LA TV*/
    /*.borde{
        background-image:url('../../img/borde.png');
        background-repeat:no-repeat;
    }*/

    /*.turno_actual{
        border:0;
        background: transparent;
        color: #fff;
        font-size: 50px;
        font-family: Impact;
        text-align: center;
    }
    .turno_anterior{
        border:0;
        background: transparent;
        color: #ffffff;
        font-size: 44px;
        font-family: Impact;
        text-align: center;
    }*/
    .txtmensaje{
        border:0;
        background: transparent;
        color: #000000;
        font-size:35px;
        font-family: arial, helvetica, sans-serif;
        width:740px;
    }

    #borde{
        top:111px;
        left:26px;
        background-repeat:no-repeat;
        position:absolute;
    }
    #noticia{
        top:684px;
        left:23px;
        background-repeat:no-repeat;
        position:absolute;
    }
    #menu{
        top:217px;
        left:715px;
        background-repeat:no-repeat;
        position:absolute;
    }
    #logo{
        top:20px;
        left:710px;
        background-repeat:no-repeat;
        position:absolute;
    }
    .noticiavertical{
        font-size:35px;
    }
    #div_fecha_hora{
        top:80px;
        left:20px;
        position:absolute;
    }
    #hora{
        font-size:25px;
        background-color:transparent;
        border:0;
        color:#ffffff;
        text-align: center;
    }
    #fecha{
        font-size:25px;
        background-color:transparent;
        border:0;
        color:#ffffff;
        text-align: center;
    }

    #nombre_empresa{
        font-size:".$tl_pno."px;
        background-color:transparent;
        border:0;
        color:#".$cl_pno.";
        text-align: ".$al_pno.";
    }


    /*Inicio drag*/
    #div_logo_institucion{
        width: ".$wi_pli.";
        height: ".$he_pli.";
        top:".$yy_pli."px;
        left:".$xx_pli."px;
        position:absolute;
    }
    #div_empresa{
        width: 600px;
        height: 40px;
        padding: 0.5em;
        top:".$yy_pno."px;
        left:".$xx_pno."px;
        position:absolute;
    }

</style>

<div  id='mainContent' class='fondo'>
    <div id='div_fecha_hora'>
        <?php echo Tag::textField('fecha','size: 11', 'maxlength: 20','class:  numeric'); ?>
        <?php echo Tag::textField('hora','size: 11', 'maxlength: 20','class:  numeric'); ?>
    </div>

    <div class='media' id='media'></div> <!-- Div para el timbre -->

    <div id='borde'>
        <img src='../../img/borde.png' width='680px'>
    </div>

    <div id='logo'>
        <?php echo Tag::image('logo.png', 'alt: Imagen', 'width: 297px') ?>
    </div>

    <div id='noticia'>
        <?php echo Tag::image('noticia.png', 'alt: Imagen', 'height: 56px') ?>
    </div>

    <div id='menu'>
        <?php echo Tag::image('menu.png', 'alt: Imagen', 'width: 294px') ?>
    </div>

    <div id='midiv' style='top:140px; left:50px; position:absolute'></div> <!-- Div para el video -->

    
    <div id='t_actual' style='top:302px; left:720px; position:absolute'>
            <input type='text' id='t1' name='t1' value=' ' readonly='readonly'>
    </div>
    <div id='m_actual' style='top:302px; left:890px; position:absolute'>
        <input type='text' id='ta1' name='ta1' value=' ' readonly='readonly'>
    </div>

    <div id='t_anterior1' style='top:368px; left:720px; position:absolute'>
        <input type='text' id='t2' name='t2' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:180px'>
    </div>
    <div id='m_anterior1' style='top:368px; left:890px; position:absolute'>
        <input type='text' id='ta2' name='ta2' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:100px'>
    </div>

    <div id='t_anterior2' style='top:430px; left:720px; position:absolute'>
        <input id='t3' name='t3' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:180px'>
    </div>
    <div id='m_anterior2' style='top:430px; left:890px; position:absolute'>
        <input id='ta3' name='ta3' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:100px'>
    </div>

    <div id='t_anterior3' style='top:490px; left:720px; position:absolute'>
        <input id='t4' name='t4' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:180px' >
    </div>
    <div id='m_anterior3' style='top:490px; left:890px; position:absolute'>
        <input id='ta4' name='ta4' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:100px'>
    </div>

    <div id='t_anterior4' style='top:550px; left:720px; position:absolute'>
        <input id='t5' name='t5' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:180px'>
    </div>
    <div id='m_anterior4' style='top:550px; left:890px; position:absolute'>
        <input id='ta5' name='ta5' value=' ' readonly='readonly' style='border:0px;background: transparent;color: #ffffff;font-size: 44px;font-family: Impact;text-align: center;width:100px'>
    </div>

    <div id='logo_inferior' style='top:646px; left:822px; position:absolute'>
        <embed src='../../img/logoinferior.swf' width='180' height='92'> </embed>
    </div>

    <div style='top:690px; left:40px; position:absolute'>
        
    </div>

    <div id='div_logo_institucion' class='ui-widget-content' onmousemove='xy_logo_institucion()' onclick='ver_div_logo_institucion()'>
        <embed src='../../img/".$fl_pli.".swf' width='".$wi_pli."' height='".$he_pli."'> </embed>
    </div>


    <div id='div_empresa' class='ui-widget-content' onmousemove='xy_nombre()' onclick='ver_div_nombre()'>
        <center><input type='text' style='font-size:".$tl_pno."px; text-align:".$al_pno."; color=".$cl_pno."' value='".$no_pno."'></center>
    </div>


</div>";





        $id=1;
        $estilopantallaoperador = new Estilopantallaoperador();
        $estilopantallaoperador->setId($id);
        $estilopantallaoperador->setContenido($html);
        $estilopantallaoperador->setParametros($parametros);
        $estilopantallaoperador->save();
    //$estilopantallaoperador->setCreacionAt($creacionAt);
    //        if($estilopantallaoperador->save()==false) {
    //            Flash::error('Hubo un error guardando el registro.');
    //        }else {
    //            Flash::success('Registro guardado con Ã©xito.');
    //        }

//    $estilo= new Estilopantallaoperador();
//    $buscaEstilo= $estilo->findFirst();
//    echo $buscaEstilo->getContenido();

    //echo $html;
    }


}

