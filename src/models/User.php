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
}