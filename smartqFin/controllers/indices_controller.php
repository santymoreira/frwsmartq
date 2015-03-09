<?php
class indicesController extends ApplicationController {
    /*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
    */

    public $lista_modulos=array();
    public $lista_grupo_servicio=array();
    //public $lista_servicios=array();

    public function turnosAtendidosCajaAction() {
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
    }

    public function turnosAtendidosModulosAction() {
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
    }

    /*
     * Inicio de indices de calidad de servicio
    */
    public function calidadServicioAction() {
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));
    }

    /*
     * Inicio de indices de los totales de los turnos en pastel
    */
    public function turnosAtendidosServicioAction() {
        $lista=array();
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));

        //Lista de Módulos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=7 ORDER BY numero_caja;
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
            //$this->lista_modulos[$result->getId()]=$result->getNumeroCaja();
            $lista[$result->getId()]=$result->getNumeroCaja();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $this->lista_modulos[$key]=$val;
        }

    }

    /*
     * Inicio de indices de los totales de los turnos por grupo de servicios en pastel
    */
    public function turnosAtendidosGrupoServicioAction() {
        Tag::displayTo('desde', date("Y-m-d"));
        Tag::displayTo('hasta', date("Y-m-d"));

        //Lista de Grupo de servicos
        //SELECT numero_caja FROM caja c, usercaja uc, usuario u, grupousuario gu
        //WHERE c.id=uc.caja_id AND u.id=uc.usuario_id AND u.id=gu.usuario_id AND gu.grupo_id=7 ORDER BY numero_caja;
        $this->lista_grupo_servicio[0]="Todos";
        $gruposervicio= new Gruposervicio();
        $buscaGruposervicio= $gruposervicio->find();
        foreach ($buscaGruposervicio as $result) {
            $this->lista_grupo_servicio[$result->getId()]=$result->getNombreGrupoServicio();
        }
    }

    /*
     * Muestra gráfico en forma de pastel de los turnos atendidos por servicio
     * Gráfico: pastel
     * Menú: Administracion/Indices/Turnos atendidos por servicio
    */
    public function indicesTurnosAtendidosServicioAction() {
        $caja= $this->getPostParam('cajas');
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

        $html= "";

        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";
        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="margin: [50, 200, 60, 170]";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos Atendidos desde $desde hasta $hasta'";
        //$html.="text: 'Porcentaje de Turnos Atendidos desde $desde hasta $hasta'";
        $html.="},";
        $html.="plotArea: {";
        $html.="shadow: null,";
        $html.="borderWidth: null,";
        $html.="backgroundColor: null";
        $html.="},";
        $html.="tooltip: {";
        $html.="formatter: function() {";
        //$html.="return '<b>'+ this.point.name +'</b>: '+ this.y +' %';"; //aparece dos porcentajes
        $html.="return '<b>'+ this.point.name +'</b>'";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="pie: {";
        $html.="allowPointSelect: true,";
        $html.="cursor: 'pointer',";
        $html.="dataLabels: {";
        $html.="enabled: true,";
        $html.="formatter: function() {";
        $html.="if (this.y > 0) return this.point.name;";   //para el título de cada valor en el pai
        $html.="},";
        $html.="color: 'black',";
        $html.="style: {";
        $html.="font: '12px Trebuchet MS, Verdana, sans-serif'";
        $html.="}";
        $html.="}";
        $html.="}";
        $html.="},";
        $html.="legend: {";
        $html.="layout: 'vertical',";
        $html.="style: {";
        $html.="left: 'auto',";
        $html.="bottom: 'auto',";
        $html.="right: '0px',";     //50px
        $html.="top: '30px'";       //100px
        $html.="}";
        $html.="},";
        $html.="series: [{";
        $html.="type: 'pie',";
        $html.="name: 'Browser share',";
        $html.="data: [";

        //INICIO CALCULO DE TOTAL TURNOS ATENDIDOS POR CAJA
        //SELECT COUNT(*) AS total FROM turnos;
        $total="";
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $result = $db->query("SELECT COUNT(*) AS total FROM turnos WHERE atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta'");
        else
            $result = $db->query("SELECT COUNT(*) AS total FROM turnos WHERE atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta' AND caja_id= $caja");
        while($row = $db->fetchArray($result)) {
            $total=$row[0];
        }
        //FIN CALCULO DE TOTAL TURNOS ATENDIDOS POR CAJA

        //SELECT nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id GROUP BY servicio_id;
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $result = $db->query("SELECT nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id AND atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta' GROUP BY servicio_id");
        else
            $result = $db->query("SELECT nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id AND atendido= 1 AND rechazado=0 AND t.caja_id= $caja AND fecha_emision BETWEEN '$desde' AND '$hasta' GROUP BY servicio_id");
        while($row = $db->fetchArray($result)) {
            $porcentaje= round(($row[2]*100)/$total,2);
            $html.="['".$row[0]."-".$row[2]." t., ".$porcentaje."%"."',       ".$porcentaje."],";
        }

        //        $query = new ActiveRecordJoin(array(
        //            "entities" => array("Turnos", "Servicio"),
        //            "fields" => array(
        //            "{#Caja}.numero_caja",
        //        ));

        /*$html.="['Firefox',   45.0],";
        $html.="['IE',       26.8],";
        $html.="['Chrome',       12.8],";
        $html.="['Safari',    8.5],";
        $html.="['Opera',     6.2],";
        $html.="['Others',   0.7]";*/

        $html.="]";

        $html.="}]";
        $html.="});";
        $html.="});";

        $html.="</script>";

        $html.="</head>";

        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 1200px; height: 700px; margin: 0 auto'></div>";
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $nom_caja = "Todos";
        else {
            $result = $db->query("SELECT numero_caja FROM caja WHERE id=$caja");
            while($row = $db->fetchArray($result)) {
                $nom_caja = $row[0];
            }
        }
        $html.="<center>MÃ³dulo: $nom_caja<br>
                Total turnos atendidos: $total</center>";

        $html.="</body>";
        $html.="</html>";

        echo $html;
    }

    /*
     * Función que muestra el gráfico en pastel de los totales de turnos atendidos por grupo
    */
    public function indicesTurnosAtendidosGrupoServicioAction() {
        $grupo_servicio= $this->getPostParam('cajas');
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');

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
        $html.="margin: [50, 200, 60, 170]";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos Atendidos desde $desde hasta $hasta'";
        //$html.="text: 'Porcentaje de Turnos Atendidos desde $desde hasta $hasta'";
        $html.="},";
        $html.="plotArea: {";
        $html.="shadow: null,";
        $html.="borderWidth: null,";
        $html.="backgroundColor: null";
        $html.="},";
        $html.="tooltip: {";
        $html.="formatter: function() {";
        $html.="return '<b>'+ this.point.name +'</b>: '+ this.y +' %';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="pie: {";
        $html.="allowPointSelect: true,";
        $html.="cursor: 'pointer',";
        $html.="dataLabels: {";
        $html.="enabled: true,";
        $html.="formatter: function() {";
        $html.="if (this.y > 5) return this.point.name;";
        $html.="},";
        $html.="color: 'black',";
        $html.="style: {";
        $html.="font: '13px Trebuchet MS, Verdana, sans-serif'";
        $html.="}";
        $html.="}";
        $html.="}";
        $html.="},";
        $html.="legend: {";
        $html.="layout: 'vertical',";
        $html.="style: {";
        $html.="left: 'auto',";
        $html.="bottom: 'auto',";
        $html.="right: '50px',";
        $html.="top: '100px'";
        $html.="}";
        $html.="},";
        $html.="series: [{";
        $html.="type: 'pie',";
        $html.="name: 'Browser share',";
        $html.="data: [";

        //INICIO CALCULO DE TOTAL TURNOS ATENDIDOS POR MODULO PARA EL CALCULO DEL PORCENTAGE
        //SELECT COUNT(*) AS total FROM turnos t, servicio s, gruposervicio gs
        //WHERE s.id=t.servicio_id AND gs.id=s.gruposervicio_id AND
        //atendido= 1 AND fecha_inicio_atencion BETWEEN '2010-11-01' AND '2010-11-30' AND gs.id=1;
        $total="";
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $result = $db->query("SELECT COUNT(*) AS total FROM turnos t, servicio s, gruposervicio gs WHERE s.id=t.servicio_id AND gs.id=s.gruposervicio_id AND atendido= 1 AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'");
        else
            $result = $db->query("SELECT COUNT(*) AS total FROM turnos t, servicio s, gruposervicio gs WHERE s.id=t.servicio_id AND gs.id=s.gruposervicio_id AND atendido= 1 AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' AND gs.id=$grupo_servicio");
        while($row = $db->fetchArray($result)) {
            $total=$row[0];
        }
        //FIN CALCULO DE TOTAL TURNOS ATENDIDOS POR MODULO

        //Todos los grupos de servicios
        //SELECT nombre, letra, nombre_grupo_servicio, COUNT(*) AS total
        //FROM turnos t, servicio s, gruposervicio gs
        //WHERE s.id=t.servicio_id AND gs.id=s.gruposervicio_id AND atendido= 1
        //AND fecha_inicio_atencion BETWEEN '2010-11-01' AND '2010-11-30' GROUP BY nombre_grupo_servicio;
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $result = $db->query("SELECT nombre, letra, nombre_grupo_servicio, COUNT(*) AS total FROM turnos t, servicio s, gruposervicio gs WHERE s.id=t.servicio_id AND gs.id=s.gruposervicio_id AND atendido= 1 AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' GROUP BY nombre_grupo_servicio;");
        else
            $result = $db->query("SELECT nombre, letra, COUNT(*) AS total FROM turnos t, servicio s WHERE s.id=t.servicio_id AND atendido= 1 AND t.caja_id= $caja AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta' GROUP BY servicio_id");
        while($row = $db->fetchArray($result)) {
            $porcentaje= round(($row[2]*100)/$total,2);
            $html.="['".$row[0]."-".$row[2]." t., ".$porcentaje."%"."',       ".$porcentaje."],";
        }

        //        $query = new ActiveRecordJoin(array(
        //            "entities" => array("Turnos", "Servicio"),
        //            "fields" => array(
        //            "{#Caja}.numero_caja",
        //        ));

        /*$html.="['Firefox',   45.0],";
        $html.="['IE',       26.8],";
        $html.="['Chrome',       12.8],";
        $html.="['Safari',    8.5],";
        $html.="['Opera',     6.2],";
        $html.="['Others',   0.7]";*/

        $html.="]";

        $html.="}]";
        $html.="});";
        $html.="});";

        $html.="</script>";

        $html.="</head>";

        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 1000px; height: 700px; margin: 0 auto'></div>";
        $db = DbBase::rawConnect();
        if ($caja==0) //0= todos
            $nom_caja = "Todos";
        else {
            $result = $db->query("SELECT numero_caja FROM caja WHERE id=$caja");
            while($row = $db->fetchArray($result)) {
                $nom_caja = $row[0];
            }
        }
        $html.="<center>MÃ³dulo: $nom_caja</center>";


        $html.="</body>";
        $html.="</html>";

        echo $html;
    }

    /*
     * Muestra gráfico en forma de barras de los turnos atendidos por módulo
     * Gráfico: barras
     * Menú: Administracion/Indices/Turnos atendidos por modulo
    */
    public function indicesTurnosAtendidosModulosAction() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        $html= "";

        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";
        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'column',";
        $html.="margin: [ 50, 50, 150, 180]";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos Atendidos por MÃ³dulo desde $desde hasta $hasta'";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: [";

        //INICIO BUSCAR MODULOS
        $lista=array();
        $condicion="grupousuario.grupo_id=5";
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
            $lista[$result->getId()]="N.".$result->getNumeroCaja()." - ".$result->getNombres();
        }
        natsort($lista);
        foreach ($lista as $key => $val) {
            $html.= "'".$val."',";
        }
        //FIN BUSCAR MODULOS
        $html.="],";
        $html.="labels: {";
        $html.="rotation: -30,";
        $html.="align: 'right',";
        $html.="style: {";
        $html.="font: 'normal 13px Verdana, sans-serif'";
        $html.="}";
        $html.="}";
        $html.="},";
        $html.="yAxis: {";
        $html.="min: 0,";
        $html.="title: {";
        $html.="text: 'Total Turnos'";
        $html.="}";
        $html.="},";
        $html.="legend: {";
        $html.="enabled: false";
        $html.="},";
        $html.="tooltip: {";
        $html.="formatter: function() {";
        //$html.="return '<b>'+ this.x +'</b><br/>'+";      //número de módulo
        $html.="return ";
        //$html.="'Population in 2008: '+ Highcharts.numberFormat(this.y, 1) +";
        $html.="Highcharts.numberFormat(this.y, 0) +";
        $html.="' turnos';";
        $html.="}";
        $html.="},";
        $html.="series: [{";
        $html.="name: 'Population',";

        $html.="data: [";

        $total_turnos=0;
        foreach ($lista as $key => $val) {
            $id_caja=$key;
            $db = DbBase::rawConnect();
            $result1 = $db->query("SELECT COUNT(*) as total FROM turnos t, servicio s, caja c WHERE s.id=t.servicio_id AND c.id=t.caja_id AND c.id='$id_caja' AND atendido= 1 AND rechazado=0 AND fecha_emision BETWEEN '$desde' AND '$hasta'");
            while($row1 = $db->fetchArray($result1)) {
                $porcentaje=2;
                $html.= $row1['total'].",";
                $total_turnos+=$row1['total'];
            }
        }
        //FIN BUSCAR MODULOS

        $html.="],";

        $html.="dataLabels: {";
        $html.="enabled: true,";
        $html.="rotation: -90,";
        //$html.="color: '#FFFFFF',";
        $html.="color: '#000',";
        $html.="align: 'right',";
        $html.="x: -3,";
        $html.="y: -30,";
        $html.="formatter: function() {";
        $html.="return this.y;";
        $html.="},";
        $html.="style: {";
        $html.="font: 'normal 12px Verdana, sans-serif'";
        $html.="}";
        $html.="}";
        $html.="}]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="</head>";
        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 900px; height: 600px; margin: 0 auto'></div>";
        $html.="<center>Total Turnos Atendidos: $total_turnos</center>";
        $html.="</body>";
        $html.="</html>";

        echo $html;
    }

    public function indicesTurnosAtendidosCaja2Action() {
        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
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
        $html.="defaultSeriesType: 'column'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Turnos Atendidos por Cajas'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";

        $html.="xAxis: {";
        $html.="categories: [";

        //INICIO BUSCAR CAJAS
        $caja=new Caja();
        //$turno=new Turnos();
        $buscaCaja= $caja->find();

        //$condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'";
        foreach ($buscaCaja as $result1) {
            $id_caja= $result1->getId();
            $html.= "'".$result1->getNumeroCaja()."',";
            //$html.="'Jan', ";
            //            $html.= "<tr>";
            //                $html.= "<th scope='row'>".$result1->getDescripcion()." Caja: ".$result1->getNumeroCaja()."</th>";
            //                foreach ($buscaServicio as $result2) {
            //                $id_servicio= $result2->getId();
            //                $turnos_atendidos= $turno->count("caja_id = $id_caja AND servicio_id= $id_servicio AND atendido = 1","condition: $condicion");
            //                $html.= "<td>".$turnos_atendidos."</td>";
            //                //SELECT COUNT(*) FROM turnos WHERE caja_id=29 AND servicio_id=1;
            //                }
            //                $html.= "</tr>";
        }

        //FIN BUSCAR CAJAS

        /*$html.="'Jan', ";
                $html.="'Feb', ";
                $html.="'Mar', ";
                $html.="'Apr', ";
                $html.="'May', ";
                $html.="'Jun', ";
                $html.="'Jul', ";
                $html.="'Aug', ";
                $html.="'Sep', ";
                $html.="'Oct', ";
                $html.="'Nov', ";
                $html.="'Dec'";*/
        $html.="]";
        $html.="},";
        $html.="yAxis: {";
        $html.="min: 0,";
        $html.="title: {";
        $html.="text: 'Total Turnos Atendidos'";
        $html.="}";
        $html.="},";
        $html.="legend: {";
        $html.="layout: 'vertical',";
        $html.="backgroundColor: '#FFFFFF',";
        $html.="align: 'left',";
        $html.="verticalAlign: 'top',";
        $html.="x: 100,";
        $html.="y: 70";
        $html.="},";
        $html.="tooltip: {";
        $html.="formatter: function() {";
        $html.="return ''+";
        $html.="this.x +': '+ this.y +' turnos';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="column: {";
        $html.="pointPadding: 0.2,";
        $html.="borderWidth: 0";
        $html.="}";
        $html.="},";



        //$html.="series: [ {";
        $html.="series: [ ";


        $servicio= new Servicio();
        $buscaServicio= $servicio->find();

        //$condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'";
        $data="";

        foreach ($buscaServicio as $result) {
            $numero_caja="";
            $nombre_servicio= $result->getNombre();
            $html.="{";
            $html.="name: '".$nombre_servicio."',";
            $html.="data: [";

            //SELECT numero_caja, nombre FROM servicio s, serviciocaja sc, caja c WHERE s.id=sc.servicio_id AND c.id=sc.caja_id;
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT numero_caja, nombre FROM servicio s, serviciocaja sc, caja c WHERE s.id=sc.servicio_id AND c.id=sc.caja_id");
            while($row = $db->fetchArray($result)) {
                $numero_caja=$row['numero_caja'];

                //SELECT COUNT(*) FROM turnos t, servicio s, caja c WHERE s.id=t.servicio_id AND c.id=t.caja_id AND numero_caja='INF3'  AND atendido= 1  AND fecha_emision BETWEEN '2010-08-31' AND '2010-08-31';
                $db = DbBase::rawConnect();
                $result1 = $db->query("SELECT COUNT(*) as total FROM turnos t, servicio s, caja c WHERE s.id=t.servicio_id AND c.id=t.caja_id AND numero_caja='$numero_caja' AND atendido= 1 AND fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'");
                while($row1 = $db->fetchArray($result1)) {
                    //$html.="49.9,";
                    $html.= $row1['total'].",";
                }

            }

            //            for ( $i = 1 ; $i <= 18 ; $i ++) {
            //                $html.="49.9,";
            //            }
            //            $html.="]";
            //            $html.="},";
            //

            $html.="]";
            $html.="},";
        }


        $html.="{ name: 'Microcredito',";
        $html.="data: [49, 71, 106, 129, 144, 176, 135, 148, 216, 194, 95, 54, 54, 216, 194, 95, 54, 54]";
        //
        //        $html.="}, {";
        //        $html.="name: 'Informacion General',";
        //        $html.="data: [83, 78, 98, 93, 106, 84, 105, 104, 91, 83, 106, 92, 54]";
        //
        //        $html.="}, {";
        //        $html.="name: 'Reclamos',";
        //        $html.="data: [48, 38, 39, 41, 47, 48, 59, 59, 52, 65, 59, 51, 54]";
        //
        //        $html.="}, {";
        //        $html.="name: 'Cobranzas',";
        //        $html.="data: [42, 33, 34, 39, 52, 75, 57, 60, 47, 39, 46, 51, 54]";



        $html.="}]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="</head>";
        $html.="<body>";

        $html.="    <!-- 3. Add the container -->";
        $html.="    <div id='container' style='width: 2400px; height: 700px; margin: 0 auto'></div>";


        $html.="</body>";
        $html.="</html>";

        echo $html;

    }

    /*
     * Indice de calidad de servicio de los módulos
    */
    public function indicesCalidadServicioAction() {

        $desde= $this->getPostParam('desde');
        $hasta= $this->getPostParam('hasta');
        $html= "";

        $fun= new Funciones();
        $html.= $fun->encabezadoHighcharts("Title");

        $html.="var chart;";
        $html.="$(document).ready(function() {";
        $html.="chart = new Highcharts.Chart({";
        $html.="chart: {";
        $html.="renderTo: 'container',";
        $html.="defaultSeriesType: 'column'";
        $html.="},";
        $html.="title: {";
        $html.="text: 'Calificaciones de los clientes por mÃ³dulo desde $desde hasta $hasta'";
        $html.="},";
        $html.="subtitle: {";
        $html.="text: ''";
        $html.="},";
        $html.="xAxis: {";
        $html.="categories: [";

        //INICIO BUSCAR MODULOS
        //$caja=new Caja();
        //$buscaCaja= $caja->find("order: numero_caja");
        //foreach ($buscaCaja as $result) {
        //$id_caja= $result->getId();
        //$html.= "'MOD ".$result->getNumeroCaja()."',";
        //}

        //SELECT c.id, numero_caja
        //FROM usuario u, caja c, usercaja uc, grupousuario gu
        //WHERE u.id=uc.usuario_id AND c.id=uc.caja_id AND u.id=gu.usuario_id AND gu.grupo_id=5 ORDER BY numero_caja;
        $condicion = "{#Grupousuario}.grupo_id = 5"; //operadores
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Usuario", "Caja","Usercaja","Grupousuario"),
                        "fields" => array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja",
                                "{#Usuario}.nombres"),
                        "conditions" => $condicion,
                        "order" => "{#Caja}.numero_caja"
        ));
        foreach($query->getResultSet() as $result) {
            $id_caja= $result->getId();
            $modulo="N.".$result->getNumeroCaja()." - ".$result->getNombres();
            $html.= "'$modulo',";
            //$listausuarios[$result->getId()]=$result->getNombres();
        }
        //FIN BUSCAR MODULOS

        /*$html.="'Jan', ";
                                                                $html.="'Feb', ";
                                                                $html.="'Mar', ";
                                                                $html.="'Apr', ";
                                                                $html.="'May', ";
                                                                $html.="'Jun', ";
                                                                $html.="'Jul', ";
                                                                $html.="'Aug', ";
                                                                $html.="'Sep', ";
                                                                $html.="'Oct', ";
                                                                $html.="'Nov', ";
                                                                $html.="'Dec'";*/
        $html.="]";
        $html.="},";
        $html.="yAxis: {";
        $html.="min: 0,";
        $html.="title: {";
        $html.="text: 'Calificaciones (clf)'";
        $html.="}";
        $html.="},";
        $html.="legend: {";
        $html.="layout: 'vertical',";
        $html.="backgroundColor: '#FFFFFF',";
        $html.="align: 'left',";
        $html.="verticalAlign: 'top',";
        $html.="x: 100,";
        $html.="y: 70";
        $html.="},";
        $html.="tooltip: {";
        $html.="formatter: function() {";
        $html.="return ''+";
        $html.="this.x +': '+ this.y +' clf';";
        $html.="}";
        $html.="},";
        $html.="plotOptions: {";
        $html.="column: {";
        $html.="pointPadding: 0.2,";
        $html.="borderWidth: 0";
        $html.="}";
        $html.="},";
        $html.="series: [";

        //INICIO BUSCAR CAJAS
        $condicion = "{#Grupousuario}.grupo_id = 5"; //operadores
        $query = new ActiveRecordJoin(array(
                        "entities" => array("Usuario", "Caja","Usercaja","Grupousuario"),
                        "fields" => array(
                                "{#Caja}.id",
                                "{#Caja}.numero_caja"),
                        "conditions" => $condicion,
                        "order" => "{#Caja}.numero_caja"
        ));
        foreach($query->getResultSet() as $result1) {
            $cont=0;
            //foreach ($buscaCaja as $result) {
            $cont=$cont+1;
            $id_caja= $result1->getId();
            $numero_caja= $result1->getNumeroCaja();
            $excelente= 0;
            $muybueno=0;
            $bueno=0;
            $regular=0;
            $db = DbBase::rawConnect();
            $result = $db->query("SELECT calificacion, COUNT(*) as total FROM turnos WHERE caja_id = $id_caja AND atendido= 1 AND rechazado= 0 AND fecha_emision BETWEEN '$desde' AND '$hasta' GROUP BY calificacion");
            while($row = $db->fetchArray($result)) {
                $calif= $row['calificacion'];
                if ($calif=="Excelente")
                    $excelente=$row['total'];
                else if ($calif=="Muy Bueno")
                    $muybueno=$row['total'];
                else if ($calif=="Bueno")
                    $bueno=$row['total'];
                else if ($calif=="Regular")
                    $regular=$row['total'];
            }
            //$cont=array ($excelente, $bueno, $regular, $malo);
            //*print_r ($cont);
            $datos[] = array($cont, $excelente, $muybueno, $bueno, $regular);
            //print_r ($datos);
            //$datos[] = array(1, 'gato', 'blanco');
            //$datos[] = array(2, 'liebre', 'gris');
            //$datos[] = array(3, 'oso', 'marron');


            /*$html.="{";
                                                    $html.="name: '$numero_caja',";
                                                    $html.="data: [$excelente, $bueno, $regular, $malo]";
                                                    $html.="},";*/
        }
        foreach ($datos as $key => $fila) {
            $ex[$key]  = $fila[1]; // columna de excelente
            $bue[$key] = $fila[2]; //columna de bueno
            $reg[$key] = $fila[3]; //columna de regular
            $mal[$key] = $fila[4]; //columna de malo
        }
        //FIN BUSCAR CAJAS

        //INICIO FORMAR COLUMNAS DE CALIFICACION
        $html.="{";
        $html.="name: 'Excelente',";
        $data="";
        foreach ($ex as $val) {
            $data.=$val.",";
        }
        $html.="data: [$data]";

        $html.="}, {";
        $html.="name: 'Muy Bueno',";
        $data="";
        foreach ($bue as $val) {
            $data.=$val.",";
        }
        $html.="data: [$data]";
        $html.="}, {";

        $html.="name: 'Bueno',";
        $data="";
        foreach ($reg as $val) {
            $data.=$val.",";
        }
        $html.="data: [$data]";
        $html.="}, {";

        $html.="name: 'Regular',";
        $data="";
        foreach ($mal as $val) {
            $data.=$val.",";
        }
        $html.="data: [$data]";
        $html.="},";
        //FIN FORMAR COLUMNAS DE CALIFICACION



        /*$html.="{";
                                                        $html.="name: 'Tokyo',";
                                                        $html.="data: [49.9, 71.5, 106.4, 129.2,]";

                                                $html.="}, {";
                                                        $html.="name: 'New York',";
                                                        $html.="data: [83.6, 78.8, 98.5, 93.4,]";

                                                $html.="}, {";
                                                        $html.="name: 'London',";
                                                        $html.="data: [48.9, 38.8, 39.3, 41.4,]";

                                                $html.="}, {";
                                                        $html.="name: 'Berlin',";
                                                        $html.="data: [42.4, 33.2, 34.5, 39.7,]";

                                                $html.="},";*/


        $html.="]";
        $html.="});";


        $html.="});";

        $html.="</script>";

        $html.="</head>";
        $html.="<body>";

        $html.="<!-- 3. Add the container -->";
        $html.="<div id='container' style='width: 1400px; height: 500px; margin: 0 auto'></div>";


        $html.="</body>";
        $html.="</html>";

        echo $html;
    }


    /*public function indicesTurnosAtendidosCajaAction() {
$desde= $this->getPostParam('desde');
$hasta= $this->getPostParam('hasta');
$html= "";
$html.="<html>";
    $html.="<head>";

        $html.="<title>Charting</title>";
        $html.="<link href='../../css/basic.css' type='text/css' rel='stylesheet' />";
        $html.="<script type='text/javascript' src='../../_shared/EnhanceJS/enhance.js'></script>";
        $html.="<script type='text/javascript'>";

            $html.="enhance({";
            $html.="loadScripts: [";
            $html.="'../../js/excanvas.js',";
            $html.="'../../_shared/jquery.min.js',";
            $html.="'../../js/visualize.jQuery.js',";
            $html.="'../../js/example.js'";
            $html.="],";
            $html.="loadStyles: [";
            $html.="'../../css/visualize.css',";
            $html.="'../../css/visualize-dark.css'";
            $html.="]";
            $html.="});";
            $html.="</script>";
        $html.="</head>";
    $html.="<body>";

        //INICIO MOSTRAR LOS SERVICIOS
        $servicio= new Servicio();
        $buscaServicio= $servicio->find();
        foreach ($buscaServicio as $result) {
        $html.= "<br> <label style='font-size:12px'>".$result->getLetra().". ".$result->getNombre()."</label></br>";
        }
        //FIN MOSTRAR LOS SERVICIOS

        $html.="<div id='div1'>";
            $html.= "<table >";
                $html.= "<caption>Turnos Atendidos por Caja</caption>";
                $html.= "<thead>";
                    $html.= "<tr>";
                        $html.= "<td></td>";

                        //INICIO CABECERA DE LA TABLA
                        $servicio= new Servicio();
                        $buscaServicio= $servicio->find();
                        foreach ($buscaServicio as $result) {
                        $html.= "<th scope='col'>".$result->getLetra()."</th>";
                        }
                        //FIN CABECERA DE LA TABLA

                        /*$html.= "<th scope='col'>food</th>";
                        $html.= "<th scope='col'>auto</th>";
                        $html.= "<th scope='col'>household</th>";
                        $html.= "<th scope='col'>furniture</th>";
                        $html.= "<th scope='col'>kitchen</th>";
                        $html.= "<th scope='col'>bath</th>";*/

    /*$html.= "</tr>";
                    $html.= "</thead>";
                $html.= "<tbody>";

                    //INICIO BUSCAR CAJAS
                    $caja=new Caja();
                    $turno=new Turnos();
                    $buscaCaja= $caja->find();

                    $condicion="fecha_inicio_atencion BETWEEN '$desde' AND '$hasta'";
                    foreach ($buscaCaja as $result1) {
                    $id_caja= $result1->getId();
                    $html.= "<tr>";
                        $html.= "<th scope='row'>".$result1->getDescripcion()." Caja: ".$result1->getNumeroCaja()."</th>";
                        foreach ($buscaServicio as $result2) {
                        $id_servicio= $result2->getId();
                        $turnos_atendidos= $turno->count("caja_id = $id_caja AND servicio_id= $id_servicio AND atendido = 1","condition: $condicion");
                        $html.= "<td>".$turnos_atendidos."</td>";
                        //SELECT COUNT(*) FROM turnos WHERE caja_id=29 AND servicio_id=1;
                        }
                        $html.= "</tr>";
                    }

                    //FIN BUSCAR CAJAS

                    $html.= "</tbody>";
                $html.= "</table>";
            $html.="</div>";
        $html.= "</body>";
    $html.= "</html>";

echo $html;
}*/
}
?>
