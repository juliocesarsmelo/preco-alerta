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

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$name || !$email || !$password) {
            $_SESSION['erro'] = "Preencha todos os campos";
            header("Location: index.php?route=register");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = "Email inválido";
            header("Location: index.php?route=register");
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['erro'] = "Senha muito curta";
            header("Location: index.php?route=register");
            exit;
        }

        if ($this->user->findUserByEmail($email)) {
            $_SESSION['erro'] = "Email já existe";
            header("Location: index.php?route=register");
            exit;
        }

        $this->user->createUser($name, $email, $password);

        $_SESSION['sucesso'] = "Cadastro realizado!";
        header("Location: index.php?route=login");
        exit;
    }

    public function login() {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['erro'] = "Preencha todos os campos";
            header("Location: index.php?route=login");
            exit;
        }

        $user = $this->user->findUserByEmail($email);

        if (!$user || !password_verify($password, $user['senha'])) {
            $_SESSION['erro'] = "Login inválido";
            header("Location: index.php?route=login");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = $user['id'];
        $_SESSION['name'] = $user['nome'];

        header("Location: index.php?route=home");
        exit;
    }

    public function profile() {

        $user = $this->user->findUserById($_SESSION['user']);
        require __DIR__ . '/../Views/user/profile.php';
    }

    public function updateProfile() {

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $id = $_SESSION['user'];

        if (!$name || !$email) {
            $_SESSION['erro'] = "Preencha todos os campos";
            header("Location: index.php?route=profile");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = "Email inválido";
            header("Location: index.php?route=profile");
            exit;
        }

        //evitar email duplicado
        $existingUser = $this->user->findUserByEmail($email);

        if ($existingUser && $existingUser['id'] != $id) {
            $_SESSION['erro'] = "Email já está em uso";
            header("Location: index.php?route=profile");
            exit;
        }

        $this->user->updateUser($_SESSION['user'], $name, $email);

        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        $_SESSION['sucesso'] = "Perfil atualizado!";
        header("Location: index.php?route=profile");
        exit;
}
}
