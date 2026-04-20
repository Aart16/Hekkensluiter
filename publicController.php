<?php 
class publicController {
    public function __construct() {
        
    }

    public function home() {
        require_once "models/home.php";
    }

    public function login($auth) {
        require_once "models/login.php";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            try {
                $auth->login($_POST['email'], $_POST['password']);
                echo 'User is logged in';

            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                die('Verkeerd e-mailadres');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                die('Verkeerd wachtwoord');
            }
            catch (\Delight\Auth\EmailNotVerifiedException $e) {
                die('Email is niet geverifiëerd');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                die('teveel mogelijkheden geprobeerd');
            }
        header("location: overview");
        exit();
        }
    }
}