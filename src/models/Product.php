<?php

namespace Juliomelo\PrecoAlerta\Models;

use PDO;

class Product {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createProduct($userId, $name, $link, $targetPrice) {

        $sql = "INSERT INTO produtos_monitorados
                (usuario_id, nome, link, preco_desejado)
                VALUES
                (:usuario_id, :nome, :link, :preco_desejado)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':usuario_id' => $userId,
            ':nome' => $name,
            ':link' => $link,
            ':preco_desejado' => $targetPrice
        ]);
    }

    public function getProductsByUser($userId) {

        $sql = "SELECT * FROM produtos_monitorados
                WHERE usuario_id = :usuario_id
                ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function toggleAlert($id, $status, $userId) {

        $sql = "UPDATE produtos_monitorados
                SET alerta_ativo = :status
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }

    public function deleteProduct($id, $userId) {

        $sql = "DELETE FROM produtos_monitorados
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }

    public function findProductById($id, $userId) {
        $sql = "SELECT * FROM produtos_monitorados
                WHERE id = :id
                AND usuario_id = :usuario_id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $userId
        ]);

        return $stmt->fetch();
    }

    public function updateCurrentPrice($id, $userId, $price) {
        $sql = "UPDATE produtos_monitorados
                SET preco_atual = :preco_atual
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':preco_atual' => $price,
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }

    public function updateLastAlertPrice($id, $userId, $price) {
        $sql = "UPDATE produtos_monitorados
                SET ultimo_preco_alertado = :price
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':price' => $price,
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }

    public function addPriceHistory($productId, $price) {
        $sql = "INSERT INTO historico_precos (produto_id, preco)
                VALUES (:produto_id, :preco)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':produto_id' => $productId,
            ':preco' => $price
        ]);
    }

    public function getPriceHistory($productId, $userId) {
        $sql = "SELECT h.*
                FROM historico_precos h
                INNER JOIN produtos_monitorados p
                    ON h.produto_id = p.id
                WHERE h.produto_id = :produto_id
                AND p.usuario_id = :usuario_id
                ORDER BY h.coletado_em DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':produto_id' => $productId,
            ':usuario_id' => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function getAllProducts() {

        $sql = "SELECT * FROM produtos_monitorados
                WHERE alerta_ativo = 1";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function markAlertAsSent($id, $userId) {
        $sql = "UPDATE produtos_monitorados
                SET alerta_enviado = 1
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }

    public function resetAlertSent($id, $userId) {
        $sql = "UPDATE produtos_monitorados
                SET alerta_enviado = 0
                WHERE id = :id
                AND usuario_id = :usuario_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':usuario_id' => $userId
        ]);
    }
}