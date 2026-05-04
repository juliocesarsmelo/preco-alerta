<?php

session_start();

require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use Juliomelo\PrecoAlerta\Controllers\AuthController;

$pdo = \Database::conectar();
$auth = new AuthController($pdo);

$route = $_GET['route'] ?? 'login';

$public_routes = ['login', 'register'];
$admin_routes = ['admin', 'toggle-user'];

//Proteção rotas públicas
if (!isset($_SESSION['user']) && !in_array($route, $public_routes)) {
    header("Location: index.php?route=login");
    exit;
}

//Evitar login/cadastro já estando logado
if (isset($_SESSION['user']) && in_array($route, $public_routes)) {
    header("Location: index.php?route=home");
    exit;
}

//Proteção rotas admin
if (in_array($route, $admin_routes) && (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'admin')) {
    echo "Acesso negado";
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

    case 'profile':
        $auth->profile();
        break;

    case 'update-profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->updateProfile();
        }
        break;

    case 'admin':
        $auth->admin();
        break;

    case 'toggle-user':
        $auth->toggleUser();
        break;

    default:
        header("Location: index.php?route=login");
        exit;
}

?>