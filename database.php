<?php

//initialiseren
define('HOST', 'localhost');
define('DATABASE', 'Hekkensluiter');
define('USER', 'root');
define('PASSWORD','');
$dbconn='';
//connectie maken
try {
    $dbconn = new PDO("mysql:host=" . HOST . ";dbname=" . DATABASE . ";charset=utf8mb4", USER,PASSWORD);
 }
catch (exception $e) {
    $dbconn = $e->getMessage();
}