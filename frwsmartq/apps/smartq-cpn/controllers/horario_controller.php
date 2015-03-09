<?php

/**
 * Controlador Horario
 *
 * @access public
 * @version 1.0
 */
class HorarioController extends ApplicationController {

/**
 * id
 *
 * @var int
 */
    public $id;

    /**
     * nombre_horario
     *
     * @var string
     */
    public $nombreHorario;

    /**
     * descripcion_horario
     *
     * @var string
     */
    public $descripcionHorario;

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
        $columnsGrid[]=array('name'=>'Nombre','index'=>'nombre_horario');
        $columnsGrid[]=array('name'=>'Descripción','index'=>'descripcion_horario');
        Tag::setColumnsToGrid($columnsGrid);
    }

    /**
     * Crear un Horario/
     *
     */
    public function nuevoAction() {

    }

    /**
     * Editar el Horario
     *
     */
    public function editarAction($id=null) {

        $filter = new Filter();
        $id = $filter->applyFilter($id,"int");
        $horario = $this->Horario->findFirst($id);
        if($horario) {
            Tag::displayTo('id', $horario->getId());
            Tag::displayTo('nombre_horario', $horario->getNombreHorario());
            Tag::displayTo('descripcion_horario', $horario->getDescripcionHorario());

            $array_dias_semana= array (1=>"Lunes",2=>"Martes",3=>"Miércoles",4=>"Jueves",5=>"Viernes",6=>"Sábado",7=>"Domingo");
            $detallehorario= new Detallehorario();
            $buscaDetallehorario= $detallehorario->find("conditions: horario_id=$id");
            foreach ($buscaDetallehorario as $result) {
                $detallehorario_id=$result->getId();
                $dia=$result->getDia();
                foreach ($array_dias_semana as $key=>$valor) {
                    if ($valor==$dia)
                        $dia_id=$key;
                }
                //Tag::displayTo("chk_dia_semana[]", 1);
                Tag::displayTo('hora_i1_'.$dia_id, $result->getHoraInicial1());
                Tag::displayTo('hora_f1_'.$dia_id, $result->getHoraFinal1());
                Tag::displayTo('hora_i2_'.$dia_id, $result->getHoraInicial2());
                Tag::displayTo('hora_f2_'.$dia_id, $result->getHoraFinal2());
                Tag::displayTo('hora_i3_'.$dia_id, $result->getHoraInicial3());
                Tag::displayTo('hora_f3_'.$dia_id, $result->getHoraFinal3());
                Tag::displayTo('horas_laborables_'.$dia_id, $result->getHorasLaborables());
            }
            Tag::displayTo('creacion_at', $horario->getCreacionAt());
        }else {
            Flash::error('Registro no encontrado.');
            $this->routeTo('action: index');
        }
    }

    /**
     * Guardar el Horario
     *
     */
    public function guardarAction($isEdit=false,$id=null) {

        $nombreHorario = $this->getPostParam("nombre_horario", "striptags", "extraspaces");
        $descripcionHorario = $this->getPostParam("descripcion_horario", "striptags", "extraspaces");
        $creacionAt = $this->getPostParam("creacion_at");
        $horario = new Horario();
        $horario->setId($id);
        $horario->setNombreHorario($nombreHorario);
        $horario->setDescripcionHorario($descripcionHorario);
        $horario->setCreacionAt($creacionAt);

        $action=($isEdit==true) ? "editar" : 'nuevo';
        if($horario->save()==false) {
            Flash::error('Hubo un error guardando el registro.');
        }else {
            $detallehorario= new Detallehorario();
            if($isEdit==false)      //por nuevo horario
                $id= $horario->maximum("id");
            else      //por nuevo horario
                $detallehorario->deleteAll("horario_id=$id");
            $chk_dias_semana= $this->getPostParam("chk_dia_semana");
            $array_dias_semana= array (1=>"Lunes",2=>"Martes",3=>"Miércoles",4=>"Jueves",5=>"Viernes",6=>"Sábado",7=>"Domingo");

            foreach($chk_dias_semana as $valor) {
                $detallehorario->setId(null);
                $detallehorario->setHorarioId($id);
                $detallehorario->setDia($array_dias_semana[$valor]);
                $hora_inicial1 = $this->getPostParam("hora_i1_".$valor, "striptags", "extraspaces");
                $hora_final1 = $this->getPostParam("hora_f1_".$valor, "striptags", "extraspaces");
                $hora_inicial2 = $this->getPostParam("hora_i2_".$valor, "striptags", "extraspaces");
                $hora_final2 = $this->getPostParam("hora_f2_".$valor, "striptags", "extraspaces");
                $hora_inicial3 = $this->getPostParam("hora_i3_".$valor, "striptags", "extraspaces");
                $hora_final3 = $this->getPostParam("hora_f3_".$valor, "striptags", "extraspaces");
                $horas_laborables = $this->getPostParam("horas_laborables_".$valor, "striptags", "extraspaces");
                $detallehorario->setHoraInicial1($hora_inicial1);
                $detallehorario->setHoraFinal1($hora_final1);
                $detallehorario->setHoraInicial2($hora_inicial2);
                $detallehorario->setHoraFinal2($hora_final2);
                $detallehorario->setHoraInicial3($hora_inicial3);
                $detallehorario->setHoraFinal3($hora_final3);
                $detallehorario->setHorasLaborables($horas_laborables);
                $detallehorario->save();
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
    }

    /**
     * Eliminar el Horario
     *
     */
    public function eliminarAction() {

        $this->setResponse('ajax');
        $msg='';
        if($this->getPostParam('oper')=='del') {
            $ids=explode(',',$this->getPostParam('id'));
            for($i=0;$i<count($ids);$i++) {
                if(!$this->Horario->delete($ids[$i])) {
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
                    case 'nombre_horario':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                    case 'descripcion_horario':
                        $condicion.=' AND '.Utils::toSqlParamSearchGrid($k,'bw',$v);break;
                }
            }
        }
        $orden="$col_orden $dir_orden";  //construimos la cadena de orden
        //comparar que el grupo no este en grupo_usuario
        $contar =$this->Horario->count("conditions: $condicion");  //contar el numero total de registros existentes
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
        $resultado=$this->Horario->find("conditions: $condicion","order: $orden","limit: $limite");
        //construimos el resultado para el grid como objeto
        $jqgrid=null;
        @$jqgrid->page = $pagina;   //pagina de navegacion actual
        @$jqgrid->total = $total_pags; // numero total de paginas en base al numero de registros obtenidos
        @$jqgrid->records = $contar;  // numero total de registros obtenidos
        if($limite>$contar)$limite=$contar;
        for($i=$inicio;$i<$limite;$i++) {
            $Horario=$resultado[$i];
            $jqgrid->rows[]=array('id'=>$Horario->getId(),'cell'=>array($Horario->getNombreHorario(),$Horario->getDescripcionHorario()));
        }
        //impresion de la respuesta formato json
        echo $this->jsonEncode($jqgrid);
    }

    /*
     * Permite ver la vista previa del horario
     */
    public function verHorarioAction() {
        $horario_id=$this->getPostParam('horario_id');
        $html="";
        $html.="<fieldset class='ui-corner-all ui-widget-content'>
        <legend><b>Días de la semana</b></legend>
        <table id='tbl_semana' border='1' class='empty'>
           <tr>
               <th align='left'>Día de la semana</th>
               <th>Desde</th>
               <th>Hasta</th>
               <th>Horas laborables</th>
           </tr>";
        $condicion = "{#Horario}.id = $horario_id";
        $order= "{#Horario}.id ASC";
        $query = new ActiveRecordJoin(array(
            "entities" => array("Horario", "Detallehorario"),
            "fields" => array(
            "{#Detallehorario}.dia",
            "{#Detallehorario}.hora_inicial1",
            "{#Detallehorario}.hora_final1",
            "{#Detallehorario}.hora_inicial2",
            "{#Detallehorario}.hora_final2",
            "{#Detallehorario}.hora_inicial3",
            "{#Detallehorario}.hora_final3",
            "{#Detallehorario}.horas_laborables"),
            "conditions" => $condicion,
            "order" => $order
        ));
        foreach($query->getResultSet() as $result) {
            $html.= "<tr><td align='center'>".$result->getDia()."</td>";
            $html.= "<td colspan='2' align='right'>".$result->getHoraInicial1()." - ".$result->getHoraFinal1()."<br>".
                $result->getHoraInicial2()." - ".$result->getHoraFinal2()."<br>".
                $result->getHoraInicial3()." - ".$result->getHoraFinal3()."</td>";
            $html.= "<td align='center'>".$result->getHorasLaborables()."</td></tr>";
        }
        $html.="</table>";
        $html.="</fieldset>";
        echo $html;
    }
}