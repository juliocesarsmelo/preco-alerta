<?php

namespace Juliomelo\PrecoAlerta\Controllers;

use Juliomelo\PrecoAlerta\Models\User;
use PDO;

class AuthController {

    private User $user;

    public function __construct(PDO $pdo) {
        $this->user = new User($pdo);
    }

    public function register() {

        $name  = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$name || !$email || !$password) {
            echo "Preencha todos os campos";
            return;
        }

        if ($this->user->findUserByEmail($email)) {
            echo "Email já cadastrado";
            return;
        }

        $this->user->createUser($name, $email, $password);

        echo "Usuário cadastrado com sucesso!";
    }
}
