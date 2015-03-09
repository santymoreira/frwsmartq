<?php
class Reportes_sucursalesController extends ApplicationController {

    public $lista_cajas=array();
    public $lista_modulos=array();
    public $lista_servicios=array();
    public $lista_grupo_servicios=array();
    public $lista_modulos_tad=array();  //cuadro de turnos atendidos por dias
    public $lista_servicios_tad=array();//cuadro de turnos atendidos por dias
    public $lista_modulos_xls=array(); //modulos xls
    public $lista_servicios_xls=array(); //modulos xls


    public $id;
    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        //$this->setTemplateAfter("menu");
    }

    public function indexAction() {
    }

    /*
     * Permite listas los servicios que corresponden al modulo
    */
    public function listaServiciosAction() {
        $this->setResponse('ajax');
        $lista_servicios=array();
        $id_modulo=$this->getPostParam('id');
        $lista_servicios[0]="Todos";
        $condicion="{#Caja}.id=$id_modulo";
        $query=new ActiveRecordJoin(array(
                        "entities"=>array("Caja","Servicio","Serviciocaja"),
                        "fields"=>array("{#Servicio}.id","{#Servicio}.nombre"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista_servicios[$result->getId()]=$result->getNombre();
        }
        echo "<fieldset class='ui-corner-all ui-widget-content'>
            <legend><b>Servicios</b></legend>
            <p><label class='labelform' ><span for='usuarios'>Seleccione el servicio:</span></label>".Tag::selectStatic("servicios", $lista_servicios,"class:  required")."</p>
        </fieldset>";
    }

    /*
     * Inicio para el reporte de calificaciones por mÛdulos
    */
    public $lista_modulos_calif=array();
    public $lista_servicios_calif=array();
    public $lista_preguntas_calif=array();
    public function calificacionesAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        Tag::displayTo('hora_i', "08:00:00");
        Tag::displayTo('hora_f', "17:00:00");
        //INICIO LISTA DE M”DULOS
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        $condicion="grupousuario.grupo_id=5";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $this->lista_modulos_calif[$key]=$val;
        }
        //FIN LISTA DE M”DULOS

        //INICIO LISTA DE SERVICIO
        $servicio=new Servicio();
        $buscaServicios=$servicio->find("conditions: estado= 1","order: nombre");
        foreach($buscaServicios as $result) {
            $this->lista_servicios_calif[$result->getId()]=$result->getNombre();
        }
        //FIN LISTA DE SERVICIO

        //INICIO LISTA DE PREGUNTAS
        $preguntas=new Preguntas();
        $buscaPreguntas=$preguntas->find("conditions: publicar= 1","order: orden");
        foreach($buscaPreguntas as $result) {
            $this->lista_preguntas_calif[$result->getId()]=$result->getNomPregunta();
        }
        //FIN LISTA DE PREGUNTAS
    }

    /*
     * Inicio para el reporte de los turnos emitidos por el dispensador
    */
    public $lista_servicios_te;
    public $lista_grupo_servicios_te;
    public function turnosEmitidosAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //$lista_servicios[0]="Todos"; //Para combo si sirve
        $servicios= new Servicio();
        $buscaServicios= $servicios->find('conditions: estado=1');
        foreach ($buscaServicios as $result) {
            $this->lista_servicios_te[$result->getId()]=$result->getNombre();
        }
        $grupo_servicios= new Gruposervicio();
        $buscaGrupoServicios= $grupo_servicios->find();
        foreach ($buscaGrupoServicios as $result) {
            $this->lista_grupo_servicios_te[$result->getId()]=$result->getNombreGrupoServicio();
        }
    }
    /*
     * Permite listas los servicios o los grupos de servicios seg˙n la opciÛn seleccionada en turnosEmitidos.html
    */
    public function listaOpcionAction() {
        $this->setResponse('ajax');
        $lista_servicios=array();
        $opcion=$this->getPostParam('opcion');  //Si es por servicios o por Grupo de servicios
        $html="";
        if ($opcion==1) { //mostrar todos los servicios
            //            $html.="<fieldset class='i-corner-all ui-widget-content'>";
            //            $html.="<legend><b>Servicios</b></legend>";
            //            foreach($this->lista_servicios as $key=> $val)
            //                $html.= "   *".$val.Tag::checkboxField("chkservicios[]", "value: $key","checked: checked");
            //            $html.="</fieldset>";

            $html.="<fieldset class='ui-corner-all ui-widget-content'>
        <legend><b>Servicios</b></legend>";
            $col=0;
            $array_valores=array();
            foreach($this->lista_servicios_te as $key=> $val) {
                $array_valores[]=Tag::checkboxField("chkservicios[]", "value: $key","checked: ").$val."&nbsp;&nbsp;";
            }
            $html.="<table>";

            $cont_mod=count($this->lista_servicios_te);
            $cont_filas=ceil($cont_mod/4);
            $x=$cont_filas*4;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) {
                $html.= "<tr>";
                for ($c=1;$c<=4;$c++) {
                    $html.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html.= "</tr>";
            }
            $html.= "</table>";
            $html.="</fieldset>";

        } else if ($opcion==2) {
//                $html.="<fieldset class='i-corner-all ui-widget-content'>";
//                $html.="<legend><b>Grupo de Servicios</b></legend>";
//                foreach($this->lista_grupo_servicios as $key=> $val)
//                    $html.= "   *".$val.Tag::checkboxField("chkgruposervicios[]", "value: $key","checked: checked");
//                $html.="</fieldset>";


            $html.="<fieldset class='ui-corner-all ui-widget-content'>
        <legend><b>Servicios</b></legend>";
            $col=0;
            $array_valores=array();
            foreach($this->lista_grupo_servicios_te as $key=> $val) {
                $array_valores[]=Tag::checkboxField("chkgruposervicios[]", "value: $key","checked: ").$val."&nbsp;&nbsp;";
            }
            $html.="<table>";

            $cont_mod=count($this->lista_grupo_servicios_te);
            $cont_filas=ceil($cont_mod/4);
            $x=$cont_filas*4;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) {
                $html.= "<tr>";
                for ($c=1;$c<=4;$c++) {
                    $html.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html.= "</tr>";
            }
            $html.= "</table>";
            $html.="</fieldset>";
        }
        echo $html;
    }

    /*
     * Reporte de turnos emitidos por el turnero
    */
    public function verCuadroTurnosEmitidosAction() {
        $this->setResponse('ajax');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $lista_servicios=$this->getPostParam('chkservicios');
        $lista_grupo_servicios=$this->getPostParam('chkgruposervicios');

        $arrayServicios= array();
        $servicios= new Servicio();

        if (!empty($lista_servicios)) {
            foreach ($lista_servicios as $key) {
                $buscaServicios= $servicios->find("conditions: estado=1 AND id= $key");
                foreach ($buscaServicios as $result) {
                    $arrayServicios[$result->getId()]=$result->getNombre();
                }
            }
        }
        if (!empty($lista_grupo_servicios)) {
            foreach ($lista_grupo_servicios as $key) {
                $buscaServicios= $servicios->find("conditions: estado=1 AND gruposervicio_id= $key");
                foreach ($buscaServicios as $result) {
                    $arrayServicios[$result->getId()]=$result->getNombre();
                }
            }
        }
        /*$buscaServicios= $servicios->find('conditions: estado=1');
        foreach ($buscaServicios as $result){
            $arrayServicios[$result->getId()]=$result->getNombre();
        }*/


        //Inicio c·lculo de dias entre fechas
        $s = strtotime($hasta)-strtotime($desde);
        $d = intval($s/86400) + 1;
        $fun= new Funciones();
        $reportes= new ReportesSucursales();
        //fin calculo de dias entre fechas

        $html="";
        //INICIO ENCABEZADO
        $html="<center><h2>Reporte de Turnos Emitidos por el Dispensador<br>Desde $desde hasta $hasta</h2></center>";
        //FIN ENCABEZADO
        $html.="<script type='text/javascript' src='../../js/ventana_secundaria.js'></script>";
        $html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $html.="<table align='center'>";

        $array_encabezado_servicio=array(
                'TE'=>array('etiqueta'=>'TE','titulo'=>'Turnos Emitidos'),
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos anulados'),
                'Tn'=>array('etiqueta'=>'Tn','titulo'=>'Turnos no timbrados')
        );
        $cont_encabezado=count($array_encabezado_servicio);

        $html.="<tr>";
        $html.="<th style='$fondo_titulo'>Fecha</th>";
        $html.="<th style='$fondo_titulo'>Dia</th>";
        $html.="<th style='$fondo_titulo'>Primer Turno</th>";
        $html.="<th style='$fondo_titulo'>Ultimo Turno</th>";
        foreach ($arrayServicios as $nom_servicio) {
            $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$nom_servicio</th>";
        }
        $html.="<th style='$fondo_titulo' title='Total Turnos Emitidos'>TTE</th>";
        $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
        $html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
        $html.="<th style='$fondo_titulo' title='Total Turnos no timbrados'>TTn</th>";
        $html.="</tr>";

        $html.="<tr>";
        $html.="<th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>";
        foreach ($arrayServicios as $nom_servicio) {
            foreach ($array_encabezado_servicio as $valor)
                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="</tr>";
        $grantotal_turnos_emitidos=0;
        $grantotal_turnos_atendidos=0;
        $grantotal_turnos_anulados=0;
        $grantotal_turnos_no_timbrados=0;
        $suma_totales_turnos_emitidos=array();

        //INICIO INICIALIZAR ARRAY PARA SUMA DE CADA SERVICIO
        foreach ($arrayServicios as $key=>$nom_servicio) {
            $suma_totales_turnos_emitidos[$key]=0;
            $suma_totales_turnos_atendidos[$key]=0;
            $suma_totales_turnos_anulados[$key]=0;
            $suma_totales_turnos_no_timbrados[$key]=0;
        }
        //FIN INICIALIZAR ARRAY PARA SUMA DE CADA SERVICIO
        $forma_array=array();
        $fecha= $desde;
        $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
        for ($i=1;$i<=$d;$i++) {
            $fecha= date("Y-m-d", strtotime( "$fecha + 1 day"));
            $separa = explode('-',$fecha);
            $mes = $separa[1];
            $dia = $separa[2];
            $ano = $separa[0];
            $dia= $fun->dia_semana($dia, $mes, $ano);
            if ($dia!='S&aacute;bado' & $dia!='Domingo') {
                $subtotal_turnos_emitidos=0;
                $subtotal_turnos_atendidos=0;
                $subtotal_turnos_anulados=0;
                $subtotal_turnos_no_timbrados=0;
                $html.="<tr>";
                $html.="<td style='background-color:#e5f1f4;'>$fecha</td>";
                $html.="<td style='background-color:#e5f1f4;'>$dia</td>"; //dia
                $primer_turno= $reportes->primerTurno($fecha);
                $ultimo_turno= $reportes->ultimoTurno($fecha);
                $html.="<td style='background-color:#e5f1f4;'>$primer_turno</td>";
                $html.="<td style='background-color:#e5f1f4;'>$ultimo_turno</td>";
                //INICIO DATOS POR SERVICIO
                $forma_array_fila=array();
                foreach ($arrayServicios as $key=>$nom_servicio) {
                    $id_servicio= $key;
                    //$turnos_emitidos=0;
                    $turnos_emitidos= $reportes->turnosEmitidos($id_servicio, $fecha);
                    $turnos_atendidos= $reportes->turnosAtendidos($id_servicio, $fecha);
                    $turnos_anulados= $reportes->turnosAnulados($id_servicio, $fecha);
                    $turnos_no_timbrados= $reportes->turnosNoTimbrados($id_servicio, $fecha);
                    $html.="<td align='right' style='background-color:#FEE1BA;'>$turnos_emitidos</td>";
                    $html.="<td align='right' style='background-color:#e5f1f4;'>$turnos_atendidos</td>";
                    $html.="<td align='right' style='background-color:#e5f1f4;'>$turnos_anulados</td>";
                    $html.="<td align='right' style='background-color:#e5f1f4;'>$turnos_no_timbrados</td>";
                    $subtotal_turnos_emitidos+=$turnos_emitidos;
                    $subtotal_turnos_atendidos+=$turnos_atendidos;
                    $subtotal_turnos_anulados+=$turnos_anulados;
                    $subtotal_turnos_no_timbrados+=$turnos_no_timbrados;
                    $suma_totales_turnos_emitidos[$key]=$suma_totales_turnos_emitidos[$key]+$turnos_emitidos;
                    $suma_totales_turnos_atendidos[$key]=$suma_totales_turnos_atendidos[$key]+$turnos_atendidos;
                    $suma_totales_turnos_anulados[$key]=$suma_totales_turnos_anulados[$key]+$turnos_anulados;
                    $suma_totales_turnos_no_timbrados[$key]=$suma_totales_turnos_no_timbrados[$key]+$turnos_no_timbrados;
                    $forma_array_fila[$key]=$turnos_emitidos;
                }
                //FIN DATOS POR SERVICIO
                $html.="<td align='right' style='background-color:#bce774;'>$subtotal_turnos_emitidos</td>";
                $html.="<td align='right' style='background-color:#e5f1f4;'>$subtotal_turnos_atendidos</td>";
                $html.="<td align='right' style='background-color:#e5f1f4;'>$subtotal_turnos_anulados</td>";
                $html.="<td align='right' style='background-color:#e5f1f4;'>$subtotal_turnos_no_timbrados</td>";
                $html.="</tr>";
                $forma_array[$fecha]=$forma_array_fila;
            }
        }
        //FORMAR TOTALES EN LA ULTIMA FILA
        $html.="<tr>";
        $html.="<td style='background-color:#e5f1f4;'></td>";
        $html.="<td style='background-color:#e5f1f4;'></td>";
        $html.="<td style='background-color:#e5f1f4;'></td>";
        $html.="<td style='background-color:#e5f1f4;'></td>";
        foreach ($suma_totales_turnos_emitidos as $key=>$valor) {
            $html.="<td align='right' style='background-color:#FEE1BA;'><b>$valor</b></td>";
            $html.="<td align='right' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_atendidos[$key]</b></td>";
            $html.="<td align='right' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_anulados[$key]</b></td>";
            $html.="<td align='right' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_no_timbrados[$key]</b></td>";
            $grantotal_turnos_emitidos+=$suma_totales_turnos_emitidos[$key];
            $grantotal_turnos_atendidos+=$suma_totales_turnos_atendidos[$key];
            $grantotal_turnos_anulados+=$suma_totales_turnos_anulados[$key];
            $grantotal_turnos_no_timbrados+=$suma_totales_turnos_no_timbrados[$key];
        }
        $html.="<td align='right' style='background-color:#bce774;'><b>$grantotal_turnos_emitidos</b></td>";
        $html.="<td align='right' style='background-color:#e5f1f4;'><b>$grantotal_turnos_atendidos</b></td>";
        $html.="<td align='right' style='background-color:#e5f1f4;'><b>$grantotal_turnos_anulados</b></td>";
        $html.="<td align='right' style='background-color:#e5f1f4;'><b>$grantotal_turnos_no_timbrados</b></td>";
        $html.="</tr>";
        $mi_array= $forma_array;
        $compactada=serialize($mi_array);
        $compactada=urlencode($compactada);
        //$html.="<a href=javascript:ventanaSecundaria('verGraficoTurnosEmitidos?array=".$compactada."')>Graficar Todos</a>";
        echo $html;
    }
    /*
     * Graficar Todos servicios por fechas
     * recibe un array multidimencional
     * NOTA: no utilizo porque no se puede formar el array multidimencional
    */
    public function verGraficoTurnosEmitidosAction() {
        $array=$_GET['array'];
        $tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);

        $html= "";
        $html.=" <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>";
        $html.="<html>";
        $html.="<head>";
        $html.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        $html.="<title>Highcharts Example</title>";


        $html.="<!-- 1. Add these JavaScript inclusions in the head of your page -->";
        $html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='../../js/highcharts.js'></script>";

        $html.="<!-- 1a) Optional: the exporting module -->";
        $html.="<script type='text/javascript' src='../../js/modules/exporting.js'></script>";


        $html.="<!-- 2. Add the JavaScript to initialize the chart on document ready -->";
        $html.="<script type='text/javascript'>";

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [";
        /*$html.="{";
						$html.="name: 'Total turnos',";
						//$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie."]";
                                                $html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
					$html.="},";*/
        /*$html.="{";
						$html.="name: 'Promedio',";
                                                //$html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="},";*/

        foreach ($tmp as $indice1=> $valor) {
            $html.="{";
            $html.="name: 'Mod. $indice1',";
            $html.="data: [";
            //echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                //echo $indice2." = ".$valor2."<br>";
                $html.="$valor2,";
            }
            $html.="]";
            $html.="},";
        }

        $html.="]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="</head>";
        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 800px; height: 600px; margin: 0 auto'></div>";


        $html.="</body>";
        $html.="</html>";

        echo $html;
    }

    /*
     * Funcion que retorna los modulos y servicios de la sucursal seleccionada
    */
    public function obtenerModulosServiciosAction() {
        $this->setResponse('json');

        $sucursal_id=   $this->getPostParam('sucursal_id');
        $db = DbBase::rawConnect();

        $html_modulos="";
        $html_servicios="";

        $result = $db->query("select numero_modulo, modulo_id_sucursal from sincturnos WHERE sucursal_id=$sucursal_id group by numero_modulo;");
        $col=0;
        $array_valores=array();
        while($row = $db->fetchArray($result)) {
            $numero_modulo      =$row['numero_modulo'];
            $modulo_id_sucursal =$row['modulo_id_sucursal'];
            $array_valores[]=Tag::checkboxField("chkmodulos[]", "value: $modulo_id_sucursal","checked: ")."M√≥dulo ".$numero_modulo."&nbsp;&nbsp;";
        }
        //$html_modulos.= "<input id='chk_all_modulos' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
        $html_modulos.= "<table>";
        $cont_mod=count($array_valores);
        $cont_filas=ceil($cont_mod/5);
        $x=$cont_filas*5;
        $y=$x-$cont_mod;
        for ($z=1;$z<=$y;$z++)
            $array_valores[]="";
        $cont_key=0;
        for ($f=1; $f<=$cont_filas; $f++) { //filas
            $html_modulos.= "<tr>";
            for ($c=1;$c<=5;$c++) { //columnas
                $html_modulos.= "<td>".$array_valores[$cont_key]."</td>";
                $cont_key+=1;
            }
            $html_modulos.= "</tr>";
        }
        $html_modulos.= "</table>";



        $result = $db->query("SELECT nombre_servicio, servicio_id_sucursal FROM sincturnos WHERE sucursal_id=$sucursal_id GROUP BY nombre_servicio");
        $col=0;
        $array_valores=array();
        while($row = $db->fetchArray($result)) {
            $nombre_servicio     =$row['nombre_servicio'];
            $servicio_id_sucursal =$row['servicio_id_sucursal'];
            $array_valores[]=Tag::checkboxField("chkservicios[]", "value: $servicio_id_sucursal","checked: ").$nombre_servicio."&nbsp;&nbsp;";
        }
        //$html_servicios.= "<input id='chk_all_servicios' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
        $html_servicios.= "<table>";
        $cont_mod=count($array_valores);
        $cont_filas=ceil($cont_mod/5);
        $x=$cont_filas*5;
        $y=$x-$cont_mod;
        for ($z=1;$z<=$y;$z++)
            $array_valores[]="";
        $cont_key=0;
        for ($f=1; $f<=$cont_filas; $f++) { //filas
            $html_servicios.= "<tr>";
            for ($c=1;$c<=5;$c++) { //columnas
                $html_servicios.= "<td>".$array_valores[$cont_key]."</td>";
                $cont_key+=1;
            }
            $html_servicios.= "</tr>";
        }
        $html_servicios.= "</table>";

        $datos= array("modulos"=>$html_modulos, "servicios"=>$html_servicios);
        return ($datos);
    }

    /*
     * Funcion que retorna el cuadro de total turnos atendidos por dias
     * Menu: Cuadro de turnos por dias
    */
    public function verCuadroAction() {
        $this->setResponse('ajax');
        $desde      =$this->getPostParam('desde');
        $hasta      =$this->getPostParam('hasta');
        $sucursal_id=$this->getPostParam('sucursal_id');

        $lista_modulos=$this->getPostParam('chkmodulos');
        $lista_servicios=$this->getPostParam('chkservicios');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_condicion_servicios="";
        $html="";
        if (!empty($lista_servicios) & !empty($lista_modulos)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }

        if (!empty($lista_modulos) & !empty($lista_servicios) ) {
            $array_dias_semana = array('Lunes','Martes','Mi√©rcoles','Jueves','Viernes','S√°bado','Domingo');
            $array_encabezado_dia=array(
                    'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                    'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                    'DT'=>array('etiqueta'=>'DT','titulo'=>'Duraci√≥n Total'),
                    'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atenci√≥n'),
                    'Pll'=>array('etiqueta'=>'Pll','titulo'=>'Promedio de Llamada a Atenci√≥n'),
                    'ES'=>array('etiqueta'=>'','titulo'=>'',)
            );
            $cont_encabezado=count($array_encabezado_dia);
            $fondo_titulo= "background-color:#328aa4; color:#fff";

            //INICIO SEPARAR POR SEMANAS
            $s = strtotime($hasta)-strtotime($desde);
            $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas

            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;   //solo para q empiece desde un dÌa menos

            $forma_array_semanas= array();
            $array_aux= array();
            $aux="";

            $dias= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');

            for ($i=1;$i<=$d;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ; //a la fecha anterior aumenta un dÌa
                $separa = explode("-",$fecha);
                $anio = $separa[0];
                $mes= $separa[1];
                $dia = $separa[2];
                $num_semana = date('W', mktime(0, 0, 0, $mes, $dia, $anio));
                $pru=strtoupper($dias[intval((date("w",mktime(0,0,0,$mes,$dia,$anio))))]);  //obtener el dia de la semana
                //$forma_array_semanas[$num_semana][$pru]=$dia;   //forma el array milti con el numero de semana y los dias
                $forma_array_semanas[$num_semana][$pru]="$anio-$mes-$dia";   //forma el array milti con el numero de semana y los dias
                //echo "num semana: ".$num_semana."<br>";
            }

            /*foreach($forma_array as $key=>$val) {
                echo $key;  //numero de semana
                foreach($val as $key2=>$val2 ) {    //$key2= dia de la semana
                    echo $key2." ".$val2." ";
                }
                echo "<br>";
            }*/
            //FIN SEPARAR POR SEMANAS

            //INICIO ENCABEZADO

            $html="<center><h2>Cuadro de turnos atentidos, anulados, promedios<br>Desde $desde hasta $hasta</h2></center>";
            $html.="<table align='center'>";
            $html.="<tr><td><table>
            <tr><td><b>Turnos con duraci√≥n a partir de:</b></td><td align='right'>$forma_duracion</td></tr>
            </table></tr></td>";
            $html.="<script type='text/javascript' src='../../js/ventana_secundaria.js'></script>";
            $html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
            //FIN ENCABEZADO


            foreach($forma_array_semanas as $key=>$val) {
                $desde = reset( $val );
                $hasta = end( $val );

                $html.="<tr><td align='right' style='background-color:#FEE1BA;'>
                Semana desde $desde hasta $hasta
                </td></tr>";

                $html.="<tr><td style='background-color:#fff;'>";


                $html.="<table align='center'>";
                $html.="<tr>";
                $html.="<th style='$fondo_titulo'>M√≥dulo</th>";
                $html.="<th style='$fondo_titulo'>Usuario</th>";
                //$html.="<th style='background-color:#328aa4; color:#fff'></th>";
                foreach ($array_dias_semana as $dia) {
                    $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$dia</th>";
                }
                $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
                $html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
                $html.="<th style='$fondo_titulo' title='Promedio de Atenci√≥n Lunes a Viernes'>PA</th>";
                $html.="<th style='$fondo_titulo' title='Promedio de Llamada Lunes a Viernes'>PLL</th>";
                $html.="<th style='$fondo_titulo'></th>";
                $html.="</tr>";

                $html.="<tr>";
                $html.="<th style='$fondo_titulo'></th>
                        <th style='$fondo_titulo'></th>";
                foreach ($array_dias_semana as $dia) {
                    foreach ($array_encabezado_dia as $valor)
                        $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
                }
                $html.="<th style='$fondo_titulo'></th>";
                $html.="<th style='$fondo_titulo'></th>";
                $html.="<th style='$fondo_titulo'></th>";
                $html.="<th style='$fondo_titulo'></th>";
                $html.="<th style='$fondo_titulo'></th>";
                $html.="</tr>";

                //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
                //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
                //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";

                //DURACION
                //SELECT duracion FROM caja c, turnos t, servicio s, usercaja uc, usuario u
                //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
                //AND DAYNAME(fecha_emision)='Monday' AND fecha_inicio_atencion BETWEEN '2010-11-15' AND '2010-11-24' AND c.id= 38 AND atendido=1 AND rechazado=0;
                $suma_totales_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0,'Sunday'=>0);
                $suma_totales_turnos_anulados=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0,'Sunday'=>0);
                $suma_duracion_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0,'Sunday'=>0);
                $suma_promedio_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0,'Sunday'=>0);
                $suma_promedio_turnos_llamada=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0,'Sunday'=>0);
                $grantotal_turnos_atendidos=0;
                $grantotal_turnos_anulados=0;
                $gran_promedio_atencion=0;
                $gran_promedio_llamada=0;
                $forma_array=array();
                foreach($lista_modulos as $modulo_id_sucursal) {
//                    $condicion1="grupousuario.grupo_id=5 AND caja.id=$id_c";
//                    $query = new ActiveRecordJoin(array(
//                                    "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
//                                    "fields" => array(
//                                            "{#Caja}.id",
//                                            "{#Caja}.numero_caja",
//                                            "{#Usuario}.nombres"),
//                                    "conditions" => $condicion1,
//                                    "order"=>"{#Caja}.numero_caja"
//                    ));
//                    foreach($query->getResultSet() as $result) {
//                        $id_caja=$result->getId();

//                    }

                    $num_modulo =1;
                    $usuario    =2;

                    $reportes = new ReportesSucursales();
                    $fun= new Funciones();
                    $condicion="fecha_emision BETWEEN '$desde' AND '$hasta' AND modulo_id_sucursal= $modulo_id_sucursal AND atendido=1 AND rechazado=0 AND servicio_id_sucursal IN ($forma_condicion_servicios) AND duracion>='$forma_duracion' AND sucursal_id=$sucursal_id" ;
                    $condicion_anulados="fecha_emision BETWEEN '$desde' AND '$hasta' AND modulo_id_sucursal= $modulo_id_sucursal AND (rechazado=1 OR atendido=0) AND servicio_id_sucursal IN ($forma_condicion_servicios) AND sucursal_id=$sucursal_id" ;
                    $html.="<tr>";
                    $html.="<td align='center' style='background-color:#e5f1f4;'>".$num_modulo."</td>";
                    $html.="<td align='left' style='background-color:#e5f1f4;'>".$usuario."</td>";
                    //$html.="<td align='center' style='background-color:#e5f1f4;'>".Tag::checkboxField("chkmodulo[]", "value: $id_caja", "checked: ")."</td>";
                    $totales_turnos_atendidos= array(); //sirve para graficar
                    $tta=0;
                    $dias_semana= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
                    $subtotal_total_turnos_atendidos=0;
                    $subtotal_total_turnos_anulados=0;
                    $subtotal_en_segudos_prom_atencion=0;
                    $subtotal_en_segundos_prom_llamada=0;
                    foreach ($dias_semana as $dia) {
                        $duracion_total=0;
                        $duracion_promedio=0;
                        $llamada_promedio=0;
                        $tot_t_atendidos_x_dia= $reportes->totalTurnosAtendidosPorDia($dia, $condicion);
                        $tot_t_anulados_x_dia= $reportes->totalTurnosAnuladosPorDia($dia, $condicion_anulados);
                        //$tot_t_no_atendidos_x_dia= $reportes->totalTurnosNoAtendidosPorDia($dia, $condicion_no_atendidos); //(NULL)
                        list($cont_reg,$total_segundos) = $reportes->duracionAtencion($dia, $condicion);
                        list($cont_reg_llamada,$total_segundos_llamada) = $reportes->promedioLlamada($dia, $condicion);
                        if ($tot_t_atendidos_x_dia != 0) {
                            $duracion_total=$fun->tiempo($total_segundos);
                            $duracion_promedio=$fun->tiempo(round($total_segundos/$cont_reg));
                            $llamada_promedio=$fun->tiempo(round($total_segundos_llamada/$cont_reg_llamada));
                            $suma_totales_turnos_atendidos[$dia]=$suma_totales_turnos_atendidos[$dia]+$tot_t_atendidos_x_dia;
                            $suma_totales_turnos_anulados[$dia]=$suma_totales_turnos_anulados[$dia]+$tot_t_anulados_x_dia;
                            $suma_duracion_turnos_atendidos[$dia]=$suma_duracion_turnos_atendidos[$dia]+$total_segundos;
                            $suma_promedio_turnos_atendidos[$dia]=$suma_promedio_turnos_atendidos[$dia]+round($total_segundos/$cont_reg);
                            $suma_promedio_turnos_llamada[$dia]=$suma_promedio_turnos_llamada[$dia]+round($total_segundos_llamada/$cont_reg_llamada);

                            $subtotal_en_segudos_prom_atencion+=$total_segundos/$cont_reg;
                            $subtotal_en_segundos_prom_llamada+=$total_segundos_llamada/$cont_reg_llamada;
                        }
                        $html.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_dia."</td>";
                        $html.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_dia."</td>";
                        $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
                        $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
                        $html.="<td align='right' style='background-color:#e5f1f4;'>".$llamada_promedio."</td>";
                        //$html.="<td style='background-color:#e5f1f4;'><a title='Graficar este dia' href=javascript:ventanaSecundaria('verGraficoHoras?id_modulo=".$id_caja."&dia=".$dia."&ttat=".$tot_t_atendidos_x_dia."&ttan=".$tot_t_anulados_x_dia."&dt=".$duracion_total."&pa=".$duracion_promedio."&desde=".$desde."&hasta=".$hasta."&id_servicio=".$id_servicio."')> <img src='../../img/icono_reporte4.jpg' border=0 width='20px'/> </a></td>";
                        $mi_array= $lista_servicios;
                        $compactada=serialize($mi_array);
                        $compactada=urlencode($compactada);
                        $html.="<td style='background-color:#e5f1f4;'><a title='Graficar este dia' href=javascript:ventanaSecundaria('verGraficoHoras?id_modulo=".$modulo_id_sucursal."&dia=".$dia."&ttat=".$tot_t_atendidos_x_dia."&ttan=".$tot_t_anulados_x_dia."&dt=".$duracion_total."&pa=".$duracion_promedio."&desde=".$desde."&hasta=".$hasta."&id_servicio=".$compactada."&forma_duracion=".$forma_duracion."')> <img src='../../img/icono_reporte4.jpg' border=0 width='20px'/> </a></td>";
                        $totales_turnos_atendidos[$dia]=$tot_t_atendidos_x_dia; //para graficar este dia
                        $forma_array[$num_modulo]=$totales_turnos_atendidos; //para graficar todos
                        $subtotal_total_turnos_atendidos+=$tot_t_atendidos_x_dia;
                        $subtotal_total_turnos_anulados+=$tot_t_anulados_x_dia;


                        $grantotal_turnos_atendidos+=$tot_t_atendidos_x_dia;
                        $grantotal_turnos_anulados+=$tot_t_anulados_x_dia;

                    }
                    $gran_promedio_atencion+=$subtotal_en_segudos_prom_atencion/5;
                    $gran_promedio_llamada+=$subtotal_en_segundos_prom_llamada/5;
                    $html.="<td align='right' style='background-color:#bce774;'>".$subtotal_total_turnos_atendidos."</td>";
                    $html.="<td align='right' style='background-color:#bce774;'>".$subtotal_total_turnos_anulados."</td>";
                    $html.="<td align='right' style='background-color:#bce774;'>".$fun->tiempo(round($subtotal_en_segudos_prom_atencion/5))."</td>";
                    $html.="<td align='right' style='background-color:#bce774;'>".$fun->tiempo(round($subtotal_en_segundos_prom_llamada/5))."</td>";
                    $html.="<td style='background-color:#e5f1f4;'><a href=javascript:ventanaSecundaria('verGrafico?id_modulo=".$modulo_id_sucursal."&ttat=".$subtotal_total_turnos_atendidos."&ttan=".$subtotal_total_turnos_anulados."&desde=".$desde."&hasta=".$hasta."&lu=".$totales_turnos_atendidos['Monday']."&ma=".$totales_turnos_atendidos['Tuesday']."&mi=".$totales_turnos_atendidos['Wednesday']."&ju=".$totales_turnos_atendidos['Thursday']."&vi=".$totales_turnos_atendidos['Friday']."&sa=".$totales_turnos_atendidos['Saturday']."&do=".$totales_turnos_atendidos['Sunday']."')>Graficar</a></td>";
                    $html.="</tr>";
                }

                //INICIO IMPRIMIR LA FILA DE LA SUMA DE TODOS LOS MODULOS POR DIAS
                $html.="<tr>";
                $html.="<td style='background-color:#e5f1f4;'></td>";
                $html.="<td style='background-color:#e5f1f4;'><b>TOTALES</b></td>";
                //$html.="<th style='background-color:#e5f1f4;'></th>";
                foreach ($suma_totales_turnos_atendidos as $key => $val) {
                    $html.="<td style='background-color:#FEE1BA;'><b>".$suma_totales_turnos_atendidos[$key]."</b></td>";
                    $html.="<td style='background-color:#e5f1f4;'><b>".$suma_totales_turnos_anulados[$key]."</b></td>";
                    $html.="<td style='background-color:#e5f1f4;'><b>".$fun->tiempo($suma_duracion_turnos_atendidos[$key])."</b></td>";
                    $html.="<td style='background-color:#e5f1f4;'><b>".$fun->tiempo($suma_promedio_turnos_atendidos[$key]/(count($lista_modulos)))."</b></td>";
                    $html.="<td style='background-color:#e5f1f4;'><b>".$fun->tiempo($suma_promedio_turnos_llamada[$key]/(count($lista_modulos)))."</b></td>";
                    $html.="<td style='background-color:#e5f1f4;'></td>";
                }
                $html.="<td style='background-color:#bce774;' title='Suma Total de los Turnos Atendidos'><b>$grantotal_turnos_atendidos</b></td>";
                $html.="<td style='background-color:#bce774;' title='Suma Total de los Turnos Anulados'><b>$grantotal_turnos_anulados</b></td>";
                $html.="<td style='background-color:#bce774;' title='Promedio de Atencion General'><b>".$fun->tiempo($gran_promedio_atencion/(count($lista_modulos)))."</b></td>";
                $html.="<td style='background-color:#bce774;' title='Promedio de Llamadas a Atencion General'><b>".$fun->tiempo($gran_promedio_llamada/(count($lista_modulos)))."</b></td>";
                $html.="<td style='background-color:#e5f1f4;'><b><a href=javascript:ventanaSecundaria('verGrafico?ttat=".$grantotal_turnos_atendidos."&ttan=".$grantotal_turnos_anulados."&desde=".$desde."&hasta=".$hasta."&lu=".$suma_totales_turnos_atendidos['Monday']."&ma=".$suma_totales_turnos_atendidos['Tuesday']."&mi=".$suma_totales_turnos_atendidos['Wednesday']."&ju=".$suma_totales_turnos_atendidos['Thursday']."&vi=".$suma_totales_turnos_atendidos['Friday']."&sa=".$suma_totales_turnos_atendidos['Saturday']."&do=".$suma_totales_turnos_atendidos['Sunday']."')>Graficar</a></b></td>";
                $html.="</tr>";
                $html.="</table>";
                $html.="</tr></td>";

                $mi_array= $forma_array;
                $compactada=serialize($mi_array);
                $compactada=urlencode($compactada);
                $html.="<tr><td>
                <a href=javascript:ventanaSecundaria('verGraficoTodos?array=".$compactada."')>Graficar Todos</a>
                </td></tr>";
                //<a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$compactada."&array_categorias=".$lista_modulos_compactada."&array_etiqueta_fila=".$lista_preguntas_compactada."')>Graficar Todos</a>
            }
            $html.="</table>";
            
        } else {
            $html.="No ha seleccionado alg√∫n m√≥dulo o servicio";
            //$this->routeTo("action: turnosAtendidosDias");
        }
        echo $html;
    }

    /*
     * Graficar el modulo por horas
    */
    public function verGraficoHorasAction() {
        $id_modulo=$_GET['id_modulo'];
        $dia=$_GET['dia'];
        $ttat=$_GET['ttat'];
        $ttan=$_GET['ttan'];
        $dt=$_GET['dt'];
        $pa=$_GET['pa'];
        $desde=$_GET['desde'];
        $hasta=$_GET['hasta'];

        $array=$_GET['id_servicio'];
        $array_servicios= stripslashes($array);
        $array_servicios = urldecode($array_servicios);
        $array_servicios = unserialize($array_servicios);
        $forma_condicion_servicios="";
        for ($i=0;$i<count($array_servicios);$i++) {
            if ($i==(count($array_servicios)-1))
                $forma_condicion_servicios.=$array_servicios[$i];
            else
                $forma_condicion_servicios.=$array_servicios[$i].",";
        }
        $forma_duracion=$_GET['forma_duracion'];

        $dias_semana_es= array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes');
        $dias_semana_en= array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday');
        foreach ($dias_semana_en as $key=>$dias)
            if ($dias==$dia)
                $titulo_dia=$dias_semana_es[$key];

        $condicion="grupousuario.grupo_id=5 AND caja.id=$id_modulo";
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                        "fields" => array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Usuario}.nombres"),
                        "conditions" => $condicion,
                        "order"=>"{#Caja}.numero_caja"
        ));
        foreach($query->getResultSet() as $result) {
            $num_modulo=$result->getNumeroCaja();
            $usuario=$result->getNombres();
        }
        $reportes = new ReportesSucursales();
        $fun= new Funciones();

        $html="";
        $html="<center><h2>Reporte de Turnos por Horas del(os) Dia(s) $titulo_dia<br>Desde $desde hasta $hasta</h2></center>";
        $html.="<table align='left'>";
        $html.="<tr><td><b>Modulo:</b></td><td align='right'>$num_modulo</td></tr>";
        $html.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $html.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        $html.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        $html.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        $html.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $html.="</table><br>";

        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_horas= array('8:00-9:00','9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00');
        $array_encabezado_hora=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duraci√≥n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atenci√≥n')
        );
        $html.="<table align='center'>";
        $cont_encabezado=count($array_encabezado_hora);
        $html.="<tr>";
        foreach ($array_horas as $hora) {
            $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$hora</th>";
        }
        $html.="</tr>";
        $html.="<tr>";
        foreach ($array_horas as $hora) {
            foreach ($array_encabezado_hora as $valor)
                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $html.="</tr>";

        $array_tot_t_hora=array();
        $array_hora_ini= array('08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00');
        $array_hora_fin= array('08:59:59','09:59:59','10:59:59','11:59:59','12:59:59','13:59:59','14:59:59','15:59:59','16:59:59','17:59:59');
        foreach ($array_hora_ini as $indice=> $hora) {
            $hora_ini= $array_hora_ini[$indice];
            $hora_fin= $array_hora_fin[$indice];
            $duracion_total=0;
            $duracion_promedio=0;
            $tot_t_atendidos_x_hora= $reportes->totalTurnosAtendidosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin, $forma_condicion_servicios, $forma_duracion);
            $tot_t_anulados_x_hora= $reportes->totalTurnosAnuladosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin, $forma_condicion_servicios, $forma_duracion);
            list($cont_reg,$total_segundos) = $reportes->duracionAtencionHora($id_modulo, $dia, $desde, $hasta,$hora_ini,$hora_fin, $forma_condicion_servicios, $forma_duracion);
            if ($tot_t_atendidos_x_hora != 0) {
                $duracion_total=$fun->tiempo($total_segundos);
                $duracion_promedio=$fun->tiempo(round($total_segundos/$cont_reg));
            }
            $html.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_hora."</td>";
            $html.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_hora."</td>";
            $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
            $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
            $array_tot_t_hora[$indice]=$tot_t_atendidos_x_hora;
        }
        $html.="</tr>";
        $html.="</table>";
        $forma_array[$id_modulo]=$array_tot_t_hora;

        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['8-9', '9-10', '10-11', '11-12', '12-13', '13-14', '14-15', '15-16', '16-17', '17-18']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [";

        /*$html.="{";
						$html.="name: 'Total turnos',";
						//$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie."]";
                                                $html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
					$html.="},";
                                        $html.="{";
						$html.="name: 'Promedio',";
                                                //$html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="},";*/
        foreach ($forma_array as $indice1=> $valor) {
            $html.="{";
            //$html.="name: 'Mod. $indice1',";
            $html.="name: '',";
            $html.="data: [";
            //echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                //echo $indice2." = ".$valor2."<br>";
                $html.="$valor2,";
            }
            $html.="]";
            $html.="},";
        }
        $html.="]";
        $html.="});";
        $html.="});";
        $html.="</script>";
        $html.="<div id='container' style='width: 700px; height: 400px; margin: 0 auto'></div>";
        echo $html;
    }

    /*
     * Graficar el cajero por horas
    */
    public function verGraficoHorasColasAction() {
        //$array=$_GET['array'];
        /*$tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);*/

        /*foreach ($tmp as $indice1=> $valor) {
            echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                echo $indice2." = ".$valor2."<br>";
            }
        }*/
        $id_modulo=$_GET['id_modulo'];
        $dia=$_GET['dia'];
        $ttat=$_GET['ttat'];
        //$ttan=$_GET['ttan'];
        $dt=$_GET['dt'];
        $pa=$_GET['pa'];
        $desde=$_GET['desde'];
        $hasta=$_GET['hasta'];
        //$id_servicio=$_GET['id_servicio'];
        $dias_semana_es= array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes',6=>'Sabado');
        $dias_semana_en= array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday');
        foreach ($dias_semana_en as $key=>$dias)
            if ($dias==$dia)
                $titulo_dia=$dias_semana_es[$key];

        $condicion="grupousuario.grupo_id=7 AND caja.id=$id_modulo";
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                        "fields" => array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Usuario}.nombres"),
                        "conditions" => $condicion,
                        "order"=>"{#Caja}.numero_caja"
        ));
        foreach($query->getResultSet() as $result) {
            $num_modulo=$result->getNumeroCaja();
            $usuario=$result->getNombres();
        }
        $reportes = new ReportesSucursales();
        $fun= new Funciones();

        $html="";
        $html="<center><h2>Reporte de Turnos Atendidos por Cajeros por Horas del(os) Dia(s) $titulo_dia<br>Desde $desde hasta $hasta</h2></center>";
        $html.="<table align='left'>";
        $html.="<tr><td><b>Caja:</b></td><td align='right'>$num_modulo</td></tr>";
        $html.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $html.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        // $html.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        $html.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        $html.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $html.="</table><br>";

        //$html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
        //$html.="<link href='../../css/tablecloth2.css' rel='stylesheet' type='text/css' media='screen' />";
        //$html.="<script type='text/javascript' src='../../js/tablecloth.js'></script>";

        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_horas= array('8:00-9:00','9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00');
        $array_encabezado_hora=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                //'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duraci√≥n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atenci√≥n')
        );
        $html.="<table align='center'>";
        $cont_encabezado=count($array_encabezado_hora);
        $html.="<tr>";
        foreach ($array_horas as $hora) {
            $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$hora</th>";
        }
        $html.="</tr>";
        $html.="<tr>";
        foreach ($array_horas as $hora) {
            foreach ($array_encabezado_hora as $valor)
                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $html.="</tr>";

        $array_tot_t_hora=array();
        $array_hora_ini= array('08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00');
        $array_hora_fin= array('08:59:59','09:59:59','10:59:59','11:59:59','12:59:59','13:59:59','14:59:59','15:59:59','16:59:59','17:59:59');
        foreach ($array_hora_ini as $indice=> $hora) {
            $hora_ini= $array_hora_ini[$indice];
            $hora_fin= $array_hora_fin[$indice];
            $duracion_total=0;
            $duracion_promedio=0;
            $tot_t_atendidos_x_hora= $reportes->totalColasAtendidosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin);
            //$tot_t_anulados_x_hora= $reportes->totalTurnosAnuladosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin);
            list($cont_reg,$total_segundos) = $reportes->duracionAtencionColasHora($id_modulo, $dia, $desde, $hasta,$hora_ini,$hora_fin);
            if ($tot_t_atendidos_x_hora != 0) {
                $duracion_total=$fun->tiempo($total_segundos);
                $duracion_promedio=$fun->tiempo(round($total_segundos/$cont_reg));
            }
            $html.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_hora."</td>";
            //$html.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_hora."</td>";
            $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
            $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
            $array_tot_t_hora[$indice]=$tot_t_atendidos_x_hora;
        }
        $html.="</tr>";
        $html.="</table>";
        $forma_array[$id_modulo]=$array_tot_t_hora;

        $html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='../../js/highcharts.js'></script>";
        //$html.="<!-- 1a) Optional: the exporting module -->";
        $html.="<script type='text/javascript' src='../../js/modules/exporting.js'></script>";
        //$html.="<!-- 2. Add the JavaScript to initialize the chart on document ready -->";
        $html.="<script type='text/javascript'>";

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['8-9', '9-10', '10-11', '11-12', '12-13', '13-14', '14-15', '15-16', '16-17', '17-18']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [";

        /*$html.="{";
						$html.="name: 'Total turnos',";
						//$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie."]";
                                                $html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
					$html.="},";
                                        $html.="{";
						$html.="name: 'Promedio',";
                                                //$html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="},";*/
        foreach ($forma_array as $indice1=> $valor) {
            $html.="{";
            //$html.="name: 'Mod. $indice1',";
            $html.="name: '',";
            $html.="data: [";
            //echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                //echo $indice2." = ".$valor2."<br>";
                $html.="$valor2,";
            }
            $html.="]";
            $html.="},";
        }
        $html.="]";
        $html.="});";
        $html.="});";
        $html.="</script>";
        $html.="<div id='container' style='width: 700px; height: 400px; margin: 0 auto'></div>";
        echo $html;
    }

    /*
     * Graficar Todos los modulos por dias
     * recibe un array multidimencional
    */
    public function verGraficoTodosAction() {
        $array=$_GET['array'];
        $tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);

        $html= "";
        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [";
        /*$html.="{";
						$html.="name: 'Total turnos',";
						//$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie."]";
                                                $html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
					$html.="},";*/
        /*$html.="{";
						$html.="name: 'Promedio',";
                                                //$html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="},";*/

        foreach ($tmp as $indice1=> $valor) {
            $html.="{";
            $html.="name: 'Mod. $indice1',";
            $html.="data: [";
            //echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                //echo $indice2." = ".$valor2."<br>";
                $html.="$valor2,";
            }
            $html.="]";
            $html.="},";
        }

        $html.="]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="</head>";
        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 800px; height: 600px; margin: 0 auto'></div>";


        $html.="</body>";
        $html.="</html>";

        echo $html;
    }
    /*
     * Graficar
    */
    public function verGraficoAction() {
        $num_modulo="Todos";
        $usuario="Todos";
        if (!empty ($_GET['id_modulo'])) {
            $id_modulo=$_GET['id_modulo'];
            $condicion="grupousuario.grupo_id=5 AND caja.id=$id_modulo";
            $query = new ActiveRecordJoin(array(
                            "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres"),
                            "conditions" => $condicion,
                            "order"=>"{#Caja}.numero_caja"
            ));
            foreach($query->getResultSet() as $result) {
                $num_modulo=$result->getNumeroCaja();
                $usuario=$result->getNombres();
            }
        }
        $ttat=$_GET['ttat'];
        $ttan=$_GET['ttan'];
        $desde=$_GET['desde'];
        $hasta=$_GET['hasta'];
        $lun="$_GET[lu]";
        $mar="$_GET[ma]";
        $mie="$_GET[mi]";
        $jue="$_GET[ju]";
        $vie="$_GET[vi]";
        $sab="$_GET[sa]";
        $dom="$_GET[do]";


        $reportes = new ReportesSucursales();
        $fun= new Funciones();

        $html="";
        $html="<center><h2>Reporte de Turnos por Dias de la Semana <br>Desde $desde hasta $hasta</h2></center>";
        //$html="<center><h2></h2></center>";
        $html.="<table align='center'>";
        $html.="<tr><td><b>Modulo:</b></td><td align='right'>$num_modulo</td></tr>";
        $html.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $html.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        $html.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        //$html.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        //$html.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $html.="</table><br>";

        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [{";
        $html.="name: 'Total turnos',";
        $html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie.", ".$sab.", ".$dom."]";
        //$html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
        $html.="}";
        /*$html.=", {";
						$html.="name: 'Promedio',";
                                                $html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						//$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="}";*/
        $html.="]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 600px; height: 400px; margin: 0 auto'></div>";
        echo $html;
        //echo "variable".$variable1;
        /*echo "<fieldset class='ui-corner-all ui-widget-content'>
            <legend><b>Servicios</b></legend>
            <p><label class='labelform' ><span for='usuarios'>Seleccione el servicio:</span></label></p>
        </fieldset>";*/
    }
    public function verGraficoColasAction() {
        $num_modulo="Todos";
        $usuario="Todos";
        if (!empty ($_GET['id_modulo'])) {
            $id_modulo=$_GET['id_modulo'];
            $condicion="grupousuario.grupo_id=5 AND caja.id=$id_modulo";
            $query = new ActiveRecordJoin(array(
                            "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres"),
                            "conditions" => $condicion,
                            "order"=>"{#Caja}.numero_caja"
            ));
            foreach($query->getResultSet() as $result) {
                $num_modulo=$result->getNumeroCaja();
                $usuario=$result->getNombres();
            }
        }
        $ttat=$_GET['ttat'];
        $ttan=$_GET['ttan'];
        $desde=$_GET['desde'];
        $hasta=$_GET['hasta'];
        $lun="$_GET[lu]";
        $mar="$_GET[ma]";
        $mie="$_GET[mi]";
        $jue="$_GET[ju]";
        $vie="$_GET[vi]";
        $sab="$_GET[sa]";


        $reportes = new ReportesSucursales();
        $fun= new Funciones();

        $html="";
        $html="<center><h2>Reporte de Turnos por Dias de la Semana <br>Desde $desde hasta $hasta</h2></center>";
        //$html="<center><h2></h2></center>";
        $html.="<table align='center'>";
        $html.="<tr><td><b>Modulo:</b></td><td align='right'>$num_modulo</td></tr>";
        $html.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $html.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        $html.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        //$html.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        //$html.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $html.="</table><br>";

        $html.="<!-- 1. Add these JavaScript inclusions in the head of your page -->";
        $html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='../../js/highcharts.js'></script>";

        $html.="<!-- 1a) Optional: the exporting module -->";
        $html.="<script type='text/javascript' src='../../js/modules/exporting.js'></script>";


        $html.="<!-- 2. Add the JavaScript to initialize the chart on document ready -->";
        $html.="<script type='text/javascript'>";

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie','Sab']";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        $html.="series: [{";
        $html.="name: 'Total turnos',";
        $html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie.", ".$sab."]";
        //$html.="data: [7.0, 6.9, 9.5, 14.5, 18.4]";
        $html.="}";
        /*$html.=", {";
						$html.="name: 'Promedio',";
                                                $html.="data: [".$plun.",".$pmar.", ".$pmie.", ".$pjue.", ".$pvie."]";
						//$html.="data: [3.9, 4.2, 5.7, 8.5, 11.9]";
					$html.="}";*/
        $html.="]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 600px; height: 400px; margin: 0 auto'></div>";
        echo $html;
        //echo "variable".$variable1;
        /*echo "<fieldset class='ui-corner-all ui-widget-content'>
            <legend><b>Servicios</b></legend>
            <p><label class='labelform' ><span for='usuarios'>Seleccione el servicio:</span></label></p>
        </fieldset>";*/
    }
    /*Funcion que retorna el cuadro de total turnos atendidos por dias
    */
    public function verCuadroCajasAction() {
        $this->setResponse('ajax');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $id_caja=$this->getPostParam('modulo');
        //$id_servicio=$this->getPostParam('servicio');

        $html="";
        //$html="<style> th{background-color:#328aa4;color:#fff;}</style>";

        //$html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
        //$html.="<link href='../../css/tablecloth2.css' rel='stylesheet' type='text/css' media='screen' />";
        //$html.="<script type='text/javascript' src='../../js/tablecloth.js'></script>";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $html.="<table align='center'>";
        $array_dias_semana = array('Lunes','Martes','Mi√©rcoles','Jueves','Viernes','S√°bado');
        $array_encabezado_dia=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                //'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duraci√≥n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atenci√≥n'),
                'ES'=>array('etiqueta'=>'','titulo'=>'',) //para el grafico
        );
        $cont_encabezado=count($array_encabezado_dia);
        $html.="<tr>";
        $html.="<th style='$fondo_titulo'>Caja</th>";
        $html.="<th style='$fondo_titulo'>Usuario</th>";
        //$html.="<th style='background-color:#328aa4; color:#fff'></th>";
        foreach ($array_dias_semana as $dia) {
            $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$dia</th>";
        }
        $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
        //$html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="</tr>";

        $html.="<tr>";
        $html.="<th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>";
        foreach ($array_dias_semana as $dia) {
            foreach ($array_encabezado_dia as $valor)
                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        //$html.="<th style='$fondo_titulo'></th>";
        $html.="</tr>";

        //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
        //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
        //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";

        //DURACION
        //SELECT duracion FROM caja c, turnos t, servicio s, usercaja uc, usuario u
        //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
        //AND DAYNAME(fecha_emision)='Monday' AND fecha_inicio_atencion BETWEEN '2010-11-15' AND '2010-11-24' AND c.id= 38 AND atendido=1 AND rechazado=0;
        $suma_totales_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
        $suma_totales_turnos_anulados=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
        $suma_duracion_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
        $suma_promedio_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
        $grantotal_turnos_atendidos=0;
        $grantotal_turnos_anulados=0;
        $forma_array=array();
        if ($id_caja<>0)
            $condicion1="grupousuario.grupo_id=7 AND caja.id=$id_caja";
        else
            $condicion1="grupousuario.grupo_id=7";
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                        "fields" => array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Usuario}.nombres"),
                        "conditions" => $condicion1,
                        "order"=>"{#Caja}.numero_caja"
        ));

        foreach($query->getResultSet() as $result) {
            $id_caja=$result->getId();
            $num_modulo=$result->getNumeroCaja();
            $usuario=$result->getNombres();
            $reportes = new ReportesSucursales();
            $fun= new Funciones();
            //            if ($id_servicio==0) //todos los servicios
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1";
            //            else
            //                $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=0 AND s.id= $id_servicio" ;
            //            if ($id_servicio==0) //todos los servicios
            //                $condicion_anulados="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=1";
            //            else
            //                $condicion_anulados="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=1 AND s.id= $id_servicio" ;
            $html.="<tr>";
            $html.="<td align='center' style='background-color:#e5f1f4;'>".$num_modulo."</td>";
            $html.="<td align='left' style='background-color:#e5f1f4;'>".$usuario."</td>";
            //$html.="<td align='center' style='background-color:#e5f1f4;'>".Tag::checkboxField("chkmodulo[]", "value: $id_caja", "checked: ")."</td>";
            $totales_turnos_atendidos= array(); //sirve para graficar
            $tta=0;
            $dias_semana= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
            $subtotal_total_turnos_atendidos=0;
            $subtotal_total_turnos_anulados=0;
            foreach ($dias_semana as $dia) {
                $duracion_total=0;
                $duracion_promedio=0;
                $tot_t_atendidos_x_dia= $reportes->totalColasAtendidosPorDia($dia, $condicion);
                //$tot_t_anulados_x_dia= $reportes->totalTurnosAnuladosPorDia($dia, $condicion_anulados);
                list($cont_reg,$total_segundos) = $reportes->duracionAtencionColas($dia, $condicion);
                if ($tot_t_atendidos_x_dia != 0) {
                    $duracion_total=$fun->tiempo($total_segundos);
                    $duracion_promedio=$fun->tiempo(round($total_segundos/$cont_reg));
                    $suma_totales_turnos_atendidos[$dia]=$suma_totales_turnos_atendidos[$dia]+$tot_t_atendidos_x_dia;
                    //$suma_totales_turnos_anulados[$dia]=$suma_totales_turnos_anulados[$dia]+$tot_t_anulados_x_dia;
                    $suma_duracion_turnos_atendidos[$dia]=$suma_duracion_turnos_atendidos[$dia]+$total_segundos;
                    $suma_promedio_turnos_atendidos[$dia]=$suma_promedio_turnos_atendidos[$dia]+round($total_segundos/$cont_reg);
                }
                $html.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_dia."</td>";
                //$html.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_dia."</td>";
                $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
                $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
                //$html.="<td style='background-color:#e5f1f4;'><a title='Graficar este dia' href=javascript:ventanaSecundaria('verGraficoHoras?id_modulo=".$id_caja."&dia=".$dia."&ttat=".$tot_t_atendidos_x_dia."&ttan=".$tot_t_anulados_x_dia."&dt=".$duracion_total."&pa=".$duracion_promedio."&desde=".$desde."&hasta=".$hasta."&id_servicio=".$id_servicio."')> <img src='../../img/icono_reporte4.jpg' border=0 width='20px'/> </a></td>";
                $html.="<td style='background-color:#e5f1f4;'><a title='Graficar este dia' href=javascript:ventanaSecundaria('verGraficoHorasColas?id_modulo=".$id_caja."&dia=".$dia."&ttat=".$tot_t_atendidos_x_dia."&dt=".$duracion_total."&pa=".$duracion_promedio."&desde=".$desde."&hasta=".$hasta."')> <img src='../../img/icono_reporte4.jpg' border=0 width='20px'/> </a></td>";
                $totales_turnos_atendidos[$dia]=$tot_t_atendidos_x_dia;
                $forma_array[$num_modulo]=$totales_turnos_atendidos;
                $subtotal_total_turnos_atendidos+=$tot_t_atendidos_x_dia;
                //$subtotal_total_turnos_anulados+=$tot_t_anulados_x_dia;
                $grantotal_turnos_atendidos+=$tot_t_atendidos_x_dia;
                //$grantotal_turnos_anulados+=$tot_t_anulados_x_dia;
            }
            $html.="<td align='right' style='background-color:#bce774;'>".$subtotal_total_turnos_atendidos."</td>";
            //$html.="<td align='right' style='background-color:#bce774;'>".$subtotal_total_turnos_anulados."</td>";
            $html.="<td style='background-color:#e5f1f4;'><a href=javascript:ventanaSecundaria('verGraficoColas?id_modulo=".$id_caja."&ttat=".$subtotal_total_turnos_atendidos."&ttan=".$subtotal_total_turnos_anulados."&desde=".$desde."&hasta=".$hasta."&lu=".$totales_turnos_atendidos['Monday']."&ma=".$totales_turnos_atendidos['Tuesday']."&mi=".$totales_turnos_atendidos['Wednesday']."&ju=".$totales_turnos_atendidos['Thursday']."&vi=".$totales_turnos_atendidos['Friday']."&sa=".$totales_turnos_atendidos['Saturday']."')>Graficar</a></td>";
            $html.="</tr>";
        }

        //INICIO IMPRIMIR LA FILA DE LA SUMA DE TODOS LOS MODULOS POR DIAS
        $html.="<tr>";
        $html.="<th style='background-color:#e5f1f4;'></th>";
        $html.="<th style='background-color:#e5f1f4;'>TOTALES</th>";
        //$html.="<th style='background-color:#e5f1f4;'></th>";
        foreach ($suma_totales_turnos_atendidos as $key => $val) {
            $html.="<th style='background-color:#FEE1BA;'>".$suma_totales_turnos_atendidos[$key]."</th>";
            //$html.="<th style='background-color:#e5f1f4;'>".$suma_totales_turnos_anulados[$key]."</th>";
            $html.="<th style='background-color:#e5f1f4;'>".$fun->tiempo($suma_duracion_turnos_atendidos[$key])."</th>";
            $html.="<th style='background-color:#e5f1f4;'>".$fun->tiempo($suma_promedio_turnos_atendidos[$key])."</th>";
            $html.="<th style='background-color:#e5f1f4;'></th>";
        }
        $html.="<th style='background-color:#bce774;' title='Sumar Total de los Turnos Atendidos'>$grantotal_turnos_atendidos</th>";
        //$html.="<th style='background-color:#bce774;' title='Sumar Total de los Turnos Atendidos'>$grantotal_turnos_anulados</th>";
        $html.="<th style='background-color:#e5f1f4;'><a href=javascript:ventanaSecundaria('verGrafico?ttat=".$grantotal_turnos_atendidos."&ttan=".$grantotal_turnos_anulados."&desde=".$desde."&hasta=".$hasta."&lu=".$suma_totales_turnos_atendidos['Monday']."&ma=".$suma_totales_turnos_atendidos['Tuesday']."&mi=".$suma_totales_turnos_atendidos['Wednesday']."&ju=".$suma_totales_turnos_atendidos['Thursday']."&vi=".$suma_totales_turnos_atendidos['Friday']."')>Graficar</a></th>";
        $html.="</tr>";
        $html.="</table>";
        $mi_array= $forma_array;
        $compactada=serialize($mi_array);
        $compactada=urlencode($compactada);
        $html.="<a href=javascript:ventanaSecundaria('verGraficoTodos?array=".$compactada."')>Graficar Todos</a>";
        echo $html;
    }
    /*
     * Inicio para el reporte de los turnos atendidos por mÛdulo
    */
    public function turnosAtendidosModuloAction() {
        /*$productos = array ("producto 11", "producto 1", "producto 12", "producto 2");
        natsort($productos);
        foreach ($productos as $key => $val) {
            echo $key ." = " . $val . "<br>";
        }*/

        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de MÛdulos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        //$this->lista_modulos[-1]="Seleccione";
        $this->lista_modulos[0]="Todos";
        $condicion="grupousuario.grupo_id=5";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
            //$this->lista_cajas[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            //echo $key ." = " . $val . "<br>";
            $this->lista_modulos[$key]=$val;
        }

        //Lista de Servicios
        $servicio=new Servicio();
        $buscaServicios=$servicio->find("conditions: estado= 1","order: nombre");
        $this->lista_servicios[0]="Todos";
        foreach($buscaServicios as $result) {
            $this->lista_servicios[$result->getId()]=$result->getNombre();
        }
    }

    /*
     * Inicio de exportaciÛn a Excel de Turnos
    */
    public function turnosExcelAction() {
        /*$productos = array ("producto 11", "producto 1", "producto 12", "producto 2");
        natsort($productos);
        foreach ($productos as $key => $val) {
            echo $key ." = " . $val . "<br>";
        }*/

        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de MÛdulos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        //$this->lista_modulos[-1]="Seleccione";
        //$this->lista_modulos[0]="Todos";
        $condicion="grupousuario.grupo_id=5";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
            //$this->lista_cajas[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            //echo $key ." = " . $val . "<br>";
            $this->lista_modulos_xls[$key]=$val;
        }

        //Lista de Servicios
        $servicio=new Servicio();
        $buscaServicios=$servicio->find("conditions: estado= 1","order: nombre");
        //$this->lista_servicios[0]="Todos";
        foreach($buscaServicios as $result) {
            $this->lista_servicios_xls[$result->getId()]=$result->getNombre();
        }

    }

    /*
     * Inicio de exportaciÛn a Excel de Colas
    */
    public function colasExcelAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de MÛdulos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        //$this->lista_modulos[-1]="Seleccione";
        $this->lista_cajas[0]="Todos";
        $condicion="grupousuario.grupo_id=7";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            //echo $key ." = " . $val . "<br>";
            $this->lista_cajas[$key]=$val;
        }
    }
    /*
     * Men˙ para Reporte que muestra el cuador de los totales de turnos atendidos por dÌas
    */
    public function turnosAtendidosDiasAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
    }
    /*
     * Men˙ para Reporte que muestra los totales de colas atendidos por dÌas
    */
    public function colasAtendidosDiasAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));

        //INICIO LISTA DE M”DULOS
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        $this->lista_cajas[0]="Todos";
        $condicion="grupousuario.grupo_id=7";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
            //$this->lista_cajas[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $this->lista_cajas[$key]=$val;
        }
        //FIN LISTA DE M”DULOS
    }

    /*
     * Inicio de reporte de clientes atendidos por caja
    */
    public function colasAtendidasCajaAction() {
        $lista=array();
        $this->lista_cajas[0]="Todos";
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de Cajas
        $condicion="grupousuario.grupo_id=7";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id",
                                "{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach($query->getResultSet() as $result) {
            $lista[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $this->lista_cajas[$key]=$val;
        }
    }
    /*
     * Inicio de reporte de turnos atendidos por modulo
    */
    public function turnosAtendidosCajaAction() {
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de MÛdulos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        $this->lista_cajas[0]="Todos";
        $condicion="grupousuario.grupo_id=5";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array("{#Caja}.id","{#Caja}.numero_caja"),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );
        foreach ($query->getResultSet() as $result) {
            $this->lista_cajas[$result->getId()]=$result->getNumeroCaja();
        }
        /*$caja=new Caja();
        $buscaCaja=$caja->find("conditions: estado= 1","order: numero_caja");
        $this->lista_cajas[0]="Todos";
        foreach($buscaCaja as $result) {
            $this->lista_cajas[$result->getId()]=$result->getNumerocaja();
        }*/

        //Lista de Servicios
        $servicio=new Servicio();
        $buscaServicios=$servicio->find("conditions: estado= 1","order: nombre");
        $this->lista_servicios[0]="Todos";
        foreach($buscaServicios as $result) {
            $this->lista_servicios[$result->getId()]=$result->getNombre();
        }
    }


    /*
     * Reporte que muestra los totales de turnos atendidos por MÛdulos
     * PDF
    */
    public function reporteTurnosAtendidosModuloAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

        //INICIO CALCULO DE DIAS ENTRE FECHA Y FECHA
        $s = strtotime($hasta)-strtotime($desde);
        $d = intval($s/86400) + 1;
        /*$s -= $d*86400;
        $h = intval($s/3600);
        $s -= $h*3600;
        $m = intval($s/60);
        $s -= $m*60;
        $dif= (($d*24)+$h).hrs." ".$m."min";
        $dif2= $d.$space.dias." ".$h.hrs." ".$m."min";
        echo "Diferencia en horas: ".$dif;
        echo "Diferencia en dias: ".$dif2;*/
        //FIN CALCULO DE DIAS ENTRE FECHA Y FECHA

        $id_caja=$this->getPostParam('cajas');
        $id_servicio=$this->getPostParam('servicios');

        /*Inicio Instanciar pdf*/
        $this->setResponse('Pdf');
        $pdf=new PdfDocument();
        //$pdf->addPage();
        $this->setResponse('view');
        $pdf->setEncoding(PdfDocument::ENC_UTF8);
        $black=PdfColor::fromName(PdfColor::COLOR_BLACK);
        $pdf->setTextColor($black);
        $pdf->addPage('OR_LANDSCAPE'); //Vertical: OR_PORTRAIT, horizontal: OR_LANDSCAPE
        /*Fin Instanciar pdf*/

        //Agregar el titulo
        $pdf->setFont('helvetica', 'BU', 18);
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por M√≥dulo");
        $pdf->lineFeed();

        //La fecha del reporte
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->writeCell(40, 7, "Fecha de Reporte: ".Date::getCurrentDate());
        $pdf->lineFeed();

        //Encabezados con fondo gris
        $lightGray = PdfColor::fromGrayScale(0.75);
        $pdf->setFillColor($lightGray);
        $pdf->writeCell(10, 7, '#', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'M√≥dulo', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(55, 7, 'Descripci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atenci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atenci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'FFA', 0, 0, PdfDocument::ALIGN_CENTER);
        //$pdf->writeCell(15, 7, 'HIA', 0, 0, PdfDocument::ALIGN_CENTER);
        $pdf->writeCell(20, 7, 'Duraci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS M”DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÛ un mÛdulo
            //$condicion="numero_caja= $numero_caja";
            //SELECT numero_caja, nombres FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-08-17' AND '2010-08-17';
            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
            $total_turnos_por_modulo=0;
            $total_duracion_por_modulo_segundos=0;
            for ($i=1;$i<=$d;$i++) {
                $num=0; //cuenta filas
                $total_segundos=0;

                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ;

                if ($id_servicio==0) //Todos los servicios
                    $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja  AND {#Turnos}.atendido=1 AND rechazado=0";
                else
                    $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Servicio}.id= $id_servicio  AND {#Turnos}.atendido=1 AND rechazado=0";
                $query = new ActiveRecordJoin(array(
                                "entities" => array("Caja", "Turnos", "Servicio", "Usercaja", "Usuario"),
                                "fields" => array(
                                        "{#Caja}.numero_caja",
                                        "{#Caja}.descripcion",
                                        "{#Usuario}.nombres",
                                        "{#Servicio}.letra",
                                        "{#Servicio}.nombre",
                                        "{#Turnos}.numero",
                                        "{#Turnos}.fecha_inicio_atencion",
                                        "{#Turnos}.hora_inicio_atencion",
                                        "{#Turnos}.fecha_fin_atencion",
                                        "{#Turnos}.hora_fin_atencion",
                                        "{#Turnos}.duracion"),
                                "conditions" => $condicion,
                                "order"=>"{#Turnos}.hora_inicio_atencion"
                ));

                if (!empty($query)) {
                    foreach($query->getResultSet() as $result) {
                        $num+=1;
                        $pdf->setFont('helvetica','','9');
                        $pdf->writeCell(10, 7, $num, 0, 0,PdfDocument::ALIGN_LEFT); //#
                        $pdf->writeCell(15, 7, $result->getNumeroCaja(), 0, 0,PdfDocument::ALIGN_LEFT); //Caja
                        $pdf->writeCell(55, 7, $result->getDescripcion(), 0, 0,PdfDocument::ALIGN_LEFT); //Descripcion
                        $pdf->writeCell(40, 7, $result->getNombres(), 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                        $pdf->writeCell(40, 7, $result->getLetra().". ".$result->getNombre(), 0, 0,PdfDocument::ALIGN_LEFT); //Servicio
                        $pdf->writeCell(15, 7, $result->getNumero(), 0, 0,PdfDocument::ALIGN_LEFT); //turno
                        $pdf->writeCell(40, 7, $result->getFechaInicioAtencion()." ".$result->getHoraInicioAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(40, 7, $result->getFechaFinAtencion()." ".$result->getHoraFinAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(20, 7, $result->getDuracion(), 0, 0,PdfDocument::ALIGN_LEFT); //Duracion
                        //$pdf->writeCell(20, 7, $result->getCalificacion(), 0, 0,PdfDocument::ALIGN_LEFT); //Calificacion
                        $pdf->lineFeed();

                        $duracion=$result->getDuracion();
                        $segundos= substr($duracion,6,2);
                        $minutos= substr($duracion,3,2)*60;
                        $horas= substr($duracion,0,2)*3600;
                        $total_segundos=$total_segundos+$segundos+$minutos+$horas;

                    }
                    if ($num<>0) {
                        $pdf->setFont('helvetica', '', 11);
                        $pdf->writeCell(155, 7, "TOTALES:", 0, 0, PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(80, 7, $num." Turnos atendidos", 0, 0,PdfDocument::ALIGN_LEFT);
                        $fun = new Funciones();
                        $duracion_total= $fun->tiempo($total_segundos);
                        $pdf->writeCell(40, 7, "Duraci√≥n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->lineFeed();
                        $total_turnos_por_modulo+=$num;
                        $total_duracion_por_modulo_segundos+=$total_segundos;
                    }
                }
            }
            $pdf->setFont('helvetica', '', 11);
            $pdf->writeCell(155, 7, "TOTALES POR MODULO:", 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(60, 7, "TOTAL TURNOS: ".$total_turnos_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
            $fun = new Funciones();
            $total_duracion_por_modulo= $fun->tiempo($total_duracion_por_modulo_segundos);
            $pdf->writeCell(40, 7, "DURACION TOTAL: ".$total_duracion_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
            $pdf->lineFeed();

        } else { //Por caso contrario se ha seleccionado Todos los mÛdulos
            $cajas= new Caja();
            $buscaCaja= $cajas->find();
            foreach($buscaCaja as $result) {
                $id_caja=$result->getId();

                $fecha= $desde;
                $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
                $total_turnos_por_modulo=0;
                $total_duracion_por_modulo_segundos=0;
                for ($i=1;$i<=$d;$i++) {
                    $num=0; //cuenta filas
                    $total_segundos=0;

                    $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ;

                    if ($id_servicio==0) //Todos los servicios
                        $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Turnos}.atendido=1 AND rechazado=0";
                    else
                        $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Servicio}.id= $id_servicio  AND {#Turnos}.atendido=1 AND rechazado=0" ;
                    $query = new ActiveRecordJoin(array(
                                    "entities" => array("Caja", "Turnos", "Servicio", "Usercaja", "Usuario"),
                                    "fields" => array(
                                            "{#Caja}.numero_caja",
                                            "{#Caja}.descripcion",
                                            "{#Usuario}.nombres",
                                            "{#Servicio}.letra",
                                            "{#Servicio}.nombre",
                                            "{#Turnos}.numero",
                                            "{#Turnos}.fecha_inicio_atencion",
                                            "{#Turnos}.hora_inicio_atencion",
                                            "{#Turnos}.fecha_fin_atencion",
                                            "{#Turnos}.hora_fin_atencion",
                                            "{#Turnos}.duracion"),
                                    "conditions" => $condicion,
                                    "order"=>"{#Turnos}.hora_inicio_atencion"
                    ));

                    if (!empty($query)) {
                        foreach($query->getResultSet() as $result) {
                            $num+=1;
                            $pdf->setFont('helvetica','','9');
                            $pdf->writeCell(10, 7, $num, 0, 0,PdfDocument::ALIGN_LEFT); //#
                            $pdf->writeCell(15, 7, $result->getNumeroCaja(), 0, 0,PdfDocument::ALIGN_LEFT); //Caja
                            $pdf->writeCell(55, 7, $result->getDescripcion(), 0, 0,PdfDocument::ALIGN_LEFT); //Descripcion
                            $pdf->writeCell(40, 7, $result->getNombres(), 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                            $pdf->writeCell(40, 7, $result->getLetra().". ".$result->getNombre(), 0, 0,PdfDocument::ALIGN_LEFT); //Servicio
                            $pdf->writeCell(15, 7, $result->getNumero(), 0, 0,PdfDocument::ALIGN_LEFT); //turno
                            $pdf->writeCell(40, 7, $result->getFechaInicioAtencion()." ".$result->getHoraInicioAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(40, 7, $result->getFechaFinAtencion()." ".$result->getHoraFinAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(20, 7, $result->getDuracion(), 0, 0,PdfDocument::ALIGN_LEFT); //Duracion
                            //$pdf->writeCell(20, 7, $result->getCalificacion(), 0, 0,PdfDocument::ALIGN_LEFT); //Calificacion
                            $pdf->lineFeed();

                            $duracion=$result->getDuracion();
                            $segundos= substr($duracion,6,2);
                            $minutos= substr($duracion,3,2)*60;
                            $horas= substr($duracion,0,2)*3600;
                            $total_segundos=$total_segundos+$segundos+$minutos+$horas;

                        }
                        if ($num<>0) {
                            $pdf->setFont('helvetica', '', 11);
                            $pdf->writeCell(155, 7, "TOTALES:", 0, 0, PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(80, 7, $num." Turnos atendidos", 0, 0,PdfDocument::ALIGN_LEFT);
                            $fun = new Funciones();
                            $duracion_total= $fun->tiempo($total_segundos);
                            $pdf->writeCell(40, 7, "Duraci√≥n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->lineFeed();
                            $total_turnos_por_modulo+=$num;
                            $total_duracion_por_modulo_segundos+=$total_segundos;
                        }
                    }
                }
                if ($total_turnos_por_modulo<>0) {
                    $pdf->setFont('helvetica', '', 11);
                    $pdf->writeCell(155, 7, "TOTALES POR MODULO:", 0, 0, PdfDocument::ALIGN_LEFT);
                    $pdf->writeCell(60, 7, "TOTAL TURNOS: ".$total_turnos_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
                    $fun = new Funciones();
                    $total_duracion_por_modulo= $fun->tiempo($total_duracion_por_modulo_segundos);
                    $pdf->writeCell(40, 7, "DURACION TOTAL: ".$total_duracion_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
                    $pdf->lineFeed();
                    $pdf->lineFeed();
                }
            }
        }
        //FIN OBTENER LOS M”DULOS
        $pdf->outputToBrowser();

    }

    /*
     * Reporte que muestra los totales de turnos atendidos por DÌas
     * PDF
    */
    public function reporteTurnosAtendidosDiasAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

        $id_caja=$this->getPostParam('cajas');
        $id_servicio=$this->getPostParam('servicios');

        /*Inicio Instanciar pdf*/
        $this->setResponse('Pdf');
        $pdf=new PdfDocument();
        //$pdf->addPage();
        $this->setResponse('view');
        $pdf->setEncoding(PdfDocument::ENC_UTF8);
        $black=PdfColor::fromName(PdfColor::COLOR_BLACK);
        $pdf->setTextColor($black);
        $pdf->addPage('OR_PORTRAIT'); //Vertical: OR_PORTRAIT, horizontal: OR_LANDSCAPE
        /*Fin Instanciar pdf*/

        //Agregar el titulo
        $pdf->setFont('helvetica', 'U', 18);
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por M√≥dulo");
        $pdf->lineFeed();

        //Agregar el intervalo de fecha
        $pdf->setFont('helvetica', 'U', 14);
        $pdf->writeCell(40, 7, "Desde ".$desde." hasta ".$hasta);
        $pdf->lineFeed();

        //La fecha del reporte
        $pdf->setFont('helvetica', '', 12);
        $pdf->writeCell(40, 7, "Fecha de Reporte: ".Date::getCurrentDate());
        $pdf->lineFeed();

        //Encabezados con fondo gris
        /*$lightGray = PdfColor::fromGrayScale(0.75);
        $pdf->setFillColor($lightGray);
        $pdf->writeCell(20, 7, 'M√≥dulo', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(22, 7, 'Lunes', 0, 0, PdfDocument::ALIGN_RIGHT);
        $pdf->writeCell(22, 7, 'Martes', 0, 0, PdfDocument::ALIGN_RIGHT);
        $pdf->writeCell(22, 7, 'Miercoles', 0, 0, PdfDocument::ALIGN_RIGHT);
        $pdf->writeCell(22, 7, 'Jueves', 0, 0, PdfDocument::ALIGN_RIGHT);
        $pdf->writeCell(22, 7, 'Viernes', 0, 0, PdfDocument::ALIGN_RIGHT);
        $pdf->lineFeed();*/

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS M”DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÛ un mÛdulo

            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";

            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada dÌa
                //Agregar el Todos los servicio
                $pdf->setFont('helvetica', 'U', 12);
                $pdf->writeCell(40, 7, "Servicios: Todos");
                $pdf->lineFeed();
            } else {
                //Agregar el Servicio
                $servicio= new Servicio();
                $buscaServicio= $servicio->findFirst($id_servicio);
                $nombre_servicio= $buscaServicio->getNombre();
                $pdf->setFont('helvetica', 'U', 12);
                $pdf->writeCell(40, 7, "Servicio: ".$nombre_servicio);
                $pdf->lineFeed();
            }

            //Encabezados con fondo gris
            $lightGray = PdfColor::fromGrayScale(0.75);
            $pdf->setFillColor($lightGray);
            $pdf->writeCell(20, 7, 'M√≥dulo', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(22, 7, 'Lunes', 0, 0, PdfDocument::ALIGN_RIGHT);
            $pdf->writeCell(22, 7, 'Martes', 0, 0, PdfDocument::ALIGN_RIGHT);
            $pdf->writeCell(22, 7, 'Miercoles', 0, 0, PdfDocument::ALIGN_RIGHT);
            $pdf->writeCell(22, 7, 'Jueves', 0, 0, PdfDocument::ALIGN_RIGHT);
            $pdf->writeCell(22, 7, 'Viernes', 0, 0, PdfDocument::ALIGN_RIGHT);
            $pdf->lineFeed();

            $condicion1="grupousuario.grupo_id=5 AND caja.id=$id_caja";
            $query = new ActiveRecordJoin(array(
                            "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres"),
                            "conditions" => $condicion1,
                            "order"=>"{#Caja}.numero_caja"
            ));
            foreach($query->getResultSet() as $result) {
                $id_caja=$result->getId();
                $num_modulo=$result->getNumeroCaja();
                $usuario=$result->getNombres();
            }

            if ($id_servicio==0)
                $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=0 ";
            else
                $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=0 AND s.id= $id_servicio" ;
            $db = DbBase::rawConnect();
            //LUNES
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Monday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_lunes= $row['total_dia'];
            }
            //MARTES
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Tuesday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_martes= $row['total_dia'];
            }
            //MIERCOLES
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Wednesday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_miercoles= $row['total_dia'];
            }
            //JUEVES
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Thursday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_jueves= $row['total_dia'];
            }
            //VIERNES
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Friday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_viernes= $row['total_dia'];
            }
            if ($total_lunes==0 && $total_martes==0 && $total_miercoles==0 && $total_jueves==0 && $total_viernes==0) {

            } else {
                $pdf->setFont('helvetica','','10');
                $pdf->writeCell(20, 7, $num_modulo, 0, 0,PdfDocument::ALIGN_LEFT); //Modulo
                $pdf->writeCell(40, 7, $usuario, 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                $pdf->writeCell(22, 7, $total_lunes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                $pdf->writeCell(22, 7, $total_martes, 0, 0,PdfDocument::ALIGN_RIGHT); //Martes
                $pdf->writeCell(22, 7, $total_miercoles, 0, 0,PdfDocument::ALIGN_RIGHT); //Miercoles
                $pdf->writeCell(22, 7, $total_jueves, 0, 0,PdfDocument::ALIGN_RIGHT); //Jueves
                $pdf->writeCell(22, 7, $total_viernes, 0, 0,PdfDocument::ALIGN_RIGHT); //Viernes
                $pdf->lineFeed();
            }

        } else { //Por caso contrario se ha seleccionado Todos los mÛdulos osea &id_caja==0
            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";
            $promedio_lunes=0;
            $promedio_martes=0;
            $promedio_miercoles=0;
            $promedio_jueves=0;
            $promedio_viernes=0;
            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada dÌa
                //Agregar el Todos los servicio
                $pdf->setFont('helvetica', 'U', 12);
                $pdf->writeCell(40, 7, "Servicios: Todos");
                $pdf->lineFeed();
            } else {
                //Agregar el Servicio
                $servicio= new Servicio();
                $buscaServicio= $servicio->findFirst($id_servicio);
                $nombre_servicio= $buscaServicio->getNombre();
                $pdf->setFont('helvetica', 'U', 12);
                $pdf->writeCell(40, 7, "Servicio: ".$nombre_servicio);
                $pdf->lineFeed();
            }
            //Encabezados con fondo gris
            $lightGray = PdfColor::fromGrayScale(0.75);
            $pdf->setFillColor($lightGray);
            $pdf->setFont('helvetica', 'BU', 10);
            $pdf->writeCell(20, 7, 'M√≥dulo', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(40, 7, 'Lunes', 0, 0, PdfDocument::ALIGN_CENTER);
            $pdf->writeCell(40, 7, 'Martes', 0, 0, PdfDocument::ALIGN_CENTER);
            $pdf->writeCell(40, 7, 'Miercoles', 0, 0, PdfDocument::ALIGN_CENTER);
            $pdf->writeCell(40, 7, 'Jueves', 0, 0, PdfDocument::ALIGN_CENTER);
            $pdf->writeCell(40, 7, 'Viernes', 0, 0, PdfDocument::ALIGN_CENTER);
            $pdf->lineFeed();
            $pdf->setFont('helvetica', 'U', 10);
            $pdf->writeCell(70, 7, '', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(15, 7, 'Total', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(25, 7, 'Promedio', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(15, 7, 'Total', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(25, 7, 'Promedio', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(15, 7, 'Total', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(25, 7, 'Promedio', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(15, 7, 'Total', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(25, 7, 'Promedio', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(15, 7, 'Total', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(25, 7, 'Promedio', 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->lineFeed();
            $condicion1="grupousuario.grupo_id=5";
            $query = new ActiveRecordJoin(array(
                            "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres"),
                            "conditions" => $condicion1,
                            "order"=>"{#Caja}.numero_caja"
            ));
            foreach($query->getResultSet() as $result) {
                $total_lunes=0;
                $total_martes=0;
                $total_miercoles=0;
                $total_jueves=0;
                $total_viernes=0;
                $cont_lunes=0;
                $cont_martes=0;
                $cont_miercoles=0;
                $cont_jueves=0;
                $cont_viernes=0;

                $id_caja=$result->getId();
                $num_modulo=$result->getNumeroCaja();
                $usuario=$result->getNombres();
                if ($id_servicio==0) //todos los servicios
                    $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=0 GROUP BY fecha_emision";
                else
                    $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND rechazado=0 AND s.id= $id_servicio GROUP BY fecha_emision" ;
                $db = DbBase::rawConnect();
                //LUNES
                $result = $db->query("SELECT fecha_emision, COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Monday' AND $condicion");
                while($row = $db->fetchArray($result)) {
                    $total_lunes+= $row['total_dia'];
                    $cont_lunes+=1;
                }
                //MARTES
                $result = $db->query("SELECT fecha_emision,COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Tuesday' AND $condicion");
                while($row = $db->fetchArray($result)) {
                    $total_martes+= $row['total_dia'];
                    $cont_martes+=1;
                }
                //MIERCOLES
                $result = $db->query("SELECT fecha_emision,COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Wednesday' AND $condicion");
                while($row = $db->fetchArray($result)) {
                    $total_miercoles+= $row['total_dia'];
                    $cont_miercoles+=1;
                }
                //JUEVES
                $result = $db->query("SELECT fecha_emision,COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Thursday' AND $condicion");
                while($row = $db->fetchArray($result)) {
                    $total_jueves+= $row['total_dia'];
                    $cont_jueves+=1;
                }
                //VIERNES
                $result = $db->query("SELECT fecha_emision,COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Friday' AND $condicion");
                while($row = $db->fetchArray($result)) {
                    $total_viernes+= $row['total_dia'];
                    $cont_viernes+=1;
                }
                if ($total_lunes==0 && $total_martes==0 && $total_miercoles==0 && $total_jueves==0 && $total_viernes==0) {
                } else {
                    if ($total_lunes!=0)
                        $promedio_lunes=round(($total_lunes/$cont_lunes),2);
                    if ($total_martes!=0)
                        $promedio_martes=round(($total_martes/$cont_martes),2);
                    if ($total_miercoles!=0)
                        $promedio_miercoles=round(($total_miercoles/$cont_miercoles),2);
                    if ($total_jueves!=0)
                        $promedio_jueves=round(($total_jueves/$cont_jueves),2);
                    if ($total_viernes!=0)
                        $promedio_viernes=round(($total_viernes/$cont_viernes),2);

                    $pdf->setFont('helvetica','','10');
                    $pdf->writeCell(20, 7, $num_modulo, 0, 0,PdfDocument::ALIGN_LEFT); //Modulo
                    $pdf->writeCell(40, 7, $usuario, 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                    $pdf->writeCell(20, 7, $total_lunes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->writeCell(20, 7, $promedio_lunes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->writeCell(20, 7, $total_martes, 0, 0,PdfDocument::ALIGN_RIGHT); //Martes
                    $pdf->writeCell(20, 7, $promedio_martes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->writeCell(20, 7, $total_miercoles, 0, 0,PdfDocument::ALIGN_RIGHT); //Miercoles
                    $pdf->writeCell(20, 7, $promedio_miercoles, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->writeCell(20, 7, $total_jueves, 0, 0,PdfDocument::ALIGN_RIGHT); //Jueves
                    $pdf->writeCell(20, 7, $promedio_jueves, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->writeCell(20, 7, $total_viernes, 0, 0,PdfDocument::ALIGN_RIGHT); //Viernes
                    $pdf->writeCell(20, 7, $promedio_viernes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
                    $pdf->lineFeed();
                }
            }
            //Poner la suma
            $pdf->setFont('helvetica','U','10');
            $pdf->writeCell(20, 7, "Total", 0, 0,PdfDocument::ALIGN_LEFT); //Modulo
            $pdf->writeCell(40, 7, "", 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
            $pdf->writeCell(22, 7, $promedio_lunes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
            $pdf->writeCell(22, 7, $promedio_martes, 0, 0,PdfDocument::ALIGN_RIGHT); //Martes
            $pdf->writeCell(22, 7, $promedio_miercoles, 0, 0,PdfDocument::ALIGN_RIGHT); //Miercoles
            $pdf->writeCell(22, 7, $promedio_jueves, 0, 0,PdfDocument::ALIGN_RIGHT); //Jueves
            $pdf->writeCell(22, 7, $promedio_viernes, 0, 0,PdfDocument::ALIGN_RIGHT); //Viernes
            $pdf->lineFeed();
            //Poner el promedio
            /*$db = DbBase::rawConnect();
            $result = $db->query("SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id AND DAYNAME(fecha_emision)='Monday' AND $condicion");
            while($row = $db->fetchArray($result)) {
                $total_lunes= $row['total_dia'];
                $promedio_lunes=$promedio_lunes+$total_lunes;
            }
            $pdf->setFont('helvetica','U','10');
            $pdf->writeCell(20, 7, "Promedio", 0, 0,PdfDocument::ALIGN_LEFT); //Modulo
            $pdf->writeCell(40, 7, "", 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
            $pdf->writeCell(22, 7, $promedio_lunes, 0, 0,PdfDocument::ALIGN_RIGHT); //Lunes
            $pdf->writeCell(22, 7, $promediol_martes, 0, 0,PdfDocument::ALIGN_RIGHT); //Martes
            $pdf->writeCell(22, 7, $promedio_miercoles, 0, 0,PdfDocument::ALIGN_RIGHT); //Miercoles
            $pdf->writeCell(22, 7, $promedio_jueves, 0, 0,PdfDocument::ALIGN_RIGHT); //Jueves
            $pdf->writeCell(22, 7, $promedio_viernes, 0, 0,PdfDocument::ALIGN_RIGHT); //Viernes
            $pdf->lineFeed();*/
        }
        //FIN OBTENER LOS M”DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Reporte en PDF del reporte de turnso atendidos por caja
     * filtro por mÛdulo y por servicio
    */
    public function reporteColasAtendidasCajaAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

        //INICIO CALCULO DE DIAS ENTRE FECHA Y FECHA
        $s = strtotime($hasta)-strtotime($desde);
        $d = intval($s/86400) + 1;
        /*$s -= $d*86400;
        $h = intval($s/3600);
        $s -= $h*3600;
        $m = intval($s/60);
        $s -= $m*60;
        $dif= (($d*24)+$h).hrs." ".$m."min";
        $dif2= $d.$space.dias." ".$h.hrs." ".$m."min";
        echo "Diferencia en horas: ".$dif;
        echo "Diferencia en dias: ".$dif2;*/
        //FIN CALCULO DE DIAS ENTRE FECHA Y FECHA

        $id_caja=$this->getPostParam('cajas');
        $id_servicio=$this->getPostParam('servicios');

        /*Inicio Instanciar pdf*/
        $this->setResponse('Pdf');
        $pdf=new PdfDocument();
        //$pdf->addPage();
        $this->setResponse('view');
        $pdf->setEncoding(PdfDocument::ENC_UTF8);
        $black=PdfColor::fromName(PdfColor::COLOR_BLACK);
        $pdf->setTextColor($black);
        $pdf->addPage('OR_LANDSCAPE'); //Vertical: OR_PORTRAIT, horizontal: OR_LANDSCAPE
        /*Fin Instanciar pdf*/

        //Agregar el titulo
        $pdf->setFont('helvetica', 'U', 18);
        $pdf->writeCell(40, 7, "Reporte de Clientes atendidos por Caja");
        $pdf->lineFeed();

        //La fecha del reporte
        $pdf->setFont('helvetica', '', 12);
        $pdf->writeCell(40, 7, "Fecha de Reporte: ".Date::getCurrentDate());
        $pdf->lineFeed();

        //Encabezados con fondo gris
        $lightGray = PdfColor::fromGrayScale(0.75);
        $pdf->setFillColor($lightGray);
        $pdf->writeCell(10, 7, '#', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'Caja', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Descripci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(35, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(35, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atenci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atenci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(20, 7, 'Duraci√≥n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS M”DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÛ una caja
            //$condicion="numero_caja= $numero_caja";
            //SELECT numero_caja, nombres FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-08-17' AND '2010-08-17';
            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
            $total_turnos_por_modulo=0;
            $total_duracion_por_modulo_segundos=0;
            for ($i=1;$i<=$d;$i++) {
                $num=0; //cuenta filas
                $total_segundos=0;

                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ;

                //if ($id_servicio==0) //Todos los servicios
                $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja  AND {#Colas}.atendido=1";
                //else
                //$condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Servicio}.id= $id_servicio  AND {#Turnos}.atendido=1 AND rechazado=0";
                $query = new ActiveRecordJoin(array(
                                "entities" => array("Caja", "Colas", "Usercaja", "Usuario"),
                                "fields" => array(
                                        "{#Caja}.numero_caja",
                                        "{#Caja}.descripcion",
                                        "{#Usuario}.nombres",
                                        "{#Colas}.fecha_inicio_atencion",
                                        "{#Colas}.hora_inicio_atencion",
                                        "{#Colas}.fecha_fin_atencion",
                                        "{#Colas}.hora_fin_atencion",
                                        "{#Colas}.duracion",
                                        "{#Colas}.calificacion"),
                                "conditions" => $condicion,
                                "order"=>"{#Colas}.hora_inicio_atencion"
                ));

                if (!empty($query)) {
                    foreach($query->getResultSet() as $result) {
                        $num+=1;
                        $pdf->setFont('helvetica','','10');
                        $pdf->writeCell(10, 7, $num, 0, 0,PdfDocument::ALIGN_LEFT); //#
                        $pdf->writeCell(15, 7, $result->getNumeroCaja(), 0, 0,PdfDocument::ALIGN_LEFT); //Caja
                        $pdf->writeCell(40, 7, $result->getDescripcion(), 0, 0,PdfDocument::ALIGN_LEFT); //Descripcion
                        $pdf->writeCell(35, 7, $result->getNombres(), 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                        //$pdf->writeCell(35, 7, $result->getLetra().". ".$result->getNombre(), 0, 0,PdfDocument::ALIGN_LEFT); //Servicio
                        //$pdf->writeCell(15, 7, $result->getNumero(), 0, 0,PdfDocument::ALIGN_LEFT); //turno
                        $pdf->writeCell(40, 7, $result->getFechaInicioAtencion()." ".$result->getHoraInicioAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(40, 7, $result->getFechaFinAtencion()." ".$result->getHoraFinAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(20, 7, $result->getDuracion(), 0, 0,PdfDocument::ALIGN_LEFT); //Duracion
                        //$pdf->writeCell(20, 7, $result->getCalificacion(), 0, 0,PdfDocument::ALIGN_LEFT); //Calificacion
                        $pdf->lineFeed();

                        $duracion=$result->getDuracion();
                        $segundos= substr($duracion,6,2);
                        $minutos= substr($duracion,3,2)*60;
                        $horas= substr($duracion,0,2)*3600;
                        $total_segundos=$total_segundos+$segundos+$minutos+$horas;

                    }
                    if ($num<>0) {
                        $pdf->setFont('helvetica', '', 11);
                        $pdf->writeCell(100, 7, "TOTALES:", 0, 0, PdfDocument::ALIGN_LEFT);
                        $pdf->writeCell(80, 7, $num." Clientes atendidos", 0, 0,PdfDocument::ALIGN_LEFT);
                        $fun = new Funciones();
                        $duracion_total= $fun->tiempo($total_segundos);
                        $pdf->writeCell(40, 7, "Duraci√≥n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
                        $pdf->lineFeed();
                        $total_turnos_por_modulo+=$num;
                        $total_duracion_por_modulo_segundos+=$total_segundos;
                    }
                }
            }
            $pdf->setFont('helvetica', '', 11);
            $pdf->writeCell(100, 7, "TOTALES POR CAJA:", 0, 0, PdfDocument::ALIGN_LEFT);
            $pdf->writeCell(60, 7, "TOTAL ATENDIDOS: ".$total_turnos_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
            $fun = new Funciones();
            $total_duracion_por_modulo= $fun->tiempo($total_duracion_por_modulo_segundos);
            $pdf->writeCell(40, 7, "DURACION TOTAL: ".$total_duracion_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
            $pdf->lineFeed();

        } else { //Por caso contrario se ha seleccionado Todos los mÛdulos
            $cajas= new Caja();
            $buscaCaja= $cajas->find();
            foreach($buscaCaja as $result) {
                $id_caja=$result->getId();
                $fecha= $desde;
                $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
                $total_turnos_por_modulo=0;
                $total_duracion_por_modulo_segundos=0;
                for ($i=1;$i<=$d;$i++) {
                    $num=0; //cuenta filas
                    $total_segundos=0;

                    $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ;

                    //if ($id_servicio==0) //Todos los servicios
                    $condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Colas}.atendido=1";
                    //else
                    //$condicion="fecha_inicio_atencion = '$fecha' AND {#Caja}.id= $id_caja AND {#Servicio}.id= $id_servicio  AND {#Turnos}.atendido=1 AND rechazado=0" ;
                    $query = new ActiveRecordJoin(array(
                                    "entities" => array("Caja", "Colas", "Usercaja", "Usuario"),
                                    "fields" => array(
                                            "{#Caja}.numero_caja",
                                            "{#Caja}.descripcion",
                                            "{#Usuario}.nombres",
                                            "{#Colas}.fecha_inicio_atencion",
                                            "{#Colas}.hora_inicio_atencion",
                                            "{#Colas}.fecha_fin_atencion",
                                            "{#Colas}.hora_fin_atencion",
                                            "{#Colas}.duracion",
                                            "{#Colas}.calificacion"),
                                    "conditions" => $condicion,
                                    "order"=>"{#Colas}.hora_inicio_atencion"
                    ));

                    if (!empty($query)) {
                        foreach($query->getResultSet() as $result) {
                            $num+=1;
                            $pdf->setFont('helvetica','','10');
                            $pdf->writeCell(10, 7, $num, 0, 0,PdfDocument::ALIGN_LEFT); //#
                            $pdf->writeCell(15, 7, $result->getNumeroCaja(), 0, 0,PdfDocument::ALIGN_LEFT); //Caja
                            $pdf->writeCell(40, 7, $result->getDescripcion(), 0, 0,PdfDocument::ALIGN_LEFT); //Descripcion
                            $pdf->writeCell(35, 7, $result->getNombres(), 0, 0,PdfDocument::ALIGN_LEFT); //Usuario
                            //$pdf->writeCell(35, 7, $result->getLetra().". ".$result->getNombre(), 0, 0,PdfDocument::ALIGN_LEFT); //Servicio
                            //$pdf->writeCell(15, 7, $result->getNumero(), 0, 0,PdfDocument::ALIGN_LEFT); //turno
                            $pdf->writeCell(40, 7, $result->getFechaInicioAtencion()." ".$result->getHoraInicioAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(40, 7, $result->getFechaFinAtencion()." ".$result->getHoraFinAtencion(), 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(20, 7, $result->getDuracion(), 0, 0,PdfDocument::ALIGN_LEFT); //Duracion
                            //$pdf->writeCell(20, 7, $result->getCalificacion(), 0, 0,PdfDocument::ALIGN_LEFT); //Calificacion
                            $pdf->lineFeed();

                            $duracion=$result->getDuracion();
                            $segundos= substr($duracion,6,2);
                            $minutos= substr($duracion,3,2)*60;
                            $horas= substr($duracion,0,2)*3600;
                            $total_segundos=$total_segundos+$segundos+$minutos+$horas;

                        }
                        if ($num<>0) {
                            $pdf->setFont('helvetica', '', 11);
                            $pdf->writeCell(100, 7, "TOTALES:", 0, 0, PdfDocument::ALIGN_LEFT);
                            $pdf->writeCell(80, 7, $num." Clientes atendidos", 0, 0,PdfDocument::ALIGN_LEFT);
                            $fun = new Funciones();
                            $duracion_total= $fun->tiempo($total_segundos);
                            $pdf->writeCell(40, 7, "Duraci√≥n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
                            $pdf->lineFeed();
                            $total_turnos_por_modulo+=$num;
                            $total_duracion_por_modulo_segundos+=$total_segundos;
                        }
                    }
                }
                if ($total_turnos_por_modulo<>0) {
                    $pdf->setFont('helvetica', '', 11);
                    $pdf->writeCell(100, 7, "TOTALES POR CAJA:", 0, 0, PdfDocument::ALIGN_LEFT);
                    $pdf->writeCell(60, 7, "TOTAL ATENDIDOS: ".$total_turnos_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
                    $fun = new Funciones();
                    $total_duracion_por_modulo= $fun->tiempo($total_duracion_por_modulo_segundos);
                    $pdf->writeCell(40, 7, "DURACION TOTAL: ".$total_duracion_por_modulo, 0, 0,PdfDocument::ALIGN_LEFT);
                    $pdf->lineFeed();
                    $pdf->lineFeed();
                }
            }
        }
        //FIN OBTENER LOS M”DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Funcion que construye los datos para excel en tabla
    */
    public function reporteTurnosAtendidosModuloXlsAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

        $lista_modulos=$this->getPostParam('chkmodulos');
        $lista_servicios=$this->getPostParam('chkservicios');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_condicion_servicios="";
        if (!empty($lista_servicios) & !empty($lista_modulos)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }
        $html="";
        if (!empty($lista_modulos) & !empty($lista_servicios) ) {
            $html.="<html>";
            $html.="<head>";
            $html.="<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />";
            $html.="<title>JQuery Excel</title>";
            $html.="<script type='text/javascript' src='../../js/jquery-1.3.2.min.js'></script>";
            $html.="<script language='javascript'>";
            $html.="$(document).ready(function() {";
            $html.="$('.botonExcel').click(function(event) {";
            $html.="$('#datos_a_enviar').val( $('<div>').append( $('#Exportar_a_Excel').eq(0).clone()).html());";
            $html.="$('#FormularioExportacion').submit();";
            $html.="});";
            $html.="});";
            $html.="</script>";
            $html.="<style type='text/css'>";
            $html.=".botonExcel{cursor:pointer;}";
            $html.="</style>";
            $html.="</head>";
            $html.="<body>";
            $html.="<form action='ficheroExcel' method='post' target='_blank' id='FormularioExportacion'>";
            $html.="<p>Exportar a Excel  <img src='../../img/export_to_excel.gif' class='botonExcel' /></p>";
            $html.="<input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />";
            $html.="</form>";
            $html.="<table border='1' cellpadding='2' cellspacing='0' bordercolor='#666666' id='Exportar_a_Excel' style='border-collapse:collapse;'>";
            $html.="<tr>";
            $html.="<th>Usuario</th>";
            $html.="<th>Num. M√≥dulo</th>";
            $html.="<th>Servicio</th>";
            $html.="<th>Num. turno</th>";
            $html.="<th>Fecha emisi√≥n</th>";
            $html.="<th>Hora emisi√≥n</th>";
            $html.="<th>Anulado</th>";
            $html.="<th>F. inicio atenci√≥n</th>";
            $html.="<th>H. inicio atenci√≥n</th>";
            $html.="<th>F. fin atenci√≥n</th>";
            $html.="<th>H. fin atenci√≥n</th>";
            $html.="<th>Duraci√≥n</th>";
            $html.="</tr>";

            $anulado="";
            //if (!empty($query)) {
            foreach($lista_modulos as $id_modulo) {
                $condicion="fecha_emision BETWEEN '$desde' AND '$hasta' AND modulo_id_sucursal= $id_modulo AND servicio_id_sucursal IN ($forma_condicion_servicios) AND duracion>='$forma_duracion'" ;
                $sincturnos= new Sincturnos();
                $buscaSincTurnos= $sincturnos->find("conditions: $condicion");
                foreach($buscaSincTurnos as $result) {
                    if ($result->getRechazado()==1) {
                        $anulado="SI";
                        $fia="0000-00-00";
                        $hfa="00:00:00";
                        $duracion="00:00:00";
                    } else {
                        $fia=$result->getFechaFinAtencion();
                        $hfa=$result->getHoraFinAtencion();
                        $duracion=$result->getDuracion();
                        $anulado="NO";
                    }
                    $html.="<tr>";
                    $html.="<td>".$result->getUsuario()."</td>";
                    $html.="<td>".$result->getNumeroModulo()."</td>";
                    $html.="<td>".$result->getNombreServicio()."</td>";
                    //$html.="<td>".$result->getNombre()."</td>";
                    //$html.="<td>".$result->getLetra()."</td>";
                    $html.="<td>".$result->getNumeroTurno()."</td>";
                    $html.="<td>".$result->getFechaEmision()."</td>";
                    $html.="<td>".$result->getHoraEmision()."</td>";
                    $html.="<td>".$anulado."</td>";
                    $html.="<td>".$result->getFechaInicioAtencion()."</td>";
                    $html.="<td>".$result->getHoraInicioAtencion()."</td>";
                    $html.="<td>".$fia."</td>";
                    $html.="<td>".$hfa."</td>";
                    $html.="<td>".$duracion."</td>";
                    $html.="</tr>";
                }
            }

            //INICIO VER TURNOS CON VALOR NULL EN CAJA_ID
            //Luego de ver los tunos atendidos, se ve los turnos que no han sido llamados
            $condicion="fecha_emision BETWEEN '$desde' AND '$hasta' AND servicio_id_sucursal IN ($forma_condicion_servicios) AND modulo_id_sucursal IS NULL" ;
            $buscaSincTurnos= $sincturnos->find("conditions: $condicion");
            foreach($buscaSincTurnos as $result) {
                if ($result->getRechazado()==1) {
                    $anulado="SI";
                    $fia="0000-00-00";
                    $hfa="00:00:00";
                    $duracion="00:00:00";
                } else {
                    $fia=$result->getFechaFinAtencion();
                    $hfa=$result->getHoraFinAtencion();
                    $duracion=$result->getDuracion();
                    $anulado="NO";
                }
                $html.="<tr>";
                $html.="<td>NULL</td>";
                $html.="<td>NULL</td>";
                $html.="<td>NULL</td>";
                $html.="<td>".$result->getUsuario()."</td>";
                $html.="<td>".$result->getNumeroModulo()."</td>";
                $html.="<td>".$result->getNombreServicio()."</td>";
                //$html.="<td>".$result->getLetra()."</td>";
                $html.="<td>".$result->getNumeroTurno()."</td>";
                $html.="<td>".$result->getFechaEmision()."</td>";
                $html.="<td>".$result->getHoraEmision()."</td>";
                $html.="<td>NULL</td>";
                $html.="<td>".$result->getFechaInicioAtencion()."</td>";
                $html.="<td>".$result->getHoraInicioAtencion()."</td>";
                $html.="<td>".$fia."</td>";
                $html.="<td>".$hfa."</td>";
                $html.="<td>".$duracion."</td>";
                $html.="</tr>";
            }
            //FIN VER TURNOS CON VALOR NULL EN CAJA_ID
            $html.="</table>";
            $html.="</body>";
            $html.="</html>";
        } else
            $html.="No ha seleccionado alg√∫n m√≥dulo o servicio";
        echo $html;
    }

    /*
     * Funcion que construye los datos para excele en tabla
    */
    public function reporteColasAtendidasCajaXlsAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        $id_caja=$this->getPostParam('cajas');
        $html="";
        $html.="<html>";
        $html.="<head>";
        $html.="<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />";
        $html.="<title>JQuery Excel</title>";
        $html.="<script type='text/javascript' src='../../js/jquery-1.3.2.min.js'></script>";
        $html.="<script language='javascript'>";
        $html.="$(document).ready(function() {";
        $html.="$('.botonExcel').click(function(event) {";
        $html.="$('#datos_a_enviar').val( $('<div>').append( $('#Exportar_a_Excel').eq(0).clone()).html());";
        $html.="$('#FormularioExportacion').submit();";
        $html.="});";
        $html.="});";
        $html.="</script>";
        $html.="<style type='text/css'>";
        $html.=".botonExcel{cursor:pointer;}";
        $html.="</style>";
        $html.="</head>";
        $html.="<body>";
        $html.="<form action='ficheroExcel' method='post' target='_blank' id='FormularioExportacion'>";
        $html.="<p>Exportar a Excel  <img src='../../img/export_to_excel.gif' class='botonExcel' /></p>";
        $html.="<input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />";
        $html.="</form>";
        $html.="<table border='1' cellpadding='2' cellspacing='0' bordercolor='#666666' id='Exportar_a_Excel' style='border-collapse:collapse;'>";
        $html.="<tr>";
        $html.="<th>Caja</th>";
        $html.="<th>Descripci√≥n Caja</th>";
        $html.="<th>Usuario</th>";
        //$html.="<th>Anulado</th>";
        $html.="<th>F. inicio atenci√≥n</th>";
        $html.="<th>H. inicio atenci√≥n</th>";
        $html.="<th>F. fin atenci√≥n</th>";
        $html.="<th>H. fin atenci√≥n</th>";
        $html.="<th>Duraci√≥n</th>";
        $html.="</tr>";

        //SELECT caja.numero_caja, caja.descripcion, usuario.nombres, colas.atendido, colas.fecha_inicio_atencion, colas.hora_inicio_atencion,
        //colas.fecha_fin_atencion, colas.hora_fin_atencion, colas.duracion
        //FROM caja, colas, usercaja, usuario
        //WHERE caja.id = colas.caja_id AND caja.id = usercaja.caja_id
        //AND usuario.id = usercaja.usuario_id AND fecha_inicio_atencion BETWEEN '2010-11-21' AND '2010-11-21'
        //AND colas.atendido=1 AND colas.duracion>='00:00:05' ORDER BY colas.hora_inicio_atencion;

        if ($id_caja==0) {  //Todas las cajas
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND {#Colas}.atendido=1 AND {#Colas}.duracion>='00:00:05'";
        } else { //Selecciono una caja
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND {#Caja}.id= $id_caja AND {#Colas}.atendido=1 AND {#Colas}.duracion>='00:00:05'";
        }
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Caja", "Colas", "Usercaja", "Usuario"),
                        "fields" => array(
                                "{#Caja}.numero_caja",
                                "{#Caja}.descripcion",
                                "{#Usuario}.nombres",
                                "{#Colas}.atendido",
                                "{#Colas}.fecha_inicio_atencion",
                                "{#Colas}.hora_inicio_atencion",
                                "{#Colas}.fecha_fin_atencion",
                                "{#Colas}.hora_fin_atencion",
                                "{#Colas}.duracion"),
                        "conditions" => $condicion,
                        //"order"=>"{#Colas}.hora_inicio_atencion"
                        "order"=>"{#Caja}.numero_caja"
        ));
        $anulado="";
        if (!empty($query)) {
            foreach($query->getResultSet() as $result) {
                /*if ($result->getAtendido()==1){
                                $anulado="SI";
                                $fia="0000-00-00";
                                $hfa="00:00:00";
                                $duracion="00:00:00";
                            } else {
                                $fia=$result->getFechaFinAtencion();
                                $hfa=$result->getHoraFinAtencion();
                                $duracion=$result->getDuracion();
                                $anulado="NO";
                            }*/
                $html.="<tr>";
                $html.="<td>".$result->getNumeroCaja()."</td>";
                $html.="<td>".$result->getDescripcion()."</td>";
                $html.="<td>".$result->getNombres()."</td>";
                //$html.="<td>".$anulado."</td>";
                $html.="<td>".$result->getFechaInicioAtencion()."</td>";
                $html.="<td>".$result->getHoraInicioAtencion()."</td>";
                $html.="<td>".$result->getFechaFinAtencion()."</td>";
                $html.="<td>".$result->getHoraFinAtencion()."</td>";
                $html.="<td>".$result->getDuracion()."</td>";
                $html.="</tr>";
            }
        }
        $html.="</table>";
        $html.="</body>";
        $html.="</html>";
        echo $html;
    }

    /*
     * FunciÛn que exporta a excel
    */
    public function ficheroExcelAction() {
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: filename=ReporteTurnos.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $_POST['datos_a_enviar'];
    }

    /*
     * Funcion que retorna el cuadro de las calificaciones
     * Menu: EstadÌsticas de calificaciones
    */
    public function verCuadroCalificacionesAction() {
        $this->setResponse('ajax');
        $reportes= new ReportesSucursales();
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $hora_i=$this->getPostParam('hora_i');
        $hora_f=$this->getPostParam('hora_f');
        $lista_modulos=$this->getPostParam('chkmodulos');   //me sirve para categorias para los graficos
        $lista_servicios=$this->getPostParam('chkservicios');
        $lista_preguntas=$this->getPostParam('chkpreguntas');   //me sirve pra la etiquetas de las filas
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_condicion_servicios="";
        $html="";
        if (!empty($lista_servicios)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }

        $array_calificacion= array();
        $calificacion= new Calificacion();
        $buscaCalificacion= $calificacion->find();
        foreach($buscaCalificacion as $result) {
            $array_calificacion[$result->getPuntos()]=$result->getNomCalificacion();
        }

        if (!empty($lista_modulos) & !empty($lista_servicios) ) {

            //INICIO VARIABLES
            //$array_horas= array('8:00-9:00','9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00');
            //FIN VARIABLES

            //INICIO ENCABEZADO
            $html="<center><h2>Estad√≠sticas de Calificaciones<br>Desde $desde hasta $hasta</h2></center>";
            $html.="<table align='center'><tr><td>";
            $html.="<table align='left'>";
            $html.="<tr><td><b>Turnos con duraci√≥n a partir de:</b></td><td align='right'>$forma_duracion</td></tr>";
            $html.="</table></tr></td>";
            //FIN ENCABEZADO
            $html.="<script type='text/javascript' src='../../js/ventana_secundaria.js'></script>";
            $html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            //$html.="<tr><td style='background-color:#fff;'>";

            $array_encabezado_modulo=array(
                    'TC'=>array('etiqueta'=>'TC','titulo'=>'Numero de turnos calificados'),
                    'P'=>array('etiqueta'=>'Prom','titulo'=>'Promedio'),
                    'C'=>array('etiqueta'=>'Calif','titulo'=>'Calificacion')
            );

            $cont_encabezado=count($array_encabezado_modulo);

            $html.="<table align='center'>";
            $html.="<tr>";
            $html.="<th style='$fondo_titulo'>Pregunta</th>";
            foreach($lista_modulos as $modulo_id) {
                foreach ($this->lista_modulos_calif as $key=>$valor) {
                    if ($key == $modulo_id) {
                        $html.="<th colspan='$cont_encabezado' style='$fondo_titulo'>MOD $valor</th>";
                    }
                }
            }
            $html.="<th style='$fondo_titulo' title='Total Turnos Calificados'>TTC</th>";
            $html.="<th style='$fondo_titulo' title='Promedio por pregunta'>Prom</th>";
            $html.="<th style='$fondo_titulo' title='Calificacion por pregunta'>Calif</th>";
            $html.="<th style='$fondo_titulo' title='Graficar'>Graficar</th>";
            $html.="</tr>";

            $html.="<tr>";
            $html.="<th style='$fondo_titulo'></th>";
            foreach ($lista_modulos as $mod) {
                foreach ($array_encabezado_modulo as $valor)
                    $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
            }
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";
            $html.="</tr>";

            $cont_preguntas= count($lista_preguntas);
            $cont_modulos= count($lista_modulos);       //numero de columnas

            $i=0;
            foreach($lista_preguntas as $pregunta_id) {  //por filas (preguntas)
                $i+=1;
                $j=0;
                $array_pregunta=array();        //para graficar x pregunta
                $total_turnos_calificados=0;
                $html.="<tr>";
                foreach ($this->lista_preguntas_calif as $key=>$valor) {
                    if ($key == $pregunta_id) {
                        $html.="<td style='$fondo_titulo'>$valor</td>";
                    }
                }

                $sum_promedios=0;

                $array_modulo=array();
                $array_fila=array();
                foreach($lista_modulos as $modulo_id) {
                    $j+=1;
                    $turnos_calificados=$reportes->turnosCalificados($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion);
                    $html.="<td>$turnos_calificados</td>";
                    $promedio=round($reportes->promedioCalificacion($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion));
                    $html.="<td>$promedio</td>";
                    $nom_calificacion=$reportes->nombreCalificacion($array_calificacion, $promedio);
                    $html.="<td>$nom_calificacion</td>";
                    $total_turnos_calificados+=$turnos_calificados;
                    $sum_promedios+=$promedio;
                    $array_modulo[]=$promedio;

                    $array[$pregunta_id][$modulo_id]=$promedio; //***

                }
                $array_pregunta[$pregunta_id]=$array_modulo;
                $array_fila[]=$array_pregunta;          //formo array multi para est·ndar pero solo x pregunta

                $promedio_pregunta=round($sum_promedios/$cont_modulos);
                $nom_calificacion=$reportes->nombreCalificacion($array_calificacion, $promedio_pregunta);
                $html.="<td>$total_turnos_calificados</td>";
                $html.="<td>$promedio_pregunta</td>";
                $html.="<td>$nom_calificacion</td>";
                $html.="<td><a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_fila)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar</a><td>";

                $html.="</tr>";
                $array_todos[]=$array_pregunta;         //formo array multi para est·ndar todas las preguntas
            }
            $html.="<tr>";
            /*$html.="<th style='$fondo_titulo'></th>";
            $array_mod=array();
            foreach ($lista_modulos as $modulo_id){
                $array_val=array();
                foreach($lista_preguntas as $pregunta_id){
                    $array_val[]=$array[$pregunta_id][$modulo_id];
                }
                $array2[$modulo_id]=$array_val;
                $array_mod[]=$array2;
                $html.="<th colspan='$cont_encabezado' style='$fondo_titulo'><a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_mod)."&array_categorias=".$this->compactarArray($lista_preguntas)."&array_etiqueta_fila=".$this->compactarArray($this->lista_modulos_calif)."&etiqueta_categoria=PREG.')>Graficar</a></th>";
            }
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";
            $html.="<th style='$fondo_titulo'></th>";*/
            $html.="</tr>";
            $html.="</table>";
            $html.="<table align='center'><tr><td>";
            $html.="<a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_todos)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar Todos</a>";
            $html.="</tr></td></table>";
        } else
            $html.="No ha seleccionado alg√∫n m√≥dulo o servicio";
        echo $html;
    }

    /*
     * Gr·fico en picos
    */
    public function graficarPicosAction() {
        $array_categorias_aux=$_GET['array_categorias'];    //categorias son los x's, es un valor definido
        $etiqueta_categoria=$_GET['etiqueta_categoria'];
        $array_categorias = stripslashes($array_categorias_aux);
        $array_categorias = urldecode($array_categorias);
        $array_categorias = unserialize($array_categorias);
        /*foreach($array_categorias as $valor){
            echo "Categoria = ".$valor."<br>";
        }*/

        $array_etiqueta_fila_aux=$_GET['array_etiqueta_fila'];  //es la parte de abajo
        $array_etiqueta_fila = stripslashes($array_etiqueta_fila_aux);
        $array_etiqueta_fila = urldecode($array_etiqueta_fila);
        $array_etiqueta_fila = unserialize($array_etiqueta_fila);
        /*foreach ($array_etiqueta_fila as $key=>$etiqueta) {
                echo "key: ".$key." etiqueta: ".$etiqueta."<br>";
        }*/

        $array_serie_aux=$_GET['array_serie'];              //matriz para series
        $array_serie = stripslashes($array_serie_aux);
        $array_serie = urldecode($array_serie);
        $array_serie = unserialize($array_serie);
        /*foreach ($array_serie as $indice1=> $valor) {
            echo $indice1." = ".$valor."<br>";
            foreach ($valor as $indice2 => $valor2) {
                echo $indice2." = ".$valor2."<br>";
                foreach ($valor2 as $indice3 => $valor3) {
                    echo $indice3." = ".$valor3."<br>";
                }
            }
        }*/

        //$etiqueta_categoria="MOD. ";
        $encabezado= $this->encabezadoGraficoPicos($array_categorias, $etiqueta_categoria);

        //INCIO DE LA SERIE
        $html="";
        $html.="series: [";
//        $html.="{";
//                $html.="name: '".$nom_pregunta."',";
//                $html.="data: [";
//                foreach ($tmp as $indice1=> $valor) {
//                    $html.=$valor['promedio'].",";
//                }
//                $html.="]";
//        $html.="},";
        foreach ($array_serie as $indice1=> $valor) {
            $html.="{";
            foreach ($valor as $indice2 => $valor2) {
                //busca etiqueta de la fila
                foreach ($array_etiqueta_fila as $key=>$etiqueta) {
                    if ($key == $indice2) {
                        $nom_pregunta=$etiqueta;
                    }
                }
                $html.="name: '".$nom_pregunta."',";
                $html.="data: [";
                //forma los valores por la fila
                foreach ($valor2 as $valor3) {
                    $html.=$valor3.",";
                }
                $html.="]";
            }
            $html.="},";
        }
        $html.="]";
        //FIN DE LA SERIE

        $pie=$this->pieGraficoPicos();
        echo $encabezado.$html.$pie;
    }

    public function encabezadoGraficoPicos($array_categorias, $etiqueta_categoria) {
        $html= "";
        $html.=" <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>";
        $html.="<html>";
        $html.="<head>";
        $html.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        $html.="<title>Highcharts Example</title>";


        $html.="<!-- 1. Add these JavaScript inclusions in the head of your page -->";
        $html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='../../js/highcharts.js'></script>";

        $html.="<!-- 1a) Optional: the exporting module -->";
        $html.="<script type='text/javascript' src='../../js/modules/exporting.js'></script>";


        $html.="<!-- 2. Add the JavaScript to initialize the chart on document ready -->";
        $html.="<script type='text/javascript'>";

        $html.="var chart;";

        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'line'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos atendidos por dias'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        //$html.="categories: ['Mod1', 'Mod2', 'Mod3', 'Mod4',]";     //son las etiquetas de la parte de abajo
        $html.="categories: [";
        foreach ($array_categorias as $valor) {
            $html.="'".$etiqueta_categoria.$valor."',";        //Ejmp: "Modulo 1"
        }
        $html.="]";
        $html.="},";
        $html.="yAxis: {";
        $html.="title: {";
        $html.="text: 'Turnos'";
        $html.="}";
        $html.="},";
        $html.="tooltip: {";
        $html.="enabled: false,";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.series.name +'</b><br/>'+";
        $html.="this.x +': '+ this.y +'∞C';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="line: {";
        $html.="dataLabels: {";
        $html.="enabled: true";
        $html.="},";
        $html.="enableMouseTracking: false";
        $html.="}";
        $html.="},";
        return $html;
    }

    public function pieGraficoPicos() {
        $html="";
        $html.="});";
        $html.="});";
        $html.="</script>";
        $html.="</head>";
        $html.="<body>";
        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 800px; height: 400px; margin: 0 auto'></div>";
        $html.="</body>";
        $html.="</html>";
        return $html;
    }

    public function compactarArray($array) {
        $array_compactada=serialize($array);
        $array_compactada=urlencode($array_compactada);
        return $array_compactada;
    }
}
