<?php
/*
 * Este script utiliza la librería SimplePie para descubrir la URL del archivo RSS
 * Además, el script necesita un directorio llamado "temp" donde guarda todos los archivos descargados
 */
require_once('simplepie.inc');

$url = $_GET["url"];

$canal = new SimplePie();
$canal->set_feed_url($url);
$canal->set_cache_location('./temp');
$canal->init();

echo $canal->feed_url;

?>
