<?php

$hostname_pki = "192.10.10.160";
$database_pki = "smartq-ee4";
$username_pki = "smartq";
$password_pki = "smartq";
$pki = mysql_pconnect($hostname_pki, $username_pki, $password_pki) or trigger_error(mysql_error(), E_USER_ERROR);
?>