<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "Hekkensluiter" ?></title>
    <link rel="stylesheet" href="<?= $dir_backs ?>style.css">
</head>
<body>
    <header>
        <img src="<?= $dir_backs ?>Logo_HoornHek.png" alt="Hoornhek Logo" id="img2">
        <a class="a" href="<?= $dir_backs ?>home" id="back_home">home</a>
        <a class="a" href="<?= $dir_backs ?>overview" id="overview">overview</a>
        <a class="a" href="<?= $dir_backs ?>prisoners" id="gevangenen">gevangenen</a>        
        <a class="a" href="<?= $dir_backs ?>my_account" id="mijn_account">mijn account</a>
        <a class="a" href="<?= $dir_backs ?>logout" id="logout">uitloggen</a>
    </header>

    <div id="id">