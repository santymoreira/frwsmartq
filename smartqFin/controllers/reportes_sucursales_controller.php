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
        $queryTotalSucursal=array();
        $queryTotalCajasSucursal=array();
        $lista_modulos_selecionados=array();
    }
    public function turnosAtendidosSinTiqueAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //INICIO LISTA DE MÃ¯Â¿Â½DULOS
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
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
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $this->lista_modulos_tad[$key]=$val;
        }
        //FIN LISTA DE MÃ¯Â¿Â½DULOS

        //INICIO LISTA DE SERVICIO
        $servicio=new Servicio();
        $buscaServicios=$servicio->find("conditions: estado= 1","order: nombre");
        //$this->lista_servicios[0]="Todos";
        foreach($buscaServicios as $result) {
            $this->lista_servicios_tad[$result->getId()]=$result->getNombre();
        }
        //FIN LISTA DE SERVICIO
    }
    public function turnosAtendidosConTiqueAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
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
     * Inicio para el reporte de calificaciones por m�dulos
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
        //INICIO LISTA DE M�DULOS
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
        //FIN LISTA DE M�DULOS

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
     * Permite listas los servicios o los grupos de servicios seg�n la opci�n seleccionada en turnosEmitidos.html
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


        //Inicio c�lculo de dias entre fechas
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
        $html.="this.x +': '+ this.y +'�C';";
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
    *Funcion que permite obtener las cajas de cada una de las sucursales
    */
    public function obtenerCajasSucursalesAction() {
        $this->setResponse('json');
        $sucursales=   $this->getPostParam('sucursal_id');
        $db = DbBase::rawConnect();
        $html_cajas="";
        // $html_servicios="";
        $lista_id_sucursales=explode(",",$sucursales);

        foreach($lista_id_sucursales as $sucursal_id) {
            $result = $db->query("select usuario_sucursal,numero_caja_sucursal, caja_id_sucursal from sinccajas WHERE sucursal_id=$sucursal_id group by numero_caja_sucursal;");
            $col=0;
            $array_valores=array();
            while($row = $db->fetchArray($result)) {
                $numero=$row['numero_caja_sucursal'];
                $caja_id_sucursal =$row['caja_id_sucursal'];
                $array_valores[]=Tag::checkboxField("chkmodulos[]", "value: $caja_id_sucursal ","checked: ").$row['usuario_sucursal']."&nbsp;&nbsp;";
            }
            //$html_modulos.= "<input id='chk_all_modulos' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
            $html_cajas.="<fieldset>";
            $html_cajas.=" <legend><b>Sucursal N-$sucursal_id</b></legend>";
            $html_cajas.= "<table>";
            $cont_mod=count($array_valores);
            $cont_filas=ceil($cont_mod/2);
            $x=$cont_filas*2;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) { //filas
                $html_cajas.= "<tr>";
                for ($c=1;$c<=2;$c++) { //columnas
                    $html_cajas.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html_cajas.= "</tr>";
            }
            $html_cajas.= "</table>";
            $html_cajas.="</fieldset>";
        }

//        foreach($lista_id_sucursales as $sucursal_id) {
//            $result = $db->query("SELECT nombre_servicio, servicio_id_sucursal FROM sincturnos WHERE sucursal_id=$sucursal_id GROUP BY nombre_servicio");
//            $col=0;
//            $array_valores=array();
//            while($row = $db->fetchArray($result)) {
//                $nombre_servicio     =$row['nombre_servicio'];
//                $servicio_id_sucursal =$row['servicio_id_sucursal'];
//                $array_valores[]=Tag::checkboxField("chkservicios[]", "value: $servicio_id_sucursal","checked: ").$nombre_servicio."&nbsp;&nbsp;";
//            }
//            //$html_servicios.= "<input id='chk_all_servicios' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
//            $html_servicios.="<fieldset>";
//            $html_servicios.=" <legend><b>Sucursal N-$sucursal_id</b></legend>";
//            $html_servicios.= "<table>";
//            $cont_mod=count($array_valores);
//            $cont_filas=ceil($cont_mod/2);
//            $x=$cont_filas*2;
//            $y=$x-$cont_mod;
//            for ($z=1;$z<=$y;$z++)
//                $array_valores[]="";
//            $cont_key=0;
//            for ($f=1; $f<=$cont_filas; $f++) { //filas
//                $html_servicios.= "<tr>";
//                for ($c=1;$c<=2;$c++) { //columnas
//                    $html_servicios.= "<td>".$array_valores[$cont_key]."</td>";
//                    $cont_key+=1;
//                }
//                $html_servicios.= "</tr>";
//            }
//            $html_servicios.= "</table>";
//            $html_servicios.="</fieldset>";
//        }

        $datos= array( "cajas"=>$html_cajas);
        return ($datos);
    }
    /*
     * Funcion que retorna los modulos y servicios de la sucursal seleccionada
    */
    public $NombreModulosSucursal=array();
    public function obtenerModulosServiciosAction() {
        $this->setResponse('json');
        $sucursales=   $this->getPostParam('sucursal_id');
        $db = DbBase::rawConnect();

        $html_modulos="";
        $html_servicios="";
        $lista_id_sucursales=explode(",",$sucursales);
        $lista_modulos=array();
        foreach($lista_id_sucursales as $sucursal_id) {
            $result = $db->query("select usuario,numero_modulo, modulo_id_sucursal from sincturnos WHERE sucursal_id=$sucursal_id ;");
            $col=0;
            $array_valores=array();
            $lista_modulos=array();
            while($row = $db->fetchArray($result)) {
                $numero      =$row['numero_modulo'];
                $lista_modulos[$row['modulo_id_sucursal']]=$row['usuario'];
                $modulo_id_sucursal =$row['modulo_id_sucursal'];
//               $array_valores[]=Tag::checkboxField("chkmodulos[]", "value: $modulo_id_sucursal ","checked: ").$row['usuario']."&nbsp;&nbsp;";
            }
//            print_r($lista_modulos);
            $arra_mod=array();
            $arr_mod=array_flip(array_flip($lista_modulos));
//            print_r($arr_mod);
            Foreach($arr_mod as $key=>$usuario) {
                $array_valores[]=Tag::checkboxField("chkmodulos[]", "value: $key ","checked: ").$usuario."&nbsp;&nbsp;";
                $this->NombreModulosSucursal[$sucursal_id][$key]=$usuario;
            }
            //print_r($this->NombreModulosSucursal);
            //$html_modulos.= "<input id='chk_all_modulos' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
            $html_modulos.="<fieldset>";
            $html_modulos.=" <legend><b>Sucursal N-$sucursal_id</b></legend>";
            $html_modulos.= "<table>";
            $cont_mod=count($array_valores);
            $cont_filas=ceil($cont_mod/2);
            $x=$cont_filas*2;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) { //filas
                $html_modulos.= "<tr>";
                for ($c=1;$c<=2;$c++) { //columnas
                    $html_modulos.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html_modulos.= "</tr>";
            }
            $html_modulos.= "</table>";
            $html_modulos.="</fieldset>";
        }

        foreach($lista_id_sucursales as $sucursal_id) {
            $result = $db->query("SELECT nombre_servicio, servicio_id_sucursal FROM sincturnos WHERE sucursal_id=$sucursal_id GROUP BY nombre_servicio");
            $col=0;
            $array_valores=array();
            while($row = $db->fetchArray($result)) {
                $nombre_servicio     =$row['nombre_servicio'];
                $servicio_id_sucursal =$row['servicio_id_sucursal'];
                $array_valores[]=Tag::checkboxField("chkservicios[]", "value: $servicio_id_sucursal","checked: ").$nombre_servicio."&nbsp;&nbsp;";
            }
            //$html_servicios.= "<input id='chk_all_servicios' type='checkbox'/><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
            $html_servicios.="<fieldset>";
            $html_servicios.=" <legend><b>Sucursal N-$sucursal_id</b></legend>";
            $html_servicios.= "<table>";
            $cont_mod=count($array_valores);
            $cont_filas=ceil($cont_mod/2);
            $x=$cont_filas*2;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) { //filas
                $html_servicios.= "<tr>";
                for ($c=1;$c<=2;$c++) { //columnas
                    $html_servicios.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html_servicios.= "</tr>";
            }
            $html_servicios.= "</table>";
            $html_servicios.="</fieldset>";
        }

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

        $modulos=$this->getPostParam('chkmodulos');
        $servicios=$this->getPostParam('chkservicios');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_condicion_servicios="";
        $lista_modulos=explode(",",$modulos);
        $lista_servicios=explode(",",$servicios);
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
            $array_dias_semana = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo');
            $array_encabezado_dia=array(
                    'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                    'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                    'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
                    'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención'),
                    'Pll'=>array('etiqueta'=>'Pll','titulo'=>'Promedio de Llamada a Atención'),
                    'ES'=>array('etiqueta'=>'','titulo'=>'',)
            );
            $cont_encabezado=count($array_encabezado_dia);
            $fondo_titulo= "background-color:#328aa4; color:#fff";

            //INICIO SEPARAR POR SEMANAS
            $s = strtotime($hasta)-strtotime($desde);
            $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas

            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;   //solo para q empiece desde un d�a menos

            $forma_array_semanas= array();
            $array_aux= array();
            $aux="";

            $dias= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');

            for ($i=1;$i<=$d;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ; //a la fecha anterior aumenta un d�a
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
            <tr><td><b>Turnos con duración a partir de:</b></td><td align='right'>$forma_duracion</td></tr>
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
                $html.="<th style='$fondo_titulo'>Módulo</th>";
                $html.="<th style='$fondo_titulo'>Usuario</th>";
                //$html.="<th style='background-color:#328aa4; color:#fff'></th>";
                foreach ($array_dias_semana as $dia) {
                    $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$dia</th>";
                }
                $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
                $html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
                $html.="<th style='$fondo_titulo' title='Promedio de Atención Lunes a Viernes'>PA</th>";
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
            $html.="No ha seleccionado algún módulo o servicio";
            //$this->routeTo("action: turnosAtendidosDias");
        }
//        $dat=array('datost'=>$html);
//        print_r($dat);
//        return($dat);
        echo($html);
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
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención')
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
        $html.="this.x +': '+ this.y +'�C';";
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
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención')
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
        $html.="this.x +': '+ this.y +'�C';";
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
        $html.="this.x +': '+ this.y +'�C';";
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
        $html.="this.x +': '+ this.y +'�C';";
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
        $html.="this.x +': '+ this.y +'�C';";
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
        $array_dias_semana = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
        $array_encabezado_dia=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                //'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención'),
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
     * Inicio para el reporte de los turnos atendidos por m�dulo
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
        //Lista de M�dulos
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
     * Inicio de exportaci�n a Excel de Turnos
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
        //Lista de M�dulos
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
     * Inicio de exportaci�n a Excel de Colas
    */
    public function colasExcelAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de M�dulos
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
     * Men� para Reporte que muestra el cuador de los totales de turnos atendidos por d�as
    */
    public function turnosAtendidosDiasAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
    }

    /*
     * Men� para Reporte que muestra los totales de colas atendidos por d�as
    */
    public function colasAtendidosDiasAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));

        //INICIO LISTA DE M�DULOS
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
        //FIN LISTA DE M�DULOS
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
        //Lista de M�dulos
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
     * Reporte que muestra los totales de turnos atendidos por M�dulos
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
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por Módulo");
        $pdf->lineFeed();

        //La fecha del reporte
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->writeCell(40, 7, "Fecha de Reporte: ".Date::getCurrentDate());
        $pdf->lineFeed();

        //Encabezados con fondo gris
        $lightGray = PdfColor::fromGrayScale(0.75);
        $pdf->setFillColor($lightGray);
        $pdf->writeCell(10, 7, '#', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'Módulo', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(55, 7, 'Descripción', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atención', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atención', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'FFA', 0, 0, PdfDocument::ALIGN_CENTER);
        //$pdf->writeCell(15, 7, 'HIA', 0, 0, PdfDocument::ALIGN_CENTER);
        $pdf->writeCell(20, 7, 'Duración', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS M�DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccion� un m�dulo
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
                        $pdf->writeCell(40, 7, "Duración: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los m�dulos
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
                            $pdf->writeCell(40, 7, "Duración: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS M�DULOS
        $pdf->outputToBrowser();

    }

    /*
     * Reporte que muestra los totales de turnos atendidos por D�as
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
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por Módulo");
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
        $pdf->writeCell(20, 7, 'Módulo', 0, 0, PdfDocument::ALIGN_LEFT);
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

        //INICIO OBTENER LOS M�DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccion� un m�dulo

            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";

            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada d�a
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
            $pdf->writeCell(20, 7, 'Módulo', 0, 0, PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los m�dulos osea &id_caja==0
            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";
            $promedio_lunes=0;
            $promedio_martes=0;
            $promedio_miercoles=0;
            $promedio_jueves=0;
            $promedio_viernes=0;
            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada d�a
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
            $pdf->writeCell(20, 7, 'Módulo', 0, 0, PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS M�DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Reporte en PDF del reporte de turnso atendidos por caja
     * filtro por m�dulo y por servicio
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
        $pdf->writeCell(40, 7, 'Descripción', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(35, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(35, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atención', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atención', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(20, 7, 'Duración', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS M�DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccion� una caja
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
                        $pdf->writeCell(40, 7, "Duración: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los m�dulos
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
                            $pdf->writeCell(40, 7, "Duración: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS M�DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Funcion que construye los datos para excel en tabla
    */
    public function reporteTurnosAtendidosModuloXlsAction() {
        $this->setResponse('ajax');
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        $modulos=$this->getPostParam('chkmodulos');
        $servicios=$this->getPostParam('chkservicios');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_condicion_servicios="";
        $lista_modulos=explode(',',$modulos);
        $lista_servicios=explode(',',$servicios);
        if (!empty($servicios) & !empty($modulos)) {
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
            $html.="<th>Num. Módulo</th>";
            $html.="<th>Servicio</th>";
            $html.="<th>Num. turno</th>";
            $html.="<th>Fecha emisión</th>";
            $html.="<th>Hora emisión</th>";
            $html.="<th>Anulado</th>";
            $html.="<th>F. inicio atención</th>";
            $html.="<th>H. inicio atención</th>";
            $html.="<th>F. fin atención</th>";
            $html.="<th>H. fin atención</th>";
            $html.="<th>Duración</th>";
            $html.="</tr>";

            $anulado="";
            //if (!empty($query)) {
            foreach($lista_modulos as $id_modulo) {
                $id_modulo=trim($id_modulo);
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
            $html.="No ha seleccionado algún módulo o servicio";
        echo $html;
    }

    /*
     * Funcion que construye los datos para excele en tabla
    */
    public function reporteColasAtendidasCajaXlsAction() {
        $this->setResponse('ajax');
        $sucursales_ids=$this->getPostParam('sucursal_id');
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
        $html.="<th>Descripción Sucursal</th>";
        $html.="<th>Usuario</th>";
        $html.="<th>F. inicio atención</th>";
        $html.="<th>H. inicio atención</th>";
        $html.="<th>F. fin atención</th>";
        $html.="<th>H. fin atención</th>";
        $html.="<th>Duración</th>";
        $html.="</tr>";
        if ($id_caja==0) {  //Todas las cajas
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'  AND {#Sinccajas}.duracion>='00:00:00'";
        } else { //Selecciono una caja
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND {#Sinccajas}.sucursal_id IN($sucursales_ids) AND {#Sinccajas}.caja_id_sucursal IN( $id_caja) AND {#Sinccajas}.duracion>='00:00:00'";
        }
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Sinccajas","Sucursal"),
                        "fields" => array(
                                "{#Sinccajas}.numero_caja_sucursal",
                                "{#Sucursal}.alias_sucursal",
                                "{#Sinccajas}.usuario_sucursal",
                                "{#Sinccajas}.fecha_inicio_atencion",
                                "{#Sinccajas}.hora_inicio_atencion",
                                "{#Sinccajas}.fecha_fin_atencion",
                                "{#Sinccajas}.hora_fin_atenciom",
                                "{#Sinccajas}.duracion"),
                        "conditions" => $condicion,
                        //"order"=>"{#Colas}.hora_inicio_atencion"
                        "order"=>"{#Sinccajas}.numero_caja_sucursal"
        ));
        $anulado="";
        if (!empty($query)) {
            foreach($query->getResultSet() as $result) {
                $html.="<tr>";
                $html.="<td>".$result->getNumeroCajaSucursal()."</td>";
                $html.="<td>".$result->getAliasSucursal()."</td>";
                $html.="<td>".$result->getUsuarioSucursal()."</td>";
                //$html.="<td>".$anulado."</td>";
                $html.="<td>".$result->getFechaInicioAtencion()."</td>";
                $html.="<td>".$result->getHoraInicioAtencion()."</td>";
                $html.="<td>".$result->getFechaFinAtencion()."</td>";
                $html.="<td>".$result->getHoraFinAtenciom()."</td>";
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
     * Funci�n que exporta a excel
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
     * Menu: Estad�sticas de calificaciones
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
            $html="<center><h2>Estadísticas de Calificaciones<br>Desde $desde hasta $hasta</h2></center>";
            $html.="<table align='center'><tr><td>";
            $html.="<table align='left'>";
            $html.="<tr><td><b>Turnos con duración a partir de:</b></td><td align='right'>$forma_duracion</td></tr>";
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
                $array_fila[]=$array_pregunta;          //formo array multi para est�ndar pero solo x pregunta

                $promedio_pregunta=round($sum_promedios/$cont_modulos);
                $nom_calificacion=$reportes->nombreCalificacion($array_calificacion, $promedio_pregunta);
                $html.="<td>$total_turnos_calificados</td>";
                $html.="<td>$promedio_pregunta</td>";
                $html.="<td>$nom_calificacion</td>";
                $html.="<td><a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_fila)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar</a><td>";

                $html.="</tr>";
                $array_todos[]=$array_pregunta;         //formo array multi para est�ndar todas las preguntas
            }
            $html.="<tr>";
            $html.="</tr>";
            $html.="</table>";
            $html.="<table align='center'><tr><td>";
            $html.="<a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_todos)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar Todos</a>";
            $html.="</tr></td></table>";
        } else
            $html.="No ha seleccionado algún módulo o servicio";
        echo $html;
    }

    /*
     * Gr�fico en picos
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
        $html.="this.x +': '+ this.y +'�C';";
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

    public $queryTotalSucursal=array(); //para un query global

    public function LLenaQuerySucursalAction() {
        $this->setResponse("json");
        $id_sucursal=$this->getPostParam("sucursal_id");
        $servicios=$this->getPostParam("chkservicios");
        $modulos=$this->getPostParam("chkmodulos");
        $desde=$this->getpostParam("desde");
        $hasta=$this->getpostParam("hasta");
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $lista_modulos=explode(",",$modulos);
        $lista_servicios=explode(",",$servicios);
        $html="";
        if ($modulos!="") {
            $condicion="sucursal_id IN ($id_sucursal) AND fecha_emision BETWEEN '$desde' AND '$hasta' AND modulo_id_sucursal IN($modulos) AND servicio_id_sucursal IN ($servicios) AND duracion>='$forma_duracion'" ;
            $sucursal= new Sincturnos();
            $sql= $sucursal->find("$condicion");
            $cont=0;
            $a="";
            $this->queryTotalSucursal=array();

            foreach($sql as $result) {
                $this->queryTotalSucursal[]=array('id'=>$result->getId(),'sucursal_id'=>$result->getSucursalId(),
                        'base_datos'=>$result->getBaseDatos(),'NombreUsuario'=>$result->getUsuario(),
                        'NumeroModulo'=>$result->getNumeroModulo(),'Modulo_id_Sucursal'=>$result->getModuloIdSucursal(),
                        'nombre_servicio'=>$result->getNombreServicio(),'Letra'=>$result->getLetra(),
                        'servicio_id_sucursal'=>$result->getServicioIdSucursal(),
                        'TurnoNumero'=>$result->getNumeroTurno(),'TurnoFechaEmision'=>$result->getFechaEmision(),
                        'TurnoHoraEmision'=>$result->getHoraEmision(),'TurnoAtendido'=>$result->getAtendido(),
                        'TurnoRechazado'=>$result->getRechazado(),
                        'TurnoFechaInicioAtencion'=>$result->getFechaInicioAtencion(),'TurnoHoraInicioAtencion'=>$result->getHoraInicioAtencion(),
                        'TurnoFechaFinAtencion'=>$result->getFechaFinAtencion(),'TurnoHoraFinAtencion'=>$result->getHoraFinAtencion(),
                        'TurnoDuracion'=>$result->getDuracion(),'rechazado'=>$result->getRechazado(),'calificacion'=>$result->getCalificacion());
//                $cont++;
            }
            $html= "Exitoso";
//            print_r($this->queryTotalSucursal);
        }
        else $html="No existe Datos";
        $arraycks=array('html'=>$html);
        return($arraycks);
    }
    public $queryTotalCajasSucursal=array(); //para un query global
    public function LLenaQueryCajasAction() {
        $this->setResponse("json");
        $id_sucursal=$this->getPostParam("sucursal_id");
        //$servicios=$this->getPostParam("chkservicios");
        $cajas=$this->getPostParam("chkmodulos");
        $desde=$this->getpostParam("desde");
        $hasta=$this->getpostParam("hasta");
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $lista_cajas=explode(",",$cajas);
//        $lista_servicios=explode(",",$servicios);
        $html="";
        if ($cajas!="") {
            $condicion="sucursal_id IN ($id_sucursal) AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND caja_id_sucursal IN($cajas) AND duracion>='$forma_duracion'" ;
            $sucursal= new Sinccajas();
            $sql= $sucursal->find("$condicion");
            $cont=0;
            $a="";
            $this->queryTotalCajasSucursal=array();
            foreach($sql as $result) {
                $this->queryTotalCajasSucursal[]=array('id'=>$result->getId(),'sucursal_id'=>$result->getSucursalId(),
                        'base_datos'=>$result->getBaseDatos(),'NombreUsuario'=>$result->getUsuarioSucursal(),
                        'NumeroCaja'=>$result->getNumeroCajaSucursal(),'caja_id_Sucursal'=>$result->getCajaIdSucursal(),
                        'CajaFechaInicioAtencion'=>$result->getFechaInicioAtencion(),'CajaHoraInicioAtencion'=>$result->getHoraInicioAtencion(),
                        'CajaFechaFinAtencion'=>$result->getFechaFinAtencion(),'CajaHoraFinAtencion'=>$result->getHoraFinAtenciom(),
                        'CajaDuracion'=>$result->getDuracion());
//                $cont++;
            }
            $html= "Exitoso";
//            print_r($this->queryTotalCajasSucursal);
        }
        else $html="No existe Datos";
        $arraycks=array('html'=>$html);
        return($arraycks);
    }
    public function  CalidadServicioAction() {
        $this->setResponse("json");
        $modulos=$this->getPostParam('modulos');
        $id_sucursal=$this->getPostParam('sucursal_id');
        $desde=$this->getPostParam('desde');
        $hasta =$this->getPostParam('hasta');
        $tipo=$this->getPostParam('graficar');
        $lista_modulos=explode(",", $modulos);
        $lista_id_sucursales=explode(',',$id_sucursal);
        $series=array();
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>CALIDAD DE SERVICO ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        //INICIO BUSCAR MODULOS

        $j=0;
        $modulo=new ReportesSucursales();
        foreach($lista_modulos as $val) {
            $lista[$j]=$modulo->nombremodulos($id_sucursal,$val);
            $j++;
        }
        natsort($lista);
        foreach($lista_id_sucursales as $id_sucursal) {
            foreach($lista_modulos as $modulo_id) {
                foreach($this->queryTotalSucursal as $result) {
                    if (trim($id_sucursal)==trim($result["sucursal_id"])&&trim($modulo_id)==trim($result["Modulo_id_Sucursal"])) {
                        $lista_nombres[]=array('id_sucursal'=>trim($id_sucursal),'idModulo'=>$result['Modulo_id_Sucursal'],'nombre'=>$result['NombreUsuario']);
                        break;
                    }
                }
            }
        }
// Ahora en la columna 1 cargo los valores de la sucursales
        $nombres=array();
        $array_aux=array();
        $mi_array=array();
        foreach($lista_nombres as $result) {
            $array_aux[]=$result["id_sucursal"]."-".$result["idModulo"];
        }
        $encontro=false;
        $arr = array();
        $arr= array_flip(array_flip($array_aux));
        $cont=0;
        $cont=$cont+1;
//        print_r($lista_nombres);
        foreach($arr as $key=>$valor) {
            $arr_aux=explode('-',$valor);
            foreach($lista_nombres as $val) {
                if($val["id_sucursal"]==$arr_aux[0]&&$val["idModulo"]==$arr_aux[1]&& $encontro==false) {
                    $nombres[]="Suc".$arr_aux[0]."-".$this->NombreModulosSucursal[$arr_aux[0]][$arr_aux[1]];
                    $excelente= 0;
                    $muybueno=0;
                    $bueno=0;
                    $regular=0;
                    foreach($this->queryTotalSucursal as $result) {
                        if($result["sucursal_id"]==$arr_aux[0]&&$result['Modulo_id_Sucursal']==$val["idModulo"]) {
                            $calif= $result['calificacion'];
                            if ($calif=="Excelente")
                                $excelente++;
                            else if ($calif=="Muy Bueno")
                                $muybueno++;
                            else if ($calif=="Bueno")
                                $bueno++;
                            else if ($calif=="Regular")
                                $regular++;
                        }
                    }
                    $datos[$valor] = array($val["id_sucursal"], $excelente, $muybueno, $bueno, $regular);
                    $totales[]=$this->totalModulosAtendidosPorFecha($val["id_sucursal"],$val["idModulo"],$val['nombre']);
                    $encontro=true;
                    break;
                }
                else
                    $encontro=false;
            }
        }
//        print_r($datos);
//        die();

        $i=0;
        foreach($nombres as $val) {
            $mi_array[]=array("nombre"=>$val,"total"=>$totales[$i]);
            $i++;
        }
        //INICIO BUSCAR CAJAS
        if ($modulos!="") {
            $cont=0;
            $cont=$cont+1;
            $htmltabla="<center><table border=1 width='100%'><tr style='$fondo_titulo'>";
            $htmltabla.="<th>Modulos Atendidos</th><th>Excelente</th><th>Muy Bueno</th><th> Bueno</th><th> Regular</th></tr>";// Insuficiente</th></tr>";
            $lista_datos=Array();
            $i=0;
            foreach ($datos as $key => $fila) {
                $ex[$key]  = $fila[1]; // columna de excelente
                $bue[$key] = $fila[2]; //columna de bueno
                $reg[$key] = $fila[3]; //columna de regular
                $mal[$key] = $fila[4]; //columna de malo
                $arr_nom=explode('-',$key);
                $htmltabla.="<tr><td> Sucursal $arr_nom[0]-".$this->NombreModulosSucursal[$arr_nom[0]][$arr_nom[1]]."</td>";
                $htmltabla.="<td>".$fila[1]."</td><td>".$fila[2]."</td><td>".$fila[3]."</td><td>".$fila[4]."</td></tr>";//.$fila[5]."</td></tr>";
                $lista_datos[$i++]=$fila[1];
                $lista_datos[$i++]=$fila[2];
                $lista_datos[$i++]=$fila[3];
                $lista_datos[$i++]=$fila[4];
                $i++;
            }

            $htmltabla.="</table></center>";
        }
        else {
            $htmltabla="No existe datos";
            $ex = new aray(); // columna de excelente
            $bue = new aray(); //columna de bueno
            $reg = new aray(); //columna de regular
            $mal = new aray();
//            $insuf = new aray();
        }

        //FIN BUSCAR CAJAS

        //INICIO FORMAR COLUMNAS DE CALIFICACION

        //FIN FORMAR COLUMNAS DE CALIFICACION
        $nom=implode(',', $lista);
        $tot=implode(',', $lista_datos);
        if($tipo=="barras") {
            $graficaCalidad="";
            $i=0;
            $j=0;
            $lista_datos=array();
            foreach($lista_id_sucursales as $id_sucursal) {
                $nom=array();
                $tot=array();
                $lista_datos=array();
                $i=0;
                foreach($datos as $key=>$fila) {
                    $list=explode('-',$key);
                    if ($list[0]==$id_sucursal) {
                  
                        $lista_datos[$i++]=$fila[1];
                        $nom[$i]=$this->NombreModulosSucursal[$list[0]][$list[1]]."-Excelente";
                        
                        $lista_datos[$i++]=$fila[2];
                        $nom[$i]=$this->NombreModulosSucursal[$list[0]][$list[1]]."-MuyBueno";
                        
                        $lista_datos[$i++]=$fila[3];
                        $nom[$i]=$this->NombreModulosSucursal[$list[0]][$list[1]]."-Bueno";
                        
                        $lista_datos[$i++]=$fila[4];
                        $nom[$i]=$this->NombreModulosSucursal[$list[0]][$list[1]]."-Regular";
                        $i++;
                    }
                }
                $j++;
                $n=implode(',',$nom);
                $t=implode(',',$lista_datos);
                $graficaCalidad.=$this->GraficaBarras("Calidad",$n,$t);

            }

        }
        else {
            $nom=array();
            $lista_datos=array();
            $resultado=0;
            Foreach ($datos as $valor) {
                $resultado=$resultado+$valor[0];
            }
            if ($resultado!=0) {
                $graficaCalidad="";
                $i=0;
                $j=0;
                $lista_datos=array();
                foreach($lista_id_sucursales as $id_sucursal) {
                    $i=0;
                    $nom=array();
                               $nom[]="Excelente";//Sucursal$id_sucursal Calif:Excelente";
                                $nom[]="Muy Bueno";//"Sucursal$id_sucursal Calif:Muy Bueno";
                                $nom[]="Bueno";//"Sucursal$id_sucursal Calif:Bueno";
                                $nom[]="Regular";//"Sucursal$id_sucursal Calif:Regular";
                                $lista_datos[0]=0;
                                $lista_datos[1]=0;
                                $lista_datos[2]=0;
                                $lista_datos[3]=0;
                    foreach($arr as $val) {
                        $list=explode('-',$val);      
                        foreach($datos as $key=>$fila) {
                            $l=explode("-",$key);
                            if ($key==$val&&$l[0]==$id_sucursal) {
                                $lista_datos[0]+=$fila[1];
                                $lista_datos[1]+=$fila[2];
                                $lista_datos[2]+=$fila[3];
                                $lista_datos[3]+=$fila[4];
                                $i++;
                                //  break;
                            }
                        }
                        $j++;
                    }
//                    print_r($nom);
//                    print_r($lista_datos);
//                    die();
                    $sum=0;
                    foreach($lista_datos as $val) $sum=$sum+$val;
                    if($sum!=0)
                        $graficaCalidad.=$this->Graficadorpie("Calidad Servicio Sucursal $id_sucursal ",$nom,$lista_datos);
                    else
                        $graficaCalidad.="NO HAY DATOS DE LA SUCURSAL ".$id_sucursal;
                    $nom=array();
//                    $lista_datos=array();
                }
            }
            else $graficaCalidad="NO HAY DATOS";
        }
        $array_g= array('text'=>$titulo,'categoria'=> $lista,'serie'=>$series,'tabla'=>$htmltabla,'graficaCalidad'=>$graficaCalidad);
//        echo("Final");
//        die();
        return ( $array_g);
    }
    public function Graficador1Action() {
        $this->setResponse("json");
        $id_sucursal=$this->getPostParam('sucursal_id');
        $id_caja=$this->getPostParam('modulos');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $tipo_grafica=$this->getPostParam('Grafica');
        $servicios=$this->getPostParam('chkservicios');
        $cajas=$id_caja;
        $lista_modulos=explode(",",$id_caja);
        $lista_servicios = explode(',', $servicios);
        $total_turnos=0;
        $intTotal =0;
        $i=0;
        $intTotalModulos=array();
        $forma_condicion_servicios="";
        $mod=0;
        ////cabecera de la Tabla
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>TURNOS ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $htmltabla="<center><table border=1 width='100%' align='left'><tr style='$fondo_titulo;font-size: 12px'>";
        $htmltabla.="<th> Modulos</th><th>Total Turnos Atendidos</th></tr>";
        //INICIO BUSCAR MODULOS
        $lista1=array();
        $modulo=new ReportesSucursales();
        $lista_id_sucursales=explode(",",$id_sucursal);
        $i=0;
        $j=0;
        $lista_nombres=array();

        //                 print_r($mi_array);
        //  die();
        //        $i=0;
        //        $j=1;
        //        $cantidad_modulos=array();
        //        $cont=0;
        //        foreach($lista_nombre as $id) {
        //            $arr=array();
        //            $j=0;
        //            foreach($lista as $val) {
        //                if($j==0)
        //                    $matrizDatos[$i][$j]=$id;//$modulo->totalTunosModulos($arr,$id,$val);
        //                else
        //                    $matrizDatos[$i][$j]=$totales[$j-1];
        //                $j++;
        //            }
        //            $i++;
        //        }
        //
        //
        //        $strCategorias ="<categories>";
        //// ahora coloco los nombres de las series
        ////$strDatosAnio[]=array();
        //        $strD="";
        ////for($i=0;$i<$cantidad_modulos[0];$i++){
        ////$strD.= "<dataset seriesName='Modulo $i'>";
        ////}
        //        $strDatosAnio1 = "<dataset seriesName='Modulo 1'>";
        //        $strDatosAnio2 = "<dataset seriesName='Modulo 2'>";
        //        $strDatosAnio3 = "<dataset seriesName='Modulo 3'>";
        //
        //// recorro la matr�z y cargo los datos a un arreglo
        //// Luego concateno esos datos del arreglo al string
        //
        ////print_r($matrizDatos);
        ////die();
        //        foreach($matrizDatos as $arrayDatos) {
        //            // En la posici�n 0 tengo las categor�as (gaseosas)
        //            $strCategorias .= "<category name='" . $arrayDatos[0] . "' />";
        //            // en las siguientes posiciones tengo los valores.
        ////    for($i=0;$i<$cantidad_modulos[0]-1;$i++){
        ////$strD.="<set value='" . $arrayDatos[$i] . "' />";
        ////}
        //            $strDatosAnio1 .= "<set value='" . $arrayDatos[1] . "' />";
        //            $strDatosAnio2 .= "<set value='" . $arrayDatos[2] . "' />";
        //            $strDatosAnio3 .= "<set value='" . $arrayDatos[3] . "' />";
        //        }
        //// cierro la etiqueta categorias
        //        $strCategorias .= "</categories>";
        //// cierro las etiquetas dataset
        //        for($i=0;$i<$cantidad_modulos[0];$i++) {
        //            $strD.="</dataset>";
        //        }
        //        $strDatosAnio1 .= "</dataset>";
        //        $strDatosAnio2 .= "</dataset>";
        //        $strDatosAnio3 .= "</dataset>";
        //// Paso los par�metros generales para el gr�fico.
        //        $strXML = "<chart caption='Modulos Atendidos Modulos' numberPrefix='$' rotateValues='1' yAxisName='Recaudado $ (1k = 1.000)'>";
        //// concateno todos los string en uno solo
        //        $strXML .= $strCategorias .$strDatosAnio1.$strDatosAnio2.$strDatosAnio3;
        //// cierro la etiqueta chart
        //        $strXML .="</chart>";
        //// imprimo el gr�fico finalmente
        //// le paso como par�metro el string xml

        //        foreach($lista_id_sucursales as $id) {
        //            foreach($lista as $val) {
        //            $foreach($lista as $val) {matrizDatos[$i][0]=$modulo->nombresucursales($id);
        //            $i++;
        //        }
        //         }
        //        $i=0;
        //        $j=1;
        //
        //        $arr=array();
        //        $strDatosModulos=array();
        //        foreach($lista_id_sucursales as $id_sucursal) {
        //            $arr=array();
        //            foreach($this->queryTotalSucursal as $valor) {
        //                if($valor['sucursal_id']==$id_sucursal)
        //                    $arr[]=array("Modulo_id_Sucursal"=>$valor["Modulo_id_Sucursal"],"atendido"=>$valor["TurnoAtendido"],"rechazado"=>$valor["rechazado"]);
        //            }
        //            foreach($lista as $id) {
        //                $lista1[]=$modulo->nombremodulos($id_sucursal,$id);
        //                $matrizDatos[$i][$j]=$modulo->totalTunosModulos($arr,$id_sucursal,$id);
        //                $strDatosModulos[$i] = "<dataset seriesName='Modulo $id'>";
        //                $i++;
        //            }
        //            $j++;
        //        }
        ////        print_r($matrizDatos);
        //////        print_r($lista1);
        ////       die();
        //
        ////empieso a graficar
        //        $i=0;
        //        $strCategorias="";
        //
        //        foreach($matrizDatos as $arrayDatos) {
        //            $strCategorias.= "<category name='" . $arrayDatos[0] . "' />";
        //            foreach($lista_id_sucursales as $id) {
        //            $strDatosModulos[$i].= "<set value='" . $arrayDatos[$id] . "' />";
        //            $i++;
        //            }
        //        }
        //
        //        $strCategorias .= "</categories>";
        //        $i=0;
        //        foreach($lista_id_sucursales as $id) {
        //            $strDatosModulos[$i] .= "</dataset>";
        //            $i++;
        //        }
        //        $strXML = "<chart caption='Entrada de dinero por gaseosas' numberPrefix='$' rotateValues='1' yAxisName='Recaudado $ (1k = 1.000)'>";
        //        $strXML .= $strCategorias;
        //         $i=0;
        //        foreach($lista_id_sucursales as $id) {
        //            $strXML .= $strDatosModulos[$i];
        //            $i++;
        //        }
        //// cierro la etiqueta chart
        //$strXML .="</chart>";

        //        $html= $this->renderChartHTML('../../swf_charts/MSColumn3D.swf', '', $strXML, "maestro", 600, 500, false, false);
        if (!empty($id_caja)and !empty($servicios)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }
        $nombre=array();
        $total=0;
        $usuario="";
        $i=0;
        if (!empty($id_caja)and !empty($servicios)) {
            foreach($lista_id_sucursales as $id) {
                $lista_nombre[]=$modulo->nombresucursales($id);
            }
            foreach($lista_id_sucursales as $id_sucursal) {
                foreach($lista_modulos as $modulo_id) {
                    foreach($this->queryTotalSucursal as $result) {
                        if (trim($id_sucursal)==trim($result["sucursal_id"])&&trim($modulo_id)==trim($result["Modulo_id_Sucursal"])) {
                            $lista_nombres[]=array('id_sucursal'=>trim($id_sucursal),'idModulo'=>$result['Modulo_id_Sucursal'],'nombre'=>$result['NombreUsuario']);
                            break;
                        }
                    }
                }
            }
// Ahora en la columna 1 cargo los valores de la sucursales
            $nombres=array();
            $totales=array();
            $array_aux=array();
            $mi_array=array();
            foreach($lista_nombres as $result) {
                $array_aux[]=$result["id_sucursal"]."-".$result["idModulo"];
            }
            $encontro=false;
            $arr = array();
            $arr= array_flip(array_flip($array_aux));
            foreach($arr as $valor) {
                $arr_aux=explode('-',$valor);
                foreach($lista_nombres as $val) {
                    if($val["id_sucursal"]==$arr_aux[0]&&$val["idModulo"]==$arr_aux[1]&& $encontro==false) {
                        $nombres[]="Suc".$arr_aux[0]."-".$val["nombre"];
                        $totales[]=$this->totalModulosAtendidosPorFecha($val["id_sucursal"],$val["idModulo"],$val['nombre']);
                        $encontro=true;
                        break;
                    }
                    else
                        $encontro=false;
                }
            }
            $i=0;
            foreach($nombres as $val) {
                $mi_array[]=array("nombre"=>$val,"total"=>$totales[$i]);
                $i++;
            }
            $strXML = "";
            $linkAnio=array();
            $strXML = "<chart caption = 'Gráfico 1: Módulos' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='Modulos' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
//        CALCULO EL TOTAL DE TURNOS ATENDIDOS Y ANULADOS
            $reportes = new reportes();
            $tot=0;
            foreach($mi_array as $val) {
                $htmltabla.="<tr><td>".$val["nombre"]."</td><td><center>".$val["total"]."</center></td></tr>";
                $tot+=$val["total"];
                $i++;
            }
            $htmltabla.="<tr><td aling='right'><center><b>Total</b></td><td><center><b>". $tot."</b></center></td></tr>";
            $nombres1=implode(',', $nombres);
            $totales1=implode(',',$totales);
            if ($tipo_grafica=="Barras") {
                $html="";
                $i=0;
                foreach($lista_id_sucursales as $id_sucursal) {
                    $nom=array();
                    $tot=array();
                    foreach($arr as $val) {
                        $list=explode('-',$val);
                        if($list[0]==$id_sucursal) {
                            $nom[]=$nombres[$i];
                            $tot[]=$totales[$i];
                            $i++;
                        }
                    }
//                print_r($nom);
//                print_r($tot);
                    $n=implode(',',$nom);
                    $t=implode(',',$tot);
                    $html.=$this->GraficaBarras("Modulos Sucursal $id_sucursal",$n,$t);
//                $html.=$this->Graficadorpie("Modulos sucursal $id_sucursal",$nom,$tot);
                }

            }
            else
            if ($tipo_grafica=="Pastel") {
                $html="";
                $i=0;
                foreach($lista_id_sucursales as $id_sucursal) {
                    $nom=array();
                    $tot=array();
                    foreach($arr as $val) {
                        $list=explode('-',$val);
                        if($list[0]==$id_sucursal) {
                            $nom[]=$nombres[$i];
                            $tot[]=$totales[$i];
                            $i++;
                        }
                    }
//                print_r($nom);
//                print_r($tot);
                    $html.=$this->Graficadorpie("Modulos sucursal $id_sucursal",$nom,$tot);
                }
            }
            else
                $html="Tipo Grafica incorrecta";
        }
        else {
            $html="Chequee que tenga selecinado el Modulo y Servicio...!!!";
        }
        $htmltabla.="</table></center>";
        $datosturno= array("html"=>$html,"tabla"=>$htmltabla,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function GraficadorCajasAction() {
        $this->setResponse("json");
        $id_sucursal=$this->getPostParam('sucursal_id');
        $id_caja=$this->getPostParam('cajas');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $tipo_grafica=$this->getPostParam('Grafica');
        // $servicios=$this->getPostParam('chkservicios');
        $cajas=$id_caja;
        $lista_cajas=explode(",",$id_caja);
        //$lista_servicios = explode(',', $servicios);
        $total_turnos=0;
        $intTotal =0;
        $i=0;
        $intTotalModulos=array();
        // $forma_condicion_servicios="";
        $mod=0;
        ////cabecera de la Tabla
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>TURNOS ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $htmltabla="<center><table border=1 width='100%' align='left'><tr style='$fondo_titulo;font-size: 12px'>";
        $htmltabla.="<th> Modulos</th><th>Total Turnos Atendidos</th></tr>";
        //INICIO BUSCAR MODULOS
        $lista1=array();
        $modulo=new ReportesSucursales();
        $lista_id_sucursales=explode(",",$id_sucursal);
        $i=0;
        $j=0;
        $lista_nombres=array();
        $nombre=array();
        $total=0;
        $usuario="";
        $i=0;
        if (!empty($id_caja)) {
            foreach($lista_id_sucursales as $id) {
                $lista_nombre[]=$modulo->nombresucursales($id);
            }
            foreach($lista_id_sucursales as $id_sucursal) {
                foreach($lista_cajas as $caja_id) {
                    foreach($this->queryTotalCajasSucursal as $result) {
                        if (trim($id_sucursal)==trim($result["sucursal_id"])&&trim($caja_id)==trim($result["caja_id_Sucursal"])) {
                            $lista_nombres[]=array('id_sucursal'=>trim($id_sucursal),'idcaja'=>$result['caja_id_Sucursal'],'nombre'=>$result['NombreUsuario']);
                            break;
                        }
                    }
                }
            }
// Ahora en la columna 1 cargo los valores de la sucursales
            $nombres=array();
            $totales=array();
            $array_aux=array();
            $mi_array=array();
            foreach($lista_nombres as $result) {
                $array_aux[]=$result["id_sucursal"]."-".$result["idcaja"];
            }
            $encontro=false;
            $arr = array();
            $arr= array_flip(array_flip($array_aux));
            foreach($arr as $valor) {
                $arr_aux=explode('-',$valor);
                foreach($lista_nombres as $val) {
                    if($val["id_sucursal"]==$arr_aux[0]&&$val["idcaja"]==$arr_aux[1]&& $encontro==false) {
                        $nombres[]="Suc".$arr_aux[0]."-".$val["nombre"];
                        $totales[]=$this->totalCajasAtendidosPorFecha($val["id_sucursal"],$val["idcaja"],$val['nombre']);
                        $encontro=true;
                        break;
                    }
                    else
                        $encontro=false;
                }
            }
            $i=0;
            foreach($nombres as $val) {
                $mi_array[]=array("nombre"=>$val,"total"=>$totales[$i]);
                $i++;
            }
            $strXML = "";
            $linkAnio=array();
            $strXML = "<chart caption = 'Gráfico 1: Módulos' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='Modulos' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
//        CALCULO EL TOTAL DE TURNOS ATENDIDOS Y ANULADOS
            $reportes = new reportes();
            $tot=0;
            foreach($mi_array as $val) {
                $htmltabla.="<tr><td>".$val["nombre"]."</td><td><center>".$val["total"]."</center></td></tr>";
                $tot+=$val["total"];
                $i++;
            }
            $htmltabla.="<tr><td aling='right'><center><b>Total</b></td><td><center><b>". $tot."</b></center></td></tr>";
            $nombres1=implode(',', $nombres);
            $totales1=implode(',',$totales);
            if ($tipo_grafica=="Barras") {
                $html="";
                $i=0;
                foreach($lista_id_sucursales as $id_sucursal) {
                    $nom=array();
                    $tot=array();
                    foreach($arr as $val) {
                        $list=explode('-',$val);
                        if($list[0]==$id_sucursal) {
                            $nom[]=$nombres[$i];
                            $tot[]=$totales[$i];
                            $i++;
                        }
                    }
//                print_r($nom);
//                print_r($tot);
                    $n=implode(',',$nom);
                    $t=implode(',',$tot);
                    $html.=$this->GraficaBarras("Caja Sucursal $id_sucursal",$n,$t);
//                $html.=$this->Graficadorpie("Modulos sucursal $id_sucursal",$nom,$tot);
                }

            }
            else
            if ($tipo_grafica=="Pastel") {
                $html="";
                $i=0;
                foreach($lista_id_sucursales as $id_sucursal) {
                    $nom=array();
                    $tot=array();
                    foreach($arr as $val) {
                        $list=explode('-',$val);
                        if($list[0]==$id_sucursal) {
                            $nom[]=$nombres[$i];
                            $tot[]=$totales[$i];
                            $i++;
                        }
                    }
//                print_r($nom);
//                print_r($tot);
                    $html.=$this->Graficadorpie("Caja sucursal $id_sucursal",$nom,$tot);
                }
            }
            else
                $html="Tipo Grafica incorrecta";
        }
        else {
            $html="Chequee que tenga selecinado al menos una caja...!!!";
        }
        $htmltabla.="</table></center>";
        $datosturno= array("html"=>$html,"tabla"=>$htmltabla,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function Graficadorpie($titulo,$listanombres,$listaTotales ) {
//        $listaTotales=explode(',', $totales);
//        $listanombres=explode(',',$modulo);
        $strXML = "";
        $strXML = "<chart caption = 'Grafico 2: $titulo ' bgColor='#CDDEE5' baseFontSize='12'formatNumberScale='0' thousandseparator='.'>";
        $i=0;
        $j=0;
        $c=0;
        $x=300;
        $y=350;
        $titulo=substr($titulo, 0,7);
//        echo $titulo;
//        die();
        if($titulo=="Calidad") {
            foreach($listaTotales as $key=>$val) {
                $strXML .= "<set label = '".$listanombres[$i]."' value ='".$val."'/>";
                $i++;
            }
            $x=500;
            $y=480;
        }
        else  if(count($listaTotales)==count($listanombres)&&count($listaTotales)!=0 ) {
            foreach($listaTotales as $key=>$val) {
                $key=trim($key);
                $strXML .= "<set label = '$listanombres[$c] ' value ='".$listaTotales[$key]."' />";
                $c++;
            }
            $x=300;
            $y=350;
        }
        else return "No existe Datos suficientes para:...".$titulo.'      ';

        $strXML .= "</chart>";
        return $this->renderChartHTML("../../swf_charts/Pie3D.swf", "",$strXML, "detalle", $x,$y, false);
    }
    public function GraficaBarras($titulo,$nombres,$total) {
        $strXML = "";
        $lista_nombres=explode(",", $nombres);
        $nom=substr($lista_nombres[0],0,4);
        if($titulo=="Calidad")
            $strXML = "<chart caption = 'Gráfico 1: $titulo ' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='$titulo' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
        else
            $strXML = "<chart caption = 'Gráfico 1: $titulo' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='$titulo' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
        $i=0;
        $totales=array();

        $lista_total=explode(",", $total);
        $i=0;
        $j=0;
        $x=0;
        $y=0;
        $calidad=array();
        foreach($lista_total as $key => $val) {
            if($titulo=="Calidad") {
                 if(is_int($i/4)&&$i!=0) {

                    $strXML .= "<set />";
                    $strXML .= "<set />";
                    $nom=str_replace("Módulo", "Modulo", $lista_nombres[$j]);
                    $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";
                    $j++ ;
                }
                else {
                    $nom=str_replace("Módulo", "Modulo",$lista_nombres[$j]);
                    $j++;
                    $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";

                }
                $x+=30;
                $y=480;
                if(count($lista_total)<=20) $x=500;
            }
            else {

                $nom=str_replace("Balcón", "Balcon",$lista_nombres[$key]);
                $nom=str_replace("Crédito", "Credito", $nom);
                $nom=str_replace("Módulo", "Modulo", $nom);
                $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";
                $x=300;
                $y=350;
            }
            $i++;
        }

// Cerramos la etiqueta "chart".
        $strXML .= "</chart>";
// Por Ã¯Â¿Â½ltimo imprimo el grÃ¯Â¿Â½fico.
        $html= $this->renderChartHTML('../../swf_charts/Column3D.swf', '', $strXML,'maestro', $x, $y,false);
        $strXML="";
        return($html);
    }
    function totalServiciosAtendidosPorFecha($id,$servicio_id,$NombreServicio) {
        $total=0;
        foreach($this->queryTotalSucursal as $key=> $valor) {
            if($id==$valor['sucursal_id']&&$valor['servicio_id_sucursal']==$servicio_id&&$valor['nombre_servicio']==$NombreServicio&&$valor['TurnoAtendido']==1&&$valor['TurnoRechazado']==0) {
                $total++;
            }

        }
        return ($total);
    }
    function totalModulosAtendidosPorFecha($id,$modulo_id,$NombreServicio) {
        $total=0;
        foreach($this->queryTotalSucursal as $key=> $valor) {
            if($id==$valor['sucursal_id']&&$valor['Modulo_id_Sucursal']==$modulo_id&&$valor['TurnoAtendido']==1&&$valor['TurnoRechazado']==0) {
                $total++;
            }

        }
        return ($total);
    }
    function totalCajasAtendidosPorFecha($id,$caja_id,$NombreCaja) {
        $total=0;
        foreach($this->queryTotalCajasSucursal as $key=> $valor) {
            if($id==$valor['sucursal_id']&&$valor['caja_id_Sucursal']==$caja_id) {
                $total++;
            }

        }
        return ($total);
    }
    public function GraficadorServicioBarrasAction() {
        $this->setResponse("json");
        $id_sucursales=$this->getPostParam('sucursal_id');
        $caja=$this->getPostParam('modulos');
        $desde =$this->getPostParam('desde');
        $hasta =$this->getPostParam('hasta');
        $chkservicios =$this->getPostParam('chkservicios');
        $tipo=$this->getPostParam('Tipo');
        $ar1= array();
        $ar= array();
        $html="";
        $titulo="";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        if(($chkservicios!="")&&($caja!="")) {

            $titulo="<text style='$tituloEstilo'>TURNOS ATENDIDOS POR SERVICIO DESDE:$desde HASTA: $hasta </text>";
            $lista_id_sucursales=explode(",",$id_sucursales);
            $arreglo_caja=explode(",", $caja);
            $arreglo_servicio=explode(",",$chkservicios);
            $db = DbBase::rawConnect();
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $html="<center><table border=1 width='100%'><tr style='$fondo_titulo'>";
            //cabecera de la Tabla
            $html.="<th>Servicios</th><th>Total Servicio Atendido</th><th>Porcentaje</th></tr>";

            $total=array();
            $porcentaje=0;

            //            foreach($arreglo_caja as $caja_id) {

            $mi_array=array();
            $mi_arrayrepetidos=array();
            $array_series=array();
            $array_data=array();
            $nombres=array();
            $lista_nombres=array();
            $totales=array();
            $i=0;
            if(count($this->queryTotalSucursal)!=0) {
                foreach($lista_id_sucursales as $id_sucursal) {
                    foreach($arreglo_servicio as $servicio_id) {
                        foreach($this->queryTotalSucursal as $result) {
                            // $array1[]= array ('id_caja'=>$result['id'],'id_servicio'=>$result['servicio_id_sucursal'],'nombre'=>$result['nombre_servicio'],'letra'=>$result['Letra']);
                            if (trim($id_sucursal)==$result["sucursal_id"]&&$servicio_id==$result["servicio_id_sucursal"]) {
                                //   $mi_array[]=array('id'=>$result['sucursal_id'],'id_servicio'=>$result['servicio_id_sucursal'],'nombre'=>$result['nombre_servicio'],'letra'=>$result['Letra'],'total'=>$this->totalServiciosAtendidosPorFecha(trim($id_sucursal),$servicio_id,$result['nombre_servicio']));
                                $lista_nombres[]=array('id_sucursal'=>trim($id_sucursal),'idServicio'=>$result['servicio_id_sucursal'],'nombre'=>$result['nombre_servicio']);
//                                $totales[]=$this->totalServiciosAtendidosPorFecha(trim($id_sucursal),$servicio_id,$result['nombre_servicio']);
                                break;
                            }
                        }
                    }
                }
                $array_aux=array();
                foreach($lista_nombres as $result) {
                    $array_aux[]=$result["id_sucursal"]."-".$result["idServicio"];
                }
                $encontro=false;
                $arr = array();
                $arr= array_flip(array_flip($array_aux));
                foreach($arr as $valor) {
                    $arr_aux=explode('-',$valor);
                    foreach($lista_nombres as $val) {
                        if($val["id_sucursal"]==$arr_aux[0]&&$val["idServicio"]==$arr_aux[1]&& $encontro==false) {
                            $nombres[]="Suc".$arr_aux[0]."-".$val["nombre"];
                            $totales[]=$this->totalServiciosAtendidosPorFecha($val["id_sucursal"],$val["idServicio"],$val['nombre']);
                            $encontro=true;
                            break;
                        }
                        else
                            $encontro=false;
                    }
                }
                $i=0;
                foreach($nombres as $val) {
                    $mi_array[]=array("nombre"=>$val,"total"=>$totales[$i]);
                    $i++;
                }
//        print_r($mi_array);
//                die();
            }
            else {
                echo'No hay datos';
                die();

            }
            $nombre="";
            $total=0;
            foreach($mi_array as $key=> $val) {
                $total+= $val['total'];
            }

            $array_d=array();
            $j=0;
            foreach($mi_array as $key=> $val) {
                if ($total!=0) {
                    $porcentaje= round(($val['total']*100)/$total,2);
                }
                else
                    $porcentaje=0;
                //$html.="['".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%"."', ".$porcentaje."],";
                $arr1=array("".$val['nombre']."-"." t., ".$val['total']."",$porcentaje);
                $array_d[$j]=$arr1;
                $html.="<tr><td> ".$val['nombre']." </td><td><center>".$val['total']."</td><td><center>".$porcentaje."% </td></tr>";
                $j++;
            }
            $html.="<tr><td><b><center>Total</b></td><td><b><center>".$total."<b></td><td><b><center>".round(($total*100)/$total,2)."% </td></tr>";
            $html.="</table>";
            //$array_data[$key]=array("".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%".""=>$porcentaje);

            //echo $html;
        }
        else {
            $html="Chequee que tenga selecionado caja y servicio";
            die();
        }
        $nombre=implode(',',$nombres);
        $htmlGrafica="";
        $tot=implode(',',$totales);
        if ($tipo=="Barras") {
            $htmlGrafica="";
            $i=0;
            foreach($lista_id_sucursales as $id_sucursal) {
                $nom=array();
                $tot=array();
                foreach($arr as $val) {
                    $list=explode('-',$val);
                    if($list[0]==$id_sucursal) {
                        $nom[]=$nombres[$i];
                        $tot[]=$totales[$i];
                        $i++;
                    }
                }
                $n=implode(',',$nom);
                $t=implode(',',$tot);
                $htmlGrafica.=$this->GraficaBarras("Servicios Sucursal $id_sucursal ",$n,$t);
            }
//            $htmlGrafica=$this->GraficaBarras("Servicios",$nombre,$tot);
        }
        else if ($tipo=="Pastel") {
            if($tot!="") {
                $htmlGrafica="";
                $i=0;
                foreach($lista_id_sucursales as $id_sucursal) {
                    $nom=array();
                    $tot=array();
                    foreach($arr as $val) {
                        $list=explode('-',$val);
                        if($list[0]==$id_sucursal) {
                            $nom[]=$nombres[$i];
                            $tot[]=$totales[$i];
                            $i++;
                        }
                    }
                    $htmlGrafica.=$this->Graficadorpie("Servicios Sucursal $id_sucursal ",$nom,$tot);
//                $htmlGrafica=$this->Graficadorpie("Servicios",$nombres,$totales);
                }
            }
            else
                $htmlGrafica="NO HAY DATOS";
        }
        $arraydatos[0]=array('type'=>'pie','name'=>'Turnos A. Dia','data'=>$array_d);
        $array_g= array('datos'=>$arraydatos,'tabla'=>$html,'html'=>$titulo,'grafica'=>$htmlGrafica);
        return ( $array_g);
    }

    public function GraficaChkModulosAction() {
        $this->setResponse("json");
        $Areas=$this->getPostParam("sucursal_id");
        $listaAreas=explode(',', $Areas);
        $i=0;
        $condicion="grupousuario.grupo_id=5";
        $mi_array=array();
//        $this->arrayModulosNombres=array();
        if($Areas!="") {
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT * FROM caja c,usercaja uc, usuario u,grupousuario gu WHERE uc.caja_id=c.id AND gu.usuario_id=u.id AND gu.grupo_id=5 AND uc.usuario_id=u.id AND ubicacion_id IN(".$Areas.") ");
            while($row = $db->fetchArray($result)) {
                $array1= array ('id'=>$row['caja_id'],'nombre'=>$row['descripcion']);
                $mi_array[$row['caja_id']]= "M".$row['numero_caja']."-".$row['nombres'];
                $this->arrayModulosNombres[$row['caja_id']]=$row['nombres'];
                $i++;
            }

            $html="";
            $html.="<table width='100%' >";
            $html.="<tr><td class='empty'>";
            //<fieldset class='ui-corner-all ui-widget-content'><legend><b>MÃƒÂ³dulos</b></legend>";
            //$html.=" <input id='chk_all_modulos' type='checkbox' onclick='SelecionarTodosModulos()'><label style='color:#3366FF; font-size:12px; font-weight:bold'>Marcar/desmarcar todos</label><br/>";
            $col=0;
            $i=0;
            $array_valores=array();
            //print_r($mi_array);
            foreach($mi_array as $key=> $val) {

                $array_valores[]=Tag::checkboxField("chkmodulos[]", "value: $key","checked: ","onclick:   cheks()").$val."&nbsp;&nbsp;";

            }
            $html.= "<table>";
            $cont_mod=count($mi_array);
            $cont_filas=ceil($cont_mod/3);
            $x=$cont_filas*3;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_valores[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) { //filas
                $html.= "<tr>";
                for ($c=1;$c<=3;$c++) { //columnas
                    $html.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html.="</tr>";
            }
            $html.="</table>";

            //$html.="</fieldset>
            $html.=" </td> </tr> </table>";
        }
        else
            $html="Seleccione una Area...para ver los modulos!!!";
        $arraycks=array('cks'=>$html);
        return($arraycks);
    }
    function encodeDataURL($strDataURL, $addNoCacheStr=false) {
        //Add the no-cache string if required
        if ($addNoCacheStr==true) {
            // We add ?FCCurrTime=xxyyzz
            // If the dataURL already contains a ?, we add &FCCurrTime=xxyyzz
            // We replace : with _, as FusionCharts cannot handle : in URLs
            if (strpos(strDataURL,"?")<>0)
                $strDataURL .= "&FCCurrTime=" . Date("H_i_s");
            else
                $strDataURL .= "?FCCurrTime=" . Date("H_i_s");
        }
        // URL Encode it
        return urlencode($strDataURL);
    }
    function datePart($mask, $dateTimeStr) {
        @list($datePt, $timePt) = explode(" ", $dateTimeStr);
        $arDatePt = explode("-", $datePt);
        $dataStr = "";
        // Ensure we have 3 parameters for the date
        if (count($arDatePt) == 3) {
            list($year, $month, $day) = $arDatePt;
            // determine the request
            switch ($mask) {
                case "m": return $month;
                case "d": return $day;
                case "y": return $year;
            }
            // default to mm/dd/yyyy
            return (trim($month . "/" . $day . "/" . $year));
        }
        return $dataStr;
    }
    function renderChart($chartSWF, $strURL, $strXML, $chartId, $chartWidth, $chartHeight, $debugMode, $registerWithJS) {
        //First we create a new DIV for each chart. We specify the name of DIV as "chartId"Div.
        //DIV names are case-sensitive.

        // The Steps in the script block below are:
        //
        //  1)In the DIV the text "Chart" is shown to users before the chart has started loading
        //    (if there is a lag in relaying SWF from server). This text is also shown to users
        //    who do not have Flash Player installed. You can configure it as per your needs.
        //
        //  2) The chart is rendered using FusionCharts Class. Each chart's instance (JavaScript) Id
        //     is named as chart_"chartId".
        //
        //  3) Check whether we've to provide data using dataXML method or dataURL method
        //     save the data for usage below
        if ($strXML=="")
            $tempData = "//Set the dataURL of the chart\n\t\tchart_$chartId.setDataURL(\"$strURL\")";
        else
            $tempData = "//Provide entire XML data using dataXML method\n\t\tchart_$chartId.setDataXML(\"$strXML\")";

        // Set up necessary variables for the RENDERCAHRT
        $chartIdDiv = $chartId . "Div";
        $ndebugMode = boolToNum($debugMode);
        $nregisterWithJS = boolToNum($registerWithJS);

        // create a string for outputting by the caller
        $render_chart = <<<RENDERCHART

	<!-- START Script Block for Chart $chartId -->
	<div id="$chartIdDiv" align="center">
		Chart.
	</div>
	<script type="text/javascript">
		//Instantiate the Chart
		var chart_$chartId = new FusionCharts("$chartSWF", "$chartId", "$chartWidth", "$chartHeight", "$ndebugMode", "$nregisterWithJS");
                $tempData
		//Finally, render the chart.
		chart_$chartId.render("$chartIdDiv");
	</script>
	<!-- END Script Block for Chart $chartId -->
RENDERCHART;

        return $render_chart;
    }
    function renderChartHTML($chartSWF, $strURL, $strXML, $chartId, $chartWidth, $chartHeight, $debugMode) {
        // Generate the FlashVars string based on whether dataURL has been provided
        // or dataXML.
        $HTML_chart=$chartSWF.$chartWidth.$chartHeight.$debugMode;
        $strFlashVars = "&chartWidth=" . $chartWidth . "&chartHeight=" . $chartHeight . "&debugMode=0";// .  boolToNum($debugMode);
        if ($strXML=="")
        // DataURL Mode
            $strFlashVars .= "&dataURL=" . $strURL;
        else
        //DataXML Mode
            $strFlashVars .= "&dataXML=" . $strXML;

        $HTML_chart = <<<HTMLCHART
	<!-- START Code Block for Chart $chartId -->
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="$chartWidth" height="$chartHeight" id="$chartId">
		<param name="allowScriptAccess" value="always" />
		<param name="movie" value="$chartSWF"/>
		<param name="FlashVars" value="$strFlashVars" />
		<param name="quality" value="high" />
		<embed src="$chartSWF" FlashVars="$strFlashVars" quality="high" width="$chartWidth" height="$chartHeight" name="$chartId" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	<!-- END Code Block for Chart $chartId -->
HTMLCHART;
        return $HTML_chart;
    }
    function boolToNum($bVal) {
        return (($bVal==true) ? 1 : 0);
    }
}
