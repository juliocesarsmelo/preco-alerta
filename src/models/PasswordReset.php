<?php

namespace Juliomelo\PrecoAlerta\Models;

use PDO;

class PasswordReset {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($userId, $token, $expiracao) {

        $sql = "INSERT INTO recuperacao_senha 
                (usuario_id, token, expiracao, usado)
                VALUES (:usuario_id, :token, :expiracao, 0)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':usuario_id' => $userId,
            ':token' => $token,
            ':expiracao' => $expiracao
        ]);
    }

    public function findValidToken($token) {

        $sql = "SELECT * FROM recuperacao_senha 
                WHERE token = :token 
                AND expiracao > NOW() 
                AND usado = 0
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);

        return $stmt->fetch();
    }

    public function markAsUsed($token) {

        $sql = "UPDATE recuperacao_senha 
                SET usado = 1 
                WHERE token = :token";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':token' => $token]);
    }
}