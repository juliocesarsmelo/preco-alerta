<?php

require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use Juliomelo\PrecoAlerta\Controllers\AuthController;

$pdo = \Database::conectar();

$rota = $_GET['rota'] ?? '';

$auth = new AuthController($pdo);

switch ($rota) {

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register();
        } else {
            require '../src/Views/auth/register.php';
        }
        break;

    default:
        echo "Página inicial";
}

?>