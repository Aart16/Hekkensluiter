<?php

require_once "database/database.php";
require __DIR__ . "/vendor/autoload.php";

$auth = new \Delight\Auth\Auth($dbconn);

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$urlSegments = explode('/', trim($url, '/'));

$action = !empty($urlSegments[3]) ? $urlSegments[3] : 'home';

$allowed_pages = ["home" => "home.html"];

if(isset($allowed_pages["home"])) {
    include_once $allowed_pages["home"];
}