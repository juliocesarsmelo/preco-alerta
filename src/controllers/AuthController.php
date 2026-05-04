<?php

namespace Juliomelo\PrecoAlerta\Controllers;



use Juliomelo\PrecoAlerta\Models\User;
use Juliomelo\PrecoAlerta\Models\PasswordReset;
use PDO;

class AuthController {

    private User $user;
    private PasswordReset $reset;

    public function __construct(PDO $pdo) {
        $this->user = new User($pdo);
        $this->reset = new PasswordReset($pdo);
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

        //Limpar mensagens anteriores
        unset($_SESSION['erro'], $_SESSION['sucesso']);

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $_SESSION['erro'] = "Preencha todos os campos";
            header("Location: index.php?route=login");
            exit;
        }

        $user = $this->user->findUserByEmail($email);

        if (!$user) {
            $_SESSION['erro'] = "Login inválido";
            header("Location: index.php?route=login");
            exit;
        }

        if (!$user['ativo']) {
            $_SESSION['erro'] = "Conta desativada";
            header("Location: index.php?route=login");
            exit;
        }

        if (!password_verify($password, $user['senha'])) {
            $_SESSION['erro'] = "Login inválido";
            header("Location: index.php?route=login");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = $user['id'];
        $_SESSION['name'] = $user['nome'];
        $_SESSION['perfil'] = $user['perfil'];

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

    public function admin() {

        if ($_SESSION['perfil'] !== 'admin') {
            echo "Acesso negado";
            exit;
        }

        $users = $this->user->getAllUsers();

        require __DIR__ . '/../Views/admin/admin.php';
    }

    public function toggleUser() {

        if ($_SESSION['perfil'] !== 'admin') {
            echo "Acesso negado";
            exit;
        }

        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if (!$id || !isset($status)) {
            header("Location: index.php?route=admin");
            exit;
        }

        $this->user->toggleUserStatus($id, $status);

        header("Location: index.php?route=admin");
        exit;
    }

    public function deleteAccount() {

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?route=login");
            exit;
        }

        $id = $_SESSION['user'];

        $this->user->deleteUser($id);

        // destruir sessão
        $_SESSION = [];
        session_destroy();

        header("Location: index.php?route=login");
        exit;
    }

    public function forgotPassword() {

        $email = trim($_POST['email'] ?? '');

        if (!$email) {
            $_SESSION['erro'] = "Informe o email";
            header("Location: index.php?route=forgot");
            exit;
        }

        $user = $this->user->findUserByEmail($email);

        if (!$user) {
            $_SESSION['erro'] = "Email não encontrado";
            header("Location: index.php?route=forgot");
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->reset->create($user['id'], $token, $expiracao);

        $link = "http://localhost/preco-alerta/public/index.php?route=reset&token=$token";

        echo "Link de recuperação: <a href='$link'>$link</a>";
        exit;
    }

    public function resetForm() {

        $token = $_GET['token'] ?? '';

        $data = $this->reset->findValidToken($token);

        if (!$data) {
            echo "Token inválido ou expirado";
            exit;
        }

        require __DIR__ . '/../Views/auth/reset.php';
    }

    public function resetPassword() {

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$token || !$password) {
            $_SESSION['erro'] = "Dados inválidos";
            header("Location: index.php?route=login");
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['erro'] = "Senha muito curta";
            header("Location: index.php?route=login");
            exit;
        }

        $data = $this->reset->findValidToken($token);

        if (!$data) {
            $_SESSION['erro'] = "Token inválido ou expirado";
            header("Location: index.php?route=login");
            exit;
        }

        // Atualiza senha
        $this->user->updatePassword($data['usuario_id'], $password);

        // Marca token como usado
        $this->reset->markAsUsed($token);

        $_SESSION['sucesso'] = "Senha redefinida!";
        header("Location: index.php?route=login");
        exit;
    }

    public function changePassword() {

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$new || !$confirm) {
            $_SESSION['erro'] = "Preencha todos os campos";
            header("Location: index.php?route=profile");
            exit;
        }

        if ($new !== $confirm) {
            $_SESSION['erro'] = "As senhas não coincidem";
            header("Location: index.php?route=profile");
            exit;
        }

        if (strlen($new) < 6) {
            $_SESSION['erro'] = "Nova senha muito curta";
            header("Location: index.php?route=profile");
            exit;
        }

        $user = $this->user->findUserById($_SESSION['user']);

        if (!password_verify($current, $user['senha'])) {
            $_SESSION['erro'] = "Senha atual incorreta";
            header("Location: index.php?route=profile");
            exit;
        }

        $this->user->updatePassword($user['id'], $new);

        $_SESSION['sucesso'] = "Senha alterada com sucesso!";
        header("Location: index.php?route=profile");
        exit;
    }
}
