<?php

session_start();

require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use Juliomelo\PrecoAlerta\Controllers\AuthController;
use Juliomelo\PrecoAlerta\Controllers\ProductController;

$pdo = \Database::conectar();
$auth = new AuthController($pdo);
$productController = new ProductController($pdo);

$route = $_GET['route'] ?? 'login';

$public_routes = ['login', 'register', 'forgot', 'reset'];
$admin_routes = ['admin', 'toggle-user', 'update-all-prices'];

//Proteção rotas públicas
if (!isset($_SESSION['user']) && !in_array($route, $public_routes)) {
    header("Location: index.php?route=login");
    exit;
}

//Proteção contra usuário desativado logado
if (isset($_SESSION['user'])) {

    $user = (new \Juliomelo\PrecoAlerta\Models\User($pdo))
                ->findUserById($_SESSION['user']);

    if (!$user || !$user['ativo']) {
        $_SESSION = [];
        session_destroy();

        header("Location: index.php?route=login");
        exit;
    }
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

    case 'delete-account':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->deleteAccount();
        }
        break;

    case 'forgot':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->forgotPassword();
        } else {
            require '../src/Views/auth/forgot.php';
        }
        break;

    case 'reset':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->resetPassword();
        } else {
            $auth->resetForm();
        }
        break;

    case 'change-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->changePassword();
        }
        break;

    case 'products':
        $productController->index();
        break;

    case 'create-product':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productController->create();
        }
        break;
    
    case 'toggle-alert':
        $productController->toggleAlert();
        break;

    case 'delete-product':
        $productController->delete();
        break;  
    
    case 'update-price':
        $productController->updatePrice();
        break;

    case 'price-history':
        $productController->history();
        break;

    case 'update-all-prices':
        $productController->updateAllPrices();
        break;

    default:
        header("Location: index.php?route=login");
        exit;
}

?>