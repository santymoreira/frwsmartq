<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class Funciones extends ActiveRecord {
    /**
     * Funci�n que permite Mostrar todas las letras para el servicio en nuevo/editar
     */
    public function verTodasLetras() {
        $abecedario = array("A"=>'A',"B"=>'B',"C"=>'C',"D"=>'D',"E"=>'E',"F"=>'F',"G"=>'G',
                "H"=>'H',"I"=>'I',"J"=>'J',"K"=>'K',"L"=>'L',"M"=>'M',"N"=>'N',"O"=>'O',"P"=>'P',
                "Q"=>'Q',"R"=>'R',"S"=>'S',"T"=>'T',"U"=>'U',"V"=>'V',"W"=>'W',"X"=>'X',"Y"=>'Y',"Z"=>'Z');
        return $abecedario;
    }

    /**
     * Funci�n que permite Mostrar las letras que no han sido asignadas para el servicio en nuevo/editar
     */
    public function verRestoLetras() {
        $letra_asignada = array();
        $servicio= new Servicio();
        $valor = $servicio->find();
        foreach ($valor as $val) {
            $letra_asignada[]= $val->getLetra();
        }
        $abecedario = array("A"=>'A',"B"=>'B',"C"=>'C',"D"=>'D',"E"=>'E',"F"=>'F',"G"=>'G',
                "H"=>'H',"I"=>'I',"J"=>'J',"K"=>'K',"L"=>'L',"M"=>'M',"N"=>'N',"O"=>'O',"P"=>'P',
                "Q"=>'Q',"R"=>'R',"S"=>'S',"T"=>'T',"U"=>'U',"V"=>'V',"W"=>'W',"X"=>'X',"Y"=>'Y',"Z"=>'Z');
        $letras = array_diff($abecedario, $letra_asignada);
        //$this->letras= $letras;
        return $letras;
    }


    public function guardarControlAcceso($datos) {

        //$duracion=  $this->diffechaAction($fechasalida, $fechaingreso);//saca diferrencia de tiempo
        $fecha_inicio = date("Y/m/d");
        $hora_inicio = date("H:i:s");
        $fecha_fin = '0000/00/00';
        $hora_fin = '0000/00/00';
        $user = $datos->getId();

        //        $acceso= new ControlAcceso();
        //        $controlAcceso = $acceso->findFirst(0);
        //        if($controlAcceso){
        //            print_r($controlAcceso);
        //        }
        //        //die();
        //        if(!$id)
        //        $id=$this->Email->maximum('id')+1;
        //
        //        $acceso= new ControlAcceso();
        //        $acceso->setId($id);
        //        $acceso->setUsuarioId($usuario->getId());
        //        $acceso->setIp($nombreip);
        //        $acceso->setSesionInicio($fecha_inicio);
        //        $acceso->setHoraInicio($hora_inicio);
        //        $acceso->setSesionFin($fecha_fin);
        //        $acceso->setHoraFin($hora_fin);
        //        $acceso->setDuracion("0000/00/00");
        //        $acceso->setEstado("1");
        //        $acceso->setCreacionAt(date("Y/m/d H:i:s"));
        //        $acceso->save();


        //        $acceso= new ControlAcceso();
        //        $acceso->setId($sesion->getId());
        //        $acceso->setUsuarioId($usuario->getId());
        //        $acceso->setIp($sesion->getIp());
        //        $acceso->setSesionInicio($sesion->getSesionInicio());
        //        $acceso->setHoraInicio($sesion->getHoraInicio());
        //        $acceso->setSesionFin(date("Y/m/d"));
        //        $acceso->setHoraFin(date("H:i:s"));
        //        $acceso->setDuracion($duracion);
        //        $acceso->setEstado("0");
        //        $acceso->save();
    }

    //calculo de diferencia horarria (param@fecha_inicio a @fecha_fin en tipo datettima)
    public function diffechaAction($fechasalida ,$fechaingreso) {
        $tiempo=  $fechasalida-$fechaingreso;

        $signo=($tiempo<0) ?  "-" : "+";
        $tiempo=abs($tiempo);
        $dias=floor($tiempo/86400);
        $resto_dias=$tiempo % 86400;
        $horas=floor($resto_dias/3600);
        $resto_horas=$resto_dias % 3600;
        $minutos=floor($resto_horas/60);
        $resto_minutos=$resto_horas % 60;
        $segundos=floor($resto_minutos);
        $duracion=$horas.":".$minutos.":".$segundos;
        return $duracion;
    }

    /*Uso esta función*/
    public function difFecha($fecha_inicio, $fecha_fin) {

        $f_inicio = strtotime($fecha_inicio);
        $f_fin = strtotime($fecha_fin);

        $time = $f_fin - $f_inicio;
        if($time>=0 && $time<=59) {
            // Segundos
            $tiempo = "0:0:".$time;

        } elseif($time>=60 && $time<=3599) {
            // Minutos + Segundos
            $pmin = ($f_fin - $f_inicio) / 60;
            $premin = explode('.', $pmin);

            $presec = $pmin-$premin[0];
            $sec = $presec*60;

            $tiempo = "0:".$premin[0].':'.round($sec,0);

        } elseif($time>=3600 /*&& $time<=86399*/) {
            // Horas + Minutos
            $phour = ($f_fin - $f_inicio) / 3600;
            $prehour = explode('.',$phour);

            $premin = $phour-$prehour[0];
            $min = explode('.',$premin*60);

            $presec = '0.'.$min[1];
            $sec = $presec*60;

            $tiempo = $prehour[0].':'.$min[0].':'.round($sec,0);

        }
        return $tiempo;
    }

    /*
     * Funci�n que permite aumtentar los ceros que se desee
    */

    public function usuarioConectado($sesion) {
        $estado = false;


        return true;
    }

    /*
     * Funci�n que recibe par�metro en HH:MM:SS y devuelve en segundos
    */
    function totalSegundos($tiempo) {
        $segundos= substr($tiempo,6,2);
        $minutos= substr($tiempo,3,2)*60;
        $horas= substr($tiempo,0,2)*3600;
        $total_segundos=$segundos+$minutos+$horas;
        return $total_segundos;
    }
    /*Fución que devuelve en horas*/
    function tiempo($segundos) {
        $tiempo=$segundos;
        $signo=($tiempo<0) ?  "-" : "+";
        $tiempo=abs($tiempo);
        //$dias=floor($tiempo/86400);
        //$resto_dias=$tiempo % 86400;
        $horas=floor($tiempo/3600);
        $resto_horas=$tiempo % 3600;
        $minutos=floor($resto_horas/60);
        $resto_minutos=$resto_horas % 60;
        $segundos=floor($resto_minutos);
        //return $signo.$dias." d&iacute;as, ".$horas." horas, ".$minutos." minutos, ".$segundos." segundos";
        return $horas.":".$minutos.":".$segundos;
    }

    /*Función que devuelve la hora en formato 00:00:00
     *
    */
    /*Fución que devuelve en horas*/
    function tiempo2($segundos) {
        $tiempo=$segundos;
        $signo=($tiempo<0) ?  "-" : "+";
        $tiempo=abs($tiempo);
        //$dias=floor($tiempo/86400);
        //$resto_dias=$tiempo % 86400;
        $horas=floor($tiempo/3600);
        if (strlen($horas)==1)
            $horas="0".$horas;
        $resto_horas=$tiempo % 3600;
        $minutos=floor($resto_horas/60);
        if (strlen($minutos)==1)
            $minutos="0".$minutos;
        $resto_minutos=$resto_horas % 60;
        $segundos=floor($resto_minutos);
        if (strlen($segundos)==1)
            $segundos="0".$segundos;
        //return $signo.$dias." d&iacute;as, ".$horas." horas, ".$minutos." minutos, ".$segundos." segundos";
        return $horas.":".$minutos.":".$segundos;
    }

    /*
     * Funci�n que permite quital las tildes
     * Normalmente la uso para pasar por url
    */
    function quitaTildes($str) {
        $tildes=array('�','�','�','�','�');
        $vocales=array('a','e','i','o','u');
        str_replace($vocales,$tildes,$str);
        return $str;
    }

    /*
     * Permite sacar el d�a de la semana
    */
    function dia_semana($dia, $mes, $ano) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');
        return $dias[date("w", mktime(0, 0, 0, $mes, $dia, $ano))];
    }

    /*
     * Devuelve el encabezado para graficar Highcharts
    */
    public function encabezadoHighcharts($title="") {
        $html="";
        $html.=" <!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>";
        $html.="<html>";
        $html.="<head>";
        $html.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        $html.="<title>$title</title>";

        $html.="<!-- 1. Add these JavaScript inclusions in the head of your page -->";
        //$html.="<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
        $html.="<script type='text/javascript' src='../../js/highcharts-2-1-6/js/highcharts.js'></script>";

        $html.="<!-- 1a) Optional: the exporting module -->";
        $html.="<script type='text/javascript' src='../../js/highcharts-2-1-6/js/modules/exporting.js'></script>";

        $html.="<!-- 2. Add the JavaScript to initialize the chart on document ready -->";
        $html.="<script type='text/javascript'>";
        return ($html);
    }

    /*
     * Encabezado que contiene las librerias para table sorter
     * TableSorter es ordenar las tablas
    */
    public function encabezadoTableSorter() {
        //libreria jquery para tablesorter
        $html="<script type='text/javascript' src='../../js/jquery-latest.js'></script>
        <script type='text/javascript' src='../../js/jquery.tablesorter.js'></script>
        <link rel='stylesheet' href='../../css/themes_tablesorter/blue/style.css' type='text/css' id='' media='print, projection, screen' />

        <script type='text/javascript' id='js'>$(document).ready(function() {
	// call the tablesorter plugin, the magic happens in the markup
	$('table').tablesorter();
	//assign the sortStart event
	$('table').bind('sortStart',function() {
		$('#overlay').show();
	}).bind('sortEnd',function() {
		$('#overlay').hide();
	});
        });
        </script>

        <style>
        #overlay {
                top: 30px;
                left: 50%;
                position: absolute;
                margin-left: -100px;
                width: 200px;
                text-align: center;
                display: none;
                margin-top: -10px;
                background: #FF702B;
                color: #FFF;
                font-size: 14px;
                font-weight:bold;
        }
        </style>";
        return $html;
    }

    /*
     * Permite poner lo necesario para exporta a excel
     * action='ficheroExcel' debe ir la función desde donde se llama
    */
    public function excel() {
        $html="
        <script language='javascript'>
            $(document).ready(function() {
                $('.botonExcel').click(function(event) {
                    $('#datos_a_enviar').val( $('<div>').append( $('#Exportar_a_Excel').eq(0).clone()).html());
                    $('#FormularioExportacion').submit();
                });
            });
        </script>
        <style type='text/css'>
            .botonExcel{cursor:pointer;}
        </style>

        <form action='ficheroExcel' method='post' target='_blank' id='FormularioExportacion'>
            <div class='ocultar'>
                <p>Exportar a Excel  <img src='../../img/export_to_excel.gif' class='botonExcel' /></p>
                <input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />
            </div>
        </form>";
        return $html;
    }

    /*
     * Permite poner el icono de impresi�n
    */
    public function imprimir() {
        $html="<div id='div_impresora'>
                <input type='button' name='imprimir' value='Imprimir' onclick='window.print();'>
            </div>";
        return $html;
    }

    /*
     * Orden de arreglo según el indice
    */
    function ordenarArrayMultidimensional ($toOrderArray, $field, $inverse = false) {
        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key]  = $row[$field];
            $newRow[$key] = $row;
        }
        if ($inverse) {
            arsort($position);
        }
        else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            $returnArray[] = $newRow[$key];
        }
        return $returnArray;
    }

    /*
     * Funcion que aumenta ceros en el turno
     */
    function aumentaCerosTurno($numero){
        $contturno=strlen($numero);
        $turno="";
        if ($contturno == 1)
            $turno="00".$numero;
        else if ($contturno == 2)
            $turno="0".$numero;
        else
            $turno=$numero;
        return $turno;
    }

    /*
     * Funcion que aumenta ceros para la tiempos ejemplo si es 0:0:1 = 00:00:01
     */
    function aumentaCerosTiempo($tiempo){
        $cont=strlen($tiempo);
        $resultado="";
        if ($cont == 1)
            $resultado="0".$tiempo;
        else
            $resultado=$tiempo;
        return $resultado;
    }

    # Ésta función recibe un array con campos duplicados y lo
    # devuelve sin duplicidades.
    function eliminaDuplicados($array, $campo) {
      foreach ($array as $sub) {
        $cmp[] = $sub[$campo];
      }
      $unique = array_unique($cmp);
      foreach ($unique as $k => $campo) {
        $resultado[] = $array[$k];
      }
      return $resultado;
    }
}
?>
