<?php 
require_once __DIR__ . "/database/database.php";
require __DIR__ . '/vendor/autoload.php';
$auth = new \Delight\Auth\Auth($dbconn);

foreach (glob("controllers/*.php") as $filename) {
    require_once $filename;
}
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);    
$divided_url = explode("/", trim($url, "/"));
$path = $divided_url[3] ?? "home";

$additional_info = !empty($divided_url[4]) ? $divided_url[4] : NULL;
$dir_backs = "";
// afhankelijk van hoe diep je projectmap staat, verander de waarde van $x als je te ver terug of vooruit gaat.
for ($x = 5; $x <= count($divided_url); $x++) {
    $dir_backs .= "../"; 
}

$allowed_paths = [
    "public" => [
        "login",
        "home",
        "",
    ],
    "private" => [
        "overview",
        "my_account",
        "cells",
        "prisoners",
        "history",
        "deleteprisoner",
        "users",
        "editroles",
        "logout",
        "editprisoners",
        "register",
        "addprisoner",
        "view_dossier",
        "view_history_dossier",
        "view_cell_log",
    ],
];

if (!in_array($path, $allowed_paths["public"], true) && !in_array($path, $allowed_paths["private"], true)) {
    http_response_code(404);
    exit;
}


if ($auth->isLoggedIn()) {
    require_once "Layout/private_header.php";    
} else {
    require_once "Layout/public_header.php";
}

if (in_array($path, $allowed_paths["public"], true)) {
    //dit is openbaar beschikbaar.


    // echo "public<br>";
    $controller = new publicController();
    if ($path === "login") {
        $controller->$path($auth);
    } else {
        $controller->$path();
    }

} elseif (in_array($path, $allowed_paths["private"], true)) {
    //dit is alleen als je ingelogd bent beschikbaar
    if (!$auth->isLoggedIn()) {
        header("location:" . $dir_backs . "home");
        exit;
    }
    // echo "private<br>";
    
    $controller = new privateController($auth, $path, $dir_backs, $dbconn);
        if (!isset($additional_info)) {
        $controller->$path();
    } else {
        $controller->$path($additional_info);
    }

}


if ($auth->isLoggedIn()) {
    require_once "Layout/private_bottom.php";
} else {
    require_once "Layout/public_bottom.php";
}

// verteld de browser dat hij de pagina niet kan vinden als het pad niet vindbaar is
http_response_code(404);
exit;