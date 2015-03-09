<?php
	$host = "localhost";	// el host de la base de datos
	$user = "root";			// usuario de la base de datos
	$pass = "";				// contraseña de la base de datos
	$bbdd = "dbsmartq";		// base de datos a usar
	
	/*********** esto crea la conexión a la base de datos **************/
	$conexio = mysql_connect($host,$user,$pass) or die(mysql_error()); // $conexion es la conexión a usar.
	mysql_select_db($bbdd,$conexio) or die(mysql_error());
	
	/////////////////////////////////////////////////////////////////////
	
	$consulta = "SELECT * FROM `flash2sql` ORDER BY `ID` DESC  LIMIT 0 , 4";
	$res = mysql_query($consulta)or die(mysql_error());
	echo "<palaueb>";
	while($val=mysql_fetch_array($res)){
		echo "<datos campo1=\"".$val[CAMPO1]."\" campo2=\"".$val[CAMPO2]."\" campo3=\"".$val[CAMPO3]."\" />";
	}
	echo "</palaueb>";
	
	//echo nelson;
	/*$consulta = "SELECT * FROM `flash2sql` ORDER BY `ID` DESC  LIMIT 0 , 4";
	$res = mysql_query($consulta)or die(mysql_error());
	while($val=mysql_fetch_array($res)){
//		echo " ".$val[CAMPO1]." ".$val[CAMPO2]." ".$val[CAMPO3]." ";
		$nombre = $val[CAMPO1];
		
	}
	echo $nombre;*/
?>