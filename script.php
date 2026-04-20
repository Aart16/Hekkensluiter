<?php

include "database/database.php";
require __DIR__ . '/vendor/autoload.php';

$auth = new \Delight\Auth\Auth($dbconn);

try {
    $userId = $auth->register("51282@hoornbeeck.nl", "password123", "Aart Verschuure");

    echo 'We have signed up a new user with the ID ' . $userId;
}
catch (\Delight\Auth\InvalidEmailException $e) {
    die('Invalid email address');
}
catch (\Delight\Auth\InvalidPasswordException $e) {
    die('Invalid password');
}
catch (\Delight\Auth\UserAlreadyExistsException $e) {
    die('User already exists');
}
catch (\Delight\Auth\TooManyRequestsException $e) {
    die('Too many requests');
}