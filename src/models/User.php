<?php 

namespace Juliomelo\PrecoAlerta\Models;

use PDO;

class User {
    
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createUser($name, $email, $password) {

        $sql = "INSERT INTO usuarios (nome, email, senha)
                VALUES (:nome, :email, :senha)";

        $stmt = $this->pdo->prepare($sql);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        return $stmt->execute([
            ':nome' => $name,
            ':email' => $email,
            ':senha' => $passwordHash
        ]);
    }

    public function findUserByEmail($email) {

        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function findUserById($id) {

        $sql = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function updateUser($id, $name, $email) {
        
        $sql = "UPDATE usuarios 
                SET nome = :nome, email = :email 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nome' => $name,
            ':email' => $email,
            ':id' => $id
        ]);
    }

    public function getAllUsers() {
        $sql = "SELECT id, nome, email, ativo, perfil FROM usuarios";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function toggleUserStatus($id, $status) {
        $sql = "UPDATE usuarios SET ativo = :ativo WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':ativo' => $status = $status ? 1 : 0,
            ':id' => $id
        ]);
    }
}