<?php

$hostname_pki = "192.168.25.29";
$database_pki = "smartq-ee5";
$username_pki = "smartq";
$password_pki = "123";
$pki = mysql_pconnect($hostname_pki, $username_pki, $password_pki) or trigger_error(mysql_error(), E_USER_ERROR);
?>