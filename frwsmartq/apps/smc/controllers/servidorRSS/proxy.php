<?php

/*
 * Este script necesita un directorio llamado 'temp/' en el que poder guardar la cache
 * de cada página HTML que se descarga
 */

$url = $_GET["url"];
$ct = $_GET["ct"]; // Content-Type
if(!isset($ct) || $ct == null) {
  $ct = "text/html";
}
$fichero_cache = 'temp/' . md5($url);
$diferencia_tiempo = @(time() - filemtime($fichero_cache));
$contenido = "";

if($diferencia_tiempo < 30 * 60 * 1000) {
  $contenido = file_get_contents($fichero_cache);
}
else {
  if ($f = fopen($url, 'r')) {
    while (!feof($f)) {
      $contenido .= fgets($f, 4096);
    }
    fclose($f); 
  }

  if ($f = fopen($fichero_cache, 'w')) {
    fwrite($f, $contenido, strlen($contenido));
    fclose($f);
  }
}

header("Content-Type: $ct");
echo $contenido;

?>