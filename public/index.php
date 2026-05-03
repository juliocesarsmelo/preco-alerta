<?php

session_start();

require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use Juliomelo\PrecoAlerta\Controllers\AuthController;

$pdo = \Database::conectar();
$auth = new AuthController($pdo);

$rota = $_GET['rota'] ?? '';

//Controle de acesso
if (!isset($_SESSION['user']) && $rota !== 'login' && $rota !== 'register') {
    header("Location: index.php?rota=login");
    exit;
}

switch ($rota) {

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
        session_destroy();
        header("Location: index.php?rota=login");
        exit;

    default:
        header("Location: index.php?rota=login");
        exit;
}

?>