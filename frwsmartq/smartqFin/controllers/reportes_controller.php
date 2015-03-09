<?php
class ReportesController extends ApplicationController {

    public $lista_cajas=array();
    public $lista_modulos=array();
    public $lista_servicios=array();
    public $lista_grupo_servicios=array();
    public $lista_modulos_tad=array();  //cuadro de turnos atendidos por dias
    public $lista_servicios_tad=array();//cuadro de turnos atendidos por dias
    public $lista_modulos_xls=array(); //modulos xls
    public $lista_servicios_xls=array(); //modulos xls

    public $calif_4botones_teclado;         //si tiene o no opcion de calificacion con teclado
    public $calif_4botones_pantalla;        //si tiene o no opcion de calificacion pantalla
    public $calif_matriz_pantalla;          //si tiene o no opcion de calificacion con multipregunta

    public $id;
    /**
     * Inicializador del controlador/
     *
     */
    public function initialize() {
        $this->setPersistance(true);
        $queryTotal=array();
        $queryTotalCajas=array();
        $arrayServicios=array();
        $arrayModulosNombres=array();
        $array_total_modulos=array();
        $array_total_servicios=array();
        $array_turnero=array();

        $empresa= new Empresa();
        $buscaEmpresa= $empresa->findFirst();
        $this->calif_4botones_teclado   =$buscaEmpresa->getCalif4botonesTeclado();
        $this->calif_4botones_pantalla  =$buscaEmpresa->getCalif4botonesPantalla();
        $this->calif_matriz_pantalla    =$buscaEmpresa->getCalifMatrizPantalla();
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
     * Inicio para el reporte de calificaciones por mÃ¯Â¿Â½dulos
    */
    public $lista_modulos_calif=array();
    public $lista_servicios_calif=array();
    public $lista_preguntas_calif=array();
    public function calificacionesAction() {
        $lista=array();
        $this->setResponse('ajax');
//        Tag::displayTo('desde', date("Y-m-d"));
//        Tag::displayTo('hasta', date("Y-m-d"));
//        Tag::displayTo('hora_i', "08:00:00");
//        Tag::displayTo('hora_f', "17:00:00");
        //INICIO LISTA DE MÃ¯Â¿Â½DULOS
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
        //FIN LISTA DE MÃ¯Â¿Â½DULOS

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
        $html= "<table>";
        foreach($this->lista_preguntas_calif as $key=> $val) {
//                        echo "hola".$val;
            $html.= "<tr><td>";
            $html.= Tag::checkboxField("chkpreguntas[]", "value: $key","checked: ").$val."&nbsp;&nbsp;";
            $html.= "</tr></td>";
        }
        $html.= "<tr><td><input type='button' value='Aceptar' id='btnAceptar' /></td></tr>";
        $html.= "</table>";
        echo $html;
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
     * Permite listas los servicios o los grupos de servicios segÃ¯Â¿Â½n la opciÃ¯Â¿Â½n seleccionada en turnosEmitidos.html
    */
    public function listaOpcionAction() {
        $this->setResponse('ajax');
        $lista_servicios=array();
        $opcion=$this->getPostParam('opcion');  //Si es por servicios o por Grupo de servicios
        $html="";
        if ($opcion==1) {
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
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $servicios=$this->getPostParam('chkservicios');
        $modulos=$this->getPostParam('chkmodulos');
        $grupo_servicios=$this->getPostParam('chkgruposervicios');
        $lista_servicios=explode(',', $servicios);
        $lista_modulos=explode(',',$modulos);
        $lista_servicios_aux=implode(',', $lista_servicios);
        $lista_grupo_servicios=explode(',',$grupo_servicios);
        //Inicio calculo de dias entre fechas
        $s = strtotime($hasta)-strtotime($desde);
        $d = intval($s/86400) + 1;
        $fun= new Funciones();
        $reportes= new Reportes();
        //fin calculo de dias entre fechas
        $html1="";
        $html="";
        //INICIO ENCABEZADO
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $html.="<table style='width: 100%;background-color:white' cellpadding='1' cellspacing='1' border='0'>";
        $array_encabezado_servicio=array(
                'TE'=>array('etiqueta'=>'TE','titulo'=>'Turnos Emitidos'),
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos anulados'),
                'Tn'=>array('etiqueta'=>'Tn','titulo'=>'Turnos no timbrados')
        );
        $cont_encabezado=count($array_encabezado_servicio);

        $html.="<thead><tr>";
        $html.="<th style='$fondo_titulo;width: 80px'>Fecha</th>";
        $html.="<th style='$fondo_titulo;width: 60px'>Dia</th>";
        $html.="<th style='$fondo_titulo;width: 60px'>Primer Turno</th>";
        $html.="<th style='$fondo_titulo;width: 60px'>Ultimo Turno</th>";
        foreach ($lista_servicios as $key) {
            $nom=$this->arrayServicios[$key];
            $html.="<th  colspan=$cont_encabezado style='$fondo_titulo' aling=center>$nom</th>";
        }
        $html.="<th aling=center style='$fondo_titulo;width: 30px' title='Total Turnos Emitidos' >TTE</th>";
        $html.="<th aling=center style='$fondo_titulo;width: 30px' title='Total Turnos Atendidos'>TTA</th>";
        $html.="<th aling=center style='$fondo_titulo;width: 30px' title='Total Turnos anulados'>TTa</th>";
        $html.="<th aling=center style='$fondo_titulo;;width: 100px'  title='Total Turnos no timbrados' align=left>TTn</th>";
        $html.="</tr>";

        $html.="<tr>";
        $html.="<th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>
            <th style='$fondo_titulo'></th>";
        foreach ($lista_servicios as $nom_servicio) {
            foreach ($array_encabezado_servicio as $valor)
                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'></th>";
        $html.="<th style='$fondo_titulo'>         </th>";
        $grantotal_turnos_emitidos=0;
        $grantotal_turnos_atendidos=0;
        $grantotal_turnos_anulados=0;
        $grantotal_turnos_no_timbrados=0;
        $suma_totales_turnos_emitidos=array();

        //INICIO INICIALIZAR ARRAY PARA SUMA DE CADA SERVICIO
        foreach ($lista_servicios as $key) {
            $suma_totales_turnos_emitidos[$key]=0;
            $suma_totales_turnos_atendidos[$key]=0;
            $suma_totales_turnos_anulados[$key]=0;
            $suma_totales_turnos_no_timbrados[$key]=0;
        }
        //FIN INICIALIZAR ARRAY PARA SUMA DE CADA SERVICIO
        $forma_array=array();
        $fecha= $desde;
        $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
        $html.="<tbody>";
        for ($i=1;$i<=$d;$i++) {
            $fecha= date("Y-m-d", strtotime( "$fecha + 1 day"));
            $separa = explode('-',$fecha);
            $mes = $separa[1];
            $dia = $separa[2];
            $ano = $separa[0];
            $dia= $fun->dia_semana($dia, $mes, $ano);
            if ($dia!='S�bado' & $dia!='Domingo') {
                $subtotal_turnos_emitidos=0;
                $subtotal_turnos_atendidos=0;
                $subtotal_turnos_anulados=0;
                $subtotal_turnos_no_timbrados=0;
                $html.="<tr>";
                $html.="<td style='background-color:#e5f1f4;width: 80px'>$fecha</td>";
                $html.="<td style='background-color:#e5f1f4;width: 60px'>$dia</td>"; //dia
                $primer_turno= $reportes->primerTurno($fecha);
                $ultimo_turno= $reportes->ultimoTurno($fecha);
                $html.="<td style='background-color:#e5f1f4;width: 60px'aling=center>$primer_turno</td>";
                $html.="<td style='background-color:#e5f1f4;width: 60px'>$ultimo_turno</td>";
                //INICIO DATOS POR SERVICIO
                $forma_array_fila=array();
                $bandera=false;
                foreach ($lista_servicios as $key) {
                    $turnos_emitidos= $this->array_turnero[$fecha][$key]['t_emitidos'];
                    ;
                    $turnos_atendidos=$this->array_turnero[$fecha][$key]['t_atendidos'];
                    $turnos_anulados= $this->array_turnero[$fecha][$key]['t_anulados'];
                    $turnos_no_timbrados= $this->array_turnero[$fecha][$key]['no_timbrados'];
                    $html.="<td align='center' style='background-color:#FEE1BA;'>$turnos_emitidos</td>";
                    $html.="<td align='center' style='background-color:#e5f1f4;'>$turnos_atendidos</td>";
                    $html.="<td align='center' style='background-color:#e5f1f4;'>$turnos_anulados</td>";
                    $html.="<td align='center' style='background-color:#e5f1f4;'>$turnos_no_timbrados</td>";
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
                $html.="<td align='center' style='background-color:#bce774;' title='Turnos Emitidos'>$subtotal_turnos_emitidos</td>";
                $html.="<td align='center' style='background-color:#e5f1f4;' title='Turnos Atendidos'>$subtotal_turnos_atendidos</td>";
                $html.="<td align='center' style='background-color:#e5f1f4;' title=' Turnos Anulados'>$subtotal_turnos_anulados</td>";
                $html.="<td align='left' style='background-color:#e5f1f4;' title='No Timbrados Emitidos'>$subtotal_turnos_no_timbrados</td>";
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
            $html.="<td align='center' style='background-color:#FEE1BA;'><b>$valor</b></td>";
            $html.="<td align='center' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_atendidos[$key]</b></td>";
            $html.="<td align='center' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_anulados[$key]</b></td>";
            $html.="<td align='left' style='background-color:#FEE1BA;'><b>$suma_totales_turnos_no_timbrados[$key]</b></td>";
            $grantotal_turnos_emitidos+=$suma_totales_turnos_emitidos[$key];
            $grantotal_turnos_atendidos+=$suma_totales_turnos_atendidos[$key];
            $grantotal_turnos_anulados+=$suma_totales_turnos_anulados[$key];
            $grantotal_turnos_no_timbrados+=$suma_totales_turnos_no_timbrados[$key];
        }
        $html.="<td align='center' style='background-color:#bce774;width: 30px'><b>$grantotal_turnos_emitidos</b></td>";
        $html.="<td align='center' style='background-color:#e5f1f4;width: 30px'><b>$grantotal_turnos_atendidos</b></td>";
        $html.="<td align='center' style='background-color:#e5f1f4;width: 30px'><b>$grantotal_turnos_anulados</b></td>";
        $html.="<td align='left' style='background-color:#e5f1f4;width: 30px'><b>$grantotal_turnos_no_timbrados</b></td>";
        $html.="</tr></table>";
        $mi_array= $forma_array;
        $compactada=serialize($mi_array);
        $compactada=urlencode($compactada);
        //$html.="<a href=javascript:ventanaSecundaria('verGraficoTurnosEmitidos?array=".$compactada."')>Graficar Todos</a>";
        $arrayTurno=array('turnos'=>$html,'encabezadoTurnero'=>$html1);
        return $arrayTurno;
////        echo $html;
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
        $html.="this.x +': '+ this.y +'Ã¯Â¿Â½C';";
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
     * Para encabezado por cada semana
    */
    function encabezadoPorSemana() {
        $html="<table align='right'> <thead><tr>";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_dias_semana = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo',"Totales");
        $html.="<th style='$fondo_titulo' width=500px></th>";
        foreach ($array_dias_semana as $dia) {
            $html.="<th style='$fondo_titulo ' width=400px >$dia</th>";
        }
//        $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
//        $html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
//        $html.="<th style='$fondo_titulo' title='Duración Total'>DT</th>";
//        $html.="<th style='$fondo_titulo' title='Promedio de Atención Lunes a Viernes'>PA</th>";
//        $html.="<th style='$fondo_titulo' title='Promedio de Llamada Lunes a Viernes'>PLL</th>";
//        $html.="<th style='$fondo_titulo'></th>";
        $html.="</tr> </thead>";
        return $html;
    }

    /*
     * Para encabezado de cada dia de la semana
    */
    function encabezadoPorDia() {
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_encabezado_dia=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención'),
                //'Pll'=>array('etiqueta'=>'Pll','titulo'=>'Promedio de Llamada a AtenciÃ³n'),
//                'ES'=>array('etiqueta'=>'','titulo'=>'',)
        );
        $html="";

        $html.="<tr>";
        foreach ($array_encabezado_dia as $valor) {
            $html.="<th style='$fondo_titulo' title='{$valor['titulo']}'>{$valor['etiqueta']}</th>";
        }
        $html.="</tr>";
        return $html;
    }
    /*
     * Funcion que retorna el cuadro de total turnos atendidos por dias
     * Menu: Cuadro de turnos por dias
    */
    public function verCuadroAction() {
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $modulos=$this->getPostParam('chkmodulos');
        $servicios=$this->getPostParam('chkservicios');
        $lista_servicios=explode(',',$servicios);
        $html="";
        if (!empty($modulos) & !empty($lista_servicios) ) {
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
            $tituloCabecera="<center><text style='$tituloEstilo'>Cuadro de turnos Atendidos, Anulados y Promedios<br />Desde ".$desde."hasta ". $hasta."</text></center><br />";
//            $tituloCabecera.=$this->encabezadoPorSemana();
            //INICIO SEPARAR POR SEMANAS
            $s = strtotime($hasta)-strtotime($desde);
            $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas

            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;   //solo para q empiece desde un dÃƒÂ¯Ã‚Â¿Ã‚Â½a menos

            $forma_array_semanas= array();
            $array_aux= array();
            $aux="";

            $dias= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');

            for ($i=1;$i<=$d;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ; //a la fecha anterior aumenta un dia
                $separa = explode("-",$fecha);
                $anio = $separa[0];
                $mes= $separa[1];
                $dia = $separa[2];
                $num_semana = date('W', mktime(0, 0, 0, $mes, $dia, $anio));        //numero de semanas
                $dia_de_semana=strtoupper($dias[intval((date("w",mktime(0,0,0,$mes,$dia,$anio))))]);  //obtener el dia de la semana
                //$forma_array_semanas[$num_semana][$pru]=$dia;   //forma el array milti con el numero de semana y los dias
                $forma_array_semanas[$num_semana][$dia_de_semana]="$anio-$mes-$dia";   //forma el array milti con el numero de semana y los dias
                //echo "num semana: ".$num_semana."<br>";
            }
            //FIN SEPARAR POR SEMANAS

            $html="";
            $dias = array('Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado','Domingo');
            $fun= new Funciones();
//            $html.="<table border='0'align='right'>";
            $html.=$this->encabezadoPorSemana();
            $html.="<tbody>";
            //INICIO POR SEMANAS
            $array_total= array();
            $num_semana=0;
            foreach($forma_array_semanas as $num_semana=>$val) { //al numero de semanas
                $desde = reset( $val ); //primer dia de la semana
                $hasta = end( $val );   //ultimo dia de la semana
                $fecha_en_semana= date("Y-m-d", strtotime( "$desde - 1 day")) ;   //solo para q empiece desde un dia meno
//                $html.="Semana del $desde al $hasta <tr>" ;  //por cada semana un tr y dentro de este una tabla

                $s = strtotime($hasta)-strtotime($desde);
                $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas
                $array_datos= array('Lunes'=>"", 'Martes'=>"", 'Mi&eacute;rcoles'=>"", 'Jueves'=>"", 'Viernes'=>"", 'S&aacute;bado'=>"",'Domingo'=>"");
                $array_suma_dias= array();      //para poner los turnos atendidos por modulo de lunes a viernes para graficar
                foreach($this->array_datos_modulo as $val) {
                    $modulo_id= $val['modulo_id'];
                    foreach ($array_datos as $key=>$dia) {
                        $array_suma_dias[$modulo_id][$key]['suma_t_atendidos']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_t_anulados']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_duracion_total']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_promedio_total']=0;
                    }
                }
                $array_total[$num_semana]=$array_suma_dias;

                for ($i=1;$i<=$d;$i++) {
                    $datos="";
                    $fecha_en_semana= date("Y-m-d", strtotime( "$fecha_en_semana + 1 day")) ; //a la fecha anterior aumenta un dia
                    $datos.="<table border=0>";
                    $datos.=$this->encabezadoPorDia();
                    $total_t_atendidos_x_dia=0;
                    $total_t_anulados_x_dia=0;
                    $total_duracion_x_dia=0;
                    $total_promedio_x_dia=0;
                    foreach($this->array_datos_modulo as $val) {
                        $modulo_id= $val['modulo_id'];
                        $promedio_en_horas=0;
                        $promedio_en_segundos=0;
                        $dia= $this->array_cuadro[$fecha_en_semana][$modulo_id]['dia'];
                        $t_atendidos= $this->array_cuadro[$fecha_en_semana][$modulo_id]['t_atendidos'];
                        $t_anulados= $this->array_cuadro[$fecha_en_semana][$modulo_id]['t_anulados'];
                        $duracion_en_segundos= $this->array_cuadro[$fecha_en_semana][$modulo_id]['duracion_t'];
                        $duracion_en_horas= $fun->tiempo($duracion_en_segundos);
                        if ($t_atendidos!=0) {
                            $promedio_en_segundos= round($duracion_en_segundos/$t_atendidos);
                            $promedio_en_horas= $fun->tiempo($promedio_en_segundos);
                        }
                        $total_t_atendidos_x_dia+=$t_atendidos;
                        $total_t_anulados_x_dia+=$t_anulados;
                        $total_duracion_x_dia+=$duracion_en_segundos;
                        $total_promedio_x_dia+=$promedio_en_segundos;
                        $datos.="<tr><td align='center' style='background-color:#e5f1f4;'>$t_atendidos</td>
                        <td align='center' style='background-color:#e5f1f4;'>$t_anulados</td>
                        <td align='center' style='background-color:#e5f1f4;'>$duracion_en_horas</td>
                        <td align='center' style='background-color:#e5f1f4;'>$promedio_en_horas</td></tr>";

                        $array_suma_dias[$modulo_id][$dia]['suma_t_atendidos']+=$t_atendidos;
                        $array_suma_dias[$modulo_id][$dia]['suma_t_anulados']+=$t_anulados;
                        $array_suma_dias[$modulo_id][$dia]['suma_duracion_total']+=$duracion_en_segundos;
                        //$array_suma_dias[$modulo_id][$dia]['suma_promedio_total']+=$t_atendidos;
                    }
                    $datos.="<tr>";
                    $datos.="<td align='right'  style='background-color:#FEE1BA;'>".$total_t_atendidos_x_dia."</td>";
                    $datos.="<td align='right'  style='background-color:#FEE1BA;'>".$total_t_anulados_x_dia."</td>";
                    $datos.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($total_duracion_x_dia)."</td>";
                    $datos.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($total_promedio_x_dia)."</td>";
                    //$html.="<td align='right' style='background-color:#e5f1f4;'>".$llamada_promedio."</td>";
                    $datos.="</tr>";
                    $datos.="</table>";
                    $array_datos[$dia]=$datos;
                }
                $html.="<td width=20%>";
                $html.="<table  border='0'>";
                $html.="<tr style='$fondo_titulo'><td style='$fondo_titulo'>Módulo</td><td style='$fondo_titulo'width=200px  align='left'>Usuario</td></tr>";
                //foreach($lista_modulos as $modulo_id) {
                foreach($this->array_datos_modulo as $val) {
                    $modulo_id= $val['modulo_id'];
                    $num_modulo= $val['numero'];
                    $usuario_modulo= $val['usuario'];
                    $html.="<tr><td align='left' width=2% style='background-color:#e5f1f4;'>$num_modulo</td>";
                    $html.="<td align='left' width=18% style='background-color:#e5f1f4;'>$usuario_modulo</td></tr>";
                }
                $html.="<tr><td></td><td align='left' style='background-color:#FEE1BA;'>Totales: </td></tr>";
                $html.="</table >";

                $html.="</td>";

                foreach($dias as $dia) {
                    $html.="<td align='right'>$array_datos[$dia]</td>";
                }
                //tabla de totales por semana
                $html.="<td align='right'>";
                $html.="<table width=15%>";
                $html.=$this->encabezadoPorDia();
                $acu_t_atendidos=0;
                $acu_t_anulados=0;
                $acu_suma_duracion=0;
                $acu_suma_promedio=0;
                foreach($this->array_datos_modulo as $val) {
                    $modulo_id= $val['modulo_id'];
                    $html.="<tr>";
                    $suma_t_atendidos=0;
                    $suma_t_anulados=0;
                    $suma_duracion_t=0;
                    $suma_promedio_t=0;
                    foreach ($array_datos as $key=>$dia) {       //de lunes a domingo
                        $suma_t_atendidos += $array_suma_dias[$modulo_id][$key]['suma_t_atendidos'];
                        $suma_t_anulados += $array_suma_dias[$modulo_id][$key]['suma_t_anulados'];
                        $suma_duracion_t += $array_suma_dias[$modulo_id][$key]['suma_duracion_total'];
                        $suma_promedio_t += $array_suma_dias[$modulo_id][$key]['suma_promedio_total'];
                    }
                    $PA=0;
                    if($suma_t_atendidos!=0)
                        $PA=$suma_duracion_t/$suma_t_atendidos;
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$suma_t_atendidos."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$suma_t_anulados."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($suma_duracion_t)."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($PA)."</td>";
                    $html.="<td align='right' > -       - </td>";
                    $html.="</tr>";
                    $acu_t_atendidos+=$suma_t_atendidos;
                    $acu_t_anulados+=$suma_t_anulados;
                    $acu_suma_duracion+=$suma_duracion_t;
                    $acu_suma_promedio+=$PA;

                }
                //totales por semana de todos los modulos
                $html.="</tr>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>$acu_t_atendidos&nbsp;</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>$acu_t_anulados</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($acu_suma_duracion)."</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($acu_suma_promedio)."</td>";
                $html.="</tr>";
                $html.="</table>";
                $html.="</td>";
                $html.="</tr>";
                $html.="<tr>";
                $html.="<td colspan='9'>";
                $array_aux= array();
                $array_aux[]=$array_suma_dias;
                $array_total[$num_semana]=$array_suma_dias;
                $mi_array= $array_aux;
                $compactada=serialize($mi_array);
                $compactada=urlencode($compactada);
                $fecm7=$desde;
                $html.="<input type='button' style='background-color:#e5f1f4;width:250px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;' value='Graficar Semana $fecm7 al $fecha_en_semana' onclick="." \"verGraficoTotalSemanasTodos('$compactada')\""."/>";
                $html.="</td>";
                $html.="</tr>";
                //print_r ($array_suma_dias);
            }
            //print_r ($array_total);
            //FIN POR SEMANAS

            $html.="</table>";

            $mi_array= $array_total;
            $compactada=serialize($mi_array);
            $compactada=urlencode($compactada);
            $html.="<input type='button' style='background-color:#e5f1f4;width:100px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;' value='Graficar Todo' onclick="." \"verGraficoTotalSemanasTodos('$compactada')\""."/>";


            $html.="</table>";
        } else
            $html.="No ha seleccionado algún módulo o servicio";

        $datos= array("v1"=>$html,"titulo"=>$tituloCabecera);
        return ($datos);
    }

//Funcion que suma o resta n dias a una fecha
    public function DiasFecha($fecha,$dias,$operacion) {
        Switch($operacion) {
            case "sumar":
                $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
                return $varFecha;
                break;
            case "restar":
                $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
                return $varFecha;
                break;
            default:
                $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
                break;
        }
    }


    /* Calcula los turnos atendidos de cada uno de los modulos
    */
    /*
     * Graficar el cajero por horas
    */
    public function verGraficoHorasColas1Action() {
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        ;
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
        $reportes = new Reportes();
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
                'DT'=>array('etiqueta'=>'DT','titulo'=>'DuraciÃƒÂ³n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de AtenciÃƒÂ³n')
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
        $html.="this.x +': '+ this.y +'Ã¯Â¿Â½C';";
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

    function totalTurnosAtendidosPorDia($caja_id,$fecha) {
        $total=0;
        $bandera=true;
        foreach($this->queryTotal as $key=> $valor) {
            if($valor['id']==$caja_id&&$valor['TurnoFechaInicioAtencion']==$fecha&&$valor['TurnoAtendido']==1&&$valor['TurnoRechazado']==0) {
                $total++;
                $bandera=true;

            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$total=0;
        }
        return ($total);
    }
    function totalTurnosAtendidosPorFechas($caja_id) {
        $total=0;
        $bandera=true;
        foreach($this->queryTotal as $key=> $valor) {
            if($valor['id']==$caja_id&&$valor['TurnoAtendido']==1&&$valor['TurnoRechazado']==0) {
                $total++;
                $bandera=true;
            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$total=0;
        }

        return ($total);
    }
    function totalColasAtendidosPorFechas($caja_id) {
        $total=0;
        $bandera=true;
        foreach($this->queryTotalCajas as $key=> $valor) {
            if($valor['id']==$caja_id&&$valor['ColaAtendido']==1) {
                $total++;
                $bandera=true;
            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotalCajas)==$key&&$bandera==false)$total=0;
        }
        return ($total);
    }
    function duracionAtencion($caja_id,$fecha) {
        $totalseg=0;
        $bandera=true;
        $cont_reg=0;
        $fun= new funciones();
        foreach($this->queryTotal as $key=> $result) {
            if($result['id']==$caja_id&&$result['TurnoFechaInicioAtencion']==$fecha&&$result['TurnoAtendido']==1&&$result['TurnoRechazado']==0) {
                $tiempo=$result['TurnoDuracion'];
                $totalseg=$totalseg+$fun->totalSegundos($tiempo);
                $cont_reg++;
                $bandera=true;
            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$totalseg=0;
        }
        // echo $totalseg."---";
        return array($cont_reg,$totalseg);
    }
    function promedioLlamada($caja_id,$fecha) {
        $totalseg=0;
        $bandera=true;
        $cont_reg=0;
        $fun= new funciones();
        foreach($this->queryTotal as $key=>$result) {
            if($result['id']==$caja_id&&$result['TurnoFechaInicioAtencion']==$fecha&&$result['TurnoAtendido']==1&&$result['TurnoRechazado']==0) {
                $tiempo=$result['TurnoDuracion'];
                $totalseg=$totalseg+$fun->totalSegundos($tiempo);
                $cont_reg++;
                $bandera=true;
                $cont_reg+=1;
                $total_segundos_ini_atencion=$fun->totalSegundos($result['TurnoHoraInicioAtencion']); //retorna la duracion en segundos
                $total_segundos_emision=$fun->totalSegundos($result['TurnoHoraEmision']); //retorna la duracion en segundos
                if (($total_segundos_ini_atencion-$total_segundos_emision)>=0) {
                    $totalseg=$total_segundos_ini_atencion-$total_segundos_emision;

                }
            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$totalseg=0;
        }
        // echo $totalseg."---";
        return array($cont_reg,$totalseg);
    }
    function totalTurnosAnuladosPorDia($caja_id,$fecha) {
        $total=0;
        $bandera=true;
        foreach($this->queryTotal as $key=>$result) {
            if($result['id']==$caja_id&&$result['TurnoFechaInicioAtencion']==$fecha&&($result['TurnoAtendido']==0||$result['TurnoRechazado']==1)) {
                $total++;
                $bandera=true;
            }
            else if($caja_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$total=0;
        }
        return($total);
    }
    function totalServiciosAtendidosPorFecha($servicio_id,$NombreServicio) {
        $total=0;
        $bandera=true;

//        echo($caja_id."..".$fecha."<br />");

        foreach($this->queryTotal as $key=> $valor) {
//            $total=5;
//           echo$result->getatendido();
            if($valor['ServicioId']==$servicio_id&&$valor['ServicioNombre']==$NombreServicio&&$valor['TurnoAtendido']==1&&$valor['TurnoRechazado']==0) {
                $total++;
                $bandera=true;

            }
            else if($servicio_id!="") $bandera=false;
            if(count($this->queryTotal)==$key&&$bandera==false)$total=0;
        }
        return ($total);
    }

    /*Graficar modulos por Fechas
    */
    public function verGraficoFechasAction() {
        $this->setResponse('json');
        $array=$this->getPostParam('array');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);
        $modulos=array();
        $categoria=array();
        $totales=array();
        $array=array();
        $j=0;
        $s = strtotime($hasta)-strtotime($desde);
        $d = intval($s/86400) + 1;
        foreach ($tmp as $indice1=> $valor) {
            foreach ($valor as $indice2 => $valor2) {
                foreach ($valor2 as $indice3 => $valor3) {
                    if($indice3=="modulo")$modulos[$j]=$valor3;
                    if($indice3=="Total")$totales[$j]=$valor3;
                    if($indice3=="Fecha")$categoria[$j]=$valor3;
                }
                $j++;
            }
        }
        $html= "Grafica Estadistica desde ".$desde." hasta ". $hasta ;
//       $html.="categories: [";//'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']";
        $j=0;
        $arrayCategoria=array();
        for($i=1;$i<=$d;$i++) {
            $fecha=getdate();
            $fecha=date("d M", strtotime($categoria[$j]));

            $arrayCategoria[$j]=$fecha;
            $j++;

        }

        $i=1;
        $k=0;
        $l=0;
        $arrayname=array();
        $arraydata=array();
        $arrayseries=array();
        foreach($tmp as $indice1=> $valor1) {
            $j=0;
            $arrayname[$l]=$modulos[$k];
            for($i=1;$i<=$d;$i++) {
                $arraydata[$j]=$totales[$k]+0;

                $j++;
                $k++;
            }
            $arrayseries[$l]=array('name'=>$arrayname[$l],'data'=>$arraydata);
            $l++;
        }

        $arrayTot=array('data'=>$arrayseries,'html'=>$html,'categoria'=>$arrayCategoria);
        return($arrayTot);
    }

    public function verGraficoHorasIndividualAction() {
        $id_modulos=$this->getpostparam('chkmodulos');
        $listamodulos=explode(",",$id_modulos);

        $html="<table border=1>";
        foreach($listamodulos AS $caja_id) {
            $html.="<tr>";
            //echo "dsfasf"; print_r($this->queryTotal);
            foreach($this->queryTotal as $valor) {

                if($caja_id==$valor['id']) {
                    $html.="<td>".$valor['id']."</td>"."<td>".$valor['numeroCaja']."</td>"."<td>".$valor['NombreUsuario']."</td>";

                }
            }
            $html.="</tr>";
        }
        $html.="</table>";
        echo $html;
    }

    /*
     * Graficar Todos los modulos por dias
     * recibe un array multidimencional
    */
    public function verGraficoTodosAction() {
        $this->setResponse('json');
        $array=$this->getPostParam('array');
        $tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);
        $html= "Reporte Gráfico de toda la Semana";
        $fun= new Funciones();
        $data=array();
        $series=array();
        $arraySeries=array();
        $array_datos= array('Lunes'=>"", 'Martes'=>"", 'Mi&eacute;rcoles'=>"", 'Jueves'=>"", 'Viernes'=>"", 'S&aacute;bado'=>"",'Domingo'=>"");
        $array_totales = array();
        foreach ($this->array_datos_modulo as $valor) {
            $modulo_id= $valor['modulo_id'];
            foreach ($array_datos as $dia=>$val) {
                $array_totales[$modulo_id][$dia]=0;
            }
        }
//        print_r($tmp);
//       die();
        foreach ($tmp as $indice0=>$val) {
            $i=0;
//            print_r($val);
//            die();
            foreach ($val as $indice1=> $valor) {   //recorre el modulo
                $suma_t_atendidos=0;
                foreach ($valor as $indice2 => $valor2) {   //recorre todos los dias
                    $t_atendidos=$valor2['suma_t_atendidos'];
                    $array_totales[$indice1][$indice2]=$array_totales[$indice1][$indice2]+$t_atendidos;
                }
            }
        }

        $i=0;
        //foreach ($array_totales as $modulo_id=>$valor){ //valor=array de dias con los totales
        foreach ($this->array_datos_modulo as $v) {
            $modulo_id= $v['modulo_id'];
            $valor= $array_totales[$modulo_id];
            //$data[$modulo_id]="Mod. {$this->array_datos_modulo[$modulo_id]['numero']}";        //modulo_id
            $data[$modulo_id]="Mod. {$v['numero']}";        //modulo_id
            $j=0;
            foreach($valor as $dia=>$t_atendidos) {
                $series[$j]=$t_atendidos+0;
                $j++;
            }
            $arraySeries[$i]=array('name'=>$data[$modulo_id],'data'=>$series);
            $i++;
        }
        $arrayTot=array('data'=>$arraySeries,'html'=>$html);
        return($arrayTot);
    }
    /*
     * Graficar
    */
    public function verGraficoTodosCajasAction() {
        $this->setResponse('json');
        $array=$this->getPostParam('array');
        $id_caja=$this->getPostParam('cajas');
        $tmp = stripslashes($array);
        $tmp = urldecode($tmp);
        $tmp = unserialize($tmp);
        $html= "Reporte Gráfico de toda la Semana";
        $fun= new Funciones();
        $data=array();
        $series=array();
        $arraySeries=array();
        $array_datos= array('Lunes'=>"", 'Martes'=>"", 'Mi&eacute;rcoles'=>"", 'Jueves'=>"", 'Viernes'=>"", 'S&aacute;bado'=>"",'Domingo'=>"");
        $array_totales = array();

//        foreach ($this->array_datos_modulo as $valor){
//            $modulo_id= $valor['modulo_id'];
//            foreach ($array_datos as $dia=>$val){
//                $array_totales[$modulo_id][$dia]=0;
//            }
//        }
//

        $condicion1="grupousuario.grupo_id=7 AND caja.id IN($id_caja)";


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
            $array_datos_cajas[]=array('modulo_id'=>$result->getId(),'usuario'=>$result->getNombres(),'numero'=>$result->getNumeroCaja());
        }
        foreach ($array_datos_cajas as $valor) {
            $modulo_id= $valor['modulo_id'];
            foreach ($array_datos as $dia=>$val) {
                $array_totales[$modulo_id][$dia]=0;
            }
        }
//        print_r($array_totales);
//       die();
        foreach ($tmp as $indice0=>$val) {
            $i=0;
//            print_r($val);
//            die();
            foreach ($val as $indice1=> $valor) {   //recorre el modulo
                $suma_t_atendidos=0;
                foreach ($valor as $indice2 => $valor2) {   //recorre todos los dias
                    $t_atendidos=$valor2['suma_t_atendidos'];
                    $array_totales[$indice1][$indice2]=$array_totales[$indice1][$indice2]+$t_atendidos;
                }
            }
        }

        $i=0;
        //foreach ($array_totales as $modulo_id=>$valor){ //valor=array de dias con los totales
        foreach ($array_datos_cajas as $v) {
            $modulo_id= $v['modulo_id'];
            $valor= $array_totales[$modulo_id];
            //$data[$modulo_id]="Mod. {$this->array_datos_modulo[$modulo_id]['numero']}";        //modulo_id
            $data[$modulo_id]="Mod. {$v['numero']}";        //modulo_id
            $j=0;
            foreach($valor as $dia=>$t_atendidos) {
                $series[$j]=$t_atendidos+0;
                $j++;
            }
            $arraySeries[$i]=array('name'=>$data[$modulo_id],'data'=>$series);
            $i++;
        }
        $arrayTot=array('data'=>$arraySeries,'html'=>$html);
        return($arrayTot);
    }
    public function verGraficoAction() {
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $id_caja=$this->getPostParam('modulo');
        $ttat= $this->getPostParam('subtotal_total_turnos_atendidos');
        $ttan= $this->getPostParam(' subtotal_total_turnos_anulados');
        $lun=$this->getPostParam('lunes');
        $mar=$this->getPostParam('martes');
        $mie=$this->getPostParam('miercoles');
        $jue=$this->getPostParam('jueves');
        $vie=$this->getPostParam('viernes');
        $sab=$this->getPostParam('sabado');
        $dom=$this->getPostParam('domingo');
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



        $reportes = new Reportes();
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

        $arrayCategoria=array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');
        $arrayDAta=array($lun+0,$mar+0,$mie+0,$jue+0,$vie+0,$sab+0, $dom+0);
        // $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']";
        $arraySeries[0]=array('name'=>'Tunos A. Semana','data'=>$arrayDAta);
        //$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie.", ".$sab.", ".$dom."]";
        $arrayTot=array('categoria'=>$arrayCategoria,'data'=>$arraySeries,'html'=>$html);
        return($arrayTot);
        //echo "variable".$variable1;
        /*echo "<fieldset class='ui-corner-all ui-widget-content'>
            <legend><b>Servicios</b></legend>
            <p><label class='labelform' ><span for='usuarios'>Seleccione el servicio:</span></label></p>
        </fieldset>";*/
    }
    public function verGraficoColasAction() {
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $id_caja=$this->getPostParam('modulo');
        $ttat= $this->getPostParam('subtotal_total_turnos_atendidos');
        $ttan= $this->getPostParam(' subtotal_total_turnos_anulados');
        $lun=$this->getPostParam('lunes');
        $mar=$this->getPostParam('martes');
        $mie=$this->getPostParam('miercoles');
        $jue=$this->getPostParam('jueves');
        $vie=$this->getPostParam('viernes');
        $sab=$this->getPostParam('sabado');
        $num_modulo= $id_caja;
        $usuario="Todos";
        if (!empty ($_GET['id_modulo'])) {
            $id_modulo=$_GET['id_modulo'];
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
        }



        $reportes = new Reportes();
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

        $arrayCategoria=array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab');
        $arrayDAta=array($lun+0,$mar+0,$mie+0,$jue+0,$vie+0,$sab+0);
        // $html.="categories: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']";
        $arraySeries[0]=array('name'=>'Tunos A. Semana','data'=>$arrayDAta);
        //$html.="data: [".$lun.",".$mar.", ".$mie.", ".$jue.", ".$vie.", ".$sab.", ".$dom."]";
        $arrayTot=array('categoria'=>$arrayCategoria,'data'=>$arraySeries,'html'=>$html);
        return($arrayTot);
        //echo "variable".$variable1;
        /*echo "<fieldset class='ui-corner-all ui-widget-content'>
            <legend><b>Servicios</b></legend>
            <p><label class='labelform' ><span for='usuarios'>Seleccione el servicio:</span></label></p>
        </fieldset>";*/
    }
    public function verGraficoColas1Action() {
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


        $reportes = new Reportes();
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
        $html.="this.x +': '+ this.y +'Ã¯Â¿Â½C';";
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
        $this->setResponse('json');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $id_caja=$this->getPostParam('cajas');
        $html="";
        $tituloCabecera="";
        if (!empty($id_caja)) {
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
            $tituloCabecera="<center><text style='$tituloEstilo'>Cuadro de turnos Atendidos, Anulados y Promedios<br />Desde ".$desde."hasta ". $hasta."</text></center><br />";
//            $tituloCabecera.=$this->encabezadoPorSemana();
            //INICIO SEPARAR POR SEMANAS
            $s = strtotime($hasta)-strtotime($desde);
            $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas

            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;   //solo para q empiece desde un dÃƒÂ¯Ã‚Â¿Ã‚Â½a menos

            $forma_array_semanas= array();
            $array_aux= array();
            $aux="";

            $dias= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
            $array_datos_cajas=array();
            if ($id_caja!="")
                $condicion1="grupousuario.grupo_id=7 AND caja.id IN($id_caja)";
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
                $array_datos_cajas[]=array('modulo_id'=>$result->getId(),'usuario'=>$result->getNombres(),'numero'=>$result->getNumeroCaja());
            }
//        print_r($array_datos_cajas);
//        die();
            for ($i=1;$i<=$d;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day")) ; //a la fecha anterior aumenta un dia
                $separa = explode("-",$fecha);
                $anio = $separa[0];
                $mes= $separa[1];
                $dia = $separa[2];
                $num_semana = date('W', mktime(0, 0, 0, $mes, $dia, $anio));        //numero de semanas
                $dia_de_semana=strtoupper($dias[intval((date("w",mktime(0,0,0,$mes,$dia,$anio))))]);  //obtener el dia de la semana
                //$forma_array_semanas[$num_semana][$pru]=$dia;   //forma el array milti con el numero de semana y los dias
                $forma_array_semanas[$num_semana][$dia_de_semana]="$anio-$mes-$dia";   //forma el array milti con el numero de semana y los dias
                //echo "num semana: ".$num_semana."<br>";
            }
            //FIN SEPARAR POR SEMANAS

            $html="";
            $dias = array('Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado','Domingo');
            $fun= new Funciones();
//            $html.="<table border='0'align='right'>";
            $html.=$this->encabezadoPorSemana();
            $html.="<tbody>";
            //INICIO POR SEMANAS
            $array_total= array();
            $num_semana=0;
            foreach($forma_array_semanas as $num_semana=>$val) { //al numero de semanas
                $desde = reset( $val ); //primer dia de la semana
                $hasta = end( $val );   //ultimo dia de la semana
                $fecha_en_semana= date("Y-m-d", strtotime( "$desde - 1 day")) ;   //solo para q empiece desde un dia meno
//                $html.="Semana del $desde al $hasta <tr>" ;  //por cada semana un tr y dentro de este una tabla

                $s = strtotime($hasta)-strtotime($desde);
                $d = intval($s/86400) + 1;      //calcular el total de dias entre fechas
                $array_datos= array('Lunes'=>"", 'Martes'=>"", 'Mi&eacute;rcoles'=>"", 'Jueves'=>"", 'Viernes'=>"", 'S&aacute;bado'=>"",'Domingo'=>"");
                $array_suma_dias= array();      //para poner los turnos atendidos por modulo de lunes a viernes para graficar
                foreach($array_datos_cajas as $val) {
                    $modulo_id= $val['modulo_id'];
                    foreach ($array_datos as $key=>$dia) {
                        $array_suma_dias[$modulo_id][$key]['suma_t_atendidos']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_t_anulados']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_duracion_total']=0;
                        $array_suma_dias[$modulo_id][$key]['suma_promedio_total']=0;
                    }
                }
                $array_total[$num_semana]=$array_suma_dias;

                for ($i=1;$i<=$d;$i++) {
                    $datos="";
                    $fecha_en_semana= date("Y-m-d", strtotime( "$fecha_en_semana + 1 day")) ; //a la fecha anterior aumenta un dia
                    $datos.="<table border=0>";
                    $datos.=$this->encabezadoPorDia();
                    $total_t_atendidos_x_dia=0;
                    $total_t_anulados_x_dia=0;
                    $total_duracion_x_dia=0;
                    $total_promedio_x_dia=0;
                    foreach($array_datos_cajas as $val) {
                        $modulo_id= $val['modulo_id'];
                        $promedio_en_horas=0;
                        $promedio_en_segundos=0;
                        $dia= $this->array_cuadro_cajas[$fecha_en_semana][$modulo_id]['dia'];
                        $t_atendidos= $this->array_cuadro_cajas[$fecha_en_semana][$modulo_id]['t_atendidos'];
                        $t_anulados= $this->array_cuadro_cajas[$fecha_en_semana][$modulo_id]['t_anulados'];
                        $duracion_en_segundos= $this->array_cuadro_cajas[$fecha_en_semana][$modulo_id]['duracion_t'];
                        $duracion_en_horas= $fun->tiempo($duracion_en_segundos);
                        if ($t_atendidos!=0) {
                            $promedio_en_segundos= round($duracion_en_segundos/$t_atendidos);
                            $promedio_en_horas= $fun->tiempo($promedio_en_segundos);
                        }
                        $total_t_atendidos_x_dia+=$t_atendidos;
                        $total_t_anulados_x_dia+=$t_anulados;
                        $total_duracion_x_dia+=$duracion_en_segundos;
                        $total_promedio_x_dia+=$promedio_en_segundos;
                        $datos.="<tr><td align='center' style='background-color:#e5f1f4;'>$t_atendidos</td>
                        <td align='center' style='background-color:#e5f1f4;'>$t_anulados</td>
                        <td align='center' style='background-color:#e5f1f4;'>$duracion_en_horas</td>
                        <td align='center' style='background-color:#e5f1f4;'>$promedio_en_horas</td></tr>";

                        $array_suma_dias[$modulo_id][$dia]['suma_t_atendidos']+=$t_atendidos;
                        $array_suma_dias[$modulo_id][$dia]['suma_t_anulados']+=$t_anulados;
                        $array_suma_dias[$modulo_id][$dia]['suma_duracion_total']+=$duracion_en_segundos;
                        //$array_suma_dias[$modulo_id][$dia]['suma_promedio_total']+=$t_atendidos;
                    }
                    $datos.="<tr>";
                    $datos.="<td align='right'  style='background-color:#FEE1BA;'>".$total_t_atendidos_x_dia."</td>";
                    $datos.="<td align='right'  style='background-color:#FEE1BA;'>".$total_t_anulados_x_dia."</td>";
                    $datos.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($total_duracion_x_dia)."</td>";
                    $datos.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($total_promedio_x_dia)."</td>";
                    //$html.="<td align='right' style='background-color:#e5f1f4;'>".$llamada_promedio."</td>";
                    $datos.="</tr>";
                    $datos.="</table>";
                    $array_datos[$dia]=$datos;
                }
                $html.="<td width=20%>";
                $html.="<table  border='0'>";
                $html.="<tr style='$fondo_titulo'><td style='$fondo_titulo'>Módulo</td><td style='$fondo_titulo'width=200px  align='left'>Usuario</td></tr>";
                //foreach($lista_modulos as $modulo_id) {
                foreach($array_datos_cajas as $val) {
                    $modulo_id= $val['modulo_id'];
                    $num_modulo= $val['numero'];
                    $usuario_modulo= $val['usuario'];
                    $html.="<tr><td align='left' width=2% style='background-color:#e5f1f4;'>$num_modulo</td>";
                    $html.="<td align='left' width=18% style='background-color:#e5f1f4;'>$usuario_modulo</td></tr>";
                }
                $html.="<tr><td></td><td align='left' style='background-color:#FEE1BA;'>Totales: </td></tr>";
                $html.="</table >";

                $html.="</td>";

                foreach($dias as $dia) {
                    $html.="<td align='right'>$array_datos[$dia]</td>";
                }
                //tabla de totales por semana
                $html.="<td align='right'>";
                $html.="<table width=10%>";
                $html.=$this->encabezadoPorDia();
                $acu_t_atendidos=0;
                $acu_t_anulados=0;
                $acu_suma_duracion=0;
                $acu_suma_promedio=0;
                foreach($array_datos_cajas as $val) {
                    $modulo_id= $val['modulo_id'];
                    $html.="<tr>";
                    $suma_t_atendidos=0;
                    $suma_t_anulados=0;
                    $suma_duracion_t=0;
                    $suma_promedio_t=0;
                    foreach ($array_datos as $key=>$dia) {       //de lunes a domingo
                        $suma_t_atendidos += $array_suma_dias[$modulo_id][$key]['suma_t_atendidos'];
                        $suma_t_anulados += $array_suma_dias[$modulo_id][$key]['suma_t_anulados'];
                        $suma_duracion_t += $array_suma_dias[$modulo_id][$key]['suma_duracion_total'];
                        $suma_promedio_t += $array_suma_dias[$modulo_id][$key]['suma_promedio_total'];
                    }
                    $PA=0;
                    if($suma_t_atendidos!=0)
                        $PA=$suma_duracion_t/$suma_t_atendidos;
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$suma_t_atendidos."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$suma_t_anulados."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($suma_duracion_t)."</td>";
                    $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($PA)."</td>";
                    $html.="<td align='right' > -       - </td>";
                    $html.="</tr>";
                    $acu_t_atendidos+=$suma_t_atendidos;
                    $acu_t_anulados+=$suma_t_anulados;
                    $acu_suma_duracion+=$suma_duracion_t;
                    $acu_suma_promedio+=$PA;

                }
                //totales por semana de todos los modulos
                $html.="</tr>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>$acu_t_atendidos&nbsp;</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>$acu_t_anulados</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($acu_suma_duracion)."</td>";
                $html.="<td align='right' style='background-color:#FEE1BA;'>".$fun->tiempo($acu_suma_promedio)."</td>";
                $html.="</tr>";
                $html.="</table>";
                $html.="</td>";
                $html.="</tr>";
                $html.="<tr>";
                $html.="<td colspan='9'>";
                $array_aux= array();
                $array_aux[]=$array_suma_dias;
                $array_total[$num_semana]=$array_suma_dias;
                $mi_array= $array_aux;
                $compactada=serialize($mi_array);
                $compactada=urlencode($compactada);
                $fecm7=$desde;
                $html.="<input type='button' style='background-color:#e5f1f4;width:250px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;' value='Graficar Semana $fecm7 al $fecha_en_semana' onclick="." \"verGraficoSemanaColas('$compactada')\""."/>";
                $html.="</td>";
                $html.="</tr>";
                //print_r ($array_suma_dias);
            }
            //print_r ($array_total);
            //FIN POR SEMANAS

            $html.="</table>";

            $mi_array= $array_total;
            $compactada=serialize($mi_array);
            $compactada=urlencode($compactada);
            $html.="<input type='button' style='background-color:#e5f1f4;width:100px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;' value='Graficar Todo' onclick="." \"verGraficoSemanaColas('$compactada')\""."/>";


            $html.="</table>";
        } else
            $html.="No ha seleccionado algún módulo o servicio";

        $datos= array("v1"=>$html,"titulo"=>$tituloCabecera);
//        $fondo_titulo= "background-color:#328aa4; color:#fff";
//        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
//        $tituloCabecera="<center><text style='$tituloEstilo'>Cuadro de Turnos, Duración y Promedios<br />Desde ".$desde."hasta ". $hasta."</text></center>";
//        $fondo_titulo= "background-color:#328aa4; color:#fff";
//        $html.="<table align='center'>";
//        $array_dias_semana = array('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
//        $array_encabezado_dia=array(
//                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
//                //'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
//                'DT'=>array('etiqueta'=>'DT','titulo'=>'Duración Total'),
//                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de Atención'),
//                'ES'=>array('etiqueta'=>'','titulo'=>'',) //para el grafico
//        );
//        $cont_encabezado=count($array_encabezado_dia);
//        $html.="<tr>";
//        $html.="<th style='$fondo_titulo'>Caja</th>";
//        $html.="<th style='$fondo_titulo'>Usuario</th>";
//        //$html.="<th style='background-color:#328aa4; color:#fff'></th>";
//        foreach ($array_dias_semana as $dia) {
//            $html.="<th colspan=$cont_encabezado style='$fondo_titulo'>$dia</th>";
//        }
//        $html.="<th style='$fondo_titulo' title='Total Turnos Atendidos'>TTA</th>";
//        //$html.="<th style='$fondo_titulo' title='Total Turnos anulados'>TTa</th>";
//        $html.="<th style='$fondo_titulo'></th>";
//        $html.="</tr>";
//
//        $html.="<tr>";
//        $html.="<th style='$fondo_titulo'></th>
//            <th style='$fondo_titulo'></th>";
//        foreach ($array_dias_semana as $dia) {
//            foreach ($array_encabezado_dia as $valor)
//                $html.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
//        }
//        $html.="<th style='$fondo_titulo'></th>";
//        $html.="<th style='$fondo_titulo'></th>";
//        //$html.="<th style='$fondo_titulo'></th>";
//        $html.="</tr>";
//        $suma_totales_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
//        $suma_totales_turnos_anulados=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
//        $suma_duracion_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
//        $suma_promedio_turnos_atendidos=array('Monday'=>0,'Tuesday'=>0,'Wednesday'=>0,'Thursday'=>0,'Friday'=>0,'Saturday'=>0);
//        $grantotal_turnos_atendidos=0;
//        $grantotal_turnos_anulados=0;
//        $forma_array=array();
//        if ($id_caja<>0)
//            $condicion1="grupousuario.grupo_id=7 AND caja.id IN($id_caja)";
//        else
//            $condicion1="grupousuario.grupo_id=7";
//        $query = new ActiveRecordJoin(array(
//                        "entities" => array("Caja", "Usercaja", "Usuario","Grupousuario"),
//                        "fields" => array(
//                                "{#Caja}.id",
//                                "{#Caja}.numero_caja",
//                                "{#Usuario}.nombres"),
//                        "conditions" => $condicion1,
//                        "order"=>"{#Caja}.numero_caja"
//        ));
//
//        foreach($query->getResultSet() as $result) {
//            $id_caja=$result->getId();
//            $num_modulo=$result->getNumeroCaja();
//            $usuario=$result->getNombres();
//            $reportes = new Reportes();
//            $fun= new Funciones();
//            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND c.id= $id_caja AND atendido=1 AND duracion>='00:00:05'";
//            $html.="<tr>";
//            $html.="<td align='center' style='background-color:#e5f1f4;'>".$num_modulo."</td>";
//            $html.="<td align='left' style='background-color:#e5f1f4;'>".$usuario."</td>";
//            $totales_turnos_atendidos= array(); //sirve para graficar
//            $tta=0;
//            $dias_semana= array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
//            $subtotal_total_turnos_atendidos=0;
//            $subtotal_total_turnos_anulados=0;
//            foreach ($dias_semana as $dia) {
//                $duracion_total=0;
//                $duracion_promedio=0;
//                $tot_t_atendidos_x_dia= $reportes->totalColasAtendidosPorDia($dia, $condicion);
//                list($cont_reg,$total_segundos) = $reportes->duracionAtencionColas($dia, $condicion);
//                if ($tot_t_atendidos_x_dia != 0) {
//                    $duracion_total=$fun->tiempo($total_segundos);
//                    $duracion_promedio=$fun->tiempo(round($total_segundos/$cont_reg));
//                    $suma_totales_turnos_atendidos[$dia]=$suma_totales_turnos_atendidos[$dia]+$tot_t_atendidos_x_dia;
//                     $suma_duracion_turnos_atendidos[$dia]=$suma_duracion_turnos_atendidos[$dia]+$total_segundos;
//                    $suma_promedio_turnos_atendidos[$dia]=$suma_promedio_turnos_atendidos[$dia]+round($total_segundos/$cont_reg);
//                }
//                $html.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_dia."</td>";
//                //$html.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_dia."</td>";
//                $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
//                $html.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
//                $html.="<td style='background-color:#e5f1f4;'><input type='button' title='Graficar este dia' style='background-color:#e5f1f4;width:50px;height:20px;border-width:thin;border-style:solid;border-color:white;color:black;'value='Graficar' onclick=datosGraficoHorasCajas($id_caja,'$dia',$tot_t_atendidos_x_dia)> </td>";
//                $totales_turnos_atendidos[$dia]=$tot_t_atendidos_x_dia;
//                $forma_array[$num_modulo]=$totales_turnos_atendidos;
//                $subtotal_total_turnos_atendidos+=$tot_t_atendidos_x_dia;
//
//                $grantotal_turnos_atendidos+=$tot_t_atendidos_x_dia;
//
//            }
//            $html.="<td align='right' style='background-color:#bce774;'>".$subtotal_total_turnos_atendidos."</td>";
//            $lunes=$totales_turnos_atendidos['Monday'];
//            $martes=$totales_turnos_atendidos['Tuesday'];
//            $miercoles=$totales_turnos_atendidos['Wednesday'];
//            $jueves=$totales_turnos_atendidos['Thursday'];
//            $viernes=$totales_turnos_atendidos['Friday'];
//            $sabado=$totales_turnos_atendidos['Saturday'];
//            $html.="<td style='background-color:#e5f1f4;'><input type='button' style='background-color:#e5f1f4;width:50px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;'value='Graficar' width='20px' onclick="." \"verGraficoSemanaColas('$id_caja','$subtotal_total_turnos_atendidos','$subtotal_total_turnos_anulados','$desde','$hasta',$lunes,$martes,$miercoles,$jueves,$viernes,$sabado)\""."/></td>";
//            $html.="</tr>";
//        }
//
//        //INICIO IMPRIMIR LA FILA DE LA SUMA DE TODOS LOS MODULOS POR DIAS
//        $html.="<tr>";
//        $html.="<th style='background-color:#e5f1f4;'></th>";
//        $html.="<th style='background-color:#e5f1f4;'>TOTALES</th>";
//        foreach ($suma_totales_turnos_atendidos as $key => $val) {
//            $html.="<th style='background-color:#FEE1BA;'>".$suma_totales_turnos_atendidos[$key]."</th>";
//            $html.="<th style='background-color:#e5f1f4;'>".$fun->tiempo($suma_duracion_turnos_atendidos[$key])."</th>";
//            $html.="<th style='background-color:#e5f1f4;'>".$fun->tiempo($suma_promedio_turnos_atendidos[$key])."</th>";
//            $html.="<th style='background-color:#e5f1f4;'></th>";
//        }
//        $lun=$suma_totales_turnos_atendidos['Monday'];
//        $mar=$suma_totales_turnos_atendidos['Tuesday'];
//        $mier=$suma_totales_turnos_atendidos['Wednesday'];
//        $jue=$suma_totales_turnos_atendidos['Thursday'];
//        $vie=$suma_totales_turnos_atendidos['Friday'];
//        $sab=$suma_totales_turnos_atendidos['Saturday'];
//        $html.="<th style='background-color:#bce774;' title='Sumar Total de los Turnos Atendidos'>$grantotal_turnos_atendidos</th>";
//        $html.="<td style='background-color:#e5f1f4;'><input type='button' style='background-color:#e5f1f4;width:50px;height:20px;border-width:thin;border-style:solid;border-color:white;color:red;'value='Graficar' width='20px' onclick="." \"verGraficoSemanaColas('$id_caja',$grantotal_turnos_atendidos,$grantotal_turnos_anulados,'$desde','$hasta',$lun,$mar,$mier,$jue,$vie,$sab)\""."/></td>";
//        $html.="</tr>";
//        $html.="</table>";
//        $mi_array= $forma_array;
//        $compactada=serialize($mi_array);
//        $compactada=urlencode($compactada);
//        $datos= array("v1"=>$html,"titulo"=>$tituloCabecera);
        return ($datos);
    }
    /*
     * Inicio para el reporte de los turnos atendidos por mÃ¯Â¿Â½dulo
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
        //Lista de MÃ¯Â¿Â½dulos
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
     * Inicio de exportaciÃ¯Â¿Â½n a Excel de Turnos
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
        //Lista de MÃ¯Â¿Â½dulos
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
     * Inicio de exportaciÃ¯Â¿Â½n a Excel de Colas
    */
    public function colasExcelAction() {
        $this->setResponse('ajax');
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        //$id_caja=$this->getPostParam('cajas');
        $cajas=$this->getPostParam('chkcajas');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $lista=array();
//        Tag::displayTo('desde', date("Y-m-d"));
//        Tag::displayTo('hasta', date("Y-m-d"));
        //Lista de MÃ¯Â¿Â½dulos
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
     * MenÃ¯Â¿Â½ para Reporte que muestra el cuador de los totales de turnos atendidos por dÃ¯Â¿Â½as
    */
    public function turnosAtendidosConTiqueAction() {
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
    /*
     * MenÃ¯Â¿Â½ para Reporte que muestra los totales de colas atendidos por dÃ¯Â¿Â½as
    */
    public function colasAtendidosDiasAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));

        //INICIO LISTA DE MÃ¯Â¿Â½DULOS
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
        //FIN LISTA DE MÃ¯Â¿Â½DULOS
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
        //Lista de MÃ¯Â¿Â½dulos
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
     * Reporte que muestra los totales de turnos atendidos por MÃ¯Â¿Â½dulos
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
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por M�dulo");
        $pdf->lineFeed();

        //La fecha del reporte
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->writeCell(40, 7, "Fecha de Reporte: ".Date::getCurrentDate());
        $pdf->lineFeed();

        //Encabezados con fondo gris
        $lightGray = PdfColor::fromGrayScale(0.75);
        $pdf->setFillColor($lightGray);
        $pdf->writeCell(10, 7, '#', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'M�dulo', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(55, 7, 'Descripci�n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atenci�n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atenci�n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'FFA', 0, 0, PdfDocument::ALIGN_CENTER);
        //$pdf->writeCell(15, 7, 'HIA', 0, 0, PdfDocument::ALIGN_CENTER);
        $pdf->writeCell(20, 7, 'Duraci�n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS MÃ¯Â¿Â½DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÃ¯Â¿Â½ un mÃ¯Â¿Â½dulo
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
                        $pdf->writeCell(40, 7, "Duraci�n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los mÃ¯Â¿Â½dulos
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
                            $pdf->writeCell(40, 7, "Duraci�n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS MÃ¯Â¿Â½DULOS
        $pdf->outputToBrowser();

    }

    /*
     * Reporte que muestra los totales de turnos atendidos por DÃ¯Â¿Â½as
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
        $pdf->writeCell(40, 7, "Reporte de Turnos atendidos por MÃƒÂ³dulo");
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
        $pdf->writeCell(20, 7, 'MÃƒÂ³dulo', 0, 0, PdfDocument::ALIGN_LEFT);
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

        //INICIO OBTENER LOS MÃ¯Â¿Â½DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÃ¯Â¿Â½ un mÃ¯Â¿Â½dulo

            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";

            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada dÃ¯Â¿Â½a
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
            $pdf->writeCell(20, 7, 'MÃƒÂ³dulo', 0, 0, PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los mÃ¯Â¿Â½dulos osea &id_caja==0
            //SELECT COUNT(*) AS total_dia FROM caja c, turnos t, servicio s, usercaja uc, usuario u
            //WHERE c.id=t.caja_id AND s.id=t.servicio_id AND c.id=uc.caja_id AND u.id=uc.usuario_id
            //AND fecha_inicio_atencion BETWEEN '2010-11-22' AND '2010-11-22' AND DAYNAME(fecha_emision)="Monday";
            $promedio_lunes=0;
            $promedio_martes=0;
            $promedio_miercoles=0;
            $promedio_jueves=0;
            $promedio_viernes=0;
            if ($id_servicio==0) {  //Todos los servicios y hacer consulta por cada dÃ¯Â¿Â½a
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
            $pdf->writeCell(20, 7, 'MÃƒÂ³dulo', 0, 0, PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS MÃ¯Â¿Â½DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Reporte en PDF del reporte de turnso atendidos por caja
     * filtro por mÃ¯Â¿Â½dulo y por servicio
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
        $pdf->writeCell(40, 7, 'DescripciÃƒÂ³n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(35, 7, 'Usuario', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(35, 7, 'Servicio', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(15, 7, 'Turno', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. inicio atenciÃƒÂ³n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(40, 7, 'F. fin atenciÃƒÂ³n', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->writeCell(20, 7, 'DuraciÃƒÂ³n', 0, 0, PdfDocument::ALIGN_LEFT);
        //$pdf->writeCell(20, 7, 'Calidad de Serv.', 0, 0, PdfDocument::ALIGN_LEFT);
        $pdf->lineFeed();

        //Volver al fondo blanco
        $white = PdfColor::fromName(PdfColor::COLOR_WHITE);
        $pdf->setFillColor($white);

        //INICIO OBTENER LOS MÃ¯Â¿Â½DULOS
        if ($id_caja<>0) { //Si es <> 0, entonces seleccionÃ¯Â¿Â½ una caja
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
                        $pdf->writeCell(40, 7, "DuraciÃƒÂ³n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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

        } else { //Por caso contrario se ha seleccionado Todos los mÃ¯Â¿Â½dulos
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
                            $pdf->writeCell(40, 7, "DuraciÃƒÂ³n: ".$duracion_total, 0, 0,PdfDocument::ALIGN_LEFT);
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
        //FIN OBTENER LOS MÃ¯Â¿Â½DULOS
        $pdf->outputToBrowser();
    }

    /*
     * Funcion que construye los datos para excel en tabla
    */
    public function reporteTurnosAtendidosModuloXlsAction() {
        $this->setResponse('ajax');
        // $reportes= new Reportes();
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        //$id_caja=$this->getPostParam('cajas');
        $modulos=$this->getPostParam('chkmodulos');
        $servicios=$this->getPostParam('chkservicios');

        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        if(($modulos!="")&&($servicios!="")) {
            $lista_modulos=explode(',', $modulos);
            $lista_servicios=explode(",",$servicios);
            $forma_duracion=$horas.":".$minutos.":".$segundos;
            $forma_condicion_servicios="";
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }

            //$id_servicio=$this->getPostParam('servicios'); //para combo
            $html="";
            //$html.="<html>";
            //$html.="<head>";
            //$html.="<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />";
            //$html.="<title>JQuery Excel</title>";
            //$html.="<script type='text/javascript' src='../../js/jquery-1.3.2.min.js'></script>";

            $fun= new Funciones();
            $html.=$fun->encabezadoTableSorter();
            $fondo_titulo= "background-color:#328aa4; color:black";
            //crear tabla con id y class=tablesorter
//            $html.="<div id='overlay' aling=center>
//		Please wait...
//		</div>";

            $html.="<center><table class='tablesorter' id='Exportar_a_Excel' aling=center >
        <thead>";
            $html.="<tr style ='$fondo_titulo'>";
            $html.="<th>Módulo</th>";
            //$html.="<th>DescripciÃƒÂ³n MÃƒÂ³dulo</th>";
            $html.="<th >Usuario</th>";
            $html.="<th>Servicio</th>";
            $html.="<th>Letra</th>";
            $html.="<th>Num. turno</th>";
            $html.="<th>Fecha emisión</th>";
            $html.="<th>Hora emisión</th>";
            $html.="<th>Anulado</th>";
            $html.="<th>F. inicio atención</th>";
            $html.="<th>H. inicio atención</th>";
            $html.="<th>F. fin atención</th>";
            $html.="<th>H. fin atención</th>";
            $html.="<th>Duración</th>";
            $html.="</tr>
        </thead>
        <tbody>";

            $anulado="";
            foreach($lista_modulos as $id_modulo) {
                foreach($this->queryTotal as $valor) {
                    if ($valor['id']==$id_modulo) {
                        if ( $valor['TurnoAtendido']==1) { //fue correctamente atendido
                            if ($valor['TurnoRechazado']==1) {
                                $anulado="SI";
                                $fia="0000-00-00";
                                $hfa="00:00:00";
                                $duracion="00:00:00";
                            } else {
                                $anulado="NO";
                                $fia=$valor['TurnoFechaFinAtencion'];
                                $hfa=$valor['TurnoHoraFinAtencion'];
                                $duracion=$valor['TurnoDuracion'];
                            }
                        } else {
                            $fia="0000-00-00";
                            $hfa="00:00:00";
                            $duracion="00:00:00";
                            if ( $valor['TurnoPorAtender']==1) {
                                $anulado="En atención";
                            }
                        }
                        $html.="<tr style='background-color:#e5f1f4;' >";
                        $html.="<td >".$valor['numeroCaja']."</td>";
                        //$html.="<td>".$result->getDescripcion()."</td>";
                        $html.="<td>".$valor['NombreUsuario']."</td>";
                        $html.="<td>".$valor['ServicioNombre']."</td>";
                        $html.="<td>".$valor['ServicioLetra']."</td>";
                        $html.="<td>".$valor['TurnoNumero']."</td>";
                        $html.="<td>".$valor['TurnoFechaEmision']."</td>";
                        $html.="<td>".$valor ['TurnoHoraEmision']."</td>";
                        $html.="<td>".$anulado."</td>";
                        $html.="<td>".$valor['TurnoFechaInicioAtencion']."</td>";
                        $html.="<td>".$valor['TurnoHoraInicioAtencion']."</td>";
                        $html.="<td>".$fia."</td>";
                        $html.="<td>".$hfa."</td>";
                        $html.="<td>".$duracion."</td>";
                        $html.="</tr>";
                    }
                }

            }
        }
        else
            $html="Escoja servicio";
        echo $html;
    }

    /*
     * Funcion que construye los datos para excele en tabla
    */
    public function reporteColasAtendidasCajaXlsAction() {
        $this->setResponse('ajax');
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
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $html.="<form action='ficheroExcel' method='post' target='_blank' id='FormularioExportacion'>";
//        $html.="<p>Exportar a Excel  <img src='../../img/export_to_excel.gif' class='botonExcel' /></p>";
        $html.="<input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />";
        $html.="</form>";
        $html.="<center><table border='1' cellpadding='2' cellspacing='0' bordercolor='#666666' id='Exportar_a_Excel' style='border-collapse:collapse;'>";
        $html.="<tr style='$fondo_titulo'>";
        $html.="<th>Caja</th>";
        $html.="<th>Descripción Caja</th>";
        $html.="<th>Usuario</th>";
        //$html.="<th>Anulado</th>";
        $html.="<th>F. inicio atención</th>";
        $html.="<th>H. inicio atención</th>";
        $html.="<th>F. fin atención</th>";
        $html.="<th>H. fin atención</th>";
        $html.="<th>Duración</th>";
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
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND {#Caja}.id IN( $id_caja) AND {#Colas}.atendido=1 AND {#Colas}.duracion>='00:00:05'";
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
                $html.="<tr style='background-color:#e5f1f4;'>";
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
     * FunciÃ¯Â¿Â½n que exporta a excel
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
     * Menu: EstadÃ¯Â¿Â½sticas de calificaciones
    */
    public function verCuadroCalificacionesAction() {
        $this->setResponse('ajax');
        $reportes= new Reportes();
        
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $hora_i="08:30";//$this->getPostParam('hora_i');
        $hora_f="17:30";//$this->getPostParam('hora_f');
        $modulos=$this->getPostParam('chkmodulos');   //me sirve para categorias para los graficos
        $servicios=$this->getPostParam('chkservicios');
        $preguntas=$this->getPostParam('preguntas');   //me sirve pra la etiquetas de las filas
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $forma_duracion="00:00:00";
        $forma_condicion_servicios="";
        $html="";
        $lista_modulos=explode(',',$modulos);
        $lista_servicios=explode(',',$servicios);
        $lista_preguntas=explode(',',$preguntas);
        if (!empty($lista_servicios)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }

        $array_calificacion= array();
//        $calificacion= new Calificacion();
//        $buscaCalificacion= $calificacion->find();
//        foreach($buscaCalificacion as $result) {
//            $array_calificacion[$result->getPuntos()]=$result->getNomCalificacion();
//        }

        if (!empty($lista_modulos) & !empty($lista_servicios)&!empty($preguntas)) {

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
            //$html.="<link href='../../css/tablecloth.css' rel='stylesheet' type='text/css' media='screen' />";
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
//           print_r($this->lista_modulos_calif);
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
//                print_r($lista_modulos);
//                die();
                $i=0;
                $lista_Total_califacada=array();
                foreach($lista_modulos as $modulo_id) {
                    $j+=1;
                    $turnos_calificados=$reportes->turnosCalificados($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion);
                    $lista_Total_califacada[$i]=$turnos_calificados;
                    $html.="<td>$turnos_calificados</td>";
                    $promedio=round($reportes->promedioCalificacion($desde,$hasta,$hora_i,$hora_f,$modulo_id,$pregunta_id,$forma_condicion_servicios,$forma_duracion));
                    $html.="<td>$promedio</td>";
                    $nom_calificacion=$reportes->nombreCalificacion($array_calificacion, $promedio);
                    $html.="<td>$nom_calificacion</td>";
                    $total_turnos_calificados+=$turnos_calificados;
                    $sum_promedios+=$promedio;
                    $array_modulo[]=$promedio;

                    $array[$pregunta_id][$modulo_id]=$promedio; //***
                    $i++;
                }
                $array_pregunta[$pregunta_id]=$array_modulo;
                $array_fila[]=$array_pregunta;          //formo array multi para estÃ¯Â¿Â½ndar pero solo x pregunta

                $promedio_pregunta=round($sum_promedios/$cont_modulos);
                $nom_calificacion=$reportes->nombreCalificacion($array_calificacion, $promedio_pregunta);
                $html.="<td>$total_turnos_calificados</td>";
                $html.="<td>$promedio_pregunta</td>";
                $html.="<td>$nom_calificacion</td>";
                $total=implode(',',$lista_Total_califacada);
//                $html.="<td><a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_fila)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar</a><td>";

                $html.="<td><a href=javascript:GraficarPregunta($pregunta_id,'$total')>Graficar</a><td>";
                $html.="</tr>";
                $array_todos[]=$array_pregunta;         //formo array multi para estÃ¯Â¿Â½ndar todas las preguntas
            }
            $html.="<tr>";
            $html.="</tr>";
            $html.="</table>";
            $html.="<table align='center'><tr><td>";
            $todos=$this->compactarArray($array_todos);
            $modulos=$this->compactarArray($lista_modulos);
            $listaPregunta=$this->compactarArray($this->lista_preguntas_calif);
//            $html.="<a href=javascript:ventanaSecundaria('graficarPicos?array_serie=".$this->compactarArray($array_todos)."&array_categorias=".$this->compactarArray($lista_modulos)."&array_etiqueta_fila=".$this->compactarArray($this->lista_preguntas_calif)."&etiqueta_categoria=MOD.')>Graficar Todos</a>";
            //$html.="<td><a href=javascript:GraficarTodasLasPreguntas('$todos','$modulos','$listaPregunta','MOD')>Graficar Todos</a><td>";
            $html.="</tr></td></table>";
        } else
            $html.="No ha seleccionado algún módulo o servicio";
        echo $html;
    }

    /*
     * GrÃ¯Â¿Â½fico en picos
    */
    public function graficarPicosAction() {
        $this->setResponse('json');
        $array_categorias_aux=$this->getPostParam('array_categorias');    //categorias son los x's, es un valor definido
        $etiqueta_categoria=$this->getPostParam('etiqueta_categoria');
        $array_categorias = stripslashes($array_categorias_aux);
        $array_categorias = urldecode($array_categorias);
        $array_categorias = unserialize($array_categorias);

        $array_etiqueta_fila_aux=$this->getpostParam('array_etiqueta_fila');  //es la parte de abajo
        $array_etiqueta_fila = stripslashes($array_etiqueta_fila_aux);
        $array_etiqueta_fila = urldecode($array_etiqueta_fila);
        $array_etiqueta_fila = unserialize($array_etiqueta_fila);


        $array_serie_aux=$this->getpostParam('array_serie');              //matriz para series
        $array_serie = stripslashes($array_serie_aux);
        $array_serie = urldecode($array_serie);
        $array_serie = unserialize($array_serie);
        //$etiqueta_categoria="MOD. ";
        $encabezado= $this->encabezadoGraficoPicos($array_categorias, $etiqueta_categoria);
        //INCIO DE LA SERIE
        $html="";
        $html.="series: [";
        $arraySeries=Array();
        $arraynom=array();
        foreach ($array_serie as $indice1=> $valor) {
            $html.="{";
            foreach ($valor as $indice2 => $valor2) {
                //busca etiqueta de la fila
                foreach ($array_etiqueta_fila as $key=>$etiqueta) {
                    if ($key == $indice2) {
                        $nom_pregunta=$etiqueta;
                    }
                }
                $arraynom[]=$nom_pregunta;
                $html.="name: '".$nom_pregunta."',";
                $html.="data: [";
                //forma los valores por la fila
                $i=1;
                $data=array();
                foreach ($valor2 as $valor3) {
                    if (count($valor2)==$i) {
                        $html.=$valor3;
                        $data[]=$valor3;
                    }
                    else {
                        $html.=$valor3.",";
                        $data[]=$valor3;
                    }
                    $i++;
                }
                $arraySeries[]=array('name: '=>$nom_pregunta,'data'=>$data);
                $html.="]";
            }
            $html.="},";
        }
        $html.="]";
//        print_r($array_serie);
//        die();
        foreach ($array_serie as $indice1=> $valor) {

        }
        //FIN DE LA SERIE

        $pie=$this->pieGraficoPicos();
        $grafico= "$encabezado"."$html"."$pie";
//       echo $grafico;
//        $arra1=array('series'=>$arraySeries,'data'=>$data);
        $arrayTot=array('datos'=>$arraySeries,'categoria'=>$arraynom);
        return ($arrayTot);
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
        $html.="this.x +': '+ this.y +'Ã¯Â¿Â½C';";
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
    public function ejemploAction() {
        $this->setResponse("json");
        $html="";
//	$html.="<head>";
        $html.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        $html.="<title>Highcharts Example</title>";
//	 $html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='http://localhost/pruebas/Highcharts-2-1-6/js/highcharts.js'></script>";
//	$html.="<script type='text/javascript' src='http://localhost/pruebas/Highcharts-2-1-6/js/themes/gray.js></script>";
        $html.="<script type='text/javascript' src='http://localhost/pruebas/Highcharts-2-1-6/js/modules/exporting.js'></script>";
        $html.="<script type='text/javascript'>";
        $html.="var chart";
        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',	defaultSeriesType: 'line',
						marginRight: 130,
						marginBottom: 25
					},
					title: {
						text: 'Monthly Average Temperature',
						x: -20 //center
					},
					subtitle: {
						text: 'Source: WorldClimate.com',
						x: -20
					},
					xAxis: {
						categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
							'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
					},
					yAxis: {
						title: {
							text: 'Temperature (C)'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						formatter: function() {
				                return '<b>'+ this.series.name +'</b><br/>'+
								this.x +': '+ this.y +'C';
						}
					},
					legend: {
						layout: 'vertical',
						align: 'right',
						verticalAlign: 'top',
						x: -10,
						y: 100,
						borderWidth: 0
					},
					series: [{
						name: 'Tokyo',
						data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
					}, {
						name: 'New York',
						data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
					}, {
						name: 'Berlin',
						data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
					}, {
						name: 'London',
						data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
					}]
				});


			});

		</script>";

//	$html.="</head>";
//         $html."<form><fieldset>";
//        $html.="</fieldset></form>";
//        $html.="</div>";


        $datosturno= array("html"=>$html);
        return ($datosturno);
    }
    public function VerPorcentajeCalificadosAction() {
        $this->setResponse("json");
        $this->verCuadroCalificacionesAction();

    }
    public function GraficaBarras($titulo,$nombres,$total) {
        $fun=new Funciones();
        $strXML = "";
        $strXML = "<chart caption = 'Gráfico 1: $titulo' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
//        CALCULO EL TOTAL DE TURNOS ATENDIDOS Y ANULADOS
        $i=0;
        $totales=array();
        $lista_nombres=explode(",", $nombres);
        $lista_total=explode(",", $total);
        $i=0;
        $j=0;
        $x=0;
        $y=0;

        $calidad=array();
        foreach($lista_total as $key => $val) {
//                $linkModulo[$mod] = urlencode("\"javascript:detalleAnios('Modulo ".$nombres."', '".$total."');\"");
            $titulo=substr($titulo,0,7);
            if($titulo=="Calidad") {
                if(is_int($i/4)&&$i!=0) {

                    $strXML .= "<set />";
                    $strXML .= "<set />";
                    $nom=str_replace("MÃ³dulo", "Modulo", $lista_nombres[$j]);
                    $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";
                    $j++ ;
                }
                else {
                    $nom=str_replace("MÃ³dulo", "Modulo",$lista_nombres[$j]);
                    $j++;
                    $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";

                }
                $x+=15;
                $y=480;
                if(count($lista_total)<=20) $x=500;
            }
            else {
//                $nom=str_replace("BalcÃ³n", "Balcon",$lista_nombres[$key]);
                $nom=$fun->quitaTildes($lista_nombres[$key]);
                $strXML .= "<set label = '".$nom. "' value ='".$lista_total[$key]."'/>";
                $x=600;
                $y=480;
            }
            $i++;
        }

// Cerramos la etiqueta "chart".
        $strXML .= "</chart>";
// Por Ã?Â¯Ã?Â¿Ã?Â½ltimo imprimo el grÃ?Â¯Ã?Â¿Ã?Â½fico.
        $html= $this->renderChartHTML('../../swf_charts/Column3D.swf', '', $strXML,'maestro', $x, $y,false);
        $strXML="";
        return($html);
    }
    public function GraficadorColasAction() {
        $this->setResponse("json");
        $id_caja=$this->getPostParam('cajas');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        //$servicios=$this->getPostParam('chkservicios');
        $cajas=$id_caja;
        $lista=explode(",",$id_caja);
        // $lista_servicios = explode(',', $servicios);
        $total_turnos=0;
        $intTotal =0;
        $i=0;
        $intTotalModulos=array();
        $forma_condicion_servicios="";
        $mod=0;
        ////cabecera de la Tabla
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>TURNOS ATENDIDOS POR CAJAS DESDE: $desde HASTA: $hasta</text><br />";
        $htmltabla="<center><table border=1 align='center'><tr style='$fondo_titulo;font-size: 12px'>";
        $htmltabla.="<th> Cajas</th><th>Total Turnos Atendios</th></tr>";
        //INICIO BUSCAR MODULOS
        $lista1=array();
        $condicion="grupousuario.grupo_id=7";
        $query=new ActiveRecordJoin(
                array(
                        "entities"=>array("Caja","Usercaja","Usuario","Grupousuario"),
                        "fields"=>array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Usuario}.nombres"
                        ),
                        "conditions"=>$condicion,
                        "order"=>"{#Caja}.numero_caja"
                )
        );

        foreach ($query->getResultSet() as $result) {
            foreach($lista as $key=> $val) {
                if ($val==$result->getId()) {
                    $lista1[$key]="C".$result->getNumeroCaja()."-".$result->getNombres();
                    // $j++;
                }
            }
        }
//empieso a graficar

        $nombre=array();
        $total=0;
        $usuario="";
        if (!empty($id_caja)) {
            foreach ($lista as $key => $val) {
                $id_caja=$key;
                foreach($this->queryTotalCajas as $valor) {
                    //echo $result->getId();
                    if($valor['id']==$val) {
                        $id_caja=$valor['id'];
                        $num_modulo=$valor['numeroCaja'];
                        $usuario=$valor['NombreUsuario'];
                        if($valor['ColaAtendido']==1)$total++;
                    }
                }
                $intTotalModulos[$val]= $usuario;
                $i++;
            }
            $strXML = "";
            $linkAnio=array();
            $strXML = "<chart caption = 'Gráfico 1: Cajas' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='Cajas' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
//        CALCULO EL TOTAL DE TURNOS ATENDIDOS Y ANULADOS
            $i=0;
            $totales=array();
            $reportes = new reportes();
            foreach($lista as $mod => $val) {
                $tot_t_atendidos= $this->totalColasAtendidosPorFechas($val);
                $totales[$mod]=$tot_t_atendidos;
                $i++;
            }
            $i=0;
            $resultado=0;
            Foreach ($totales as $valor) {
                $resultado=$resultado+$valor;
            }
            foreach($lista1 as $key1=>$nom) {
//                        if ($key1==$val)
                $htmltabla.="<tr><td>".$nom."</td><td><center>".$totales[$key1]."</center></td></tr>";
                $i++;
            }
            $htmltabla.="<tr><td><center><b> Total </td><td><center><b>".$resultado."</center></td></tr>";
//            echo $htmltabla;
//            die();
            $total=$totales.implode($totales,',') ;
            $nom=$lista1.implode($lista1,',') ;
//                echo$total;

            $i=0;
//                print_r($totales);

            foreach($intTotalModulos as $mod => $val) {
                $linkModulo[$mod] = urlencode("\"javascript:detalleAnios('Modulo ".$cajas."', '".$total."');\"");
                $strXML .= "<set label = '".$lista1[$i]. "' value ='".$totales[$i]."' link = ".$linkModulo[$mod]." />";
                $i++;

            }

// Cerramos la etiqueta "chart".
            $strXML .= "</chart>";
// Por ÃƒÂ¯Ã‚Â¿Ã‚Â½ltimo imprimo el grÃƒÂ¯Ã‚Â¿Ã‚Â½fico.
            $html= $this->renderChartHTML('../../swf_charts/Column3D.swf', '', $strXML,'maestro', 450, 480,false);
            $strXML="";
        }
        else {
            $html="Chequee que tenga selecinado el Caja...!!!";
        }
        $htmltabla.="</table></center>";
        $datosturno= array("html"=>$html,"tabla"=>$htmltabla,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function Graficador1Action() {
        $this->setResponse("json");
        $id_caja=$this->getPostParam('modulos');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $servicios=$this->getPostParam('chkservicios');
        $cajas=$id_caja;
        $lista=explode(",",$id_caja);
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
        $htmltabla="<center><table border=1 align='center'><tr style='$fondo_titulo;font-size: 12px'>";
        $htmltabla.="<th> Módulos</th><th>Total Turnos Atendidos</th></tr>";
        //INICIO BUSCAR MODULOS
        $lista1=array();
        //empieso a graficar
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
        if (!empty($id_caja)and !empty($servicios)) {
            foreach ($lista as $key => $val) {
                $usuario=$this->arrayModulosNombres[$val];
                $lista_nom[$val]= $usuario;
            }
            $strXML = "";
            $linkAnio=array();
            $strXML = "<chart caption = 'Gráfico 1: Módulos' bgColor='#CDDEE5' baseFontSize='12' showValues='1' xAxisName='Módulos' formatNumberScale='0' thousandseparator='.' labelDisplay='ROTATE'>";
//        CALCULO EL TOTAL DE TURNOS ATENDIDOS Y ANULADOS
            $i=0;
            $totales=array();
            $reportes = new reportes();
            foreach($lista as $val) {
                $tot_t_atendidos= $this->array_total_modulos[$val];
                $totales[$val]=$tot_t_atendidos;
            }
            $i=0;
            $tot=0;
            foreach($lista_nom as $key1=>$nom) {
                $htmltabla.="<tr><td>".$nom."</td><td><center>".$totales[$key1]."</center></td></tr>";
                $tot+=$totales[$key1];
                $i++;
            }
            $htmltabla.="<tr><td aling='right'><center><b>Total</b></td><td><center><b>". $tot."</b></center></td></tr>";
            $total=$totales.implode($totales,',') ;
            $nom=$lista1.implode($lista_nom,',') ;

            foreach($this->array_total_modulos as $mod => $val) {
                $nom=str_replace("Módulo", "Modulo",$lista_nom[$mod]);
                // $linkModulo[$mod] = urlencode("\"javascript:detalleAnios('Modulo ".$cajas."', '".$total."');\"");
                $strXML .= "<set label = '".$nom. "' value ='".$val."' />";
                $i++;

            }

// Cerramos la etiqueta "chart".
            $strXML .= "</chart>";
// Por Ã¯Â¿Â½ltimo imprimo el grÃ¯Â¿Â½fico.
            $html= $this->renderChartHTML('../../swf_charts/Column3D.swf', '', $strXML,'maestro',600, 480,false);
            $strXML="";
        }
        else {
            $html="Chequee que tenga selecinado el Modulo y Servicio...!!!";
        }
        $htmltabla.="</table></center>";
        $datosturno= array("html"=>$html,"tabla"=>$htmltabla,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function GraficadorServicioBarrasAction() {
        $this->setResponse("json");
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
            $arreglo_caja=explode(",", $caja);
            $arreglo_servicio=explode(",",$chkservicios);
            $db = DbBase::rawConnect();
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $html="<center><table border=1 aling='center'><tr style='$fondo_titulo'>";
            //cabecera de la Tabla
            $html.="<th>Servicios</th><th>Total Turnos Atendido</th><th>Porcentaje</th></tr>";

            $total=array();
            $porcentaje=0;

            //            foreach($arreglo_caja as $caja_id) {

            $mi_array=array();
            $mi_arrayrepetidos=array();
            $array_series=array();
            $array_data=array();
            $nombres=array();
            $totales=array();
            $i=0;

            if(count($this->queryTotal)!=0) {
                foreach($arreglo_servicio as $servicio_id) {
                    $nombres[]=$this->arrayServicios[$servicio_id];
                    $totales[]=$this->array_total_servicios[$servicio_id];
                }
                $encontro=false;
            }
            else {
//                echo'No hay datos';
//                die();

            }
            $nombre="";
            $total=0;
            foreach($this->array_total_servicios as $key=> $val) {
                $total+= $val;
            }

            $array_d=array();
            $j=0;
            foreach($this->array_total_servicios as $key=> $val) {
                if ($total!=0) {
                    $porcentaje= round(($val*100)/$total,2);

                }
                else
                    $porcentaje=0;
                //$html.="['".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%"."', ".$porcentaje."],";
                $arr1=array("".$this->arrayServicios[$key]."-".$val['letra']." t., ".$val."",$porcentaje);
                $array_d[$j]=$arr1;
                $html.="<tr><td> ".$this->arrayServicios[$key]." </td><td><center>".$val."</td><td><center>".$porcentaje."% </td></tr>";
                $j++;
            }
            if($total!=0)
                $html.="<tr><td><b><center>Total</b></td><td><b><center>".$total."<b></td><td><b><center>".round(($total*100)/$total,2)."% </td></tr>";
            else
                $html.="<tr><td><b><center>Total</b></td><td><b><center>".$total."<b></td><td><b><center> 0% </td></tr>";
            $html.="</table>";
        }
        else {
            $html="Chequee que tenga selecionado caja y servicio";
            die();
        }
        $nombre=implode(',',$nombres);
        $htmlGrafica="";
        $tot=implode(',',$totales);
        if ($tipo=="Barras")
            $htmlGrafica=$this->GraficaBarras("Servicios",$nombre,$tot);
        else if ($tipo=="Pastel") {
            if($tot!="")
                $htmlGrafica=$this->Graficadorpie("Servicios",$nombres,$totales);
            else
                $htmlGrafica="NO HAY DATOS";
        }
        $arraydatos[0]=array('type'=>'pie','name'=>'Turnos A. Dia','data'=>$array_d);
        $array_g= array('datos'=>$arraydatos,'tabla'=>$html,'html'=>$titulo,'grafica'=>$htmlGrafica);
        return ( $array_g);
    }
    public function Graficador2Action() {
        $totales = $_POST['calcular'];
        $modulos=$_POST['moduloId'];
        $listaTotales=explode(',', $totales);
        $listaModulos=explode(',',$modulos);
        $strXML = "";
        $strXML = "<chart caption = 'Gráfico 2: Detalle ' bgColor='#CDDEE5' baseFontSize='12' >";
        $i=0;
        foreach($listaTotales as $key=>$val) {
            $strXML .= "<set label = 'modulos $listaModulos[$i]' value ='".$val."' />";
            $i++;
        }
        $strXML .= "</chart>";
        echo $this->renderChartHTML("../../swf_charts/Pie3D.swf", "",$strXML, "detalle", 500, 480, false);
    }
    public function Graficadorpie($titulo,$listanombres,$listaTotales ) {
        $strXML = "";
        $strXML = "<chart caption = 'Gráfico 2: $titulo 'bgColor='#CDDEE5' baseFontSize='12'formatNumberScale='0' thousandseparator='.'>";
        $i=0;
        $j=1;
        $x=0;
        $y=0;
        $titulo=substr($titulo,0,7);
        if($titulo=="Calidad") {
//            print_r($listaTotales);
//            die();
            foreach($listaTotales as $key=>$val) {
                $strXML .= "<set label = '".$listanombres[$key]. "' value ='".$listaTotales[$key]."'/>";
                $x+=10;
                $y=480;
                if(count($listaTotales)<=20) $x=500;
            }
        }
        else  if(count($listaTotales)==count($listanombres)) {
            foreach($listaTotales as $key=>$val) {
                $strXML .= "<set label = '$listanombres[$key] ' value ='".$listaTotales[$key]."' />";
            }
            $x=600;
            $y=480;
        }
        else return "No existe Datos suficientes";

        $strXML .= "</chart>";
        return $this->renderChartHTML("../../swf_charts/Pie3D.swf", "",$strXML, "detalle",$x,$y, false);
    }
    public function GraficadorPastelAction() {
        $this->setResponse("json");
        $id_caja=$this->getPostParam('modulos');
        $id_grupo=$this->getPostParam('grupos');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $servicios=$this->getPostParam('chkservicios');
        $lista_cajas=explode(",",$id_caja);
        $lista1=array();
        $lista_servicios=explode(",",$servicios);
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>TURNOS ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $forma_condicion_servicios="";
        if (!empty($id_caja)and !empty($servicios)) {
            for ($i=0;$i<count($lista_servicios);$i++) {
                if ($i==(count($lista_servicios)-1))
                    $forma_condicion_servicios.=$lista_servicios[$i];
                else
                    $forma_condicion_servicios.=$lista_servicios[$i].",";
            }
        }

        foreach($lista_cajas as $val) {
            $lista1[$val]=$this->arrayModulosNombres[$val];

        }

//calcula los Totales
        $i=0;
        $totales=array();
        $reportes = new reportes();

        foreach($lista_cajas as $mod => $val) {
            $tot_t_atendidos=$this->array_total_modulos[$val];
            $totales[$val]=$tot_t_atendidos;
            $i++;
        }
        $resultado=0;
        Foreach ($totales as $valor) {
            $resultado=$resultado+$valor;
        }
        if ($resultado!=0)
            $html=$this->Graficadorpie("Módulos",$lista1,$totales);
        else
            $html="NO HAY DATOS!!";
        $datosturno= array("html"=>$html,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function GraficadorPastelColasAction() {
        $this->setResponse("json");
        $id_caja=$this->getPostParam('cajas');
        $id_grupo=$this->getPostParam('grupos');
        $desde=$this->getPostParam('desde');
        $hasta=$this->getPostParam('hasta');
        $servicios=$this->getPostParam('chkservicios');
        $lista_cajas=explode(",",$id_caja);
        $lista1=array();
        $lista_servicios=explode(",",$servicios);
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>TURNOS ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $forma_condicion_servicios="";
        foreach($lista_cajas as $val) {
            foreach ($this->queryTotalCajas as $result) {

                if ($val==$result['id']) {
                    $lista1[$val]=$result['NombreUsuario'];
                }
            }
        }
//calcula los Totales
        $i=0;
        $totales=array();
        $reportes = new reportes();

        foreach($lista_cajas as $mod => $val) {
            $tot_t_atendidos=$this->totalColasAtendidosPorFechas($val);
            $totales[$val]=$tot_t_atendidos;
            $i++;
        }
        $resultado=0;
        Foreach ($totales as $valor) {
            $resultado=$resultado+$valor;
        }
//        print_r($this->arraycajasNombres);
//        print_r($totales);
        if ($resultado!=0)
            $html=$this->Graficadorpie("Cajas",$this->arraycajasNombres,$totales);
        else
            $html="NO HAY DATOS!!";
        $datosturno= array("html"=>$html,'titulo'=>$titulo);
        return ($datosturno);
    }
    public function GraficadorPastelServicioAction() {
        $this->setResponse("json");
        $caja=$this->getPostParam('modulos');
        $desde =$this->getPostParam('desde');
        $hasta =$this->getPostParam('hasta');
        $chkservicios =$this->getPostParam('chkservicios');
        $ar1= array();
        $ar= array();
        $html="";
        $titulo="";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        if(($chkservicios!="")&&($caja!="")) {

            $titulo="<text style='$tituloEstilo'>TURNOS ATENDIDOS POR SERVICIO DESDE:$desde HASTA: $hasta </text>";
            $arreglo_caja=explode(",", $caja);
            $arreglo_servicio=explode(",",$chkservicios);
            $db = DbBase::rawConnect();
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $html="<center><table border=1 width='100%'><tr style='$fondo_titulo'>";
            //cabecera de la Tabla
            $html.="<th>Servicios</th><th>Total</th><th>Porcentaje</th></tr>";

            $total=array();
//        foreach($arreglo_caja as $servicio_id) {
//            echo "dd".$servicio_id;
//            print_r($arreglo_caja);
//        }

            $porcentaje=0;

            //            foreach($arreglo_caja as $caja_id) {

            $mi_array=array();
            $array_series=array();
            $array_data=array();
            $nombres=array();
            $totales=array();
            $i=0;
            $result = $db->query("SELECT s.id, nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id AND atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta' AND s.id IN ($chkservicios) AND t.caja_id IN ($caja) GROUP BY servicio_id;");
            while($row = $db->fetchArray($result)) {
                $array1= array ('nombre'=>$row['nombre'],'letra'=>$row['letra'],'total'=>$row['total']);
                $nombres[$i]=$row['nombre'];
                $totales[$i]=$row['total'];
                $mi_array[$i]= $array1;

                $i++;
            }


//               print_r($mi_array);
//              die();
            $nombre="";
            $total=0;
            //$html="";
            foreach($mi_array as $key=> $val) {
                $total+= $val['total'];
            }


            // echo $total;
            $array_d=array();
            $j=0;
            foreach($mi_array as $key=> $val) {
                if ($total!=0) {
                    $porcentaje= round(($val['total']*100)/$total,2);

                }
                else
                    $porcentaje=0;
                //$html.="['".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%"."', ".$porcentaje."],";
                $arr1=array("".$val['nombre']."-".$val['letra']." t., ".$val['total']."",$porcentaje);
                $array_d[$j]=$arr1;
                $html.="<tr><td> Servicio:".$val['nombre']." </td><td>".$val['total']."</td><td>".$porcentaje."% </td></tr>";
                $j++;
            }
            $html.="</table>";
            //$array_data[$key]=array("".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%".""=>$porcentaje);

            //echo $html;
        }
        else {
            $html="Chequee que tenga selecionado caja y servicio";
            die();
        }
//        $nombre=implode(',',$nombres);
        $tot=implode(',',$totales);
        if($tot!="")
            $htmlGrafica=$this->Graficadorpie("Servicios",$nombres,$totales);
        else
            $htmlGrafica="NO HAY DATOS";
        $arraydatos[0]=array('type'=>'pie','name'=>'Turnos A. Dia','data'=>$array_d);
        $array_g= array('datos'=>$arraydatos,'tabla'=>$html,'html'=>$titulo,'graficapastel'=>$htmlGrafica);
        return ( $array_g);
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
    public function verGraficoHorasAction() {
        $this->setResponse("json");
        $id_modulo      =$this->getPostParam('id_modulo');
        $dia            =$this->getPostParam('dia');
        $ttat           =$this->getPostParam('tot_t_atendidos_x_dia');
        $ttan           =$this->getPostParam('tot_t_anulados_x_dia');
        $dt             =$this->getPostParam('duracion_total');
        $pa             =$this->getPostParam('duracion_promedio');
        $desde          =$this->getPostParam('desde');
        $hasta          =$this->getPostParam('hasta');
        $array          =$this->getPostParam('id_servicio');
        $array_servicios= stripslashes($array);
        $array_servicios = urldecode($array_servicios);
        $array_servicios = unserialize($array_servicios);
        $forma_condicion_servicios="";
        $titulo_dia="";

        for ($i=0;$i<count($array_servicios);$i++) {
            if ($i==(count($array_servicios)-1))
                $forma_condicion_servicios.=$array_servicios[$i];
            else
                $forma_condicion_servicios.=$array_servicios[$i].",";
        }
        $forma_duracion =$this->getPostParam('forma_duracion');

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
        $reportes = new Reportes();
        $fun= new Funciones();

        $html="";
        $t="";
        $m="";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $tituloGrafica="<center><text style='$tituloEstilo'>Reporte de Turnos por Horas del Dia ". $titulo_dia."</text></center>";
        $m.="<table align='left' border=0>";
        $m.="<tr><td><b>Modulo:</b></td><td align='right'>$num_modulo</td></tr>";
        $m.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $m.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        $m.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        $m.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        $m.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $m.="</table><br>";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_horas= array('8:00-9:00','9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00','18:00-19:00');
        $array_encabezado_hora=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'DuraciÃƒÂ³n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de AtenciÃƒÂ³n')
        );
        $m.="<table align='center'>";
        $cont_encabezado=count($array_encabezado_hora);
        $m.="<tr>";
        foreach ($array_horas as $hora) {
            $m.="<th colspan=$cont_encabezado style='$fondo_titulo'>$hora</th>";
        }
        $m.="</tr>";
        $m.="<tr>";
        foreach ($array_horas as $hora) {
            foreach ($array_encabezado_hora as $valor)
                $m.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $m.="</tr>";

        $array_tot_t_hora=array();
        $array_hora_ini= array('08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00');
        $array_hora_fin= array('08:59:59','09:59:59','10:59:59','11:59:59','12:59:59','13:59:59','14:59:59','15:59:59','16:59:59','17:59:59','18:59:59');
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
            $m.="<td align='right' style='background-color:#FEE1BA;'>".$tot_t_atendidos_x_hora."</td>";
            $m.="<td align='right' style='background-color:#e5f1f4;'>".$tot_t_anulados_x_hora."</td>";
            $m.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_total."</td>";
            $m.="<td align='right' style='background-color:#e5f1f4;'>".$duracion_promedio."</td>";
            $array_tot_t_hora[$indice]=$tot_t_atendidos_x_hora;
        }
        $m.="</tr>";
        $m.="</table>";
        $forma_array[$id_modulo]=$array_tot_t_hora;

        //$fun= new Funciones();
        //$html.= $fun->encabezadoHighcharts("Title");


        $ar1= array();



        $cont=0;
        foreach ($forma_array as $indice1=> $valor) {
            $array_data= array();
            foreach ($valor as $indice2 => $valor2) {
                $array_data[$indice2]=$valor2+0;
            }
            $ar1[$cont]=array('name'=>'Turnos A. Dia','data'=>$array_data);
            $cont+=1;
        }
        $array_g= array('v1'=>$ar1,'v2'=>$m,'titulo'=>$tituloGrafica);
        return ($array_g);
    }
    public function verGraficoHorasColasAction() {

        $this->setResponse("json");
        $id_caja      =$this->getPostParam('id_caja');
        $dia            =$this->getPostParam('dia');
        $ttat           =$this->getPostParam('tot_t_atendidos_x_dia');
        $ttan           =$this->getPostParam('tot_t_anulados_x_dia');
        $dt             =$this->getPostParam('duracion_total');
        $pa             =$this->getPostParam('duracion_promedio');
        $desde          =$this->getPostParam('desde');
        $hasta          =$this->getPostParam('hasta');
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
//        $array          =$this->getPostParam('id_servicio');

        $forma_condicion_servicios="";
        $titulo_dia="";
//        for ($i=0;$i<count($array_servicios);$i++) {
//            if ($i==(count($array_servicios)-1))
//                $forma_condicion_servicios.=$array_servicios[$i];
//            else
//                $forma_condicion_servicios.=$array_servicios[$i].",";
//        }
        $forma_duracion =$this->getPostParam('forma_duracion');

        $dias_semana_es= array(1=>'Lunes',2=>'Martes',3=>'Miercoles',4=>'Jueves',5=>'Viernes');
        $dias_semana_en= array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday');
        foreach ($dias_semana_en as $key=>$dias)
            if ($dias==$dia)
                $titulo_dia=$dias_semana_es[$key];

        $condicion="grupousuario.grupo_id=7 AND caja.id=$id_caja";
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
        $reportes = new Reportes();
        $fun= new Funciones();

        $html="";
        $t="";
        $m="";
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $tituloGrafica="<center><text style='$tituloEstilo'>Reporte de Turnos por Horas del Dia ". $titulo_dia."</text></center>";
        $m.="<table align='left' border=0>";
        $m.="<tr><td><b>Modulo:</b></td><td align='right'>$num_modulo</td></tr>";
        $m.="<tr><td><b>Usuario:</b></td><td align='right'>$usuario</td></tr>";
        $m.="<tr><td><b>Total Turnos Atendidos:</b></td><td align='right'>$ttat</td></tr>";
        $m.="<tr><td><b>Total Turnos Anulados:</b></td><td align='right'>$ttan</td></tr>";
        $m.="<tr><td><b>Duracion Total:</b></td><td align='right'>$dt</td></tr>";
        $m.="<tr><td><b>Promedio de Atencion:</b></td><td align='right'>$pa</td></tr>";
        $m.="</table><br>";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $array_horas= array('8:00-9:00','9:00-10:00','10:00-11:00','11:00-12:00','12:00-13:00','13:00-14:00','14:00-15:00','15:00-16:00','16:00-17:00','17:00-18:00','18:00-19:00');
        $array_encabezado_hora=array(
                'TA'=>array('etiqueta'=>'TA','titulo'=>'Turnos Atendidos'),
                'Ta'=>array('etiqueta'=>'Ta','titulo'=>'Turnos Anulados'),
                'DT'=>array('etiqueta'=>'DT','titulo'=>'DuraciÃƒÂ³n Total'),
                'PA'=>array('etiqueta'=>'PA','titulo'=>'Promedio de AtenciÃƒÂ³n')
        );
        $m.="<table align='center'>";
        $cont_encabezado=count($array_encabezado_hora);
        $m.="<tr>";
        foreach ($array_horas as $hora) {
            $m.="<th colspan=$cont_encabezado style='$fondo_titulo'>$hora</th>";
        }
        $m.="</tr>";
        $m.="<tr>";
        foreach ($array_horas as $hora) {
            foreach ($array_encabezado_hora as $valor)
                $m.="<th style='$fondo_titulo' title='$valor[titulo]'>$valor[etiqueta]</th>";
        }
        $m.="</tr>";

        $array_tot_t_hora=array();
        $array_hora_ini= array('08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00');
        $array_hora_fin= array('08:59:59','09:59:59','10:59:59','11:59:59','12:59:59','13:59:59','14:59:59','15:59:59','16:59:59','17:59:59','18:59:59');
        foreach ($array_hora_ini as $indice=> $hora) {
            $hora_ini= $array_hora_ini[$indice];
            $hora_fin= $array_hora_fin[$indice];
            $duracion_total=0;
            $duracion_promedio=0;
            $tot_t_atendidos_x_hora= $reportes->totalColasAtendidosPorHora($id_caja, $dia, $desde, $hasta, $hora_ini,$hora_fin);
            // $tot_t_anulados_x_hora= $reportes->totalTurnosAnuladosPorHora($id_modulo, $dia, $desde, $hasta, $hora_ini,$hora_fin);
            list($cont_reg,$total_segundos) = $reportes->duracionAtencionColasHora($id_caja, $dia, $desde, $hasta,$hora_ini,$hora_fin);
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
        $m.="</tr>";
        $m.="</table>";
        $forma_array[$id_caja]=$array_tot_t_hora;

        //$fun= new Funciones();
        //$html.= $fun->encabezadoHighcharts("Title");


        $ar1= array();



        $cont=0;
        foreach ($forma_array as $indice1=> $valor) {
            $array_data= array();
            foreach ($valor as $indice2 => $valor2) {
                $array_data[$indice2]=$valor2+0;
            }
            $ar1[$cont]=array('name'=>'Turnos A. Dia','data'=>$array_data);
            $cont+=1;
        }
        $array_g= array('v1'=>$ar1,'v2'=>$m,'titulo'=>$tituloGrafica);
        return ($array_g);
    }
    public function GraficadorServicioAction() {
        $this->setResponse("json");
        $caja=$this->getPostParam('caja');
        $desde =$this->getPostParam('desde');
        $hasta =$this->getPostParam('hasta');
        $chkservicios =$this->getPostParam('chkservicios');
        $ar1= array();
        $ar= array();
        $html="";
        $titulo="";
        if(($chkservicios!="")&&($caja!="")) {
            $titulo="TURNOS ATENDIDOS POR SERVICIO DESDE:$desde HASTA: $hasta";
            $arreglo_caja=explode(",", $caja);
            $arreglo_servicio=explode(",",$chkservicios);
            $db = DbBase::rawConnect();

            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $html="<center><table border=1 width='450px'><tr style='$fondo_titulo'>";
            //cabecera de la Tabla
            $html.="<th>Servicios</th><th>Total</th><th>Porcentaje</th></tr>";

            $total=array();
//        foreach($arreglo_caja as $servicio_id) {
//            echo "dd".$servicio_id;
//            print_r($arreglo_caja);
//        }

            $porcentaje=0;

            //            foreach($arreglo_caja as $caja_id) {

            $mi_array=array();
            $array_series=array();
            $array_data=array();
            $i=0;
            $result = $db->query("SELECT s.id, nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id AND atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta' AND s.id IN ($chkservicios) AND t.caja_id IN ($caja) GROUP BY servicio_id;");
            while($row = $db->fetchArray($result)) {
                $array1= array ('nombre'=>$row['nombre'],'letra'=>$row['letra'],'total'=>$row['total']);
                $mi_array[$i]= $array1;

                $i++;
            }


//               print_r($mi_array);
//              die();
            $nombre="";
            $total=0;
            //$html="";
            foreach($mi_array as $key=> $val) {
                $total+= $val['total'];
            }


            // echo $total;
            $array_d=array();
            $j=0;
            foreach($mi_array as $key=> $val) {
                if ($total!=0) {
                    $porcentaje= round(($val['total']*100)/$total,2);

                }
                else
                    $porcentaje=0;
                //$html.="['".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%"."', ".$porcentaje."],";
                $arr1=array("".$val['nombre']."-".$val['letra']." t., ".$val['total']."",$porcentaje);
                $array_d[$j]=$arr1;
                $html.="<tr><td> Servicio:".$val['nombre']." </td><td>".$val['total']."</td><td>".$porcentaje."% </td></tr>";
                $j++;
            }
            $html.="</table>";
            //$array_data[$key]=array("".$val['nombre']."-".$val['letra']." t., ".$porcentaje."%".""=>$porcentaje);

            //echo $html;
        }
        else {
            $html="Chequee que tenga selecionado caja y servicio";
            die();
        }
        $arraydatos[0]=array('type'=>'pie','name'=>'Turnos A. Dia','data'=>$array_d);

        $array_g= array('datos'=>$arraydatos,'tabla'=>$html,'html'=>$titulo);
        return ( $array_g);
    }
    public function GraficaPreguntaAction() {
        $this->setResponse("json");
        $modulos=$this->getPostParam('chkmodulos');
        $id_pregunta =$this->getPostParam('pregunta');
        $chkservicios =$this->getPostParam('chkservicios');
        $tipo=$this->getPostParam('tipo');
        $totales=$this->getPostParam('totales');
        $lista_modulos=explode(',',$modulos);
        $lista_totales=explode(',',$totales);
        $arrayNombres=array();
        $con=0;
        $j=0;
        if ($modulos!="" ||  $chkservicios!="") {
            foreach($lista_modulos as $id) {
                foreach($this->arrayModulosNombres as $key=>$nombre) {
                    if($key==$id) {
                        $arrayNombres[$j]=$nombre;
                        $j++;
                    }
                }
            }
//        print_r($arrayNombres);
//        echo $modulos."..>".$totales;
//        die();
            if (count($arrayNombres)!=0) {
                $modulos=implode($arrayNombres,',');
                if ($tipo=="Barras") {
                    $arraydatos[0]=$this->graficaBarras('Calificacion',$modulos,$totales);
                    $array_g= array('datos'=>$arraydatos);
                }else if ($tipo=="Pastel") {
                    $arraydatos[0]=$this->Graficadorpie('Calificacion',$lista_modulos,$lista_totales);
                    $array_g= array('datos'=>$arraydatos);
                }else $array_g= array('datos'=>"No existe Datos");
            }
            else $array_g=array('datos'=>'Seleccione fecha diferentes!!!');

        }
        else $array_g=array('datos'=>'Seleccione moldulos o servicios!!!');
        return ( $array_g);
    }
    public function  CalidadServicioAction() {
        $this->setResponse("json");
        $modulos=$this->getPostParam('modulos');
        $desde=$this->getPostParam('desde');
        $hasta =$this->getPostParam('hasta');
        $tipo=$this->getPostParam('graficar');
        $lista_modulos=explode(",", $modulos);
        $series=array();
        $tituloEstilo="font-family: Lucida Grande,Lucida Sans Unicode,Verdana,Arial,Helvetica,sans-serif; font-size: 16px; color: #3E576F; text-anchor=middle zIndex=1";
        $titulo="<center><text style='$tituloEstilo'>CALIDAD DE SERVICIO ATENDIDOS POR MODULOS DESDE: $desde HASTA: $hasta</text><br />";
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        //INICIO BUSCAR MODULOS
        $lista=array();
        $lista_nombres_calidad=array();

        $j=0;
        foreach($lista_modulos as $val) {
            $lista[$j]=$this->arrayModulosNombres[$val];
            $j++;
        }
        //INICIO BUSCAR CAJAS
        $htmltabla="<center><table border=1 aling='center'><tr style='$fondo_titulo'>";
        $htmltabla.="<th>Modulos Atendidos</th><th>Excelente</th><th>Muy Bueno</th><th> Bueno</th><th> Regular</th></tr>";// Insuficiente</th></tr>";
        $lista_datos=Array();
        $lista_datos_suma=Array();
        $i=0;
        $j=0;
        $k=0;
        $lista_datos_suma[0]=0;
        $lista_datos_suma[1]=0;
        $lista_datos_suma[2]=0;
        $lista_datos_suma[3]=0;
        foreach ($this->calidad_servicio_modulo as $key=>$Resultado) {
            $ex[$key]  = $Resultado['excelente']; // columna de excelente
            $bue[$key] = $Resultado['muybueno']; //columna de bueno
            $reg[$key] = $Resultado['bueno']; //columna de regular
            $mal[$key] = $Resultado['regular']; //columna de malo
            $htmltabla.="<tr><td>".$lista[$j]."</td>";
            $htmltabla.="<td>".$Resultado['excelente']."</td><td>".$Resultado['muybueno']."</td><td>".$Resultado['bueno']."</td><td>".$Resultado['regular']."</td></tr>";//.$fila[5]."</td></tr>";
            $lista_datos[$i++]=$Resultado['excelente'];
            $lista_datos_suma[0]+=$Resultado['excelente'];
            $lista_nombres_calidad[$i]=$lista[$j]." Excelente";
            $lista_datos[$i++]=$Resultado['muybueno'];
            $lista_datos_suma[1]+=$Resultado['muybueno'];
            $lista_nombres_calidad[$i]=$lista[$j]." Muy Bueno";
            $lista_datos[$i++]=$Resultado['bueno'];
            $lista_datos_suma[2]+=$Resultado['bueno'];
            $lista_nombres_calidad[$i]=$lista[$j]." Bueno";
            $lista_datos[$i++]=$Resultado['regular'];
            $lista_datos_suma[3]+=$Resultado['regular'];
            $lista_nombres_calidad[$i]=$lista[$j]." Regular";
            $j++;
        }
//            print_r($lista_nombres_calidad );
//            die();
        $htmltabla.="</table></center>";
        //Graficar modulos
        $nom=implode(',', $lista_nombres_calidad);
        $tot=implode(',', $lista_datos);
        if($tipo=="barras") {
            $graficaCalidad=$this->GraficaBarras("Calidad de Atención",$nom,$tot);
        }else {
            $resultado=0;
            Foreach ($lista_datos as $valor) {
                $resultado=$resultado+$valor;
            }
            if ($resultado!=0) {
                $lista_nombres=array('Excelente','MuyBueno','Bueno','Regular');
//                print_r($lista_nombres);
//                print_r($lista_datos_suma);
//                die();
                $graficaCalidad=$this->Graficadorpie("Calidad Total de Atención",$lista_nombres,$lista_datos_suma);
            }
            else $graficaCalidad="NO HAY DATOS";

        }
        $array_g= array('text'=>$titulo,'categoria'=> $lista,'serie'=>$series,'tabla'=>$htmltabla,'graficaCalidad'=>$graficaCalidad);
        return ( $array_g);
    }
    public $arrayModulosNombres=array();
    public function GraficaChkModulosAction() {
        $this->setResponse("json");
        $Areas=$this->getPostParam("areas");
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
                natsort($mi_array);
                $this->arrayModulosNombres[$row['caja_id']]="M".$row['numero_caja']."-".$row['nombres'];
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
            natsort($mi_array);
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
    public $arraycajasNombres=array();
    public function GraficaChkCajasAction() {
        $this->setResponse("json");
        $Areas=$this->getPostParam("areas");
        $listaAreas=explode(',', $Areas);
        $i=0;
        $this->arraycajasNombres=array();
        $condicion="grupousuario.grupo_id=7";

        $mi_array=array();
        if($Areas!="") {
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT * FROM caja c,usercaja uc, usuario u,grupousuario gu WHERE uc.caja_id=c.id AND gu.usuario_id=u.id AND gu.grupo_id=7 AND uc.usuario_id=u.id AND ubicacion_id IN(".$Areas.")");
            while($row = $db->fetchArray($result)) {
                $array1= array ('id'=>$row['caja_id'],'nombre'=>$row['descripcion']);
                $mi_array[$row['caja_id']]= "M".$row['numero_caja']."-".$row['nombres'];
                $this->arraycajasNombres[$row['caja_id']]="M".$row['numero_caja']."-".$row['nombres'];
                $i++;
            }
            $html="";
            $html.="<table width='100%' >";
            $html.="<tr><td class='empty'>";
            $col=0;
            $i=0;
            $array_valores=array();
            natsort($mi_array);
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
            $html.=" </td> </tr> </table>";
        }
        else
            $html="Seleccione una Area...para ver las cajas!!!";
        $arraycks=array('cks'=>$html);
        return($arraycks);
    }
    public function GraficarChkGrupoAction() {
        $this->setResponse("json");
        $cajas=$this->getPostParam("modulos");
        $listacajas=explode(',', $cajas);
        $html="";
        if($cajas!="") {
            $db = DbBase::rawConnect();
            $areas=array();
            $nombres=array();
            $array_servicios=array();
            $result = $db->query("SELECT gs.id, nombre_grupo_servicio FROM gruposervicio gs,servicio s,serviciocaja sc  WHERE s.id=sc.servicio_id AND gs.id=gruposervicio_id  AND sc.caja_id IN ($cajas) GROUP BY gs.id ");
            while($row = $db->fetchArray($result)) {
                $areas[$row['id']]= $row['nombre_grupo_servicio'];
                $nombres[$row['id']]=$row['nombre_grupo_servicio'];
            }
            natsort($nombres);
            foreach($nombres as $id=>$nom)
                $array_servicios[]=Tag::checkboxField("chkgruposervicio[]", "value: $id","checked: ","onclick: chekGrupo()").$nom."&nbsp;&nbsp;";
            $opcion= $nombres;
            $html.="<table>";
            $cont_mod=count($array_servicios);
            $cont_filas=ceil($cont_mod/3);
            $x=$cont_filas*3;
            $y=$x-$cont_mod;
            for ($z=1;$z<=$y;$z++)
                $array_servicios[]="";
            $cont_key=0;
            for ($f=1; $f<=$cont_filas; $f++) { //filas
                $html.= "<tr>";
                for ($c=1;$c<=3;$c++) { //columnas
                    $html.= "<td>".$array_servicios[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html.= "</tr>";
            }
            $html.= "</table>";
            $arraycks=array('cks'=>$html);
        }else
            $html.="Seleccione Cajas... para ver Servicios";
        $arraycks1=array('cksgrupo'=>$html);
        return($arraycks1);
    }
    public $arrayServicios=array();
    public function GraficarChkServiciosAction() {
        $this->setResponse("json");
        $grupos=$this->getPostParam("grupos_servicios");
        $i=0;
        $mi_array=array();
        $this->arrayServicios=array();
        $html="";
        if($grupos!="") {
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT * FROM servicio WHERE gruposervicio_id IN(".$grupos.")");
            while($row = $db->fetchArray($result)) {
                $array1= array ('id'=>$row['id'],'nombre'=>$row['nombre']);
                $mi_array[$row['id']]= $row['nombre'];
                $this->arrayServicios[$row['id']]=$row['nombre'];
                $i++;
            }
//            $html.="<fieldset class='ui-corner-all ui-widget-content'> <legend><b>Servicios</b></legend><br/>";
            $col=0;
            $array_valores=array();
            natsort($mi_array);
            foreach($mi_array as $key=> $val) {
                $array_valores[]=Tag::checkboxField("chkservicios[]", "value: $key","checked: ","onclick: chekservicio()").$val."&nbsp;&nbsp;";
            }
            $html.= "<table>";
            $cont_mod=count($mi_array);
            $cont_filas=ceil($cont_mod/2);
            $cont_key=0;

            if ($cont_mod<=2) {
                $html.= "<tr>";
                for ($c=1;$c<=$cont_mod;$c++) {
                    $html.= "<td>".$array_valores[$cont_key]."</td>";
                    $cont_key+=1;
                }
                $html.= "</tr>";
            } else {
                $x=$cont_filas*4;   //numero de celdas
                $y=$x-$cont_mod;    //numero de celdas vacias
                for ($z=1;$z<=$y;$z++)
                    $array_valores[]="";
                for ($f=1; $f<=$cont_filas; $f++) {
                    $html.="<tr>";
                    for ($c=1;$c<=2;$c++) {
                        $html.= "<td>".$array_valores[$cont_key]."</td>";
                        $cont_key+=1;
                    }
                    $html.="</tr>";
                }
            }
            $html.="</table>";

//                                             $html.=" </fieldset>";
        }
        else {
            $html.="Lo sentimos no existe servicios!!!";
        }

        $arraycks=array('chkservicio'=>$html);
        return($arraycks);
    }
    public $queryTotal=array(); //para un query global
    public $array_total_modulos=array();//para llenar los modulos totales
    public $array_total_servicios=array();//para lenar los totales de los servicios
    public $array_turnero=array();//para iniciliza el reporte turnero
    public $calidad_servicio_modulo=array();//ilinicaliza el reporte calidad
    public $array_cuadro    =array();//para iniciliza el reporte cuadro por semanas
    public $array_datos_modulo= array();
    public function LLenaQueryAction() {
        $this->setResponse("json");
        $fun= new Funciones();
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
        $array_datos_modulo_aux= array();
        $html="";
        if ($modulos!="" && $servicios!="") {
            $condicion="{#Caja}.id IN($modulos) " ;
            $sql= new ActiveRecordJoin (array(
                            "entities" => array("Caja", "Usuario","Usercaja"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres"),
                            "conditions" => $condicion,
                    //"order"=>"{#Turnos}.hora_inicio_atencion"
            ));
            foreach ($sql->getResultSet() as $result) {
                $modulo_id= $result->getId();
                $array_datos_modulo_aux[]=array('modulo_id'=>$result->getId(),'usuario'=>$result->getNombres(),'numero'=>$result->getNumeroCaja());
            }

            $this->array_datos_modulo= $fun->ordenarArrayMultidimensional($array_datos_modulo_aux,'numero',false);
            $condicion="({#Caja}.id IN($modulos) OR {#Caja}.id IS NULL) AND fecha_emision BETWEEN '$desde' AND '$hasta' AND {#Servicio}.id IN ($servicios) AND hora_inicio_atencion>='$forma_duracion'" ;
            $sql= new ActiveRecordJoin (array(
                            "entities" => array("Caja", "Turnos", "Servicio", "Usercaja", "Usuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    //"{#Caja}.descripcion",
                                    "{#Usuario}.nombres",
                                    "{#Servicio}.id  as id_servicio",
                                    "{#Servicio}.letra",
                                    "{#Servicio}.nombre",
                                    "{#Turnos}.numero",
                                    "{#Turnos}.fecha_emision",
                                    "{#Turnos}.hora_emision",
                                    "{#Turnos}.atendido",
                                    "{#Turnos}.por_atender",
                                    "{#Turnos}.rechazado",
                                    "{#Turnos}.fecha_inicio_atencion",
                                    "{#Turnos}.hora_inicio_atencion",
                                    "{#Turnos}.fecha_fin_atencion",
                                    "{#Turnos}.hora_fin_atencion",
                                    "{#Turnos}.duracion",
                                    "{#Turnos}.calificacion"),
                            "conditions" => $condicion,
                            "order"=>"{#Turnos}.hora_inicio_atencion"
            ));
            $cont=0;
            $a="";
            $this->queryTotal=array();
            $this->array_total_modulos=array();
            $this->array_total_servicios=array();
            $this->array_turnero=array();
            $this->calidad_servicio_modulo=array();
            $array_turnos_modulos=array();
            $fun= new Funciones();

            foreach($lista_modulos as $modulo_id) {
                $this->array_total_modulos[$modulo_id]=0;
                $this->calidad_servicio_modulo[$modulo_id]=array ('excelente'=>0,'muybueno'=>0,'bueno'=>0,'regular'=>0);
            }
            $this->array_cuadro =array();   //ensero el array para cuadro
            $this->array_turnero=array();
            $s = strtotime($hasta)-strtotime($desde);
            $numero_dias = intval($s/86400) + 1;      //numero de dias
            $subtotal_turnos_emitidos=array();
            $subtotal_turnos_atendidos=array();
            $subtotal_turnos_anulados=array();
            $subtotal_turnos_no_timbrados=array();
            foreach($lista_servicios as $servicio_id) {
                $this->array_total_servicios[$servicio_id]=0;
            }

            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
            for ($i=1;$i<=$numero_dias;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day"));
                $separa = explode('-',$fecha);
                $mes = $separa[1];
                $dia = $separa[2];
                $ano = $separa[0];
                $dia= $fun->dia_semana($dia, $mes, $ano);
                foreach($lista_servicios as $servicio_id) {
                    $array_servicio[$servicio_id]= array ('dia'=>$dia,'t_emitidos'=>0,'t_atendidos'=>0,'t_anulados'=>0,'no_timbrados'=>0);
                }
                $this->array_turnero[$fecha]=$array_servicio;
                //INICIO INICIALIZAR ARRAY_CUADRO
                foreach($lista_modulos as $modulo_id) {
                    $array_modulo[$modulo_id]= array ('dia'=>$dia,'t_atendidos'=>0,'t_anulados'=>0,'duracion_t'=>0,'promedio'=>0,'promedio_ll'=>0);
                }
                $this->array_cuadro[$fecha]=$array_modulo;
                //FIN INICIALIZAR ARRAY_CUADRO
            }
            $rep=new Reportes();
            foreach($sql->getResultSet() as $result) {
                $modulo_id= $result->getId();
                $servicio_id=$result->getIdServicio();
                if(($result->getAtendido()==1)&&($result->getRechazado()==0)) {
                    $this->array_total_modulos[$modulo_id]+=1;
                    $this->array_total_servicios[$servicio_id]+=1;

                    //if (!empty($result->getCalificacion())) {
                    if($result->getCalificacion()=="Excelente")
                        $this->calidad_servicio_modulo[$modulo_id]['excelente']+=1;
                    if($result->getCalificacion()=="MuyBueno")
                        $this->calidad_servicio_modulo[$modulo_id]['muybueno']+=1;
                    if($result->getCalificacion()=="Bueno")
                        $this->calidad_servicio_modulo[$modulo_id]['bueno']+=1;
                    if($result->getCalificacion()=="Regular")
                        $this->calidad_servicio_modulo[$modulo_id]['regular']+=1;

                    //}
                }

                //INICIO REPORTE TURNERO
                $key_fecha= $result->getFechaEmision();
                $key_servicio_id= $result->getIdServicio();
                $duracion_en_segundos= $fun->totalSegundos($result->getDuracion());     //devuelve en segundos
                if(($result->getAtendido()==1)&&($result->getRechazado()==0)) {
                    //array_turnero
                    $this->array_turnero[$key_fecha][$key_servicio_id]['t_atendidos']+= 1;
                    //array_cuadro
                    $this->array_cuadro[$key_fecha][$modulo_id]['t_atendidos']+= 1;
                    $this->array_cuadro[$key_fecha][$modulo_id]['duracion_t']+= $duracion_en_segundos;
                }
                if(($result->getAtendido()==1)&&($result->getRechazado()==1))
                    $this->array_turnero[$key_fecha][$key_servicio_id]['t_anulados']+= 1;
                $this->array_turnero[$key_fecha][$key_servicio_id]['no_timbrados']=$rep->turnosNoTimbrados($key_servicio_id, $key_fecha);//$array_turnero[$key_fecha][$key_servicio_id]['t_emitidos']-($array_turnero[$key_fecha][$key_servicio_id]['t_atendidos']+ $array_turnero[$key_fecha][$key_servicio_id]['t_anulados']) ;
                $this->array_turnero[$key_fecha][$key_servicio_id]['t_emitidos']=   $this->array_turnero[$key_fecha][$key_servicio_id]['t_atendidos']+ $this->array_turnero[$key_fecha][$key_servicio_id]['t_anulados']+ $this->array_turnero[$key_fecha][$key_servicio_id]['no_timbrados'];
                //FIN REPORTE TURNERO

                $this->queryTotal[]=array('id'=>$result->getId(),'numeroCaja'=>$result->getNumeroCaja(),
                        'NombreUsuario'=>$result->getNombres(),'ServicioLetra'=>$result->getLetra(),
                        'ServicioNombre'=>$result->getNombre(),
                        'ServicioId'=>$result->getIdServicio(),
                        'TurnoNumero'=>$result->getNumero(),'TurnoFechaEmision'=>$result->getFechaEmision(),
                        'TurnoHoraEmision'=>$result->getHoraEmision(),'TurnoAtendido'=>$result->getAtendido(),
                        'TurnoPorAtender'=>$result->getPorAtender(),'TurnoRechazado'=>$result->getRechazado(),
                        'TurnoFechaInicioAtencion'=>$result->getFechaInicioAtencion(),'TurnoHoraInicioAtencion'=>$result->getHoraInicioAtencion(),
                        'TurnoFechaFinAtencion'=>$result->getFechaFinAtencion(),'TurnoHoraFinAtencion'=>$result->getHoraFinAtencion(),
                        'TurnoDuracion'=>$result->getDuracion());

//                $cont++;
            }


            $html= "Exitoso";
//      print_r($this->array_cuadro);
        }
        else $html="No existe Datos--Verifique que este selecionado modulo(s) y servicio(s)!!!!";
        $arraycks=array('html'=>$html);
        return($arraycks);
    }
    public $queryTotalCajas=array(); //para un query global
    public $array_cuadro_cajas    =array();//para iniciliza el reporte cuadro por semanas
    public function LLenaQueryCajasAction() {
        $this->setResponse("json");
        $fun= new Funciones();
//        $servicios=$this->getPostParam("chkservicios");
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
            $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND {#Caja}.id IN($cajas)AND duracion>='00:00:05' and hora_inicio_atencion>='$forma_duracion'" ;
            //$condicion="fecha_emision BETWEEN '$desde' AND '$hasta'" ;
            $sql= new ActiveRecordJoin (array(
                            "entities" => array("Caja", "Colas", "Usercaja", "Usuario"),
                            "fields" => array(
                                    "{#Caja}.id",
                                    "{#Caja}.numero_caja",
                                    "{#Usuario}.nombres",
                                    "{#Colas}.atendido",
                                    "{#Colas}.por_atender",
                                    "{#Colas}.fecha_inicio_atencion",
                                    "{#Colas}.hora_inicio_atencion",
                                    "{#Colas}.fecha_fin_atencion",
                                    "{#Colas}.hora_fin_atencion",
                                    "{#Colas}.duracion",
                                    "{#Colas}.calificacion",
                                    "{#Colas}.creacion_at"),
                            "conditions" => $condicion,
                            "order"=>"{#Colas}.hora_inicio_atencion"
            ));


            //$this->queryTotal = new ActiveRecordJoin($queryTotal);
            //$this->queryTotal= $sql->getResultSet();
            $cont=0;
            $a="";
            $s = strtotime($hasta)-strtotime($desde);
            $numero_dias = intval($s/86400) + 1;      //numero de dias
            $this->queryTotalCajas=array();
            $this->array_cuadro_cajas =array();   //ensero el array para cuadro cajas
            $array_cajas=array();
            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
            for ($i=1;$i<=$numero_dias;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day"));
                $separa = explode('-',$fecha);
                $mes = $separa[1];
                $dia = $separa[2];
                $ano = $separa[0];
                $dia= $fun->dia_semana($dia, $mes, $ano);
//                foreach($lista_servicios as $servicio_id) {
//                    $array_servicio[$servicio_id]= array ('dia'=>$dia,'t_emitidos'=>0,'t_atendidos'=>0,'t_anulados'=>0,'no_timbrados'=>0);
//                }
//                $this->array_turnero[$fecha]=$array_servicio;
                //INICIO INICIALIZAR ARRAY_CUADRO
                foreach($lista_cajas as $caja_id) {
                    $array_cajas[$caja_id]= array ('dia'=>$dia,'t_atendidos'=>0,'t_anulados'=>0,'duracion_t'=>0,'promedio'=>0,'promedio_ll'=>0);
                }
                $this->array_cuadro_cajas[$fecha]=$array_cajas;
                //FIN INICIALIZAR ARRAY_CUADRO
            }
            foreach($sql->getResultSet() as $result) {
//                $a=$result->getId();
                $key_fecha= $result->getFechaInicioAtencion();
                $duracion_en_segundos= $fun->totalSegundos($result->getDuracion());     //devuelve en segundos
                if($result->getAtendido()==1) {
                    //array_cuadro
                    $this->array_cuadro_cajas[$key_fecha][$caja_id]['t_atendidos']+= 1;
                    $this->array_cuadro_cajas[$key_fecha][$caja_id]['duracion_t']+= $duracion_en_segundos;
                }



                $this->queryTotalCajas[]=array('id'=>$result->getId(),'numeroCaja'=>$result->getNumeroCaja(),
                        'NombreUsuario'=>$result->getNombres(),
                        'ColaAtendido'=>$result->getAtendido(),
                        'ColaPorAtender'=>$result->getPorAtender(),
                        'ColaFechaInicioAtencion'=>$result->getFechaInicioAtencion(),'ColaHoraInicioAtencion'=>$result->getHoraInicioAtencion(),
                        'ColaFechaFinAtencion'=>$result->getFechaFinAtencion(),'ColaHoraFinAtencion'=>$result->getHoraFinAtencion(),
                        'ColaDuracion'=>$result->getDuracion(),
                        'ColaCalificacion'=>$result->getCalificacion(),
                );
//                $cont++;
            }
            $html= "Exitoso";
// print_r($this->array_cuadro_cajas);

        }
        else $html="No existe Datos";
        $arraycks=array('html'=>$html);
        return($arraycks);
    }

    public function pruebaAction() {
        //$this->setResponse('json');
        $fun= new Funciones();
        $html = $fun->renderChart('http://localhost/pruebas/FusionCharts_Evaluation/Code/FusionCharts/Column3D.swf", "http://localhost/pruebas/FusionCharts_Evaluation/Code/PHP/BasicExample/Data/Data.xml', '', 'chart1', 600, 300, false, true);

        //$html= $this->renderChartHTML('../../swf_charts/Column3D.swf', '', $strXML,'maestro', 450, 480,false);

        //return array("html"=>$html);
    }
    public function PausasPromedioAction() {
        $this->setResponse('json');
        $fun= new Funciones();
        $modulos=$this->getPostParam("chkmodulos");
        $servicios=$this->getPostParam("chkservicios");
        $desde=$this->getpostParam("desde");
        $hasta=$this->getpostParam("hasta");
        $horas=$this->getPostParam('horas');
        $minutos=$this->getPostParam('minutos');
        $segundos=$this->getPostParam('segundos');
        $tipoGrafica=$this->getPostParam('grafica');
        $forma_duracion=$horas.":".$minutos.":".$segundos;
        $lista_modulos=explode(",",$modulos);
        $lista_servicios=explode(",",$servicios);
        $s = strtotime($hasta)-strtotime($desde);
        $numero_dias = intval($s/86400) + 1;      //numero de dias
        $html="";
        $graficoBarras="";
        $graficoPastel="";
        $array_datos_pausas_aux=array();
        $array_pausas=array();
        $array_totalpausas=array();
        $array_totalDuracion=array();
        $array_cajas=array();
        if ($modulos!="") {


            $condicion="{#CajaPausas}.caja_id IN($modulos)AND {#CajaPausas}.fecha_inicio BETWEEN '$desde' AND '$hasta'  group by ({#Pausas}.id) " ;
            $sql= new ActiveRecordJoin (array(
                            "entities" => array("Pausas", "CajaPausas"),
                            "fields" => array(
                                    "{#Pausas}.id",
                                    "{#Pausas}.nombre_pausa"),
                            "conditions" => $condicion,

            ));
            foreach ($sql->getResultSet() as $result) {
                $modulo_id= $result->getId();
                $array_datos_pausas_aux[$result->getId()]=array('Pausa_id'=>$result->getId(),'Pausa_nombre'=>$result->getNombrePausa(),'t_pausas'=>0,'TotalDuracion'=>0);
            }
            $fecha= $desde;
            $fecha= date("Y-m-d", strtotime( "$fecha - 1 day")) ;
            for ($i=1;$i<=$numero_dias;$i++) {
                $fecha= date("Y-m-d", strtotime( "$fecha + 1 day"));
                $separa = explode('-',$fecha);
                $mes = $separa[1];
                $dia = $separa[2];
                $ano = $separa[0];
                $dia= $fun->dia_semana($dia, $mes, $ano);
//                $array_pausas[$fecha]=$array_datos_pausas_aux;
                 //INICIO INICIALIZAR ARRAY_CUADRO
                foreach($lista_modulos as $caja_id) {
                    $array_cajas[$caja_id]=$array_datos_pausas_aux;
                }
                $array_pausas[$fecha]=$array_cajas;
            }
//            print_r($array_pausas);
//            die();

//            $this->array_datos_modulo= $fun->ordenarArrayMultidimensional($array_datos_modulo_aux,'numero',false);
            $condicion="({#CajaPausas}.caja_id IN($modulos) OR {#Caja}.id IS NULL) AND {#CajaPausas}.fecha_inicio BETWEEN '$desde' AND '$hasta' AND {#Servicio}.id IN ($servicios) AND duracion>='$forma_duracion' group by ({#CajaPausas}.id)" ;
            $sql= new ActiveRecordJoin (array(
                            "entities" => array("CajaPausas", "Caja","Pausas", "Servicio", "Usercaja", "Usuario"),
                            "fields" => array(
                                    "{#CajaPausas}.id",
                                    "{#CajaPausas}.caja_id",
                                    "{#CajaPausas}.pausas_id",
                                    "{#Usuario}.nombres",
                                    "{#Caja}.numero_caja",
                                    "{#Pausas}.nombre_pausa",
                                    "{#CajaPausas}.estado",
                                    "{#CajaPausas}.fecha_inicio",
                                    "{#CajaPausas}.hora_inicio",
                                    "{#CajaPausas}.fecha_fin",
                                    "{#CajaPausas}.hora_fin",
                                    "{#CajaPausas}.duracion"),
//                                    "{#Caja}.descripcion",
//                                    "{#Servicio}.id  as id_servicio",
//                                    "{#Servicio}.letra",
//                                    "{#Servicio}.nombre"),
                            "conditions" => $condicion,
                            "order"=>"{#CajaPausas}.hora_inicio"
            ));

//            $array_pausas=array();
            $caja_num=array();
            foreach($sql->getResultSet() as $result) {
                //INICIO REPORTE PAUSAS
                $pausa_id= $result->getPausasId();
                $key_fecha= $result->getFechaInicio();
                $lista_modulos=explode(',',$modulos);
                $duracion_en_segundos= $fun->totalSegundos($result->getDuracion());
                foreach ($lista_modulos as $id_modulo) {
                    if($id_modulo==$result->getCajaId()&&$pausa_id==$result->getPausasId())
                    {
                    $caja_num[$id_modulo]=$result->getNumeroCaja();
                    $array_pausas[$key_fecha][$id_modulo][$pausa_id]['t_pausas']+= 1;
                    $array_pausas[$key_fecha][$id_modulo][$pausa_id]['TotalDuracion']+= $duracion_en_segundos;
                    }
                }
               
//                $key_servicio_id= $result->getIdServicio();
                   //devuelve en segundos
//                if($result->getEstado()==0) {
//                    
////                    $array_pausas[$key_fecha][$pausa_id]['Modulos']=$caja_num;
//                }
//                $arr_numC=array();
//                $arr_numC=array_flip(array_flip($caja_num));
//                $caja_num=array();
//                natsort($arr_numC);
                $array_datos_pausas_aux[$pausa_id]['t_pausas']+= 1;
                $array_datos_pausas_aux[$pausa_id]['TotalDuracion']+= $duracion_en_segundos;
//                $array_datos_pausas_aux[$pausa_id]['Modulos']=implode(',',$arr_numC);

            }
//             print_r($array_pausas);
            $fondo_titulo= "background-color:#328aa4; color:#fff";
            $html="<table border=1><thead><tr style='$fondo_titulo'>
            <th rowspan=2>Fecha</th><th rowspan=2> Dia</th><th rowspan=2>Modulo</th>";
            $nombre_pausas=array();
            $total_promedio_pausas=array();
            $modulos_pausados=array();
//            foreach($arr_numC as $id=>$r)
//                $modulos_pausados[]=$id;
            $mp=implode(',',$modulos_pausados);
//             echo $mp;
             foreach ($array_datos_pausas_aux as $key=>$result) {
                $html.="<th colspan=3>".$result['Pausa_nombre']."</th>";
               $nombre_pausas[$key]=$result['Pausa_nombre'];
               $array_totalpausas[$key]= $result['t_pausas'];
               $array_totalDuracion[$key]=$result['TotalDuracion'];
            }
            $html.="<th colspan=3> Totales</th></tr><tr style='$fondo_titulo'>";
            foreach ($array_datos_pausas_aux as $key=>$result)
            $html.="<th>Cantidad</th><th>Total Duracion</th><th>Promedio </th>";
            $html.="<th>T.Cantidad</th><th>T. Duración</th><th>T.Promedio</th></thead>";
             $totalDia=array();
             $TotalCantidadDia=array();
             
             foreach ($array_pausas as $fecha=>$resultFecha)
                 foreach ($resultFecha as $id_modulo=>$ResultadoModulo)
                    foreach($ResultadoModulo as $key=>$id_pausa){
                      $totalDia[$key]=0;
                       $TotalCantidadDia[$key]=0;
                    }

//             print_r($array_pausas);
            $html.="<tbody>";
             foreach ($array_pausas as $fecha=>$resultFecha) {
                $col=0;
                $dia="";
                $td=0;
                $tc=0;
               
                foreach ($resultFecha as $id_modulo=>$ResultadoModulo) {
                    $separa = explode('-',$fecha);
                $mes = $separa[1];
                $dia = $separa[2];
                $ano = $separa[0];
                $dia= $fun->dia_semana($dia, $mes, $ano);
                       $html.="<tr><td>".$fecha."</td>";
                       $html.="</td><td>$dia</td><td>".$this->arrayModulosNombres[$id_modulo]."</td>";
                       $acumCantidad=0;
                       $acumTotal=0;
                       $acumPromedio=0;
                       $Promedio=0;
                       
                    foreach($ResultadoModulo as $key=>$id_pausa){
                        if($id_pausa['t_pausas']!=0)
                            $Promedio=$id_pausa['TotalDuracion']/$id_pausa['t_pausas'];
                        $html.="<td>".$id_pausa['t_pausas']."</td><td>".$fun->tiempo($id_pausa['TotalDuracion'])."</td><td>".$fun->tiempo($Promedio)."</td>";
                        $acumCantidad+=$id_pausa['t_pausas'];
                        $acumTotal+=$id_pausa['TotalDuracion'];
                        $Promedio=0;
                        $col=count($ResultadoModulo);
                     
                               $totalDia[$key]+=$id_pausa['TotalDuracion'];
                               $TotalCantidadDia[$key]+=$id_pausa['t_pausas'];
                    }

                    if($acumCantidad!=0)
                        $acumPromedio=$acumTotal/$acumCantidad;
                    $html.="<td>".$acumCantidad."</td><td>".$fun->tiempo($acumTotal)."</td><td>".$fun->tiempo($acumPromedio)."</td>";
                    
                }
                $html.="</tr><tr><td colspan=3 align=right><strong>Total del Dia: $dia</td>";
                foreach($totalDia as $k=>$total){
                 $prom=0;
                    if ($TotalCantidadDia[$k]!=0)
                $prom=$total/$TotalCantidadDia[$k];
                $html.="<td><strong>$TotalCantidadDia[$k]</td><td><strong>".$fun->tiempo($total)."</td><td><strong>".$fun->tiempo($prom)."</td>" ;
                $td+=$totalDia[$k];
                $tc+=$TotalCantidadDia[$k];
                $totalDia[$k]=0;
                $TotalCantidadDia[$k]=0;
                }
                $p=0;
                if ($tc!=0)
                    $p=$td/$tc;
                $html.="<td><strong>$tc</td><td><strong>".$fun->tiempo($td)."</td><td><strong>".$fun->tiempo($p)."</td></tr>";//<td>".$fecha."</td><td aling=center>".$fun->tiempo($promediopausas)." </td><td>".$result['t_pausas']."</td><td>".$result['Modulos']."</td><td> <input type='button' style='background-color:#e5f1f4;width:200px;height:20px;border-width:thin;border-style:solid;border-color:white;color:blue;' value='Ver Detalle' onclick="." \"verDetallePausas('$key','$mp')\""."/></td></tr>";
              }
            $html.="<tr style='$fondo_titulo'><td colspan=3 >Totales: </td>";
            $prt=0;
              $totalCantidad=0;
                  $totalDuracion=0;         
              foreach($array_totalpausas as $l=>$tot){
                  $totalCantidad+=$tot;
                  $totalDuracion+=$array_totalDuracion[$l];
                if($tot!=0)
                    $prt=$array_totalDuracion[$l]/$tot;
                  $html.=" <td>$tot</td><td>".$fun->tiempo($array_totalDuracion[$l])."</td><td>".$fun->tiempo($prt)."</td>";
              }
              if($totalCantidad!=0)$prt=$totalDuracion/$totalCantidad;
              $html.="<td>$totalCantidad</td><td>".$fun->tiempo($totalDuracion)."</td><td>".$fun->tiempo($prt)."</tr></table>";
//             print_r($array_pausas);
//             print_r($array_datos_pausas_aux
            if($tipoGrafica=="Barras") {
                $n=implode(',', $nombre_pausas);
                $t=implode(',', $array_totalpausas);
//                  print_r($nombre_pausas);
//                  print_r($total_promedio_pausas);

                $graficoBarras=$this->GraficaBarras("Reporte Pausas $desde hasta $hasta", $n, $t);
            }
            if($tipoGrafica=="Pastel") {
                $graficoPastel=$this->Graficadorpie("Reporte Pausas $desde hasta $hasta", $nombre_pausas, $array_totalpausas);
            }

        }
        else $html="No hay datos";
        $arraycks=array('html'=>$html,"GraficoBarras"=>$graficoBarras,"GraficoPastel"=>$graficoPastel);
        return($arraycks);
    }

    public function verDetallePausasAction() {
        $this->setResponse("json");
        $modulos=$this->getPostParam("modulos_id");
        $id_pausa=$this->getPostParam("id_pausa");
        $desde=$this->getpostParam("desde");
        $hasta=$this->getpostParam("hasta");
        $fondo_titulo= "background-color:#328aa4; color:#fff";
        $i=0;
        $nom_pausa="";
        $mi_array=array();
        $html="";
        $fun=new Funciones();
        $db = DbBase::rawConnect();
        $result = $db->query("SELECT c.numero_caja,p.nombre_pausa,u.nombres,cp.fecha_inicio,cp.hora_inicio,cp.fecha_fin,cp.hora_fin,cp.duracion
FROM caja_pausas cp, usuario u,caja c, pausas p
WHERE cp.usuario_id=u.id AND cp.caja_id=c.id AND u.id=cp.usuario_id AND cp.pausas_id=p.id
AND caja_id IN($modulos) AND pausas_id=$id_pausa and cp.fecha_inicio BETWEEN '$desde' AND '$hasta'");
        while($row = $db->fetchArray($result)) {
            $mi_array[]= array ('id'=>$row['numero_caja'],'nombre'=>$row['nombres'],'fecha inicio'=>$row['fecha_inicio'],'hora inicio'=>$row['hora_inicio'],'fecha fin'=>$row['fecha_fin'],'hora fin'=>$row['hora_fin'],'Duracion'=>$row['duracion']);
            $nom_pausa=$row['nombre_pausa'];
        }
//        $array_datos_pausasOr_id= $fun->ordenarArrayMultidimensional($mi_array,'hora inicio',false);
        $array_datos_pausas= $fun->ordenarArrayMultidimensional($mi_array,'id',false);
        $html="<strong>Detalle de la Pausa: $nom_pausa <table border=1><thead><tr style='$fondo_titulo'><th>Modulo</th> <th width=220px>Usuario</th><th> Fecha Inicio </th> <th> Hora Inicio </th><th> Fecha Fin </th><th> Hora Fin </th><th>  Duracion  </th> </tr></thead>";
        $html.="<tbody>";
        foreach($array_datos_pausas as $result){
            $html.="<tr>";
            $html.="<td>".$result['id']."</td>";
            $html.="<td>".$result['nombre']."</td>";
            $html.="<td>".$result['fecha inicio']."</td>";
            $html.="<td>".$result['hora inicio']."</td>";
            $html.="<td>".$result['fecha fin']."</td>";
            $html.="<td>".$result['hora fin']."</td>";
            $html.="<td>".$result['Duracion']."</td>";
            $html.="</tr><tbody>";
        }
         $html.="</table>";
         $arr=array('html'=>$html);
        return($arr);
    }
}

