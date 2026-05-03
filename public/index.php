<?php

session_start();

require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use Juliomelo\PrecoAlerta\Controllers\AuthController;

$pdo = \Database::conectar();
$auth = new AuthController($pdo);

$route = $_GET['route'] ?? '';

$public_route = ['login', 'register'];

//Controle de acesso
if (!isset($_SESSION['user']) && !in_array($route, $public_route)) {
    header("Location: index.php?route=login");
    exit;
}

//Evitar login já estando logado
if (isset($_SESSION['user']) && $route === 'login') {
    header("Location: index.php?rota=home");
    exit;
}

switch ($route) {

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        } else {
            require '../src/Views/auth/login.php';
        }
        break;
        
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register();
        } else {
            require '../src/Views/auth/register.php';
        }
        break;

    case 'home':
        require '../src/Views/home.php';
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?route=login");
        exit;

    default:
        header("Location: index.php?route=login");
        exit;
}

?>